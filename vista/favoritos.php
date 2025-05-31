<?php
// Comprobamos si la sesión no está iniciada para iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos el modelo que gestiona los favoritos
require_once '../modelo/favoritoModelo.php';

// Si no hay un usuario logueado, redirigimos a la página de login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: vista_login.php');
    exit();
}

// Obtenemos los favoritos del usuario logueado mediante su ID
$favoritos = FavoritoModelo::obtener($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Favoritos</title>
    <!-- Enlace a la hoja de estilos específica para la vista de favoritos -->
    <link rel="stylesheet" href="../styles/favoritos.css">
</head>
<body>
    <h2 class="text-center">Favoritos</h2>

    <div class="container">
        <!-- Sección para mostrar las canciones favoritas -->
        <div class="favorito-card">
            <h4>Canciones</h4>
            <ul>
                <?php if (count($favoritos['canciones']) === 0): ?>
                    <!-- Si no hay canciones favoritas mostramos mensaje -->
                    <li>No tienes canciones favoritas.</li>
                <?php else: ?>
                    <!-- Si hay canciones, las recorremos para mostrarlas -->
                    <?php foreach ($favoritos['canciones'] as $c): ?>
                        <li>
                            <!-- Mostramos el título de la canción escapando caracteres especiales -->
                            <?php echo htmlspecialchars($c['titulo']); ?>
                            <!-- Enlace para quitar la canción de favoritos, enviando tipo y id -->
                            <a class="btn-small" href="../controlador/favoritoControlador.php?accion=quitar&tipo=cancion&id=<?php echo $c['id_cancion']; ?>">Quitar</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sección para mostrar los álbumes favoritos -->
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

        <!-- Sección para mostrar los artistas favoritos -->
        <div class="favorito-card">
            <h4>Artistas</h4>
            <ul>
                <?php if (count($favoritos['artistas']) === 0): ?>
                    <li>No tienes artistas favoritos.</li>
                <?php else: ?>
                    <?php foreach ($favoritos['artistas'] as $art): ?>
                        <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <!-- Imagen del artista, con estilo para que sea redonda y bien adaptada -->
                            <img src="../<?php echo htmlspecialchars($art['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($art['nombre']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <!-- Nombre del artista -->
                            <span><?php echo htmlspecialchars($art['nombre']); ?></span>
                            <!-- Enlace para quitar al artista de favoritos -->
                            <a class="btn-small" style="margin-left: auto;" href="../controlador/favoritoControlador.php?accion=quitar&tipo=artista&id=<?php echo $art['id_artista']; ?>">Quitar</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Botón para volver a la página principal de usuario premium -->
        <a href="premium.php" class="btn btn-secondary mt-3">Volver</a> 
    </div>
</body>
</html>
