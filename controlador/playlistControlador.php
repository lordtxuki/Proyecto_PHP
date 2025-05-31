<?php
// Inicio la sesión si no está activa para manejar al usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluyo el modelo para manejar las playlists (listas de reproducción)
require_once '../modelo/playlistModelo.php';

// Si no hay usuario logueado, lo mando a la página de login para que se identifique
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

// Cojo la acción que viene por la URL para saber qué quiere hacer el usuario
$accion = $_GET['accion'] ?? '';

// Según la acción, llamo al método correspondiente del modelo
switch ($accion) {
    case 'crear':
        // Creo una nueva playlist para el usuario, usando el título enviado por POST
        PlaylistModelo::crear($_SESSION['usuario_id'], $_POST['titulo']);
        break;
    case 'eliminar':
        // Marco la playlist como eliminada (pero no la borro físicamente)
        PlaylistModelo::eliminar($_GET['id_playlist']);
        break;
    case 'recuperar':
        // Recupero una playlist eliminada, la activo de nuevo
        PlaylistModelo::recuperar($_GET['id_playlist']);
        break;
    case 'borrar':
        // Borro la playlist de la base de datos de forma definitiva
        PlaylistModelo::borrar($_GET['id_playlist']);
        break;
}

// Después de realizar la acción, redirijo a la página donde se listan las playlists
header('Location: ../vista/playlists.php');
exit();
?>
