<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir modelos para obtener artistas, saber si seguimos o tenemos en favoritos
require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

// Si no hay un usuario logueado, mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

// Obtenemos todos los artistas desde el modelo
$artistas = ArtistaModelo::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artistas</title>
    <!-- Enlaces a hojas de estilo para álbumes y artistas -->
    <link rel="stylesheet" href="../styles/albumes.css">
    <link rel="stylesheet" href="../styles/artistas.css">
</head>
<body>
    <h2 class="text-center">Artistas</h2>
    <div class="container">
        <?php foreach ($artistas as $artista): ?>
            <?php
            // Ruta de la imagen del artista
            $ruta = $artista['imagen'];

            // Verificar si la imagen existe físicamente en el servidor
            $existe = $ruta && file_exists('../' . $ruta);

            // ID del artista actual
            $id_artista = $artista['id_artista'];

            // Verificar si el usuario actual sigue a este artista
            $seguido = ArtistaModelo::esSeguido($_SESSION['usuario_id'], $id_artista);

            // Verificar si este artista está marcado como favorito por el usuario
            $favorito = FavoritoModelo::esFavorito($_SESSION['usuario_id'], $id_artista, 'artista');
            ?>
            <div class="artist-card">
                <?php if ($existe): ?>
                    <!-- Mostrar la imagen real si existe -->
                    <img src="../<?php echo htmlspecialchars($ruta); ?>" alt="Artista <?php echo htmlspecialchars($artista['nombre']); ?>">
                <?php else: ?>
                    <!-- Placeholder si no hay imagen -->
                    <div class="img-placeholder">Imagen</div>
                <?php endif; ?>

                <!-- Nombre del artista -->
                <div>
                    <strong><?php echo htmlspecialchars($artista['nombre']); ?></strong>
                </div>

                <div class="acciones">
                    <!-- Botón para seguir / dejar de seguir -->
                    <?php if ($seguido): ?>
                        <a href="../controlador/artistaControlador.php?accion=dejar&id=<?php echo $id_artista; ?>" class="btn-small rojo">
                            Dejar de seguir
                        </a>
                    <?php else: ?>
                        <a href="../controlador/artistaControlador.php?accion=seguir&id=<?php echo $id_artista; ?>" class="btn-small verde">
                            Seguir
                        </a>
                    <?php endif; ?>

                    <!-- Botón para agregar / quitar de favoritos -->
                    <?php if ($favorito): ?>
                        <a href="../controlador/artistaControlador.php?accion=quitar_favorito&id=<?php echo $id_artista; ?>" class="btn-small amarillo">
                            Quitar favorito
                        </a>
                    <?php else: ?>
                        <a href="../controlador/artistaControlador.php?accion=favorito&id=<?php echo $id_artista; ?>" class="btn-small azul">
                            Agregar favorito
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Botón para volver a la página -->
    <a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
</body>
</html>
