<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once '../controlador/conexion.php';

// Verificar que el usuario está logueado y que se recibió el id de la playlist a compartir
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id_playlist'])) {
    // Si no está logueado o falta el id, redirigir al login
    header("Location: vista_login.php");
    exit();
}

// Obtener el id del usuario actual para excluirlo de la lista
$id_usuario_actual = $_SESSION['usuario_id'];

// Consultar todos los usuarios excepto el actual para mostrar en el select
$query = "SELECT id_usuario, usuario FROM Usuario WHERE id_usuario != $id_usuario_actual";
$usuarios = $conexion->query($query);
?>

<h2>Compartir Playlist</h2>

<!-- Formulario para seleccionar el usuario con quien compartir la playlist -->
<form method="POST" action="../controlador/compartirControlador.php?accion=compartir&playlist=<?php echo htmlspecialchars($_GET['id_playlist']); ?>">
    <label>Selecciona usuario:</label>
    <select name="usuario_destino">
        <!-- Mostrar cada usuario como opción en el select -->
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($u['id_usuario']); ?>">
                <?php echo htmlspecialchars($u['usuario']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Compartir</button>
</form>
