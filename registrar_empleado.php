<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["admin_logged"]) || $_SESSION["admin_logged"] !== true) {
    header("Location: admin_login.php");
    exit;
}

$mensaje_empleado = "";
$error_empleado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $dni = trim($_POST["dni"]);
    $password = trim($_POST["password"]);

    if (!empty($nombre) && !empty($apellido) && !empty($dni) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql_insert = "INSERT INTO empleados (nombre, apellido, dni, password) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($sql_insert);
        
        if ($stmt_insert) {
            $stmt_insert->bind_param("ssss", $nombre, $apellido, $dni, $password_hash);
            if ($stmt_insert->execute()) {
                $mensaje_empleado = "¡Empleado registrado correctamente!";
            } else {
                $error_empleado = "Error al registrar el empleado (¿el DNI ya esta registrado?).";
            }
            $stmt_insert->close();
        }
    } else {
        $error_empleado = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="panel-body">

    <div class="admin-main-container" style="max-width: 600px;">
        
        <header class="panel-header" style="margin-bottom: 20px;">
            <a href="admin_panel.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al panel</a>
            <h1 style="color: #2e1065; font-size: 1.5rem;">Nuevo Empleado</h1>
        </header>

        <div class="admin-card-section">
            <div class="section-title-box">
                <h3><i class="fa-solid fa-user-plus"></i> Alta de empleado</h3>
                <p>Completa los datos para crear un nuevo usuario en el sistema.</p>
            </div>

            <?php if (!empty($mensaje_empleado)): ?>
                <div class="success-msg"><?php echo $mensaje_empleado; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_empleado)): ?>
                <div class="error-msg"><?php echo $error_empleado; ?></div>
            <?php endif; ?>

            <form action="registrar_empleado.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                
                <div class="input-group-admin">
                    <label style="display: block; color: #2e1065; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Nombre</label>
                    <input type="text" name="nombre" placeholder="Ej: Lucas" required>
                </div>

                <div class="input-group-admin">
                    <label style="display: block; color: #2e1065; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Apellido</label>
                    <input type="text" name="apellido" placeholder="Ej: Perez" required>
                </div>

                <div class="input-group-admin">
                    <label style="display: block; color: #2e1065; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">DNI</label>
                    <input type="text" name="dni" placeholder="Ej: 40111222" required>
                </div>

                <div class="input-group-admin">
                    <label style="display: block; color: #2e1065; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-action btn-ingreso" style="padding: 14px; border-radius: 30px; border: none; cursor: pointer; font-weight: 600; margin-top: 10px;">Guardar empleado</button>
            </form>
        </div>

    </div>

</body>
</html>