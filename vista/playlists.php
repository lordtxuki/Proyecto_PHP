<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/playlistModelo.php';
require_once '../modelo/cancionModelo.php';
require_once '../controlador/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: vista_login.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$playlists = PlaylistModelo::obtenerTodas($id_usuario);
$cancionesDisponibles = CancionModelo::obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Playlists</title>
    <link rel="stylesheet" href="../styles/playlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Mis Playlists</h2>

    <form method="POST" action="../controlador/playlistControlador.php?accion=crear" class="form-playlist mb-4">
        <div class="input-group">
            <input type="text" name="titulo" class="form-control playlist-input" placeholder="Nombre de la Playlist" required>
            <button type="submit" class="btn btn-primary playlist-btn">Crear</button>
        </div>
    </form>

    <?php foreach ($playlists as $pl): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <strong><?php echo $pl['titulo']; ?> (<?php echo $pl['estado']; ?>)</strong>
                <div>
                    <?php if ($pl['estado'] == 'activa'): ?>
                        <a href="../controlador/playlistControlador.php?accion=eliminar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    <?php else: ?>
                        <a href="../controlador/playlistControlador.php?accion=recuperar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-success">Recuperar</a>
                        <a href="../controlador/playlistControlador.php?accion=borrar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-success">Borrar</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../controlador/compartirControlador.php?accion=agregar&playlist=<?php echo $pl['id_playlist']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Añadir canción:</label>
                        <div class="input-group">
                            <select name="id_cancion" class="form-select">
                                <?php foreach ($cancionesDisponibles as $c): ?>
                                    <option value="<?php echo $c['id_cancion']; ?>"><?php echo $c['titulo']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-outline-primary">Añadir</button>
                        </div>
                    </div>
                </form>

                <h6>Canciones añadidas:</h6>
                <ul class="list-group">
                    <?php
                    $id = $pl['id_playlist'];
                    $query = "SELECT c.titulo, u.usuario, pc.fecha_agregado FROM playlist_canciones pc 
                                JOIN Canciones c ON pc.id_cancion = c.id_cancion 
                                JOIN Usuario u ON pc.id_usuario_que_agrega = u.id_usuario 
                                WHERE pc.id_playlist = $id";
                    $resultado = $conexion->query($query);
                    if ($resultado->num_rows > 0):
                        while ($fila = $resultado->fetch_assoc()):
                    ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $fila['titulo']; ?>
                                <span class="badge bg-secondary">Añadido por <?php echo $fila['usuario']; ?> el <?php echo date('d/m/Y', strtotime($fila['fecha_agregado'])); ?></span>
                            </li>
                    <?php endwhile; else: ?>
                            <li class="list-group-item">No hay canciones añadidas aún.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
        <a href="normal.php" class="btn btn-outline-secondary me-2">Volver</a>
    </div>
</div>

</body>
</html>
