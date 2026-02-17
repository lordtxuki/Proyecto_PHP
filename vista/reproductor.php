<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/cancionModelo.php';
require_once '../modelo/artistaModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}
?>

<link rel="stylesheet" href="../styles/reproductor.css">

<div class="reproductor-container">

<h2 class="titulo-reproductor">🎵 Reproductor</h2>

<?php

/* =====================================================
    SI VIENE ID → REPRODUCIR CANCION
===================================================== */

if (isset($_GET['id']) && is_numeric($_GET['id'])):

    $cancion = CancionModelo::obtener($_GET['id']);

    if ($cancion):

        $ruta = $cancion['ruta'];
        $rutaFisica = __DIR__ . '/../' . $ruta;
        $rutaWeb = '../' . $ruta;

        if (!empty($ruta) && file_exists($rutaFisica)):
?>

<div class="card-player">

    <h3><?php echo htmlspecialchars($cancion['titulo']); ?></h3>

    <audio controls autoplay class="audio-custom">
        <source src="<?php echo htmlspecialchars($rutaWeb); ?>" type="audio/mpeg">
        Tu navegador no soporta el audio.
    </audio>

    <form method="POST" action="../controlador/favoritoControlador.php">
        <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
        <button type="submit" class="btn-fav">
            ❤ Añadir a favoritos
        </button>
    </form>

</div>

<?php
        else:
            echo "<p class='error-msg'>El archivo no existe en el servidor.</p>";
        endif;

    else:
        echo "<p class='error-msg'>Canción no encontrada.</p>";
    endif;


/* =====================================================
    SI VIENE ARTISTA → MOSTRAR SUS CANCIONES
===================================================== */

elseif (isset($_GET['artista']) && is_numeric($_GET['artista'])):

    $canciones = CancionModelo::obtenerPorArtista($_GET['artista']);

    if ($canciones && count($canciones) > 0):
?>

<h4 class="subtitulo">Canciones del artista</h4>

<div class="lista-canciones">

<?php foreach ($canciones as $cancion): ?>

<div class="item-cancion">
    <span><?php echo htmlspecialchars($cancion['titulo']); ?></span>

    <a href="?seccion=reproductor&id=<?php echo $cancion['id_cancion']; ?>"
        class="btn-play">
        ▶ Reproducir
    </a>
</div>

<?php endforeach; ?>

</div>

<?php
    else:
        echo "<p class='info-msg'>Este artista no tiene canciones.</p>";
    endif;


/* =====================================================
    SI NO VIENE NADA → MOSTRAR ARTISTAS
===================================================== */

else:

    $artistas = ArtistaModelo::obtenerTodos();
?>

<h4 class="subtitulo">Selecciona un artista</h4>

<div class="lista-canciones">

<?php foreach ($artistas as $artista): ?>

<div class="item-cancion">
    <span><?php echo htmlspecialchars($artista['nombre']); ?></span>

    <a href="?seccion=reproductor&artista=<?php echo $artista['id_artista']; ?>"
        class="btn-play secondary">
        Ver canciones
    </a>
</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

<a href="premium.php" class="btn-volver">← Volver</a>

</div>
