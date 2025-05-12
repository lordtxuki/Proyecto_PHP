<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

$id_usuario = $_SESSION['usuario_id'];
$artistas = ArtistaModelo::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artistas</title>
    <link rel="stylesheet" href="../styles/secciones.css">
</head>
<body>
<h2 class="text-center">Artistas</h2>
<div class="container">
<?php foreach ($artistas as $artista): ?>
    <div class="artista-card">
        <div class="card-header">
            <img src="../<?php echo $artista['imagen']; ?>" alt="Artista">
            <strong><?php echo $artista['nombre']; ?></strong>
        </div>
        <div class="card-body">
            <?php if (ArtistaModelo::esSeguido($id_usuario, $artista['id_artista'])): ?>
                <a href="../controlador/artistaControlador.php?accion=dejar&id=<?php echo $artista['id_artista']; ?>" class="btn-small">Dejar de seguir</a>
            <?php else: ?>
                <a href="../controlador/artistaControlador.php?accion=seguir&id=<?php echo $artista['id_artista']; ?>" class="btn-small">Seguir</a>
            <?php endif; ?>
            <a href="../controlador/artistaControlador.php?accion=favorito&id=<?php echo $artista['id_artista']; ?>" class="btn-small">❤️ Favorito</a>
        </div>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>
