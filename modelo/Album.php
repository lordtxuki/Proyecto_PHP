<?php
require_once 'ModeloBase.php';

class Album extends ModeloBase {
    public function agregar($id_artista, $titulo, $anio_publicacion, $imagen = 'uploads/default_album.jpg') {
        $stmt = $this->conexion->prepare("INSERT INTO Albumes (id_artista, titulo, año_publicacion, imagen_portada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_artista, $titulo, $anio_publicacion, $imagen);
        return $stmt->execute();
    }

    public function listarPorArtista($id_artista) {
        $stmt = $this->conexion->prepare("SELECT * FROM Albumes WHERE id_artista = ?");
        $stmt->bind_param("i", $id_artista);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
