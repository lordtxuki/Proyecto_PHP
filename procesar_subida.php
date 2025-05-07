<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'controlador/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No tienes permisos para subir contenido.";
    exit();
}

$titulo = $_POST['titulo'];
$nombreCancion = $_FILES['cancion']['name'];
$rutaCancion = 'canciones/' . basename($nombreCancion);
move_uploaded_file($_FILES['cancion']['tmp_name'], $rutaCancion);

$rutaImagen = null;
if (!empty($_FILES['imagen']['name'])) {
    $nombreImagen = $_FILES['imagen']['name'];
    $rutaImagen = 'portadas/' . basename($nombreImagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen);
}

$conexion->begin_transaction();
try {
    $stmt = $conexion->prepare("INSERT INTO Albumes (id_artista, titulo, año_publicacion, imagen_portada) VALUES (NULL, ?, YEAR(CURDATE()), ?)");
    $stmt->bind_param("ss", $titulo, $rutaImagen);
    $stmt->execute();
    $id_album = $conexion->insert_id;

    $stmt = $conexion->prepare("INSERT INTO Canciones (id_album, titulo, duracion, veces_reproducida, ruta) VALUES (?, ?, '00:03:00', 0, ?)");
    $stmt->bind_param("iss", $id_album, $titulo, $rutaCancion);  
    $stmt->execute();

    $conexion->commit();
    echo "Canción subida correctamente.";
    echo '<br><a href="vista/premium.php">Volver al panel Premium</a>';
} catch (Exception $e) {
    $conexion->rollback();
    echo "Error al subir la canción: " . $e->getMessage();
}

header("Location: vista/premium.php");

