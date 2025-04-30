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
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    header("Location: premium.php");
    exit();
}

$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario Free</title>
    <link rel="stylesheet" href="../styles/normal.css">
</head>
<body>
    <h1>Bienvenido a tu cuenta Free</h1>
    <nav>
        <a href="?seccion=playlists">Mis Playlists</a> |
        <a href="?seccion=favoritos">Favoritos</a> |
        <a href="?seccion=reproductor">Reproductor</a> |
        <a href="logout.php">Cerrar sesión</a> |
        <a href="upgrade.php">Actualizar a Premium</a>
    </nav>

    <main>
        <?php
        switch ($seccion) {
            case 'playlists':
                include 'playlists.php';
                break;
            case 'favoritos':
                include 'favoritos.php';
                break;
            case 'reproductor':
                include 'reproductor.php';
                break;
            default:
                echo "<p>Usa el menú para acceder a tus secciones.</p>";
        }
        ?>
    </main>
</body>
</html>
