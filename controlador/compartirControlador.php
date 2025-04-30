<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}require_once '../modelo/compartirModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

$id_playlist = $_GET['playlist'];
$id_usuario = $_SESSION['usuario_id'];

if ($_GET['accion'] === 'compartir' && isset($_POST['usuario_destino'])) {
    CompartirModelo::compartir($id_playlist, $_POST['usuario_destino']);
} elseif ($_GET['accion'] === 'agregar' && isset($_POST['id_cancion'])) {
    CompartirModelo::agregarCancion($id_playlist, $_POST['id_cancion'], $id_usuario);
}

header("Location: ../vista/playlists.php");
exit();