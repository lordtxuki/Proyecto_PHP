<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: vista_login.php');
    exit();
}

$favoritos = FavoritoModelo::obtener($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Favoritos</title>
    <link rel="stylesheet" href="../styles/favoritos.css">
</head>
<body>
    <h2 class="text-center">Favoritos</h2>
    <div class="container">

        <div class="favorito-card">
            <h4>Canciones</h4>
            <ul>
                <?php if (count($favoritos['canciones']) === 0): ?>
                    <li>No tienes canciones favoritas.</li>
                <?php else: ?>
                    <?php foreach ($favoritos['canciones'] as $c): ?>
                        <li>
                            <?php echo htmlspecialchars($c['titulo']); ?>
                            <a class="btn-small" href="../controlador/favoritoControlador.php?accion=quitar&tipo=cancion&id=<?php echo $c['id_cancion']; ?>">Quitar</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="favorito-card">
            <h4>Álbumes</h4>
            <ul>
                <?php if (count($favoritos['albumes']) === 0): ?>
                    <li>No tienes álbumes favoritos.</li>
                <?php else: ?>
                    <?php foreach ($favoritos['albumes'] as $a): ?>
                        <li>
                            <?php echo htmlspecialchars($a['titulo']); ?>
                            <a class="btn-small" href="../controlador/favoritoControlador.php?accion=quitar&tipo=album&id=<?php echo $a['id_album']; ?>">Quitar</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="favorito-card">
            <h4>Artistas</h4>
            <ul>
                <?php if (count($favoritos['artistas']) === 0): ?>
                    <li>No tienes artistas favoritos.</li>
                <?php else: ?>
                    <?php foreach ($favoritos['artistas'] as $art): ?>
                        <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <img src="../<?php echo htmlspecialchars($art['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($art['nombre']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <span><?php echo htmlspecialchars($art['nombre']); ?></span>
                            <a class="btn-small" style="margin-left: auto;" href="../controlador/favoritoControlador.php?accion=quitar&tipo=artista&id=<?php echo $art['id_artista']; ?>">Quitar</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <a href="premium.php" class="btn btn-secondary mt-3">Volver</a> 
    </div>
</body>
</html>
