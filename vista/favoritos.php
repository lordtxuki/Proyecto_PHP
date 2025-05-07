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

<h2>Favoritos</h2>
<h3>Canciones</h3>
<ul>
<?php foreach ($favoritos['canciones'] as $c): ?>
    <li><?php echo $c['titulo']; ?> <a href="../controlador/favoritoControlador.php?accion=quitar&tipo=cancion&id=<?php echo $c['id_cancion']; ?>">Quitar</a></li>
<?php endforeach; ?>
</ul>
<h3>Álbumes</h3>
<ul>
<?php foreach ($favoritos['albumes'] as $a): ?>
    <li><?php echo $a['titulo']; ?> <a href="../controlador/favoritoControlador.php?accion=quitar&tipo=album&id=<?php echo $a['id_album']; ?>">Quitar</a></li>
<?php endforeach; ?>
</ul>
<a href="normal.php">Volver</a>
