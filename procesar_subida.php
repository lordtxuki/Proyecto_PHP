<?php
// Inicia la sesión para trabajar con variables de sesión
session_start();

// Incluye la conexión a la base de datos
require_once 'controlador/conexion.php';

// Comprueba si el usuario está logueado, si no, redirige a la página de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/vista_login.php");
    exit();
}

// Obtiene el ID del usuario desde la sesión
$id_usuario = $_SESSION['usuario_id'];

// Consulta para verificar si el usuario es premium
$stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

// Si no es premium, muestra mensaje y termina la ejecución
if ($resultado->num_rows === 0) {
    die("No tienes permisos para subir contenido, solo usuarios premium.");
}

// Recoge los datos enviados por el formulario (usa valores por defecto si no están)
$titulo_cancion = $_POST['titulo_cancion'] ?? '';
$titulo_album   = $_POST['titulo_album'] ?? '';
$id_artista     = $_POST['id_artista'] ?? null;
$id_album       = $_POST['id_album'] ?? null;

// Define la ruta base del proyecto para guardar archivos
$rutaProyecto = 'C:/xampp/htdocs/Recuperacion_Php';

// Variable que almacenará el ID del álbum final para insertar la canción
$id_album_final = null;

// Si no se recibe un id_album, significa que hay que crear un álbum nuevo
if (empty($id_album)) {
    // Variable para la ruta de la imagen del álbum, inicialmente null
    $rutaImagen = null;

    // Comprueba si se subió una imagen sin errores
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Si no existe el directorio donde guardar imágenes, lo crea con permisos 0755
        if (!is_dir($rutaProyecto . '/imagenes/albumes')) mkdir($rutaProyecto . '/imagenes/albumes', 0755, true);

        // Genera un nombre único para la imagen con prefijo 'album_' y mantiene la extensión original
        $nombreImagen = uniqid('album_') . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        // Ruta física donde se guardará la imagen en el servidor
        $rutaFisicaImagen = $rutaProyecto . '/imagenes/albumes/' . $nombreImagen;

        // Mueve la imagen subida del directorio temporal a la ruta definitiva
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisicaImagen)) {
            die("Error al mover el archivo de imagen a: " . $rutaFisicaImagen);
        }

        // Ruta relativa para almacenar en la base de datos
        $rutaImagen = 'imagenes/albumes/' . $nombreImagen;
    }

    // Valida que se hayan recibido el título del álbum y el id del artista para crear el álbum
    if (!$titulo_album || !$id_artista) {
        die("Faltan datos para crear el álbum.");
    }

    // Inserta el nuevo álbum en la base de datos con título, id_artista y ruta de la imagen (puede ser null)
    $stmt = $conexion->prepare("INSERT INTO Albumes (titulo, id_artista, imagen_portada) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $titulo_album, $id_artista, $rutaImagen);
    $stmt->execute();

    // Obtiene el id generado del álbum insertado
    $id_album_final = $stmt->insert_id;
    $stmt->close();

    // Si no se pudo crear el álbum, muestra error y termina
    if (!$id_album_final) {
        die("Error al crear el álbum.");
    }
} else {
    // Si sí se recibe un id_album, se usa ese directamente
    $id_album_final = intval($id_album);
}

// Variable para guardar la ruta de la canción subida
$rutaCancion = null;

// Verifica que se haya subido un archivo de canción sin errores
if (isset($_FILES['cancion']) && $_FILES['cancion']['error'] === UPLOAD_ERR_OK) {
    // Si no existe el directorio donde guardar canciones, lo crea
    if (!is_dir($rutaProyecto . '/canciones')) mkdir($rutaProyecto . '/canciones', 0755, true);

    // Genera un nombre único para la canción con prefijo 'cancion_' y la extensión original
    $nombreCancion = uniqid('cancion_') . '.' . pathinfo($_FILES['cancion']['name'], PATHINFO_EXTENSION);
    // Ruta física donde se guardará la canción
    $rutaFisicaCancion = $rutaProyecto . '/canciones/' . $nombreCancion;

    // Mueve la canción desde el directorio temporal a la ruta definitiva
    if (!move_uploaded_file($_FILES['cancion']['tmp_name'], $rutaFisicaCancion)) {
        die("Error al mover el archivo de canción a: " . $rutaFisicaCancion);
    }

    // Ruta relativa para almacenar en la base de datos
    $rutaCancion = 'canciones/' . $nombreCancion;
} else {
    // Si no se recibió archivo válido, muestra error y termina
    die("No se recibió archivo de canción válido.");
}

// Inserta la canción en la base de datos con título, id del álbum y ruta del archivo
$stmt = $conexion->prepare("INSERT INTO Canciones (titulo, id_album, ruta) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $titulo_cancion, $id_album_final, $rutaCancion);
$stmt->execute();

// Comprueba si la inserción fue exitosa
if ($stmt->affected_rows === 1) {
    // Muestra mensaje de éxito y redirige a la página premium.php
    echo "<p>Álbum y canción subidos correctamente.</p>";
    echo "<p>Serás redirigido en breve. Si no, haz clic <a href='premium.php'>aquí</a>.</p>";
    header("Location: vista/premium.php");
} else {
    // Si hubo error al insertar la canción, muestra mensaje y enlace para volver
    echo "<p>Error al guardar la canción.</p>";
    echo "<p><a href='premium.php'>Volver</a></p>";
}

// Cierra el statement y la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
