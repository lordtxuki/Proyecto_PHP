<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

$id_usuario = $_SESSION['usuario_id'] ?? null;

$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>No tienes permisos para subir contenido.</p>";
    exit();
}

$stmt_artistas = $conexion->prepare("SELECT id_artista, nombre FROM Artistas");
$stmt_artistas->execute();
$result_artistas = $stmt_artistas->get_result();
?>

<h2>Subir Canción y Álbum</h2>
<form action="../procesar_subida.php" method="POST" enctype="multipart/form-data" novalidate>
    <label for="id_artista">Seleccionar Artista:</label><br>
    <select name="id_artista" id="id_artista" required>
        <option value="" disabled selected>-- Elige un artista --</option>
        <?php while ($artista = $result_artistas->fetch_assoc()) { ?>
            <option value="<?= $artista['id_artista'] ?>"><?= htmlspecialchars($artista['nombre']) ?></option>
        <?php } ?>
    </select><br><br>

    <label for="titulo_album">Título del Álbum:</label><br>
    <input type="text" name="titulo_album" id="titulo_album" required><br><br>

    <label for="titulo_cancion">Título de la Canción:</label><br>
    <input type="text" name="titulo_cancion" id="titulo_cancion" required><br><br>

    <label for="cancion">Archivo de canción (.mp3):</label><br>
    <input type="file" name="cancion" id="cancion" accept="audio/*" required><br><br>

    <label for="imagen">Imagen del álbum (opcional):</label><br>
    <input type="file" name="imagen" id="imagen" accept="image/*"><br><br>

    <button type="submit">Subir</button>
</form>
