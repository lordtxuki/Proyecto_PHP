<?php
require_once '../controlador/conexion.php';

class CompartirModelo {
    public static function compartir($id_playlist, $usuario_destino) {
        global $conexion;
        $stmt = $conexion->prepare("INSERT INTO playlist_compartida (id_playlist, id_usuario_destino) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_playlist, $usuario_destino);
        $stmt->execute();
    }

    public static function agregarCancion($id_playlist, $id_cancion, $id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("INSERT INTO playlist_canciones (id_playlist, id_cancion, id_usuario_que_agrega, fecha_agregado) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $id_playlist, $id_cancion, $id_usuario);
        $stmt->execute();
    }

    public static function obtenerCompartidas($id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT p.* FROM Playlist p JOIN playlist_compartida pc ON p.id_playlist = pc.id_playlist WHERE pc.id_usuario_destino = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}