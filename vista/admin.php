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

// Verificar si es ADMIN
$stmt = $conexion->prepare("SELECT * FROM usuario_admin WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res_admin = $stmt->get_result();

if ($res_admin->num_rows === 0) {
    // Verificar si es Premium
    $stmt = $conexion->prepare("SELECT * FROM usuario_premium WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res_premium = $stmt->get_result();

    if ($res_premium->num_rows === 0) {
        header("Location: normal.php");
        exit();
    }
}

// Obtener nombre y género
$stmt = $conexion->prepare("SELECT usuario, genero FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $usuario = $res->fetch_assoc();
    $nombre_usuario = $usuario['usuario'];
    $genero = $usuario['genero'];
} else {
    $nombre_usuario = "Usuario";
    $genero = "otro";
}

// Definir saludo según el género
if ($genero === "masculino") {
    $saludo = "Bienvenido";
} elseif ($genero === "femenino") {
    $saludo = "Bienvenida";
} else {
    $saludo = "Bienvenido/a";
}

$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
<header>
    <div class="contenedor">
        <h1><?php echo "$saludo, " . htmlspecialchars($nombre_usuario); ?>, a tu panel de administración</h1>
    </div>
</header>

<nav>
    <div class="contenedor">
        <a href="?seccion=playlists">Mis Playlists</a> |
        <a href="?seccion=albumes">Álbumes</a> |
        <a href="?seccion=artistas">Artistas</a> |
        <a href="?seccion=recomendaciones">Recomendaciones</a> |
        <a href="?seccion=favoritos">Favoritos</a> |
        <a href="?seccion=reproductor">Reproductor</a> |
        <a href="?seccion=subir">Subir Canción/Álbum</a> |
        <a href="?seccion=crear_artista">Crear Artista</a> |
        <a href="../logout.php">Cerrar sesión</a>
    </div>
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
        case 'albumes':
            include 'albumes.php';
            break;
        case 'artistas':
            include 'artistas.php';
            break;
        case 'recomendaciones':
            include 'recomendaciones.php';
            break;
        case 'crear_artista':
            include 'crear_artista.php';
            break;
        default:
            echo "<p>Usa el menú para acceder a tus secciones.</p>";
    }
    ?>
</main>
</body>
</html>
