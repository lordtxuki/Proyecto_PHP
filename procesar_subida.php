<?php
session_start();
require_once 'controlador/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Verificar si es premium
$stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("No tienes permisos para subir contenido, solo usuarios premium.");
}

$titulo_cancion = $_POST['titulo_cancion'] ?? '';
$titulo_album   = $_POST['titulo_album'] ?? '';
$id_artista     = $_POST['id_artista'] ?? null;
$id_album       = $_POST['id_album'] ?? null;


$rutaProyecto = __DIR__;

$id_album_final = null;

/* ===============================
    CREAR ÁLBUM SI NO EXISTE
================================ */
if (empty($id_album)) {

    $rutaImagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $directorioImagenes = $rutaProyecto . '/imagenes/albumes/';

        if (!is_dir($directorioImagenes)) {
            mkdir($directorioImagenes, 0755, true);
        }

        $nombreImagen = uniqid('album_') . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $rutaFisicaImagen = $directorioImagenes . $nombreImagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisicaImagen)) {
            die("Error al mover la imagen.");
        }

        $rutaImagen = 'imagenes/albumes/' . $nombreImagen;
    }

    if (!$titulo_album || !$id_artista) {
        die("Faltan datos para crear el álbum.");
    }

    $stmt = $conexion->prepare("INSERT INTO Albumes (titulo, id_artista, imagen_portada) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $titulo_album, $id_artista, $rutaImagen);
    $stmt->execute();

    $id_album_final = $stmt->insert_id;
    $stmt->close();

    if (!$id_album_final) {
        die("Error al crear el álbum.");
    }

} else {
    $id_album_final = intval($id_album);
}

/* ===============================
    SUBIR CANCIÓN
================================ */

if (isset($_FILES['cancion']) && $_FILES['cancion']['error'] === UPLOAD_ERR_OK) {

    $directorioCanciones = $rutaProyecto . '/canciones/';

    if (!is_dir($directorioCanciones)) {
        mkdir($directorioCanciones, 0755, true);
    }

    $nombreCancion = uniqid('cancion_') . '.' . pathinfo($_FILES['cancion']['name'], PATHINFO_EXTENSION);
    $rutaFisicaCancion = $directorioCanciones . $nombreCancion;

    if (!move_uploaded_file($_FILES['cancion']['tmp_name'], $rutaFisicaCancion)) {
        die("Error al mover la canción.");
    }

    $rutaCancion = 'canciones/' . $nombreCancion;

} else {
    die("No se recibió archivo de canción válido.");
}

/* ===============================
    INSERTAR EN BD
================================ */

$stmt = $conexion->prepare("INSERT INTO Canciones (titulo, id_album, ruta) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $titulo_cancion, $id_album_final, $rutaCancion);
$stmt->execute();

$stmt->close();
$conexion->close();

/* Redirigir */
header("Location: vista/premium.php");
exit();
