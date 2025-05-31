<?php
// Iniciamos sesión si no está iniciada para usar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
require_once '../controlador/conexion.php';

// Obtenemos el id del usuario desde la sesión (o null si no existe)
$id_usuario = $_SESSION['usuario_id'] ?? null;

// Preparamos consulta para comprobar si el usuario es premium
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario); // Vinculamos el parámetro id_usuario (entero)
$stmt->execute();
$res = $stmt->get_result();

// Si no está en la tabla usuario_premium, no tiene permisos para subir contenido
if ($res->num_rows === 0) {
    echo "<p>No tienes permisos para subir contenido.</p>";
    exit(); // Detenemos la ejecución del script
}

// Preparamos consulta para obtener todos los artistas disponibles
$stmt_artistas = $conexion->prepare("SELECT id_artista, nombre FROM Artistas");
$stmt_artistas->execute();
$result_artistas = $stmt_artistas->get_result();
?>

<!-- Formulario para subir canción y álbum -->
<h2>Subir Canción y Álbum</h2>
<form action="../procesar_subida.php" method="POST" enctype="multipart/form-data" novalidate>
    <!-- Selección de artista -->
    <label for="id_artista">Seleccionar Artista:</label><br>
    <select name="id_artista" id="id_artista" required>
        <option value="" disabled selected>-- Elige un artista --</option>
        <!-- Listamos los artistas obtenidos de la base de datos -->
        <?php while ($artista = $result_artistas->fetch_assoc()) { ?>
            <option value="<?= $artista['id_artista'] ?>"><?= htmlspecialchars($artista['nombre']) ?></option>
        <?php } ?>
    </select><br><br>

    <!-- Título del álbum -->
    <label for="titulo_album">Título del Álbum:</label><br>
    <input type="text" name="titulo_album" id="titulo_album" required><br><br>

    <!-- Título de la canción -->
    <label for="titulo_cancion">Título de la Canción:</label><br>
    <input type="text" name="titulo_cancion" id="titulo_cancion" required><br><br>

    <!-- Archivo de la canción (solo audio) -->
    <label for="cancion">Archivo de canción (.mp3):</label><br>
    <input type="file" name="cancion" id="cancion" accept="audio/*" required><br><br>

    <!-- Imagen del álbum, opcional -->
    <label for="imagen">Imagen del álbum (opcional):</label><br>
    <input type="file" name="imagen" id="imagen" accept="image/*"><br><br>

    <!-- Botón para enviar el formulario -->
    <button type="submit">Subir</button>
</form>
