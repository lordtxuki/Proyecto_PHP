<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

$accion = $_GET['accion'] ?? '';
$id_artista = $_GET['id'] ?? null;
$id_usuario = $_SESSION['usuario_id'];

if ($accion === 'seguir' && $id_artista) {
    ArtistaModelo::seguir($id_usuario, $id_artista);
} elseif ($accion === 'dejar' && $id_artista) {
    ArtistaModelo::dejarSeguir($id_usuario, $id_artista);
} elseif ($accion === 'favorito' && $id_artista) {
    FavoritoModelo::agregar($id_usuario, $id_artista, 'artista');
} elseif ($accion === 'quitar_favorito' && $id_artista) {
    FavoritoModelo::quitar($id_usuario, $id_artista, 'artista');
}

header("Location: ../vista/artistas.php");
exit();
