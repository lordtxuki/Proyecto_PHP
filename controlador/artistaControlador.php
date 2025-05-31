<?php
// Inicio la sesión si no está activa, para controlar el usuario que está logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargo los archivos con las funciones para manejar álbumes y favoritos
require_once '../modelo/albumModelo.php';
require_once '../modelo/favoritoModelo.php';

// Compruebo si hay un usuario logueado, si no, lo envío al login para que inicie sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

// Guardo el id del usuario en una variable para usarla después en las funciones
$id_usuario = $_SESSION['usuario_id'];

// Recojo la acción que llega por la URL (por ejemplo, añadir o quitar favorito)
$accion = $_GET['accion'] ?? '';
// Recojo el id que llega por la URL, que debería ser el id del álbum a modificar
$id = $_GET['id'] ?? null;

// Si la acción es 'favorito' y el id es válido, llamo a la función para añadir a favoritos
if ($accion === 'favorito' && $id) {
    FavoritoModelo::agregar($id_usuario, $id, 'album');
}
// Si la acción es 'quitar_favorito' y el id es válido, llamo a la función para quitar de favoritos
elseif ($accion === 'quitar_favorito' && $id) {
    FavoritoModelo::quitar($id_usuario, $id, 'album');
}

// Después de hacer la operación, redirijo a la página de álbumes para ver los cambios
header("Location: ../vista/albumes.php");
exit();
