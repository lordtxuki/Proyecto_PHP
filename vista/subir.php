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
    return;
}
?>

<h2>Subir Canción y Álbum</h2>
<form action="../procesar_subida.php" method="POST" enctype="multipart/form-data">
    <label>Título del Álbum/Canción:</label><br>
    <input type="text" name="titulo" required><br><br>

    <label>Archivo de canción (.mp3):</label><br>
    <input type="file" name="cancion" accept="audio/*" required><br><br>

    <label>Imagen del álbum (opcional):</label><br>
    <input type="file" name="imagen" accept="image/*"><br><br>

    <button type="submit">Subir</button>
</form>
