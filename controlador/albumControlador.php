<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/albumModelo.php';
require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;

if ($accion === 'favorito' && $id) {
    FavoritoModelo::agregar($id_usuario, $id, 'album');
} elseif ($accion === 'quitar_favorito' && $id) {
    FavoritoModelo::quitar($id_usuario, $id, 'album');
}

header("Location: ../vista/albumes.php");
exit();