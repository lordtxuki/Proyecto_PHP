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

if ($res->num_rows === 0) {
    header("Location: normal.php");
    exit();
}

$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario Premium</title>
    <link rel="stylesheet" href="../styles/premium.css">
</head>
<body>
    <h1>¡Nos alegra volver a verte!</h1>
    <nav>
        <a href="?seccion=playlists">Mis Playlists</a> |
        <a href="?seccion=favoritos">Favoritos</a> |
        <a href="?seccion=reproductor">Reproductor</a> |
        <a href="?seccion=subir">Subir Canción/Álbum</a> |
        <a href="logout.php">Cerrar sesión</a>
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
            case 'subir':
                include 'subir.php';
                break;
            default:
                echo "<p>Usa el menú para acceder a tus secciones.</p>";
        }
        ?>
    </main>
</body>
</html>
