<?php
// Iniciamos sesión si no hay ninguna activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
require_once '../controlador/conexion.php';

// Comprobamos que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no hay usuario logueado, redirigimos a la página de login
    header("Location: vista_login.php");
    exit();
}

// Guardamos el id del usuario logueado
$id_usuario = $_SESSION['usuario_id'];

// Verificamos si el usuario es Premium
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

// Si no es Premium, redirigimos a la versión normal
if ($res->num_rows === 0) {
    header("Location: normal.php");
    exit();
}

// Obtenemos datos del usuario (nombre y género) para personalizar saludo
$stmt = $conexion->prepare("SELECT usuario, genero FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Si se encontró el usuario, guardamos los datos
    $usuario = $res->fetch_assoc();
    $nombre_usuario = $usuario['usuario'];
    $genero = $usuario['genero'];
} else {
    // Si no se encuentra, ponemos valores por defecto
    $nombre_usuario = "Usuario";
    $genero = "otro";
}

// Definimos el saludo según el género para que sea más personalizado
if ($genero === "masculino") {
    $saludo = "Bienvenido";
} elseif ($genero === "femenino") {
    $saludo = "Bienvenida";
} else {
    $saludo = "Bienvenido/a";
}

// Recogemos la sección a mostrar, por defecto 'inicio'
$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario Premium</title>
    <!-- Enlazamos hoja de estilos para diseño -->
    <link rel="stylesheet" href="../styles/premium.css">
</head>
<body>
    <header>
        <div class="contenedor">
            <!-- Mostramos saludo personalizado escapando caracteres especiales -->
            <h1><?php echo "$saludo, " . htmlspecialchars($nombre_usuario); ?>, a tu cuenta Premium</h1>
        </div>
    </header>

    <nav>
        <div class="contenedor">
            <!-- Menú de navegación para acceder a las diferentes secciones -->
            <a href="?seccion=playlists">Mis Playlists</a> |
            <a href="?seccion=albumes">Álbumes</a> |
            <a href="?seccion=artistas">Artistas</a> |
            <a href="?seccion=recomendaciones">Recomendaciones</a> |
            <a href="?seccion=favoritos">Favoritos</a> |
            <a href="?seccion=reproductor">Reproductor</a> |
            <a href="?seccion=subir">Subir Canción/Álbum</a> |
            <a href="?seccion=crear_artista">Crear Artista</a> |

            <!-- Enlace para cerrar sesión -->
            <a href="../logout.php">Cerrar sesión</a>
        </div>
    </nav>

    <main>
        <?php
        // Cargamos la sección correspondiente según el parámetro GET 'seccion'
        switch ($seccion) {
            case 'playlists':
                include 'playlists.php';  // Página para gestionar playlists
                break;
            case 'favoritos':
                include 'favoritos.php';  // Página para favoritos
                break;
            case 'reproductor':
                include 'reproductor.php';  // Página con el reproductor de música
                break;
            case 'subir':
                include 'subir.php';  // Página para subir canciones o álbumes
                break;
            case 'albumes':
                include 'albumes.php';  // Página para ver y gestionar álbumes
                break;
            case 'artistas':
                include 'artistas.php';  // Página para gestionar artistas
                break;
            case 'recomendaciones':
                include 'recomendaciones.php';  // Página con recomendaciones personalizadas
                break;
            case 'crear_artista':
                include 'crear_artista.php';  // Página para crear un artista nuevo
                break;
            default:
                // Si no se pasa ninguna sección válida, mostramos mensaje genérico
                echo "<p>Usa el menú para acceder a tus secciones.</p>";
        }
        ?>
    </main>
</body>
</html>
