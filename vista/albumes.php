<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../controlador/conexion.php';
require_once '../modelo/albumModelo.php';
require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

$albumes = AlbumModelo::obtenerTodos();
$favs_todos = FavoritoModelo::obtener($id_usuario);

$favs_albumes = array_map(function($row) {
    return $row['id_album'];
}, $favs_todos['albumes']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Álbumes</title>
    <link rel="stylesheet" href="../styles/albumes.css">
</head>
<body>

<h2 class="text-center">Álbumes</h2>

<div class="text-center-volver">
    <a href="premium.php" class="btn-volver">↩ Volver a Premium</a>
</div>

<div class="container">

<?php foreach ($albumes as $album): ?>

<?php
$ruta = $album['imagen_portada'];

// Ruta real física del servidor
$rutaFisica = __DIR__ . '/../' . $ruta;

// Ruta para mostrar en navegador
$rutaWeb = '../' . $ruta;

$existe = !empty($ruta) && file_exists($rutaFisica);

$canciones = AlbumModelo::obtenerCanciones($album['id_album']);
$isFavorito = in_array($album['id_album'], $favs_albumes);
?>

<div class="album-card">

    <div class="card-header">

        <?php if ($existe): ?>
            <img src="<?php echo htmlspecialchars($rutaWeb); ?>"
                    alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
        <?php else: ?>
            <img src="../uploads/default_album.jpg" alt="Portada por defecto">
        <?php endif; ?>

        <div>
            <strong><?php echo htmlspecialchars($album['titulo']); ?></strong><br>
            <small>
                <?php echo htmlspecialchars($album['año_publicacion']); ?>
                – <?php echo htmlspecialchars($album['artista'] ?? 'Sin artista'); ?>
            </small>
        </div>

    </div>

    <div class="card-body">

        <ul class="lista-canciones">
            <?php if (!empty($canciones)): ?>
                <?php foreach ($canciones as $cancion): ?>
                    <li>
                        <?php echo htmlspecialchars($cancion['titulo']); ?>
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?seccion=reproductor&id=<?php echo $cancion['id_cancion']; ?>" 
                    class="btn-small">
                    ▶ Reproducir
                    </a>

                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No hay canciones disponibles</li>
            <?php endif; ?>
        </ul>

        <?php if ($isFavorito): ?>
            <a href="../controlador/albumControlador.php?accion=quitar_favorito&id=<?php echo $album['id_album']; ?>"
                class="btn-small rojo">Quitar favorito</a>
        <?php else: ?>
            <a href="../controlador/albumControlador.php?accion=favorito&id=<?php echo $album['id_album']; ?>"
                class="btn-small verde">Añadir a favoritos</a>
        <?php endif; ?>

    </div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
