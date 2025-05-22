<?php
require_once '../controlador/conexion.php';

if (isset($_GET['eliminar'])) {
    $id_artista = intval($_GET['eliminar']);

    // 1. Obtener álbumes del artista
    $stmt_albumes = $conexion->prepare("SELECT id_album FROM Albumes WHERE id_artista = ?");
    $stmt_albumes->bind_param("i", $id_artista);
    $stmt_albumes->execute();
    $res_albumes = $stmt_albumes->get_result();

    // 2. Borrar canciones de cada álbum
    while ($album = $res_albumes->fetch_assoc()) {
        $id_album = $album['id_album'];
        $stmt_canciones = $conexion->prepare("DELETE FROM Canciones WHERE id_album = ?");
        $stmt_canciones->bind_param("i", $id_album);
        $stmt_canciones->execute();
    }

    // 3. Borrar álbumes del artista
    $stmt_borrar_albumes = $conexion->prepare("DELETE FROM Albumes WHERE id_artista = ?");
    $stmt_borrar_albumes->bind_param("i", $id_artista);
    $stmt_borrar_albumes->execute();

    // 4. Borrar artista y eliminar su imagen
    $stmt_imagen = $conexion->prepare("SELECT imagen FROM Artistas WHERE id_artista = ?");
    $stmt_imagen->bind_param("i", $id_artista);
    $stmt_imagen->execute();
    $res_imagen = $stmt_imagen->get_result();
    if ($fila_imagen = $res_imagen->fetch_assoc()) {
        $ruta_imagen = '../' . $fila_imagen['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

    $stmt_borrar_artista = $conexion->prepare("DELETE FROM Artistas WHERE id_artista = ?");
    $stmt_borrar_artista->bind_param("i", $id_artista);
    $stmt_borrar_artista->execute();

    echo "<p style='color:green;'>Artista y todo su contenido eliminado correctamente.</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imagen['type'], $allowed_types)) {
            echo "<p style='color:red;'>Formato de imagen no permitido. Usa JPG, PNG o GIF.</p>";
        } else {
            $upload_dir = '../imagenes/artistas/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('artista_') . '.' . $ext;
            $ruta_destino = $upload_dir . $nombre_archivo;

            if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                $stmt = $conexion->prepare("INSERT INTO Artistas (nombre, imagen) VALUES (?, ?)");
                $ruta_relativa = 'imagenes/artistas/' . $nombre_archivo;
                $stmt->bind_param("ss", $nombre, $ruta_relativa);
                $stmt->execute();

                echo "<p style='color:green;'>Artista creado correctamente con imagen.</p>";
            } else {
                echo "<p style='color:red;'>Error al subir la imagen.</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Debe completar el nombre y subir una imagen válida.</p>";
    }
}

// Mostrar artistas
$res = $conexion->query("SELECT id_artista, nombre, imagen FROM Artistas ORDER BY nombre ASC");
?>

<h2>Crear nuevo artista</h2>
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
<ul>
<?php while ($fila = $res->fetch_assoc()): ?>
    <?php
    // Contar álbumes del artista
    $stmt_albumes = $conexion->prepare("SELECT id_album FROM Albumes WHERE id_artista = ?");
    $stmt_albumes->bind_param("i", $fila['id_artista']);
    $stmt_albumes->execute();
    $res_albumes = $stmt_albumes->get_result();
    $num_albumes = $res_albumes->num_rows;

    // Contar canciones en esos álbumes
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
    <li>
        <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($fila['nombre']); ?>" style="width:50px; height:auto; vertical-align: middle; margin-right: 10px;">
        <?php echo htmlspecialchars($fila['nombre']); ?>
        (<?php echo $num_albumes; ?> álbum/es, <?php echo $num_canciones; ?> canción/es)
        <a href="?seccion=crear_artista&eliminar=<?php echo $fila['id_artista']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este artista y todo su contenido?')">❌</a>
    </li>
<?php endwhile; ?>
</ul>
