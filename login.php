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

    if ($empleado && password_verify($clave, $empleado["clave"])) {
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
    <title>Iniciar Sesion - Control de Empleados</title>
    <!-- Vinculo iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="login-container">
        <!-- Esta es la columna izquierda del formulario -->
        <div class="login-form-section">
            <div class="form-header">
                <a href="index.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
                <h2>Inicie Sesion</h2>
            </div>

            <?php if ($error !== ""): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="form-content">
                
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        placeholder="DNI"
                        inputmode="numeric"
                        pattern="[0-9]{1,15}"
                        maxlength="15"
                        required
                    >
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        placeholder="Contraseña"
                        required
                    >
                </div>

                <button type="submit" class="btn-submit">Ingresar</button>

            </form>
        </div>

        <!-- Columna derecha morada -->
        <div class="login-banner-section">
    <div class="banner-content">
</div>

</body>
</html>