<?php
// Iniciamos sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
require_once '../controlador/conexion.php';

// 1. PROCESO DE ELIMINAR ARTISTA Y SU CONTENIDO
if (isset($_GET['eliminar'])) {
    // Convertimos el parámetro a entero para evitar inyección
    $id_artista = intval($_GET['eliminar']);

    // Obtenemos todos los álbumes del artista para luego borrar sus canciones
    $stmt_albumes = $conexion->prepare("SELECT id_album FROM Albumes WHERE id_artista = ?");
    $stmt_albumes->bind_param("i", $id_artista);
    $stmt_albumes->execute();
    $res_albumes = $stmt_albumes->get_result();

    // Por cada álbum, borramos las canciones relacionadas
    while ($album = $res_albumes->fetch_assoc()) {
        $id_album = $album['id_album'];
        $stmt_canciones = $conexion->prepare("DELETE FROM Canciones WHERE id_album = ?");
        $stmt_canciones->bind_param("i", $id_album);
        $stmt_canciones->execute();
    }

    // Borramos todos los álbumes del artista
    $stmt_borrar_albumes = $conexion->prepare("DELETE FROM Albumes WHERE id_artista = ?");
    $stmt_borrar_albumes->bind_param("i", $id_artista);
    $stmt_borrar_albumes->execute();

    // Obtenemos la ruta de la imagen del artista para borrarla físicamente del servidor
    $stmt_imagen = $conexion->prepare("SELECT imagen FROM Artistas WHERE id_artista = ?");
    $stmt_imagen->bind_param("i", $id_artista);
    $stmt_imagen->execute();
    $res_imagen = $stmt_imagen->get_result();
    if ($fila_imagen = $res_imagen->fetch_assoc()) {
        $ruta_imagen = '../' . $fila_imagen['imagen'];
        // Verificamos que el archivo existe antes de borrarlo
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

    // Finalmente borramos el artista de la base de datos
    $stmt_borrar_artista = $conexion->prepare("DELETE FROM Artistas WHERE id_artista = ?");
    $stmt_borrar_artista->bind_param("i", $id_artista);
    $stmt_borrar_artista->execute();

    // Redirigimos indicando que la eliminación fue exitosa
    header("Location: ?seccion=crear_artista&eliminado=1");
    exit;
}

// 2. PROCESO DE CREAR NUEVO ARTISTA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiamos el nombre recibido
    $nombre = trim($_POST['nombre']);

    // Validamos que nombre no esté vacío y que se haya subido una imagen correctamente
    if (!empty($nombre) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen'];

        // Permitimos solo ciertos tipos de imagen
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imagen['type'], $allowed_types)) {
            echo "<p style='color:red;'>Formato de imagen no permitido. Usa JPG, PNG o GIF.</p>";
        } else {
            // Directorio donde guardaremos la imagen
            $upload_dir = '../imagenes/artistas/';
            // Si no existe el directorio, lo creamos con permisos 0755
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Generamos un nombre único para la imagen
            $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('artista_') . '.' . $ext;
            $ruta_destino = $upload_dir . $nombre_archivo;

            // Movemos la imagen subida al directorio destino
            if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                // Insertamos el artista en la base de datos con el nombre y la ruta relativa de la imagen
                $stmt = $conexion->prepare("INSERT INTO Artistas (nombre, imagen) VALUES (?, ?)");
                $ruta_relativa = 'imagenes/artistas/' . $nombre_archivo;
                $stmt->bind_param("ss", $nombre, $ruta_relativa);
                $stmt->execute();

                // Redirigimos indicando que la creación fue exitosa
                header("Location: ?seccion=crear_artista&creado=1");
                exit;
            } else {
                echo "<p style='color:red;'>Error al subir la imagen.</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Debe completar el nombre y subir una imagen válida.</p>";
    }
}

// Mostrar mensajes de confirmación si se creó o eliminó un artista
if (isset($_GET['creado'])) {
    echo "<p style='color:green;'>Artista creado correctamente con imagen.</p>";
}
if (isset($_GET['eliminado'])) {
    echo "<p style='color:green;'>Artista y todo su contenido eliminado correctamente.</p>";
}

// 3. CONSULTA PARA LISTAR ARTISTAS EXISTENTES
$res = $conexion->query("SELECT id_artista, nombre, imagen FROM Artistas ORDER BY nombre ASC");
?>

<h2>Crear nuevo artista</h2>

<!-- Formulario para crear nuevo artista con nombre e imagen -->
<form method="POST" enctype="multipart/form-data">
    <label for="nombre">Nombre del artista:</label>
    <input type="text" name="nombre" id="nombre" required>
    <br><br>
    <label for="imagen">Imagen del artista:</label>
    <input type="file" name="imagen" id="imagen" accept="image/*" required>
    <br><br>
    <button type="submit">Crear</button>
</form>

<hr>

<h2>Artistas existentes</h2>
<ul style="list-style: none; padding: 0;">
<?php while ($fila = $res->fetch_assoc()): ?>
    <?php
    // Para cada artista, contamos sus álbumes
    $stmt_albumes = $conexion->prepare("SELECT id_album FROM Albumes WHERE id_artista = ?");
    $stmt_albumes->bind_param("i", $fila['id_artista']);
    $stmt_albumes->execute();
    $res_albumes = $stmt_albumes->get_result();
    $num_albumes = $res_albumes->num_rows;

    // Contamos todas las canciones de esos álbumes
    $num_canciones = 0;
    while ($album = $res_albumes->fetch_assoc()) {
        $id_album = $album['id_album'];
        $stmt_canciones = $conexion->prepare("SELECT COUNT(*) AS total FROM Canciones WHERE id_album = ?");
        $stmt_canciones->bind_param("i", $id_album);
        $stmt_canciones->execute();
        $resultado = $stmt_canciones->get_result()->fetch_assoc();
        $num_canciones += $resultado['total'];
    }
    ?>
    <li style="margin-bottom: 15px; display: flex; align-items: center;">
        <!-- Imagen del artista -->
        <img src="../<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($fila['nombre']); ?>" width="60" height="60" style="object-fit: cover; margin-right: 10px; border-radius: 5px;">
        <div>
            <!-- Nombre y datos del artista -->
            <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong><br>
            (<?php echo $num_albumes; ?> álbum/es, <?php echo $num_canciones; ?> canción/es)
            <br>
            <!-- Enlace para eliminar artista con confirmación -->
            <a href="?seccion=crear_artista&eliminar=<?php echo $fila['id_artista']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este artista y todo su contenido?')">❌ Eliminar</a>
        </div>
    </li>
<?php endwhile; ?>
</ul>
