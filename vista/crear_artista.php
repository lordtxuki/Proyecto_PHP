<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../controlador/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    die("Acceso no autorizado.");
}

$id_usuario_logueado = $_SESSION['usuario_id'];


/* ============================
    1. PROCESO DE ELIMINAR ARTISTA
============================ */
if (isset($_GET['eliminar'])) {

    $id_artista = intval($_GET['eliminar']);

    // Verificar que el artista pertenece al usuario logueado
    $stmt_verificar = $conexion->prepare("SELECT id_usuario, imagen FROM Artistas WHERE id_artista = ?");
    $stmt_verificar->bind_param("i", $id_artista);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows === 0) {
        die("Artista no encontrado.");
    }

    $artista = $resultado_verificar->fetch_assoc();

    if ($artista['id_usuario'] != $id_usuario_logueado) {
        die("No tienes permisos para eliminar este artista.");
    }

    // Gracias al ON DELETE CASCADE, no necesitamos borrar manualmente álbumes y canciones
    // Solo eliminamos el artista

    // Borrar imagen física
    $ruta_imagen = '../' . $artista['imagen'];
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
    }

    $stmt_borrar = $conexion->prepare("DELETE FROM Artistas WHERE id_artista = ?");
    $stmt_borrar->bind_param("i", $id_artista);
    $stmt_borrar->execute();

    header("Location: ?seccion=crear_artista&eliminado=1");
    exit;
}

/* ============================
    2. PROCESO DE CREAR ARTISTA
============================ */
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

                $ruta_relativa = 'imagenes/artistas/' . $nombre_archivo;

                $stmt = $conexion->prepare("INSERT INTO Artistas (nombre, imagen, id_usuario) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $nombre, $ruta_relativa, $id_usuario_logueado);
                $stmt->execute();

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

/* ============================
    3. LISTAR SOLO ARTISTAS DEL USUARIO
============================ */

$stmt_lista = $conexion->prepare("SELECT id_artista, nombre, imagen FROM Artistas WHERE id_usuario = ? ORDER BY nombre ASC");
$stmt_lista->bind_param("i", $id_usuario_logueado);
$stmt_lista->execute();
$res = $stmt_lista->get_result();
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

<h2>Mis artistas</h2>
<ul style="list-style: none; padding: 0;">
<?php while ($fila = $res->fetch_assoc()): ?>

    <?php
    $stmt_albumes = $conexion->prepare("SELECT id_album FROM Albumes WHERE id_artista = ?");
    $stmt_albumes->bind_param("i", $fila['id_artista']);
    $stmt_albumes->execute();
    $res_albumes = $stmt_albumes->get_result();
    $num_albumes = $res_albumes->num_rows;

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
        <img src="../<?php echo htmlspecialchars($fila['imagen']); ?>" width="60" height="60" style="object-fit: cover; margin-right: 10px; border-radius: 5px;">
        <div>
            <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong><br>
            (<?php echo $num_albumes; ?> álbum/es, <?php echo $num_canciones; ?> canción/es)
            <br>
            <a href="?seccion=crear_artista&eliminar=<?php echo $fila['id_artista']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este artista y todo su contenido?')">❌ Eliminar</a>
        </div>
    </li>

<?php endwhile; ?>
</ul>
