<?php

session_start();

require_once "config.php";

// Verificar que el empleado haya iniciado sesión
if (!isset($_SESSION["empleado_id"])) {
    header("Location: login.php");
    exit;
}

// Impide que se registre otro movimiento mientras el sistema está mostrando la confirmación del anterior
if (isset($_SESSION["confirmando_movimiento"])) {
    header("Location: panel.php");
    exit;
}

// Aceptar únicamente solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: panel.php");
    exit;
}

// Obtener el movimiento solicitado
$tipo = $_POST["tipo"] ?? "";

// Validar que sea ingreso o egreso
if ($tipo !== "ingreso" && $tipo !== "egreso") {
    header("Location: panel.php");
    exit;
}

$empleadoId = $_SESSION["empleado_id"];

// Buscar el último movimiento del empleado
$sql = "SELECT tipo
        FROM movimientos
        WHERE empleado_id = ?
        ORDER BY fecha_hora DESC, id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("No se pudo preparar la consulta.");
}

$stmt->bind_param("i", $empleadoId);
$stmt->execute();

$resultado = $stmt->get_result();
$ultimoMovimiento = $resultado->fetch_assoc();

$stmt->close();

// Validar que los movimientos sigan una secuencia correcta.
if (
    (!$ultimoMovimiento && $tipo !== "ingreso") ||
    ($ultimoMovimiento && $ultimoMovimiento["tipo"] === $tipo)
) {
    header("Location: panel.php?error=movimiento");
    exit;
}

// Guardar el nuevo movimiento
$sql = "INSERT INTO movimientos (empleado_id, tipo)
        VALUES (?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("No se pudo preparar el registro.");
}

$stmt->bind_param("is", $empleadoId, $tipo);


if ($stmt->execute()) {
    $_SESSION["confirmando_movimiento"] = true;
    $_SESSION["tipo_movimiento_confirmado"] = $tipo;
    $_SESSION["confirmacion_expira"] = time() + 3;

    header("Location: panel.php?registro=ok");
    exit;
}

die("No se pudo registrar el movimiento.");

?>