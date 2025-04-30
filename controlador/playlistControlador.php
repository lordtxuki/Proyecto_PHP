<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}require_once '../modelo/playlistModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'crear':
        PlaylistModelo::crear($_SESSION['usuario_id'], $_POST['titulo']);
        break;
    case 'eliminar':
        PlaylistModelo::eliminar($_GET['id_playlist']);
        break;
    case 'recuperar':
        PlaylistModelo::recuperar($_GET['id_playlist']);
        break;
}

header('Location: ../vista/playlists.php');
exit();