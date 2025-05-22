<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Verificar si ya es Premium
$stmt = $conexion->prepare("SELECT * FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo "<div class='text-center mt-5'>";
    echo "<h4>Ya eres usuario Premium.</h4>";
    echo "<a href='premium.php' class='btn btn-success mt-3'>Ir a mi panel Premium</a>";
    echo "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar a Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow p-4">
            <h2 class="text-center text-primary mb-4">Actualizar a Premium</h2>
            <form action="../controlador/upgrade.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tarjeta de crédito (16 dígitos)</label>
                    <input type="text" name="tarjeta" pattern="\d{16}" maxlength="16" class="form-control" placeholder="Ej: 1234123412341234" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vencimiento</label>
                    <input type="month" name="vencimiento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">CVV</label>
                    <input type="text" name="cvv" pattern="\d{3,4}" maxlength="4" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">PayPal (opcional)</label>
                    <input type="email" name="paypal" class="form-control" placeholder="Correo de PayPal">
                </div>
                <button type="submit" class="btn btn-success w-100">Actualizar a Premium</button>
                <button class="volver-btn" onclick="history.back()">Volver atrás</button>
            </form>
        </div>
    </div>
</body>
</html>
