<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista_login.php");
    exit();
}

$artistas = ArtistaModelo::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artistas</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../styles/artistas.css">
</head>

<body class="fondo-artistas">

<div class="container py-5">

    <h2 class="text-center mb-5 titulo-artistas">Artistas</h2>

    <div class="row g-4">

        <?php foreach ($artistas as $artista): ?>

            <?php
            $ruta = $artista['imagen'];
            $existe = $ruta && file_exists('../' . $ruta);
            $id_artista = $artista['id_artista'];

            $seguido = ArtistaModelo::esSeguido($_SESSION['usuario_id'], $id_artista);
            $favorito = FavoritoModelo::esFavorito($_SESSION['usuario_id'], $id_artista, 'artista');
            ?>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="artist-card">

                    <?php if ($existe): ?>
                        <img src="../<?php echo htmlspecialchars($ruta); ?>"
                                alt="<?php echo htmlspecialchars($artista['nombre']); ?>">
                    <?php else: ?>
                        <div class="img-placeholder">Imagen</div>
                    <?php endif; ?>

                    <h5>
                        <?php echo htmlspecialchars($artista['nombre']); ?>
                    </h5>

                    <div class="acciones">

                        <?php if ($seguido): ?>
                            <a href="../controlador/artistaControlador.php?accion=dejar&id=<?php echo $id_artista; ?>"
                                class="btn-custom btn-rojo">
                                Dejar de seguir
                            </a>
                        <?php else: ?>
                            <a href="../controlador/artistaControlador.php?accion=seguir&id=<?php echo $id_artista; ?>"
                                class="btn-custom btn-verde">
                                Seguir
                            </a>
                        <?php endif; ?>

                        <?php if ($favorito): ?>
                            <a href="../controlador/artistaControlador.php?accion=quitar_favorito&id=<?php echo $id_artista; ?>"
                                class="btn-custom btn-amarillo">
                                Quitar favorito
                            </a>
                        <?php else: ?>
                            <a href="../controlador/artistaControlador.php?accion=favorito&id=<?php echo $id_artista; ?>"
                                class="btn-custom btn-azul">
                                Favorito
                            </a>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <div class="text-center mt-5">
        <a href="normal.php" class="btn-volver">Volver</a>
    </div>

</div>

</body>
</html>
