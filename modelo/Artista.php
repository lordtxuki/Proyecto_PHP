<?php
require_once 'ModeloBase.php';

class Artista extends ModeloBase {
    public function agregar($nombre, $imagen = 'uploads/default_artist.jpg') {
        $stmt = $this->conexion->prepare("INSERT INTO Artistas (nombre, imagen) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $imagen);
        return $stmt->execute();
    }

    public function obtenerTodos() {
        return $this->conexion->query("SELECT * FROM Artistas");
    }
}
?>
