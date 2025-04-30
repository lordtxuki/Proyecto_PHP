<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/cancionModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}
?>

<h2 class="text-center">Reproductor de Canciones</h2>

<?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
    <?php
    $cancion = CancionModelo::obtener($_GET['id']);
    if ($cancion):
        $rutaCancion = "../canciones/" . basename($cancion['ruta']);
        if (file_exists($rutaCancion)): 
    ?>
        <div class="text-center">
            <h4><?php echo $cancion['titulo']; ?></h4>
            <audio controls autoplay>
                <source src="<?php echo $rutaCancion; ?>" type="audio/mpeg">
                Tu navegador no soporta el reproductor de audio.
            </audio>
            <form method="POST" action="../controlador/favoritoControlador.php">
                <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
                <button type="submit" class="btn btn-outline-danger mt-3">❤ Añadir a Favoritos</button>
            </form>
        </div>
        <?php else: ?>
            <p class="text-danger">El archivo de la canción no se encuentra en el servidor.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-danger">Canción no encontrada.</p>
    <?php endif; ?>
<?php else: ?>
    <?php
    $todas = CancionModelo::obtenerTodas();
    if (count($todas) > 0):
    ?>
        <ul class="list-group">
            <?php foreach ($todas as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo $c['titulo']; ?>
                    <a href="?seccion=reproductor&id=<?php echo $c['id_cancion']; ?>" class="btn btn-sm btn-outline-primary">Reproducir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No hay canciones disponibles aún.</p>
    <?php endif; ?>
<?php endif; ?>

<a href="normal.php">Volver</a> | <a href="premium.php">Volver Premium</a>
