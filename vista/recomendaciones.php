<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/recomendacionModelo.php';

$id_usuario = $_SESSION['usuario_id'] ?? null;
if (!$id_usuario) {
    header("Location: vista/vista_login.php");
    exit();
}

$recomendaciones = RecomendacionModelo::obtenerRecomendaciones($id_usuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recomendaciones</title>
    <link rel="stylesheet" href="../styles/secciones.css">
</head>
<body>
<h2 class="text-center">Recomendaciones según tus artistas seguidos</h2>
<div class="container">
<?php if (empty($recomendaciones)): ?>
    <p>No hay recomendaciones aún. Sigue a algunos artistas para recibir sugerencias.</p>
<?php else: ?>
    <?php foreach ($recomendaciones as $album): ?>
        <div class="album-card">
            <div class="card-header">
                <?php
                $ruta = $album['imagen_portada'];
                $existe = $ruta && file_exists('../' . $ruta);
                ?>
                <?php if ($existe): ?>
                    <img src="../<?php echo htmlspecialchars($ruta); ?>"
                            alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
                <?php else: ?>
                    <div class="img-placeholder">Imagen</div>
                <?php endif; ?>
                <div>
                    <strong><?php echo htmlspecialchars($album['titulo']); ?></strong><br>
                    <small><?php echo htmlspecialchars($album['año_publicacion']); ?> – <?php echo htmlspecialchars($album['artista']); ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>