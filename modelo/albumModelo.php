<?php
require_once '../controlador/conexion.php';

class AlbumModelo {
    public static function obtenerTodos() {
        global $conexion;
        $res = $conexion->query("SELECT a.*, ar.nombre AS artista, a.imagen_portada FROM Albumes a LEFT JOIN Artistas ar ON a.id_artista = ar.id_artista ORDER BY a.año_publicacion DESC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function obtenerCanciones($id_album) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT * FROM Canciones WHERE id_album = ?");
        $stmt->bind_param("i", $id_album);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function esPremium($id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}