<?php
// Inicio la sesión si no está ya iniciada para gestionar al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Incluyo el modelo que maneja compartir playlists y añadir canciones
require_once '../modelo/compartirModelo.php';

// Si no hay usuario logueado, lo mando al login para identificarse
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

// Obtengo el id de la playlist desde la URL para saber en qué playlist actúo
$id_playlist = $_GET['playlist'];
// Guardo el id del usuario que está usando la aplicación
$id_usuario = $_SESSION['usuario_id'];

// Si la acción es compartir y viene el usuario destino por POST
if ($_GET['accion'] === 'compartir' && isset($_POST['usuario_destino'])) {
    // Comparto la playlist con el usuario destino
    CompartirModelo::compartir($id_playlist, $_POST['usuario_destino']);
}
// Si la acción es agregar canción y viene el id de la canción por POST
elseif ($_GET['accion'] === 'agregar' && isset($_POST['id_cancion'])) {
    // Agrego la canción a la playlist, guardando quién la agregó
    CompartirModelo::agregarCancion($id_playlist, $_POST['id_cancion'], $id_usuario);
}

// Redirijo a la página de playlists para mostrar la lista actualizada
header("Location: ../vista/playlists.php");
exit();
