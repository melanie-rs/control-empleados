<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["admin_logged"]) || $_SESSION["admin_logged"] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Obtener lista de empleados para el filtro
$sql_empleados = "SELECT id, nombre, apellido FROM empleados ORDER BY apellido ASC";
$result_empleados = $conexion->query($sql_empleados);
$empleados_lista = [];
if ($result_empleados) {
    while ($emp = $result_empleados->fetch_assoc()) {
        $empleados_lista[] = $emp;
    }
}

$filtro_empleado = isset($_GET["empleado_id"]) ? $_GET["empleado_id"] : "";
$filtro_fecha = isset($_GET["fecha"]) ? $_GET["fecha"] : "";

$sql_movimientos = "SELECT m.id, m.tipo, m.fecha_hora, e.nombre, e.apellido, e.dni 
                    FROM movimientos m 
                    INNER JOIN empleados e ON m.empleado_id = e.id 
                    WHERE 1=1";

$params = [];
$types = "";

if (!empty($filtro_empleado)) {
    $sql_movimientos .= " AND m.empleado_id = ?";
    $params[] = $filtro_empleado;
    $types .= "i";
}

if (!empty($filtro_fecha)) {
    $sql_movimientos .= " AND DATE(m.fecha_hora) = ?";
    $params[] = $filtro_fecha;
    $types .= "s";
}

$sql_movimientos .= " ORDER BY m.fecha_hora DESC, m.id DESC";

$stmt_mov = $conexion->prepare($sql_movimientos);
if (!empty($params)) {
    $stmt_mov->bind_param($types, ...$params);
}
$stmt_mov->execute();
$resultado_movimientos = $stmt_mov->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Control de Empleados</title>
    <!--Iconito-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="panel-body">

    <div class="admin-main-container">
        
        <!-- aca hago la cabecera del Panel Admin -->
        <header class="panel-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px;">
            <div class="panel-logo-title" style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-user-shield" style="font-size: 1.8rem; color: #581c87;"></i>
                <h1 style="font-size: 1.6rem; color: #2e1065; margin: 0;">Panel de Administrador</h1>
            </div>
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <a href="registrar_empleado.php" class="btn-action btn-ingreso" style="text-decoration: none; padding: 10px 20px; border-radius: 20px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user-plus"></i> Registrar nuevo empleado
                </a>
                <a href="index.php" class="btn-index" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesion</a>
            </div>
        </header>

        <!-- historial y filtros -->
        <div class="admin-card-section">
            <div class="section-title-box">
                <h3><i class="fa-solid fa-filter"></i> Historial general y filtros de movimientos</h3>
            </div>

            <form method="GET" action="admin_panel.php" class="admin-filters-form">
                <div class="filter-group">
                    <select name="empleado_id" class="admin-select">
                        <option value="">Todos los empleados</option>
                        <?php foreach ($empleados_lista as $emp): ?>
                            <option value="<?php echo $emp['id']; ?>" <?php echo ($filtro_empleado == $emp['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($emp['apellido'] . ", " . $emp['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <input type="date" name="fecha" value="<?php echo htmlspecialchars($filtro_fecha); ?>" class="admin-date">
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn-action btn-ingreso">Filtrar</button>
                    <a href="admin_panel.php" class="btn-action btn-egreso" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Limpiar</a>
                </div>
            </form>

            <?php if ($resultado_movimientos->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>DNI</th>
                            <th>Movimiento</th>
                            <th>Fecha y hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($mov = $resultado_movimientos->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($mov["apellido"] . ", " . $mov["nombre"]); ?></strong></td>
                                <td><?php echo htmlspecialchars($mov["dni"]); ?></td>
                                <td>
                                    <?php 
                                        $tipoTexto = $mov["tipo"];
                                        $claseBadge = (strtolower($tipoTexto) === 'ingreso') ? 'ingreso' : 'egreso';
                                    ?>
                                    <span class="badge <?php echo $claseBadge; ?>">
                                        <?php echo htmlspecialchars(ucfirst($tipoTexto)); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($mov["fecha_hora"]); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="no-records">No se encontraron movimientos con los filtros seleccionados.</p>
            <?php endif; ?>

            <?php $stmt_mov->close(); ?>
        </div>

    </div>

</body>
</html>