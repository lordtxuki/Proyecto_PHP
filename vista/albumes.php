<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a base de datos y modelos para álbumes y favoritos
require_once '../controlador/conexion.php';
require_once '../modelo/albumModelo.php';
require_once '../modelo/favoritoModelo.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no, redirigir al login
    header("Location: ../vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Obtener todos los álbumes con el método estático de AlbumModelo
$albumes = AlbumModelo::obtenerTodos();

// Obtener todos los favoritos del usuario
$favs_todos = FavoritoModelo::obtener($id_usuario);

// Extraer solo los IDs de álbumes favoritos del arreglo obtenido
$favs_albumes = array_map(function($row) {
    return $row['id_album'];
}, $favs_todos['albumes']);
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

    <!-- Botón para volver a la página principal de usuario Premium, centrado -->
    <div class="text-center-volver">
        <a href="premium.php" class="btn-volver">↩ Volver a Premium</a>
    </div>

    <div class="container">
        <?php foreach ($albumes as $album): ?>
            <?php
            // Ruta de la portada del álbum
            $ruta = $album['imagen_portada'];

            // Comprobar si la imagen existe físicamente en el servidor
            $existe = $ruta && file_exists('../' . $ruta);

            // Obtener las canciones asociadas a este álbum con método del modelo
            $canciones = AlbumModelo::obtenerCanciones($album['id_album']);

            // Verificar si el álbum está en favoritos del usuario
            $isFavorito = in_array($album['id_album'], $favs_albumes);
            ?>
            <div class="album-card">
                <div class="card-header">
                    <?php if ($existe): ?>
                        <!-- Mostrar imagen de portada -->
                        <img src="../<?php echo htmlspecialchars($ruta); ?>"
                                alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
                    <?php else: ?>
                        <!-- Mostrar texto si no hay imagen -->
                        <div class="img-placeholder">Imagen</div>
                    <?php endif; ?>
                    <div>
                        <!-- Mostrar título del álbum -->
                        <strong><?php echo htmlspecialchars($album['titulo']); ?></strong><br>
                        <!-- Mostrar año y artista -->
                        <small>
                            <?php echo htmlspecialchars($album['año_publicacion']); ?>
                            – <?php echo htmlspecialchars($album['artista'] ?? 'Sin artista'); ?>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="lista-canciones">
                        <?php if (!empty($canciones)): ?>
                            <!-- Listar canciones del álbum -->
                            <?php foreach ($canciones as $cancion): ?>
                                <li><?php echo htmlspecialchars($cancion['titulo']); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hay canciones disponibles</li>
                        <?php endif; ?>
                    </ul>

                    <!-- Botón para añadir o quitar favorito según estado -->
                    <?php if ($isFavorito): ?>
                        <a href="../controlador/albumControlador.php?accion=quitar_favorito&id=<?php echo $album['id_album']; ?>"
                            class="btn-small">❤️ Quitar favorito</a>
                    <?php else: ?>
                        <a href="../controlador/albumControlador.php?accion=favorito&id=<?php echo $album['id_album']; ?>"
                            class="btn-small">🤍 Añadir a favoritos</a>
                    <?php endif; ?>

                    <!-- Enlace para añadir canción solo si el usuario es Premium -->
                    <?php if (AlbumModelo::esPremium($_SESSION['usuario_id'])): ?>
                        <a href="agregar_cancion.php?id_album=<?php echo $album['id_album']; ?>" class="btn-small">Añadir Canción</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Botón para volver al final de la página, centrado -->
    <div class="text-center-volver">
        <a href="premium.php" class="btn-volver">↩ Volver a Premium</a>
    </div>
</body>
</html>
