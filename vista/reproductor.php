<?php
// Iniciamos sesión si no está iniciada para poder usar variables de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos los modelos que permiten obtener datos de canciones y artistas
require_once '../modelo/cancionModelo.php';
require_once '../modelo/artistaModelo.php';

// Si no hay usuario logueado (no hay id en sesión), redirigimos a login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}
?>

<h2 class="text-center">Reproductor de Canciones</h2>

<?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
    <?php
    // Si se pasa un id de canción válido por GET, obtenemos esa canción
    $cancion = CancionModelo::obtener($_GET['id']);
    if ($cancion):
        // Construimos la ruta del archivo de audio
        $rutaCancion = "../canciones/" . basename($cancion['ruta']);
        // Comprobamos que el archivo exista realmente en el servidor
        if (file_exists($rutaCancion)):
    ?>
        <div class="text-center">
            <!-- Mostramos título de la canción -->
            <h4><?php echo $cancion['titulo']; ?></h4>
            <!-- Reproductor HTML5 con controles y autoplay -->
            <audio controls autoplay>
                <source src="<?php echo $rutaCancion; ?>" type="audio/mpeg">
                Tu navegador no soporta el reproductor de audio.
            </audio>
            <!-- Formulario para añadir la canción a favoritos -->
            <form method="POST" action="../controlador/favoritoControlador.php">
                <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
                <button type="submit" class="btn btn-outline-danger mt-3">❤ Añadir a Favoritos</button>
            </form>
        </div>
        <?php else: ?>
            <!-- Mensaje de error si el archivo de audio no está en el servidor -->
            <p class="text-danger">El archivo de la canción no se encuentra en el servidor.</p>
        <?php endif; ?>
    <?php else: ?>
        <!-- Mensaje si la canción no se encontró en la base de datos -->
        <p class="text-danger">Canción no encontrada.</p>
    <?php endif; ?>

<?php elseif (isset($_GET['artista']) && is_numeric($_GET['artista'])): ?>
    <?php
    // Si se pasa un id de artista válido, obtenemos todas sus canciones
    $canciones = CancionModelo::obtenerPorArtista($_GET['artista']);
    if (count($canciones) > 0):
    ?>
        <ul class="list-group">
            <?php foreach ($canciones as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <!-- Mostramos título de la canción -->
                    <?php echo $c['titulo']; ?>
                    <!-- Botón para reproducir la canción que redirige pasando su id -->
                    <a href="?seccion=reproductor&id=<?php echo $c['id_cancion']; ?>" class="btn btn-sm btn-outline-primary">Reproducir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <!-- Mensaje si el artista no tiene canciones disponibles -->
        <p class="text-muted">Este artista no tiene canciones disponibles.</p>
    <?php endif; ?>

<?php else: ?>
    <!-- Si no hay id de canción ni de artista, mostramos la lista de artistas -->
    <h5>Selecciona un artista para ver sus canciones:</h5>
    <ul class="list-group">
        <?php
        // Obtenemos todos los artistas para listarlos
        $artistas = ArtistaModelo::obtenerTodos();
        foreach ($artistas as $artista):
        ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <!-- Nombre del artista -->
                <?php echo $artista['nombre']; ?>
                <!-- Botón para ver las canciones de ese artista -->
                <a href="?seccion=reproductor&artista=<?php echo $artista['id_artista']; ?>" class="btn btn-sm btn-outline-secondary">Ver canciones</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Botón para volver a la página principal de usuario premium -->
<br>
<a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
