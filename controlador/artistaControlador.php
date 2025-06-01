<?php
// Inicio la sesión si no está activa, para controlar el usuario que está logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargo los modelos necesarios: uno para seguir/dejar de seguir, otro para favoritos
require_once '../modelo/artistaModelo.php';
require_once '../modelo/favoritoModelo.php';

// Si no hay un usuario logueado, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];    // ID del usuario que hace la acción
$accion     = $_GET['accion'] ?? '';       // Puede ser: 'seguir', 'dejar', 'favorito', 'quitar_favorito'
$id_artista = $_GET['id'] ?? null;         // ID del artista sobre el que se actúa

// Comprobamos que el ID del artista venga y sea numérico
if (!is_numeric($id_artista)) {
    header("Location: ../vista/artistas.php");
    exit();
}

// Dependiendo de la acción (seguir, dejar, favorito, quitar_favorito), llamamos al modelo adecuado
if ($accion === 'seguir') {
    // El usuario empieza a seguir a este artista (inserta en artistas_seguidos)
    ArtistaModelo::seguir($id_usuario, $id_artista);

} elseif ($accion === 'dejar') {
    // El usuario deja de seguir a este artista
    ArtistaModelo::dejarSeguir($id_usuario, $id_artista);

} elseif ($accion === 'favorito') {
    // El usuario marca este artista como favorito (inserta en artistas_favoritos)
    FavoritoModelo::agregar($id_usuario, $id_artista, 'artista');

} elseif ($accion === 'quitar_favorito') {
    // El usuario quita este artista de favoritos
    FavoritoModelo::quitar($id_usuario, $id_artista, 'artista');
}

// Después de procesar la acción, volvemos a la lista de artistas
header("Location: ../vista/artistas.php");
exit();
