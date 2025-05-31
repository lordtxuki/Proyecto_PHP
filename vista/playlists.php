<?php
// Inicia sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos los modelos que manejan playlists y canciones
require_once '../modelo/playlistModelo.php';
require_once '../modelo/cancionModelo.php';
// Incluimos la conexión a la base de datos para consultas directas
require_once '../controlador/conexion.php';

// Si no hay usuario logueado, redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: vista_login.php');
    exit();
}

// Guardamos el id del usuario logueado
$id_usuario = $_SESSION['usuario_id'];

// Obtenemos todas las playlists del usuario mediante el modelo
$playlists = PlaylistModelo::obtenerTodas($id_usuario);
// Obtenemos todas las canciones disponibles para añadir
$cancionesDisponibles = CancionModelo::obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Playlists</title>
    <!-- Estilos propios y Bootstrap para diseño -->
    <link rel="stylesheet" href="../styles/playlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Mis Playlists</h2>

    <!-- Formulario para crear una nueva playlist -->
    <form method="POST" action="../controlador/playlistControlador.php?accion=crear" class="form-playlist mb-4">
        <div class="input-group">
            <input type="text" name="titulo" class="form-control playlist-input" placeholder="Nombre de la Playlist" required>
            <button type="submit" class="btn btn-primary playlist-btn">Crear</button>
        </div>
    </form>

    <!-- Recorremos las playlists del usuario para mostrarlas -->
    <?php foreach ($playlists as $pl): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <!-- Título y estado de la playlist -->
                <strong><?php echo $pl['titulo']; ?> (<?php echo $pl['estado']; ?>)</strong>
                <div>
                    <?php if ($pl['estado'] == 'activa'): ?>
                        <!-- Si está activa, permite eliminar (marcar como eliminada) -->
                        <a href="../controlador/playlistControlador.php?accion=eliminar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    <?php else: ?>
                        <!-- Si está eliminada, permite recuperar o borrar definitivamente -->
                        <a href="../controlador/playlistControlador.php?accion=recuperar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-success">Recuperar</a>
                        <a href="../controlador/playlistControlador.php?accion=borrar&id_playlist=<?php echo $pl['id_playlist']; ?>" class="btn btn-sm btn-success">Borrar</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Formulario para añadir canciones a la playlist -->
                <form method="POST" action="../controlador/compartirControlador.php?accion=agregar&playlist=<?php echo $pl['id_playlist']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Añadir canción:</label>
                        <div class="input-group">
                            <select name="id_cancion" class="form-select">
                                <!-- Opciones con todas las canciones disponibles -->
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
                    // Consultamos las canciones añadidas a esta playlist junto con quién y cuándo las agregó
                    $id = $pl['id_playlist'];
                    $query = "SELECT c.titulo, u.usuario, pc.fecha_agregado FROM playlist_canciones pc 
                                JOIN Canciones c ON pc.id_cancion = c.id_cancion 
                                JOIN Usuario u ON pc.id_usuario_que_agrega = u.id_usuario 
                                WHERE pc.id_playlist = $id";
                    $resultado = $conexion->query($query);

                    // Si hay canciones, las mostramos en una lista
                    if ($resultado->num_rows > 0):
                        while ($fila = $resultado->fetch_assoc()):
                    ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $fila['titulo']; ?>
                                <span class="badge bg-secondary">Añadido por <?php echo $fila['usuario']; ?> el <?php echo date('d/m/Y', strtotime($fila['fecha_agregado'])); ?></span>
                            </li>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                            <li class="list-group-item">No hay canciones añadidas aún.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Botón para volver a la página principal (puede ser premium o normal) -->
    <div class="text-center mt-4">
        <a href="premium.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
</div>
</body>
</html>
