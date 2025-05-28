<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

// 1. Verificar que hay usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/vista_login.php");
    exit;
}
$id_usuario = $_SESSION['usuario_id'];

// 2. Verificar en la base de datos si es premium
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    // No es premium: lo enviamos a su panel normal
    header("Location: ../vista/normal.php");
    exit;
}
$stmt->close();

// 3. Si es premium, procesamos creación/eliminación de artistas
if (isset($_GET['eliminar'])) {
    $id_artista = intval($_GET['eliminar']);

    // Borrar canciones de todos los álbumes del artista
    $stmt1 = $conexion->prepare("
        DELETE c 
        FROM Canciones c 
        JOIN Albumes a ON c.id_album = a.id_album 
        WHERE a.id_artista = ?
    ");
    $stmt1->bind_param("i", $id_artista);
    $stmt1->execute();
    $stmt1->close();

    // Borrar álbumes del artista
    $stmt2 = $conexion->prepare("DELETE FROM albumes WHERE id_artista = ?");
    $stmt2->bind_param("i", $id_artista);
    $stmt2->execute();
    $stmt2->close();

    // Borrar imagen del artista si existe
    $stmt3 = $conexion->prepare("SELECT imagen FROM artistas WHERE id_artista = ?");
    $stmt3->bind_param("i", $id_artista);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    if ($fila = $res3->fetch_assoc()) {
        $ruta = '../' . $fila['imagen'];
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }
    $stmt3->close();

    // Borrar el artista
    $stmt4 = $conexion->prepare("DELETE FROM artistas WHERE id_artista = ?");
    $stmt4->bind_param("i", $id_artista);
    $stmt4->execute();
    $stmt4->close();

    echo "<p style='color:green;'>Artista y todo su contenido eliminado correctamente.</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tipos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imagen']['type'], $tipos)) {
            echo "<p style='color:red;'>Formato no permitido. JPG, PNG o GIF.</p>";
        } else {
            $uploads = '../imagenes/artistas/';
            if (!is_dir($uploads)) {
                mkdir($uploads, 0755, true);
            }
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $file = uniqid('artista_') . '.' . $ext;
            $dest = $uploads . $file;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                $ruta_rel = 'imagenes/artistas/' . $file;
                $stmt = $conexion->prepare("INSERT INTO artistas (nombre, imagen) VALUES (?, ?)");
                $stmt->bind_param("ss", $nombre, $ruta_rel);
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>Artista creado correctamente.</p>";
                } else {
                    echo "<p style='color:red;'>Error al guardar en BD: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color:red;'>Error al subir la imagen.</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Debes completar el nombre y subir una imagen válida.</p>";
    }
}

// Listado de artistas
$res = $conexion->query("SELECT id_artista, nombre, imagen FROM rtistas ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear / Eliminar Artista</title>
    <link rel="stylesheet" href="../styles/premium.css">
</head>
<body>
    <h2>Crear nuevo artista</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" id="nombre" required><br><br>
        <label for="imagen">Imagen:</label><br>
        <input type="file" name="imagen" id="imagen" accept="image/*" required><br><br>
        <button type="submit">Crear</button>
    </form>

    <hr>

    <h2>Artistas existentes</h2>
    <ul>
    <?php while ($fila = $res->fetch_assoc()): ?>
        <?php
            // Contar álbumes
            $stmtA = $conexion->prepare("SELECT COUNT(*) FROM Albumes WHERE id_artista = ?");
            $stmtA->bind_param("i", $fila['id_artista']);
            $stmtA->execute();
            $num_albumes = $stmtA->get_result()->fetch_row()[0];
            $stmtA->close();

            // Contar canciones
            $stmtC = $conexion->prepare("
                SELECT COUNT(*) 
                FROM Canciones c 
                JOIN Albumes a ON c.id_album = a.id_album 
                WHERE a.id_artista = ?
            ");
            $stmtC->bind_param("i", $fila['id_artista']);
            $stmtC->execute();
            $num_canciones = $stmtC->get_result()->fetch_row()[0];
            $stmtC->close();
        ?>
        <li>
            <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="" style="width:50px; vertical-align:middle; margin-right:10px;">
            <?php echo htmlspecialchars($fila['nombre']); ?>
            (<?php echo "$num_albumes álbum/es, $num_canciones canción/es"; ?>)
            <a href="?eliminar=<?php echo $fila['id_artista']; ?>" onclick="return confirm('¿Eliminar este artista y todo su contenido?')">❌</a>
        </li>
    <?php endwhile; ?>
    </ul>
</body>
</html>
