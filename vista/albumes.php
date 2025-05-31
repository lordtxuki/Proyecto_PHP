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

                <?php if ($canciones): ?>
                    <ul class="lista-canciones">
                        <?php foreach ($canciones as $cancion): ?>
                            <li><?php echo htmlspecialchars($cancion['titulo']); ?></li>
                        <?php endforeach;?>
                    </ul>
                    <?php else: ?>
                        <p class="sin-canciones">Este album no tiene canciones.</p>
                    <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['usuario_id']) && AlbumModelo::esPremium($_SESSION['usuario_id'])): ?>
                <a href="agregar_cancion.php?id_album=<?php echo $album['id_album']; ?>" class="btn-small">Añadir Canción</a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<div class="btn-container">
        <button class="volver-btn" onclick="history.back()">Volver atrás</button>
</div>
</body>
</html>