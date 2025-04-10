<?php
require_once 'ModeloBase.php';

class Cancion extends ModeloBase {
    public function agregar($id_album, $titulo, $duracion) {
        $stmt = $this->conexion->prepare("INSERT INTO Canciones (id_album, titulo, duracion) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_album, $titulo, $duracion);
        return $stmt->execute();
    }

    public function listarPorAlbum($id_album) {
        $stmt = $this->conexion->prepare("SELECT * FROM Canciones WHERE id_album = ?");
        $stmt->bind_param("i", $id_album);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
