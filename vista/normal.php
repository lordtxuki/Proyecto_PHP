<?php
// Iniciamos sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
require_once '../controlador/conexion.php';

// Si no hay usuario logueado, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Obtenemos nombre y género del usuario
$stmt = $conexion->prepare("SELECT usuario, genero FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $usuario = $res->fetch_assoc();
    $nombre_usuario = $usuario['usuario'];
    $genero = $usuario['genero'];
} else {
    $nombre_usuario = "Usuario desconocido";
    $genero = "otro"; 
}

// Definimos saludo según género
if ($genero === "masculino") {
    $saludo = "Bienvenido";
} elseif ($genero === "femenino") {
    $saludo = "Bienvenida";
} else {
    $saludo = "Bienvenido/a";
}

// Comprobamos si es premium
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    header("Location: premium.php");
    exit();
}

// Sección actual
$seccion = $_GET['seccion'] ?? 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Necesario para que funcione el responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Usuario Free</title>

    <!-- Bootstrap solo para el responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Hoja de estilos -->
    <link rel="stylesheet" href="../styles/normal.css">
    <link rel="stylesheet" href="../styles/artistas.css">

</head>

<body class="<?php echo $seccion; ?>">


    <!-- Header -->
    <header class="py-4 text-center">
        <div class="container">
            <h1><?php echo $saludo . ", " . htmlspecialchars($nombre_usuario); ?>, a tu cuenta Free</h1>
        </div>
    </header>

    <!-- Navbar responsive -->
<nav>
    <div class="contenedor nav-flex">

        <!-- Botón hamburguesa para móviles -->
        <button class="menu-toggle" id="menuToggle">☰</button>

        <!-- Menú de navegación -->
        <div class="nav-links" id="navLinks">

            <a href="?seccion=playlists">Mis Playlists</a> |
            <a href="?seccion=albumes">Álbumes</a> |
            <a href="?seccion=artistas">Artistas</a> |
            <a href="?seccion=recomendaciones">Recomendaciones</a> |
            <a href="?seccion=favoritos">Favoritos</a> |
            <a href="?seccion=reproductor">Reproductor</a> |
            <a href="../logout.php">Cerrar sesión</a> |
            <a href="upgrade.php">Actualizar a Premium</a> |

            <!-- Botón cambio de tema -->
            <button id="toggleTema" class="boton-tema">🌙</button>

        </div>
    </div>
</nav>


    <!-- Contenido principal -->
    <main class="container my-4">

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

                echo "<p>Usa el menú para acceder a tus secciones.</p>";

                // Últimos álbumes añadidos
                $sql = "SELECT Albumes.titulo, Albumes.imagen_portada, Artistas.nombre AS artista
                        FROM Albumes
                        JOIN Artistas ON Albumes.id_artista = Artistas.id_artista
                        ORDER BY Albumes.id_album DESC
                        LIMIT 6";

                $resultado_albumes = $conexion->query($sql);

                echo "<h2 class='mt-4 mb-3'>Últimos álbumes añadidos</h2>";
                echo "<div class='row'>";

                if ($resultado_albumes && $resultado_albumes->num_rows > 0) {

                    while ($album = $resultado_albumes->fetch_assoc()) {

                        $imagen = htmlspecialchars($album['imagen_portada']);
                        $titulo = htmlspecialchars($album['titulo']);
                        $artista = htmlspecialchars($album['artista']);

                        echo "<div class='col-12 col-sm-6 col-md-4 mb-4'>";
                        echo "<div class='card-album'>";
                        echo "<img src='../$imagen' alt='$titulo'>";
                        echo "<h3>$titulo</h3>";
                        echo "<p>$artista</p>";
                        echo "</div>";
                        echo "</div>";
                    }

                } else {
                    echo "<p>No hay álbumes disponibles.</p>";
                }

                echo "</div>";
        }
        ?>

    </main>

    <!-- Bootstrap JS para que funcione el menú -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script del tema -->
        <script src="../assets/js/tema.js"></script>

        <script>
        // Control del menú hamburguesa
        document.addEventListener("DOMContentLoaded", function () {

            const toggle = document.getElementById("menuToggle");
            const links = document.getElementById("navLinks");

            toggle.addEventListener("click", function () {
                links.classList.toggle("activo");
            });

        });
        </script>


</body>
</html>
