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

// 1. Determinar rol: admin o premium
$rol = null;
$stmt = $conexion->prepare("SELECT 1 FROM usuario_admin WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $rol = 'admin';
} else {
    $stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $rol = 'premium';
    }
}
if (!$rol) {
    die("No tienes permisos para subir contenido.");
}

// 2. Recoger datos de POST y archivos
$titulo_cancion = $_POST['titulo_cancion'] ?? '';
$id_album       = $_POST['id_album'] ?? null;
$titulo_album   = $_POST['titulo_album'] ?? null;
$id_artista     = $_POST['id_artista'] ?? null;
$nuevo_artista  = $_POST['nuevo_artista'] ?? null;

// 3. Subir archivo canción si existe
$rutaCancion = null;
if (isset($_FILES['cancion']) && $_FILES['cancion']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir('canciones')) mkdir('canciones', 0755, true);
    $nombreCancion = basename($_FILES['cancion']['name']);
    $rutaCancion = 'canciones/' . $nombreCancion;
    if (!move_uploaded_file($_FILES['cancion']['tmp_name'], $rutaCancion)) {
        die("Error al subir el archivo de la canción.");
    }
}

// 4. Subir portada si existe
$rutaImagen = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir('portadas')) mkdir('portadas', 0755, true);
    $nombreImagen = basename($_FILES['imagen']['name']);
    $rutaImagen = 'portadas/' . $nombreImagen;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
        die("Error al subir la imagen de portada.");
    }
}

// 5. Insertar nuevo artista si se proporciona
if (!empty($nuevo_artista)) {
    $stmt = $conexion->prepare("INSERT INTO Artistas (nombre) VALUES (?)");
    $stmt->bind_param("s", $nuevo_artista);
    if (!$stmt->execute()) {
        die("Error al insertar artista: " . $stmt->error);
    }
    $id_artista = $conexion->insert_id;
}

// 6. Si solo es añadir canción a álbum existente
if ($id_album && $titulo_cancion && $rutaCancion) {
    $stmt = $conexion->prepare(
        "INSERT INTO Canciones (id_album, titulo, duracion, veces_reproducida, ruta)
            VALUES (?, ?, '00:03:00', 0, ?)"
    );
    $stmt->bind_param("iss", $id_album, $titulo_cancion, $rutaCancion);
    if (!$stmt->execute()) {
        die("Error al insertar canción: " . $stmt->error);
    }
    header("Location: vista/{$rol}.php");
    exit();
}

// 7. Crear nuevo álbum con primera canción
if ($titulo_album && $id_artista && $titulo_cancion && $rutaCancion) {
    $conexion->begin_transaction();
    try {
        // Insertar álbum
        $stmt = $conexion->prepare(
            "INSERT INTO Albumes (id_artista, titulo, año_publicacion, imagen_portada)
                VALUES (?, ?, YEAR(CURDATE()), ?)"
        );
        $imagenParam = $rutaImagen ?: null;
        $stmt->bind_param("iss", $id_artista, $titulo_album, $imagenParam);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $nuevo_id_album = $conexion->insert_id;

        // Insertar canción
        $stmt = $conexion->prepare(
            "INSERT INTO Canciones (id_album, titulo, duracion, veces_reproducida, ruta)
                VALUES (?, ?, '00:03:00', 0, ?)"
        );
        $stmt->bind_param("iss", $nuevo_id_album, $titulo_cancion, $rutaCancion);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $conexion->commit();
        header("Location: vista/{$rol}.php");
        exit();
    } catch (Exception $e) {
        $conexion->rollback();
        die("Fallo en la transacción: " . $e->getMessage());
    }
}

die("Faltan datos para subir la canción o crear el álbum.");