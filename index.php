<?php
session_start();

// destruye cualquier sesion 
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Control de Empleados</title>
    <!--iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="index-body">

    <div class="index-container">
        
        <div class="index-header">
            <h1>¡Bienvenido/a!</h1>
            <p>Selecciona como deseas ingresar al sistema</p>
        </div>

        <div class="role-selection-grid">

            <!-- Este es el empleado -->
            <a href="login.php?rol=empleado" class="role-card employee-card">
                <div class="role-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span>Soy Empleado</span>
            </a>

            <!-- Este es el admin -->
            <a href="admin_login.php" class="role-card admin-card">
                <div class="role-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <span>Soy Administrador</span>
            </a>
        </div>

    </div>

</body>
</html>