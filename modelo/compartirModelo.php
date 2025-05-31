<?php
require_once '../controlador/conexion.php';

class CompartirModelo {
    // Método para compartir una playlist con otro usuario
    public static function compartir($id_playlist, $usuario_destino) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Insertar registro en la tabla playlist_compartida con la playlist y el usuario destino
        $stmt = $conexion->prepare("INSERT INTO playlist_compartida (id_playlist, id_usuario_destino) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_playlist, $usuario_destino); // Vinculamos parámetros (dos enteros)
        $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para agregar una canción a una playlist compartida por un usuario
    public static function agregarCancion($id_playlist, $id_cancion, $id_usuario) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Insertar registro en playlist_canciones con playlist, canción, usuario que agrega y fecha actual
        $stmt = $conexion->prepare("INSERT INTO playlist_canciones (id_playlist, id_cancion, id_usuario_que_agrega, fecha_agregado) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $id_playlist, $id_cancion, $id_usuario); // Vinculamos parámetros (tres enteros)
        $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para obtener todas las playlists que han sido compartidas con un usuario dado
    public static function obtenerCompartidas($id_usuario) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Preparamos consulta para obtener playlists que están compartidas con el usuario destino
        $stmt = $conexion->prepare("SELECT p.* FROM Playlist p JOIN playlist_compartida pc ON p.id_playlist = pc.id_playlist WHERE pc.id_usuario_destino = ?");
        $stmt->bind_param("i", $id_usuario); // Vinculamos parámetro (entero)
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Retornamos resultados como array asociativo
    }
}
