<?php
// Arranco la sesión si no está iniciada para manejar al usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluyo los modelos para trabajar con álbumes y favoritos
require_once '../modelo/albumModelo.php';
require_once '../modelo/favoritoModelo.php';

// Si no hay usuario logueado, lo mando al login para que se identifique
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit();
}

// Guardo el id del usuario en una variable para usar después
$id_usuario = $_SESSION['usuario_id'];

// Cojo la acción que llega por la URL, por ejemplo: agregar o quitar favorito
$accion = $_GET['accion'] ?? '';
// Cojo el id que llega por la URL, que debería ser el id del álbum
$id = $_GET['id'] ?? null;

// Si la acción es agregar a favoritos y me llega un id válido
if ($accion === 'favorito' && $id) {
    // Llamo al modelo para añadir ese álbum a los favoritos del usuario
    FavoritoModelo::agregar($id_usuario, $id, 'album');
}
// Si la acción es quitar favorito y me llega un id válido
elseif ($accion === 'quitar_favorito' && $id) {
    // Llamo al modelo para eliminar ese álbum de favoritos del usuario
    FavoritoModelo::quitar($id_usuario, $id, 'album');
}

// Después de hacer la acción, redirijo de nuevo a la página de álbumes para mostrar cambios
header("Location: ../vista/albumes.php");
exit();
