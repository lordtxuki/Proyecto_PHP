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

$recomendaciones = recomendacionModelo::obtenerRecomendaciones($id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recomendaciones</title>
    <link rel="stylesheet" href="../styles/recomendaciones.css">
</head>
<body>

<h2 class="text-center">Recomendaciones</h2>

<div class="container">

<?php if (empty($recomendaciones)): ?>

    <p>No hay recomendaciones aún.</p>

<?php else: ?>

<?php foreach ($recomendaciones as $album): ?>

<?php
$ruta = $album['imagen_portada'];
$rutaFisica = __DIR__ . '/../' . $ruta;
$rutaWeb = '../' . $ruta;

$existe = !empty($ruta) && file_exists($rutaFisica);
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
                – <?php echo htmlspecialchars($album['artista']); ?>
            </small>
        </div>

    </div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</body>
</html>
