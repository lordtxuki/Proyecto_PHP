<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

require_once '../controlador/conexion.php';

/* Obtener álbumes existentes */
$sqlAlbumes = "
    SELECT a.id_album, a.titulo, ar.nombre AS artista
    FROM Albumes a
    JOIN Artistas ar ON a.id_artista = ar.id_artista
    ORDER BY ar.nombre, a.titulo
";

$albumes = $conexion->query($sqlAlbumes);

if (!$albumes) {
    die("Error en consulta álbumes: " . $conexion->error);
}


/* Obtener artistas (para crear nuevo álbum si hace falta) */
$artistas = $conexion->query("SELECT id_artista, nombre FROM Artistas ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Canción</title>
    <link rel="stylesheet" href="../styles/agregar_cancion.css">
</head>
<body>

<h2>Añadir Canción</h2>

<form action="../procesar_subida.php" method="POST" enctype="multipart/form-data">

    <!-- ===============================
        SELECCIONAR ÁLBUM EXISTENTE
    =============================== -->

    <label for="id_album">Seleccionar álbum existente:</label>
    <select name="id_album" id="id_album">
        <option value="">-- Crear nuevo álbum --</option>

        <?php while ($album = $albumes->fetch_assoc()): ?>
            <option value="<?php echo $album['id_album']; ?>">
                <?php echo htmlspecialchars($album['artista'] . " - " . $album['titulo']); ?>
            </option>
        <?php endwhile; ?>

    </select>

    <hr>

    <!-- ===============================
        CREAR NUEVO ÁLBUM
    =============================== -->

    <div id="nuevoAlbum">
        <h4>Si quieres crear nuevo álbum:</h4>


    <label for="titulo_album">Título del álbum:</label>
    <input type="text" name="titulo_album" id="titulo_album">

    <label for="id_artista">Artista:</label>
    <select name="id_artista" id="id_artista">
        <option value="">-- Seleccionar artista --</option>

        <?php while ($artista = $artistas->fetch_assoc()): ?>
            <option value="<?php echo $artista['id_artista']; ?>">
                <?php echo htmlspecialchars($artista['nombre']); ?>
            </option>
        <?php endwhile; ?>

    </select>

    <label for="imagen">Imagen del álbum (opcional):</label>
    <input type="file" name="imagen" accept="image/*">
    </div>
    <hr>

    <!-- ===============================
        DATOS CANCIÓN
    =============================== -->

    <label for="titulo_cancion">Título de la Canción:</label>
    <input type="text" name="titulo_cancion" id="titulo_cancion" required>

    <label for="cancion">Archivo de Canción (.mp3):</label>
    <input type="file" name="cancion" accept="audio/*" required>

    <button type="submit">Subir Canción</button>

</form>

<br>
<a href="premium.php">Volver</a>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const selectAlbum = document.getElementById("id_album");
    const nuevoAlbumDiv = document.getElementById("nuevoAlbum");

    function comprobarSeleccion() {
        if (selectAlbum.value !== "") {
            nuevoAlbumDiv.style.display = "none";
        } else {
            nuevoAlbumDiv.style.display = "block";
        }
    }

    // Comprobar al cargar
    comprobarSeleccion();

    // Comprobar al cambiar
    selectAlbum.addEventListener("change", comprobarSeleccion);

});
</script>

</body>
</html>
