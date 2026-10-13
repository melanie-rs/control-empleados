<?php

session_start();

require_once "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $dni = trim($_POST["dni"] ?? "");
    $clave = $_POST["clave"] ?? "";

    $sql = "SELECT id, dni, clave, nombre, apellido
            FROM empleados
            WHERE dni = ? AND activo = 1";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        die("No se pudo preparar la consulta.");
    }

    $stmt->bind_param("s", $dni);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $empleado = $resultado->fetch_assoc();

    if (
        $empleado &&
        password_verify($clave, $empleado["clave"])
    ) {

        session_regenerate_id(true);

        $_SESSION["empleado_id"] = $empleado["id"];
        $_SESSION["empleado_nombre"] = $empleado["nombre"];
        $_SESSION["empleado_apellido"] = $empleado["apellido"];

        header("Location: panel.php");
        exit;

    } else {
        $error = "DNI o contraseña incorrectos.";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>
<body>

    <h1>Control de empleados</h1>

    <h2>Iniciar sesión</h2>

    <?php if ($error !== ""): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <label for="dni">DNI:</label>
        <input
            type="text"
            id="dni"
            name="dni"
            inputmode="numeric"
            pattern="[0-9]{1,15}"
            maxlength="15"
            required
        >

        <br><br>

        <label for="clave">Contraseña:</label>
        <input
            type="password"
            id="clave"
            name="clave"
            required
        >

        <br><br>

        <button type="submit">Ingresar</button>

    </form>

</body>
</html>