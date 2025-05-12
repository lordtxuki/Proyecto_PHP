<?php
require_once 'ModeloBase.php';

class ArtistaModelo {
    public static function obtenerTodos() {
        global $conexion;
        $sql = "SELECT * FROM Artistas";
        $res = $conexion->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function seguir($id_usuario, $id_artista) {
        global $conexion;
        $stmt = $conexion->prepare("INSERT IGNORE INTO artistas_seguidos (id_usuario, id_artista) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_artista);
        $stmt->execute();
    }

    public static function dejarSeguir($id_usuario, $id_artista) {
        global $conexion;
        $stmt = $conexion->prepare("DELETE FROM artistas_seguidos WHERE id_usuario = ? AND id_artista = ?");
        $stmt->bind_param("ii", $id_usuario, $id_artista);
        $stmt->execute();
    }

    public static function esSeguido($id_usuario, $id_artista) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT * FROM artistas_seguidos WHERE id_usuario = ? AND id_artista = ?");
        $stmt->bind_param("ii", $id_usuario, $id_artista);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }
}
