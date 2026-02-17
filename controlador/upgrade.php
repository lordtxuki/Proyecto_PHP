<?php
// Comprobar si la sesión no está iniciada, y si no, iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Comprobar si el usuario no está logueado (no existe la variable de sesión 'usuario_id')
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al usuario a la página de login si no está autenticado
    header("Location: ../vista/vista_login.php");
    exit();
}

// Guardar en una variable el id del usuario que ha iniciado sesión
$id_usuario = $_SESSION['usuario_id'];

// Recoger los datos enviados por el formulario vía POST
$tarjeta = $_POST['tarjeta'];       // Número de tarjeta
$vencimiento = $_POST['vencimiento']; // Fecha de caducidad
$cvv = $_POST['cvv'];               // Código de seguridad CVV
$paypal = $_POST['paypal'];         // Usuario de PayPal (si se usa)

// Preparar la inserción en la tabla usuario_premium para marcar al usuario como premium
$stmt = $conexion->prepare("INSERT INTO usuario_premium (id_usuario) VALUES (?)");
// Vincular el parámetro $id_usuario (entero) con la consulta preparada
$stmt->bind_param("i", $id_usuario);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Obtener el id generado automáticamente para el usuario premium insertado
    $id_premium = $conexion->insert_id;

    // Preparar la inserción en la tabla Suscripciones con fecha actual y estado 'activa'
    $stmt = $conexion->prepare("INSERT INTO Suscripciones (id_usuario_premium, fecha_inicio, estado) VALUES (?, CURDATE(), 'activa')");
    // Vincular el id del usuario premium
    $stmt->bind_param("i", $id_premium);
    // Ejecutar la consulta
    $stmt->execute();

    // Guardar el id de la suscripción recién creada
    $id_suscripcion = $conexion->insert_id;

    // Determinar la forma de pago: si paypal está vacío, entonces tarjeta; si no, paypal
    $forma_pago = empty($paypal) ? 'tarjeta' : 'paypal';

    // Preparar la inserción en la tabla Pagos con los datos de pago
    $stmt = $conexion->prepare("INSERT INTO Pagos (id_suscripcion, fecha_pago, cantidad, forma_pago, num_tarjeta, caducidad, codigo_seguridad, usuario_paypal)
        VALUES (?, CURDATE(), 9.99, ?, ?, ?, ?, ?)");
    // Vincular los parámetros: id_suscripcion (int), forma_pago, tarjeta, vencimiento, cvv, paypal (todos string)
    $stmt->bind_param("isssss", $id_suscripcion, $forma_pago, $tarjeta, $vencimiento, $cvv, $paypal);
    // Ejecutar la consulta
    $stmt->execute();

    // Redirigir al usuario a la página principal premium tras realizar el proceso
    header("Location: ../vista/premium.php");
    exit();
} else {
    // Mostrar mensaje de error si no se pudo actualizar la cuenta a premium
    echo "Error al actualizar la cuenta.";
}