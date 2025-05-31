<?php
// Si la sesión no está iniciada, se inicia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye la conexión a la base de datos
require_once '../controlador/conexion.php';

// Si no hay usuario logueado, redirige a la página de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

// Guardamos el id del usuario en sesión
$id_usuario = $_SESSION['usuario_id'];

// Comprobamos si el usuario es ADMIN buscando su id en la tabla usuario_admin
$stmt = $conexion->prepare("SELECT * FROM usuario_admin WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res_admin = $stmt->get_result();

// Si no es admin, verificamos si es usuario premium
if ($res_admin->num_rows === 0) {
    $stmt = $conexion->prepare("SELECT * FROM usuario_premium WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res_premium = $stmt->get_result();

    // Si tampoco es premium, redirigimos a la página normal.php
    if ($res_premium->num_rows === 0) {
        header("Location: normal.php");
        exit();
    }
}

// Consultamos el nombre de usuario y género para personalizar el saludo
$stmt = $conexion->prepare("SELECT usuario, genero FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

// Si existe el usuario, obtenemos sus datos
if ($res->num_rows > 0) {
    $usuario = $res->fetch_assoc();
    $nombre_usuario = $usuario['usuario'];
    $genero = $usuario['genero'];
} else {
    // En caso contrario, valores por defecto
    $nombre_usuario = "Usuario";
    $genero = "otro";
}

// Definimos el saludo según el género del usuario
if ($genero === "masculino") {
    $saludo = "Bienvenido";
} elseif ($genero === "femenino") {
    $saludo = "Bienvenida";
} else {
    $saludo = "Bienvenido/a";
}

// Obtenemos la sección a mostrar desde la URL, si no existe se muestra "inicio"
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
        <!-- Mostramos el saludo personalizado -->
        <h1><?php echo "$saludo, " . htmlspecialchars($nombre_usuario); ?>, a tu panel de administración</h1>
    </div>
</header>

<nav>
    <div class="contenedor">
        <!-- Menú de navegación con enlaces que cambian la sección -->
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
    // Incluimos el archivo PHP según la sección seleccionada
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
            // Mensaje por defecto si no hay sección o no existe
            echo "<p>Usa el menú para acceder a tus secciones.</p>";
    }
    ?>
</main>
</body>
</html>
