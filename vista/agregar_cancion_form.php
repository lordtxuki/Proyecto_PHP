<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id_playlist'])) {
    header("Location: vista_login.php");
    exit();
}

$canciones = $conexion->query("SELECT * FROM Canciones");
?>

<h2>Añadir canción a la playlist</h2>
<form method="POST" action="../controlador/compartirControlador.php?accion=agregar&playlist=<?php echo $_GET['id_playlist']; ?>">
    <label>Selecciona canción:</label>
    <select name="id_cancion">
        <?php while ($c = $canciones->fetch_assoc()): ?>
            <option value="<?php echo $c['id_cancion']; ?>"><?php echo $c['titulo']; ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Agregar</button>
</form>