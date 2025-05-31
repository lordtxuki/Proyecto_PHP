<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

require_once '../controlador/conexion.php';
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
if ($id_album) {
    $stmt = $conexion->prepare("SELECT * FROM Albumes WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $album = $stmt->get_result()->fetch_assoc();

    if ($album) {
        echo "<h2>Añadir Canción al Álbum: " . htmlspecialchars($album['titulo']) . "</h2>";
        ?>
        <form action="../procesar_subida.php" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id_album" value="<?php echo $id_album; ?>">

            <label for="titulo_cancion">Título de la Canción:</label>
            <input type="text" id="titulo_cancion" name="titulo_cancion" required>

            <label for="cancion">Archivo de Canción (.mp3):</label>
            <input type="file" id="cancion" name="cancion" accept="audio/*" required>

            <button type="submit">Añadir Canción</button>
        </form>

        <a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
        <?php
    } else {
        echo "<p>Álbum no encontrado.</p>";
    }
} else {
    echo "<p>Álbum no válido.</p>";
}
?>
</body>
</html>
