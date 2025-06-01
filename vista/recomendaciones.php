<?php
// Iniciamos sesión si no está iniciada para acceder a las variables de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos el modelo que maneja las recomendaciones
require_once '../modelo/recomendacionModelo.php';

// Obtenemos el id del usuario desde la sesión
$id_usuario = $_SESSION['usuario_id'] ?? null;

// Si no hay usuario logueado, redirigimos a la página de login
if (!$id_usuario) {
    header("Location: vista/vista_login.php");
    exit();
}

// Usamos el modelo para obtener las recomendaciones para este usuario
$recomendaciones = recomendacionModelo::obtenerRecomendaciones($id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recomendaciones</title>
    <!-- Enlazamos hoja de estilos para formato general de secciones -->
    <link rel="stylesheet" href="../styles/secciones.css">
</head>
<body>
    <h2 class="text-center">Recomendaciones según tus artistas seguidos</h2>

    <div class="container">
        <?php if (empty($recomendaciones)): ?>
            <!-- Si no hay recomendaciones mostramos un mensaje para motivar a seguir artistas -->
            <p>No hay recomendaciones aún. Sigue a algunos artistas para recibir sugerencias.</p>
        <?php else: ?>
            <!-- Si hay recomendaciones, recorremos cada álbum para mostrarlo -->
            <?php foreach ($recomendaciones as $album): ?>
                <div class="album-card">
                    <div class="card-header">
                        <?php
                        // Ruta de la imagen de portada del álbum
                        $ruta = $album['imagen_portada'];
                        // Comprobamos que la ruta exista y que el archivo exista en el servidor
                        $existe = $ruta && file_exists('../' . $ruta);
                        ?>
                        <?php if ($existe): ?>
                            <!-- Si la imagen existe, la mostramos con alt descriptivo para accesibilidad -->
                            <img src="../<?php echo htmlspecialchars($ruta); ?>"
                                    alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
                        <?php else: ?>
                            <!-- Si no hay imagen, mostramos un placeholder sencillo -->
                            <div class="img-placeholder">Imagen</div>
                        <?php endif; ?>
                        <div>
                            <!-- Título del álbum -->
                            <strong><?php echo htmlspecialchars($album['titulo']); ?></strong><br>
                            <!-- Año de publicación y nombre del artista -->
                            <small><?php echo htmlspecialchars($album['año_publicacion']); ?> – <?php echo htmlspecialchars($album['artista']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
