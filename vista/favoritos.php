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
                        <li class="artista-favorito">
                            <img src="/Recuperacion_Php/imagenes/artistas/<?php echo rawurlencode($art['imagen']); ?>" alt="<?php echo htmlspecialchars($art['nombre']); ?>" class="miniatura-artista">
                            <?php echo htmlspecialchars($art['nombre']); ?>
                            <a class="btn-small" href="../controlador/favoritoControlador.php?accion=quitar&tipo=artista&id=<?php echo $art['id_artista']; ?>">Quitar</a>
                        </li>

                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <button class="volver-btn" onclick="history.back()">Volver atrás</button>
    </div>
</body>
</html>
