<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id_playlist'])) {
    header("Location: vista_login.php");
    exit();
}

// Obtener usuarios para compartir
$usuarios = $conexion->query("SELECT id_usuario, usuario FROM Usuario WHERE id_usuario != " . $_SESSION['usuario_id']);
?>

<h2>Compartir Playlist</h2>
<form method="POST" action="../controlador/compartirControlador.php?accion=compartir&playlist=<?php echo $_GET['id_playlist']; ?>">
    <label>Selecciona usuario:</label>
    <select name="usuario_destino">
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <option value="<?php echo $u['id_usuario']; ?>"><?php echo $u['usuario']; ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Compartir</button>
</form>