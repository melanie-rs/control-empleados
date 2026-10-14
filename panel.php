<?php
// Validar si el empleado ha iniciado sesion
    session_start();

    require_once "config.php";

    if (!isset($_SESSION["empleado_id"])) {
        header("Location: login.php");
        exit;
    }

    $nombre = $_SESSION["empleado_nombre"];
    $apellido = $_SESSION["empleado_apellido"];

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

        <p class="panel-desc-info">Desde aqui podras registrar tu ingreso y tu egreso.</p>

        <?php if (isset($_GET["registro"]) && $_GET["registro"] === "ok"): ?>
            <div class="success-msg">¡Movimiento registrado correctamente!</div>
        <?php endif; ?>

        <?php if (isset($_GET["error"]) && $_GET["error"] === "movimiento"): ?>
            <div class="error-msg">No podes registrar dos movimientos iguales seguidos.</div>
        <?php endif; ?>

        <div class="action-buttons-container">
            <form action="registrar.php" method="POST">
                <input type="hidden" name="tipo" value="ingreso">
                <button type="submit" class="btn-action-custom btn-ingreso"><i class="fa-solid fa-arrow-right-to-bracket"></i> Registrar ingreso</button>
            </form>

            <form action="registrar.php" method="POST">
                <input type="hidden" name="tipo" value="egreso">
                <button type="submit" class="btn-action-custom btn-egreso"><i class="fa-solid fa-arrow-right-from-bracket"></i> Registrar egreso</button>
            </form>
        </div>

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