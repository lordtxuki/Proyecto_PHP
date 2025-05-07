<?php
require_once '../controlador/conexion.php';

class PlaylistModelo {
    public static function crear($id_usuario, $titulo) {
        global $conexion;
        $stmt = $conexion->prepare("INSERT INTO Playlist (id_usuario, titulo, estado) VALUES (?, ?, 'activa')");
        $stmt->bind_param("is", $id_usuario, $titulo);
        $stmt->execute();
    }

    public static function eliminar($id_playlist) {
        global $conexion;
        $stmt = $conexion->prepare("UPDATE Playlist SET estado='eliminada', fecha_eliminacion=NOW() WHERE id_playlist=?");
        $stmt->bind_param("i", $id_playlist);
        $stmt->execute();
    }

    public static function recuperar($id_playlist) {
        global $conexion;
        $stmt = $conexion->prepare("UPDATE Playlist SET estado='activa', fecha_eliminacion=NULL WHERE id_playlist=?");
        $stmt->bind_param("i", $id_playlist);
        $stmt->execute();
    }

    public static function obtenerTodas($id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT * FROM Playlist WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function borrar($id_playlist){
        global $conexion;
        $stmt = $conexion->prepare("DELETE FROM Playlist WHERE id_playlist =?");
        $stmt->bind_param("i",$id_playlist);
        $stmt->execute();
    }
}