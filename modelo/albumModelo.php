<?php
// Incluir el archivo que contiene la conexión a la base de datos
require_once '../controlador/conexion.php';

class AlbumModelo {
    // Método estático para obtener todos los álbumes con su artista y portada
    public static function obtenerTodos() {
        // Usar la conexión global a la base de datos
        global $conexion;

        // Realizar una consulta para obtener todos los álbumes
        // Se hace un LEFT JOIN con la tabla Artistas para obtener el nombre del artista
        // Ordenar los resultados por año de publicación descendente
        $res = $conexion->query("SELECT a.*, ar.nombre AS artista, a.imagen_portada FROM Albumes a LEFT JOIN Artistas ar ON a.id_artista = ar.id_artista ORDER BY a.año_publicacion DESC");

        // Devolver todos los resultados como un array asociativo
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // Método estático para obtener todas las canciones de un álbum dado
    public static function obtenerCanciones($id_album) {
        // Usar la conexión global a la base de datos
        global $conexion;

        // Preparar la consulta para seleccionar las canciones del álbum
        $stmt = $conexion->prepare("SELECT * FROM Canciones WHERE id_album = ?");
        // Vincular el parámetro id_album (entero)
        $stmt->bind_param("i", $id_album);
        // Ejecutar la consulta
        $stmt->execute();

        // Devolver todas las canciones encontradas como array asociativo
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Método estático para comprobar si un usuario es premium
    public static function esPremium($id_usuario) {
        // Usar la conexión global a la base de datos
        global $conexion;

        // Preparar consulta para verificar si existe una fila en usuario_premium con ese id de usuario
        $stmt = $conexion->prepare("SELECT 1 FROM usuario_premium WHERE id_usuario = ?");
        // Vincular el parámetro id_usuario (entero)
        $stmt->bind_param("i", $id_usuario);
        // Ejecutar la consulta
        $stmt->execute();

        // Comprobar si se encontró al menos una fila, devolviendo true o false
        return $stmt->get_result()->num_rows > 0;
    }
}
