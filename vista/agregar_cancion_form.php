<?php
// Inicia sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye la conexión a la base de datos
require_once '../controlador/conexion.php';

// Verifica que el usuario esté logueado y que se haya recibido el id de la playlist por GET
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id_playlist'])) {
    // Si no, redirige al login
    header("Location: vista_login.php");
    exit();
}

// Consulta todas las canciones disponibles en la tabla Canciones
$canciones = $conexion->query("SELECT * FROM Canciones");
?>

<!-- Título del formulario -->
<h2>Añadir canción a la playlist</h2>

<!-- Formulario para agregar una canción a la playlist indicada -->
<form method="POST" action="../controlador/compartirControlador.php?accion=agregar&playlist=<?php echo $_GET['id_playlist']; ?>">
    
    <label>Selecciona canción:</label>
    
    <!-- Lista desplegable con todas las canciones -->
    <select name="id_cancion">
        <?php while ($c = $canciones->fetch_assoc()): ?>
            <!-- Cada opción contiene el id de la canción como valor y muestra el título -->
            <option value="<?php echo $c['id_cancion']; ?>"><?php echo $c['titulo']; ?></option>
        <?php endwhile; ?>
    </select>
    
    <!-- Botón para enviar el formulario -->
    <button type="submit">Agregar</button>
</form>
