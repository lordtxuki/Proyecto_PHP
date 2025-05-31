<?php
require_once '../controlador/conexion.php';

class PlaylistModelo {
    public static function crear($id_usuario, $titulo) {
        global $conexion;
        // Preparar la consulta para insertar una nueva playlist con estado 'activa'
        $stmt = $conexion->prepare("INSERT INTO Playlist (id_usuario, titulo, estado) VALUES (?, ?, 'activa')");
        // Asociar los parámetros id_usuario (int) y titulo (string)
        $stmt->bind_param("is", $id_usuario, $titulo);
        // Ejecutar la consulta
        $stmt->execute();
    }

    public static function eliminar($id_playlist) {
        global $conexion;
        // Preparar la consulta para marcar la playlist como 'eliminada' y registrar fecha de eliminación
        $stmt = $conexion->prepare("UPDATE Playlist SET estado='eliminada', fecha_eliminacion=NOW() WHERE id_playlist=?");
        // Asociar el parámetro id_playlist (int)
        $stmt->bind_param("i", $id_playlist);
        // Ejecutar la consulta
        $stmt->execute();
    }

    public static function recuperar($id_playlist) {
        global $conexion;
        // Preparar la consulta para restaurar la playlist estableciendo estado 'activa' y borrando fecha eliminación
        $stmt = $conexion->prepare("UPDATE Playlist SET estado='activa', fecha_eliminacion=NULL WHERE id_playlist=?");
        // Asociar el parámetro id_playlist (int)
        $stmt->bind_param("i", $id_playlist);
        // Ejecutar la consulta
        $stmt->execute();
    }

    public static function obtenerTodas($id_usuario) {
        global $conexion;
        // Preparar consulta para obtener todas las playlists de un usuario específico
        $stmt = $conexion->prepare("SELECT * FROM Playlist WHERE id_usuario = ?");
        // Asociar el parámetro id_usuario (int)
        $stmt->bind_param("i", $id_usuario);
        // Ejecutar la consulta
        $stmt->execute();
        // Devolver todas las filas como array asociativo
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function borrar($id_playlist){
        global $conexion;
        // Preparar consulta para eliminar permanentemente una playlist de la base de datos
        $stmt = $conexion->prepare("DELETE FROM Playlist WHERE id_playlist =?");
        // Asociar parámetro id_playlist (int)
        $stmt->bind_param("i",$id_playlist);
        // Ejecutar la consulta
        $stmt->execute();
    }
}
