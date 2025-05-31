<?php
// Inicio la sesión si no está activa para manejar al usuario conectado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluyo el modelo para trabajar con favoritos
require_once '../modelo/favoritoModelo.php';

// Si el usuario no está logueado, lo mando a la página de login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../vista/vista_login.php');
    exit();
}

// Guardo el id del usuario para usarlo en las operaciones
$id_usuario = $_SESSION['usuario_id'];

// Si recibo datos por POST para añadir una canción a favoritos y el id es válido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cancion']) && is_numeric($_POST['id_cancion'])) {
    // Llamo al modelo para agregar la canción a favoritos del usuario
    FavoritoModelo::agregar($id_usuario, $_POST['id_cancion'], 'cancion');
    // Redirijo a la página de favoritos para ver los cambios
    header('Location: ../vista/favoritos.php');
    exit();
}

// Recibo la acción, tipo y id por GET para agregar o quitar favoritos de otros tipos (álbum, artista, playlist)
$accion = $_GET['accion'] ?? null;
$tipo = $_GET['tipo'] ?? null;
$id = $_GET['id'] ?? null;

// Si los datos son correctos y válidos, realizo la acción correspondiente
if ($accion && $tipo && is_numeric($id)) {
    if ($accion === 'agregar') {
        FavoritoModelo::agregar($id_usuario, $id, $tipo);
    } elseif ($accion === 'quitar') {
        FavoritoModelo::quitar($id_usuario, $id, $tipo);
    }
}

// Finalmente, redirijo siempre a la página de favoritos para mostrar el estado actualizado
header('Location: ../vista/favoritos.php');
exit();
