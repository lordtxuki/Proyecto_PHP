<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/favoritoModelo.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cancion']) && is_numeric($_POST['id_cancion'])) {
    FavoritoModelo::agregar($id_usuario, $_POST['id_cancion'], 'cancion');
    header('Location: ../vista/favoritos.php');
    exit();
}

$accion = $_GET['accion'] ?? null;
$tipo = $_GET['tipo'] ?? null;
$id = $_GET['id'] ?? null;

if ($accion && $tipo && is_numeric($id)) {
    if ($accion === 'agregar') {
        FavoritoModelo::agregar($id_usuario, $id, $tipo);
    } elseif ($accion === 'quitar') {
        FavoritoModelo::quitar($id_usuario, $id, $tipo);
    }
}

header('Location: ../vista/favoritos.php');
exit();
