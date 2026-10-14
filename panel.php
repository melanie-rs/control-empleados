<?php
// Validar si el empleado ha iniciado sesion
    session_start();

    require_once "config.php";

    if (!isset($_SESSION["empleado_id"])) {
        header("Location: login.php");
        exit;
    }

    if (isset($_SESSION["confirmando_movimiento"])) {
        if (
            !isset($_SESSION["confirmacion_expira"]) ||
            time() >= $_SESSION["confirmacion_expira"]
        ) {
            header("Location: logout.php");
            exit;
            }
    }
    
    $nombre = $_SESSION["empleado_nombre"];
    $apellido = $_SESSION["empleado_apellido"];

    
    $sql_ultimo = "SELECT tipo, fecha_hora
                    FROM movimientos
                    WHERE empleado_id = ?
                    ORDER BY fecha_hora DESC, id DESC
                    LIMIT 1";

    $stmt_ultimo = $conexion->prepare($sql_ultimo);

    if (!$stmt_ultimo) {
        die("No se pudo consultar el último movimiento.");
    }

    $stmt_ultimo->bind_param("i", $_SESSION["empleado_id"]);
    $stmt_ultimo->execute();

    $resultado_ultimo = $stmt_ultimo->get_result();
    $ultimoMovimiento = $resultado_ultimo->fetch_assoc();

    $stmt_ultimo->close();

    $ultimoTipo = $ultimoMovimiento["tipo"] ?? null;


?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel del empleado</title>
        <!--iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/estilo.css">
    </head>
    <body class="panel-body">

    <div class="panel-container">
        
        <div class="panel-top-header">
            <div class="panel-logo-title">
                <i class="fa-solid fa-users"></i>
                <h1>Control de empleados</h1>
            </div>
        </div>

        <h2>Bienvenido/a, <span><?php echo htmlspecialchars($nombre . " " . $apellido); ?></span></h2>

        <p class="panel-subtext">
            <a href="logout.php" class="btn-logout-inline"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesion</a>
        </p>

        <?php if ($ultimoMovimiento): ?>
        <p class="panel-desc-info">
                Tu último registro fue:
            <strong><?php echo htmlspecialchars(strtoupper($ultimoMovimiento["tipo"])); ?></strong>
                a las
            <strong><?php echo date("H:i", strtotime($ultimoMovimiento["fecha_hora"])); ?></strong>
                del
            <strong><?php echo date("d/m/Y", strtotime($ultimoMovimiento["fecha_hora"])); ?></strong>.
        </p>
        <?php else: ?>
        <p class="panel-desc-info">
            Todavía no tenés movimientos registrados.
        </p>
        <?php endif; ?>



        <?php if (isset($_SESSION["confirmando_movimiento"])): ?>

            <div class="success-msg">
                ¡<?php echo htmlspecialchars(strtoupper($_SESSION["tipo_movimiento_confirmado"])); ?>
                registrado correctamente!

                <p>
                    La sesión se cerrará en
                    <strong id="cuenta-regresiva">3</strong>
                    segundos...
                </p>
            </div>
            
        <script>
            const horaLimite = <?php echo (int) $_SESSION["confirmacion_expira"]; ?>;
            const contador = document.getElementById("cuenta-regresiva");

            function actualizarCuentaRegresiva() {
                const ahora = Math.floor(Date.now() / 1000);
                const segundos = Math.max(0, horaLimite - ahora);

                contador.textContent = segundos;

                if (segundos <= 0) {
                    window.location.href = "logout.php";
                }
            }

            actualizarCuentaRegresiva();
            const temporizador = setInterval(actualizarCuentaRegresiva, 200);
        </script>

        <?php endif; ?>


        <?php if (isset($_GET["error"]) && $_GET["error"] === "movimiento"): ?>
            <div class="error-msg">No podes registrar dos movimientos iguales seguidos.</div>
        <?php endif; ?>

        <?php if (!isset($_SESSION["confirmando_movimiento"])): ?>
        <div class="action-buttons-container">

            <?php if ($ultimoTipo === null || $ultimoTipo === "egreso"): ?>

                <form action="registrar.php" method="POST">
                    <input type="hidden" name="tipo" value="ingreso">
                        <button type="submit" class="btn-action-custom btn-ingreso">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            Registrar ingreso
                        </button>
                </form>

            <?php elseif ($ultimoTipo === "ingreso"): ?>

                <form action="registrar.php" method="POST">
                    <input type="hidden" name="tipo" value="egreso">
                        <button type="submit" class="btn-action-custom btn-egreso">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Registrar egreso
                        </button>
                </form>

            <?php endif; ?>

        </div>
        <?php endif; ?>

        <h2 class="history-title"><i class="fa-regular fa-clock"></i> Historial de movimientos</h2>

        <?php
            $sql = "SELECT tipo, fecha_hora
                    FROM movimientos
                    WHERE empleado_id = ?
                    ORDER BY fecha_hora DESC, id DESC";

            $stmt = $conexion->prepare($sql);

            if (!$stmt) {
                die("No se pudo consultar el historial.");
            }

            $stmt->bind_param("i", $_SESSION["empleado_id"]);
            $stmt->execute();

            $resultado = $stmt->get_result();
        ?>

        <?php if ($resultado->num_rows > 0): ?>

        <div class="table-responsive">
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Movimiento</th>
                        <th>Fecha y hora</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($movimiento = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars(ucfirst($movimiento["tipo"])); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($movimiento["fecha_hora"]); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>

            <p class="no-records">Todavia no tenes movimientos registrados.</p>

        <?php endif; ?>

        <?php
            $stmt->close();
        ?>

    </div>

</body>
</html>