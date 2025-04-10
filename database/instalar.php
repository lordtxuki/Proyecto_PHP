<?php
$servername = "localhost"; 
$username = "root";        
$password = "";           
$dbname = "streaming"; 

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos creada correctamente.<br>";
} else {
    die("Error creando base de datos: " . $conn->error);
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL, 
    contrasena VARCHAR(255) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL, 
    fecha_nac DATE,
    genero ENUM('M', 'F', 'Otro'),
    pais VARCHAR(50),
    codigo_postal VARCHAR(10),
    imagen_perfil VARCHAR(255) NOT NULL DEFAULT 'uploads/default.jpg'
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS usuario_premium (
    id_usuario_premium INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNIQUE,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Suscripciones (
    id_suscripcion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_premium INT,
    fecha_inicio DATE NOT NULL,
    fecha_renovacion DATE,
    estado ENUM('activa', 'cancelada') NOT NULL,
    FOREIGN KEY (id_usuario_premium) REFERENCES usuario_premium(id_usuario_premium) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_suscripcion INT,
    fecha_pago DATE NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    forma_pago ENUM('tarjeta', 'paypal') NOT NULL,
    num_tarjeta VARCHAR(16),
    caducidad VARCHAR(5),
    codigo_seguridad VARCHAR(4),
    usuario_paypal VARCHAR(100),
    FOREIGN KEY (id_suscripcion) REFERENCES Suscripciones(id_suscripcion) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Artistas (
    id_artista INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT 'uploads/default_artist.jpg'
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Albumes (
    id_album INT AUTO_INCREMENT PRIMARY KEY,
    id_artista INT,
    titulo VARCHAR(100) NOT NULL,
    año_publicacion YEAR NOT NULL,
    imagen_portada VARCHAR(255) NOT NULL DEFAULT 'uploads/default_album.jpg',
    FOREIGN KEY (id_artista) REFERENCES Artistas(id_artista) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Canciones (
    id_cancion INT AUTO_INCREMENT PRIMARY KEY,
    id_album INT,
    titulo VARCHAR(100) NOT NULL,
    duracion TIME NOT NULL,
    veces_reproducida INT DEFAULT 0,
    FOREIGN KEY (id_album) REFERENCES Albumes(id_album) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Playlist (
    id_playlist INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    titulo VARCHAR(100) NOT NULL,
    num_canciones INT DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activa', 'eliminada') NOT NULL,
    fecha_eliminacion DATETIME NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS canciones_favoritas (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_cancion INT,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cancion) REFERENCES Canciones(id_cancion) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS albumes_favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_album INT,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_album) REFERENCES Albumes(id_album) ON DELETE CASCADE
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS usuario_canciones (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_cancion INT,
    fecha_reproduccion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cancion) REFERENCES Canciones(id_cancion) ON DELETE CASCADE
)";

$conn->query($sql);

echo "Tablas creadas correctamente.";

$conn->close();
?>