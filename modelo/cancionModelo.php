<?php
require_once '../controlador/conexion.php';

class CancionModelo {
    // Método para registrar la reproducción de una canción por un usuario
    public static function registrarReproduccion($id_usuario, $id_cancion) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Insertar en la tabla usuario_canciones el registro de reproducción
        $stmt = $conexion->prepare("INSERT INTO usuario_canciones (id_usuario, id_cancion) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_cancion); // Vinculamos parámetros (dos enteros)
        $stmt->execute(); // Ejecutamos la consulta

        // Actualizar la cantidad de veces que se ha reproducido la canción
        $stmt = $conexion->prepare("UPDATE Canciones SET veces_reproducida = veces_reproducida + 1 WHERE id_cancion = ?");
        $stmt->bind_param("i", $id_cancion); // Vinculamos parámetro (entero)
        $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para obtener información de una canción específica por su ID
    public static function obtener($id_cancion) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Preparamos la consulta para obtener los datos de la canción
        $stmt = $conexion->prepare("SELECT id_cancion, titulo, duracion, ruta FROM Canciones WHERE id_cancion = ?");
        $stmt->bind_param("i", $id_cancion); // Vinculamos parámetro (entero)
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->get_result()->fetch_assoc(); // Retornamos el resultado como array asociativo
    }

    // Método para obtener todas las canciones disponibles
    public static function obtenerTodas() {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Ejecutamos consulta para obtener todas las canciones con sus campos básicos
        $res = $conexion->query("SELECT id_cancion, titulo, duracion, ruta FROM Canciones");
        return $res->fetch_all(MYSQLI_ASSOC); // Retornamos todos los resultados como array asociativo
    }

    // Método para obtener todas las canciones de un artista específico
    public static function obtenerPorArtista($id_artista) {
        global $conexion; // Usamos la conexión global a la base de datos
        
        // Preparamos consulta para seleccionar canciones que pertenezcan a álbumes del artista dado
        $stmt = $conexion->prepare(
            "SELECT c.* FROM Canciones c 
            JOIN Albumes a ON c.id_album = a.id_album
            WHERE a.id_artista = ?"
        );
        $stmt->bind_param("i", $id_artista); // Vinculamos parámetro (entero)
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Retornamos los resultados como array asociativo
    }
}
