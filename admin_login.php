<?php
session_start();
require_once "config.php"; // conexion

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    // Dejo esto fijo asi no toco la base de datos
    if ($usuario === "admin" && $password === "admin123") {
        $_SESSION["admin_logged"] = true;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "Usuario o contraseña de administrador incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Control de Empleados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="login-container">
        <div class="login-form-section">
            
            <a href="index.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>

            <div class="form-header">
                <h2>Panel Admin</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="admin_login.php" method="POST">
                <div class="input-group">
                    <i class="fa-solid fa-user-shield"></i>
                    <input type="text" name="usuario" placeholder="Usuario administrador" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>

                <button type="submit" class="btn-submit">Ingresar como Admin</button>
            </form>

        </div>
        <div class="login-banner-section"></div>
    </div>

</body>
</html>