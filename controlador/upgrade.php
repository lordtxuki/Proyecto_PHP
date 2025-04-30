<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$tarjeta = $_POST['tarjeta'];
$vencimiento = $_POST['vencimiento'];
$cvv = $_POST['cvv'];
$paypal = $_POST['paypal'];

// Insertar en usuario_premium
$stmt = $conexion->prepare("INSERT INTO usuario_premium (id_usuario) VALUES (?)");
$stmt->bind_param("i", $id_usuario);
if ($stmt->execute()) {
    $id_premium = $conexion->insert_id;

    // Crear suscripción
    $stmt = $conexion->prepare("INSERT INTO Suscripciones (id_usuario_premium, fecha_inicio, estado) VALUES (?, CURDATE(), 'activa')");
    $stmt->bind_param("i", $id_premium);
    $stmt->execute();
    $id_suscripcion = $conexion->insert_id;

    // Insertar pago
    $forma_pago = empty($paypal) ? 'tarjeta' : 'paypal';
    $stmt = $conexion->prepare("INSERT INTO Pagos (id_suscripcion, fecha_pago, cantidad, forma_pago, num_tarjeta, caducidad, codigo_seguridad, usuario_paypal)
        VALUES (?, CURDATE(), 9.99, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id_suscripcion, $forma_pago, $tarjeta, $vencimiento, $cvv, $paypal);
    $stmt->execute();

    header("Location: ../vista/premium.php");
    exit();
} else {
    echo "Error al actualizar la cuenta.";
}
