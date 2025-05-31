<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no, redirigir al login
    header("Location: vista/vista_login.php");
    exit();
}

// Incluir conexión a base de datos
require_once '../controlador/conexion.php';

// Obtener el id del álbum pasado por GET, o null si no viene
$id_album = $_GET['id_album'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Canción</title>
    <link rel="stylesheet" href="../styles/agregar_cancion.css">
</head>
<body>
<?php
// Si se recibió un id_album válido
if ($id_album) {
    // Preparar consulta para obtener datos del álbum
    $stmt = $conexion->prepare("SELECT * FROM Albumes WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $album = $stmt->get_result()->fetch_assoc();

    // Si existe el álbum en la base de datos
    if ($album) {
        // Mostrar encabezado con título del álbum, escapando caracteres especiales para seguridad
        echo "<h2>Añadir Canción al Álbum: " . htmlspecialchars($album['titulo']) . "</h2>";
        ?>
        <!-- Formulario para subir canción -->
        <form action="../procesar_subida.php" method="POST" enctype="multipart/form-data" novalidate>
            <!-- Campo oculto para enviar el id del álbum -->
            <input type="hidden" name="id_album" value="<?php echo $id_album; ?>">

            <label for="titulo_cancion">Título de la Canción:</label>
            <input type="text" id="titulo_cancion" name="titulo_cancion" required>

            <label for="cancion">Archivo de Canción (.mp3):</label>
            <!-- Solo aceptar archivos de audio -->
            <input type="file" id="cancion" name="cancion" accept="audio/*" required>

            <button type="submit">Añadir Canción</button>
        </form>

        <!-- Enlace para volver a la página principal de usuario premium -->
        <a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
        <?php
    } else {
        // Mensaje si el álbum no se encuentra en la base de datos
        echo "<p>Álbum no encontrado.</p>";
    }
} else {
    // Mensaje si no se recibe un id_album válido
    echo "<p>Álbum no válido.</p>";
}
?>
</body>
</html>
