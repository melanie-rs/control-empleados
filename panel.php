<?php

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
    </head>
    <body>

        <h1>Control de empleados</h1>

        <h2>Bienvenido/a, <?php echo htmlspecialchars($nombre . " " . $apellido); ?></h2>

        <p>Desde aquí podrás registrar tu ingreso y tu egreso.</p>

        <?php if (isset($_GET["registro"]) && $_GET["registro"] === "ok"): ?>
            <p>¡Movimiento registrado correctamente!</p>
        <?php endif; ?>

        <?php if (isset($_GET["error"]) && $_GET["error"] === "movimiento"): ?>
            <p>No podés registrar dos movimientos iguales seguidos.</p>
        <?php endif; ?>

        <form action="registrar.php" method="POST">
            <input type="hidden" name="tipo" value="ingreso">
            <button type="submit">Registrar ingreso</button>
        </form>

        <form action="registrar.php" method="POST">
            <input type="hidden" name="tipo" value="egreso">
            <button type="submit">Registrar egreso</button>
        </form>

        <h2>Historial de movimientos</h2>

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

        <table border="1" cellpadding="8">
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

        <?php else: ?>

            <p>Todavía no tenés movimientos registrados.</p>

        <?php endif; ?>

        <?php
            $stmt->close();
        ?>

        <a href="logout.php">Cerrar sesión</a>

    </body>
</html>