<?php
session_start();

require_once 'controlador/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Verificar si es usuario premium
$stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 0) {
    die("No tienes permisos para subir contenido, solo usuarios premium.");
}

// Recoger datos del formulario
$titulo_cancion = $_POST['titulo_cancion'] ?? '';
$titulo_album   = $_POST['titulo_album'] ?? '';
$id_artista     = $_POST['id_artista'] ?? null;
$id_album       = $_POST['id_album'] ?? null;

$rutaProyecto = 'C:/xampp/htdocs/Recuperacion_Php';

// Variable para guardar el id del álbum donde insertar la canción
$id_album_final = null;

// Si no se recibe id_album, creamos uno nuevo
if (empty($id_album)) {
    // Subir imagen del álbum (opcional)
    $rutaImagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($rutaProyecto . '/imagenes/albumes')) mkdir($rutaProyecto . '/imagenes/albumes', 0755, true);

        $nombreImagen = uniqid('album_') . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $rutaFisicaImagen = $rutaProyecto . '/imagenes/albumes/' . $nombreImagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisicaImagen)) {
            die("Error al mover el archivo de imagen a: " . $rutaFisicaImagen);
        }

        $rutaImagen = 'imagenes/albumes/' . $nombreImagen;
    }

    // Crear álbum nuevo
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
    // Si sí recibimos id_album, usamos ese para la canción
    $id_album_final = intval($id_album);
}

// Subir archivo canción
$rutaCancion = null;
if (isset($_FILES['cancion']) && $_FILES['cancion']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir($rutaProyecto . '/canciones')) mkdir($rutaProyecto . '/canciones', 0755, true);

    $nombreCancion = uniqid('cancion_') . '.' . pathinfo($_FILES['cancion']['name'], PATHINFO_EXTENSION);
    $rutaFisicaCancion = $rutaProyecto . '/canciones/' . $nombreCancion;

    if (!move_uploaded_file($_FILES['cancion']['tmp_name'], $rutaFisicaCancion)) {
        die("Error al mover el archivo de canción a: " . $rutaFisicaCancion);
    }

    $rutaCancion = 'canciones/' . $nombreCancion;
} else {
    die("No se recibió archivo de canción válido.");
}

// Insertar canción en base de datos
$stmt = $conexion->prepare("INSERT INTO Canciones (titulo, id_album, ruta) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $titulo_cancion, $id_album_final, $rutaCancion);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    // Redirigir a premium.php después de 3 segundos
    echo "<p>Álbum y canción subidos correctamente.</p>";
    echo "<p>Serás redirigido en breve. Si no, haz clic <a href='premium.php'>aquí</a>.</p>";
    header("Location: vista/premium.php");
} else {
    echo "<p>Error al guardar la canción.</p>";
    echo "<p><a href='premium.php'>Volver</a></p>";
}

$stmt->close();
$conexion->close();
?>
