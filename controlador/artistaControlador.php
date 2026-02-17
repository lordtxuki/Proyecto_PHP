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

$id_usuario = $_SESSION['usuario_id'];
$accion     = $_GET['accion'] ?? '';
$id_artista = $_GET['id'] ?? null;

if (!is_numeric($id_artista)) {
    header("Location: ../vista/artistas.php");
    exit();
}

switch ($accion) {

    case 'seguir':
        ArtistaModelo::seguir($id_usuario, $id_artista);
        break;

    case 'dejar':
        ArtistaModelo::dejarSeguir($id_usuario, $id_artista);
        break;

    case 'favorito':
        FavoritoModelo::agregar($id_usuario, $id_artista, 'artista');
        break;

    case 'quitar_favorito':
        FavoritoModelo::quitar($id_usuario, $id_artista, 'artista');
        break;
}

header("Location: ../vista/artistas.php");
exit();
