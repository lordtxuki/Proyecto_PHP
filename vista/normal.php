<?php
// Iniciamos sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos el archivo con la conexión a la base de datos
require_once '../controlador/conexion.php';

// Si no hay usuario logueado, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Preparamos consulta para obtener el nombre y género del usuario
$stmt = $conexion->prepare("SELECT usuario, genero FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

// Si se encuentra el usuario en la base de datos, guardamos los datos
if ($res->num_rows > 0) {
    $usuario = $res->fetch_assoc();
    $nombre_usuario = $usuario['usuario'];
    $genero = $usuario['genero'];
} else {
    // Si no se encuentra, ponemos valores por defecto
    $nombre_usuario = "Usuario desconocido";
    $genero = "otro"; 
}

// Definimos el saludo según el género
if ($genero === "masculino") {
    $saludo = "Bienvenido";
} elseif ($genero === "femenino") {
    $saludo = "Bienvenida";
} else {
    $saludo = "Bienvenido/a";
}

// Comprobamos si el usuario es premium para redirigirlo a la página premium
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Si es premium, redirige a premium.php
    header("Location: premium.php");
    exit();
}

// Obtenemos la sección a mostrar, si no viene en URL usamos 'inicio'
$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario Free</title>
    <!-- Hoja de estilos para usuario free -->
    <link rel="stylesheet" href="../styles/normal.css">
</head>
<body>
    <header>
        <div class="contenedor">
            <!-- Saludo personalizado escapando caracteres especiales -->
            <h1><?php echo $saludo . ", " . htmlspecialchars($nombre_usuario); ?>, a tu cuenta Free</h1>
        </div>
    </header>

    <nav>
        <div class="contenedor">
            <!-- Menú con enlaces que cambian la sección que se muestra -->
            <a href="?seccion=playlists">Mis Playlists</a> |
            <a href="?seccion=albumes">Álbumes</a> |
            <a href="?seccion=artistas">Artistas</a> |
            <a href="?seccion=recomendaciones">Recomendaciones</a> |
            <a href="?seccion=favoritos">Favoritos</a> |
            <a href="?seccion=reproductor">Reproductor</a> |
            <a href="../logout.php">Cerrar sesión</a> |
            <a href="upgrade.php">Actualizar a Premium</a>
        </div>
    </nav>

    <main>
        <?php
        // Según la sección solicitada, incluimos el archivo correspondiente
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
            case 'albumes':
                include 'albumes.php';
                break;
            case 'artistas':
                include 'artistas.php';
                break;
            case 'recomendaciones':
                include 'recomendaciones.php';
                break;
            default:
                // Mensaje por defecto cuando no se selecciona sección o es 'inicio'
                echo "<p>Usa el menú para acceder a tus secciones.</p>";
        }
        ?>
    </main>
</body>
</html>
