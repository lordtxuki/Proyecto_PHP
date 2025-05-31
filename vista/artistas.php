<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

$artistas = ArtistaModelo::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artistas</title>
    <link rel="stylesheet" href="../styles/albumes.css">
    <link rel="stylesheet" href="../styles/artistas.css">
</head>
<body>
<h2 class="text-center">Artistas</h2>
<div class="container">
<?php foreach ($artistas as $artista): ?>
    <?php
    $ruta = $artista['imagen'];
    $existe = $ruta && file_exists('../' . $ruta);
    $id_artista = $artista['id_artista'];
    $seguido = ArtistaModelo::esSeguido($_SESSION['usuario_id'], $id_artista);
    $favorito = FavoritoModelo::esFavorito($_SESSION['usuario_id'], $id_artista, 'artista');
    ?>
    <div class="artist-card">
        <?php if ($existe): ?>
            <img src="../<?php echo htmlspecialchars($ruta); ?>" alt="Artista <?php echo htmlspecialchars($artista['nombre']); ?>">
        <?php else: ?>
            <div class="img-placeholder">Imagen</div>
        <?php endif; ?>
        <div>
            <strong><?php echo htmlspecialchars($artista['nombre']); ?></strong>
        </div>
        <div class="acciones">
            <?php if ($seguido): ?>
                <a href="../controlador/artistaControlador.php?accion=dejar&id=<?php echo $id_artista; ?>" class="btn-small rojo">Dejar de seguir</a>
            <?php else: ?>
                <a href="../controlador/artistaControlador.php?accion=seguir&id=<?php echo $id_artista; ?>" class="btn-small verde">Seguir</a>
            <?php endif; ?>

            <?php if ($favorito): ?>
                <a href="../controlador/artistaControlador.php?accion=quitar_favorito&id=<?php echo $id_artista; ?>" class="btn-small amarillo">Quitar favorito</a>
            <?php else: ?>
                <a href="../controlador/artistaControlador.php?accion=favorito&id=<?php echo $id_artista; ?>" class="btn-small azul">Agregar favorito</a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

    <a href="premium.php" class="btn btn-secondary mt-3">Volver</a> 
</body>
</html>
