<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';
require_once '../modelo/albumModelo.php';

$albumes = AlbumModelo::obtenerTodos();
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
<div class="container">
<?php foreach ($albumes as $album): ?>
    <?php
    $ruta = $album['imagen_portada'];
    $existe = $ruta && file_exists('../' . $ruta);
    $canciones = AlbumModelo::obtenerCanciones($album['id_album']);
    ?>
    <div class="album-card">
        <div class="card-header">
            <?php if ($existe): ?>
                <img src="../<?php echo htmlspecialchars($ruta); ?>"
                        alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
            <?php else: ?>
                <div class="img-placeholder">Imagen</div>
            <?php endif; ?>
            <div>
                <strong><?php echo htmlspecialchars($album['titulo']); ?></strong><br>
                <small><?php echo htmlspecialchars($album['año_publicacion']); ?> – <?php echo htmlspecialchars($album['artista'] ?? 'Sin artista'); ?></small>
            </div>
        </div>
        <div class="card-body">
            <ul class="lista-canciones">
                <?php if (!empty($canciones)): ?>
                    <?php foreach ($canciones as $cancion): ?>
                        <li><?php echo htmlspecialchars($cancion['titulo']); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay canciones disponibles</li>
                <?php endif; ?>
            </ul>

            <?php if (isset($_SESSION['usuario_id']) && AlbumModelo::esPremium($_SESSION['usuario_id'])): ?>
                <a href="agregar_cancion.php?id_album=<?php echo $album['id_album']; ?>" class="btn-small">Añadir Canción</a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
</body>
</html>
