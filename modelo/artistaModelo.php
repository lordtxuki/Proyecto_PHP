<?php
require_once '../controlador/conexion.php';

class ArtistaModelo {
    // Método para obtener todos los artistas
    public static function obtenerTodos() {
        global $conexion; // Usamos la conexión global a la base de datos
        $res = $conexion->query("SELECT * FROM Artistas"); // Consulta para seleccionar todos los artistas
        return $res->fetch_all(MYSQLI_ASSOC); // Devuelve todos los resultados en formato array asociativo
    }

    // Método para que un usuario siga a un artista
    public static function seguir($id_usuario, $id_artista) {
        global $conexion; // Usamos la conexión global a la base de datos
        // Preparamos la consulta para insertar el seguimiento, usando INSERT IGNORE para evitar duplicados
        $stmt = $conexion->prepare("INSERT IGNORE INTO artistas_seguidos (id_usuario, id_artista) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_artista); // Vinculamos parámetros (dos enteros)
        $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para que un usuario deje de seguir a un artista
    public static function dejarSeguir($id_usuario, $id_artista) {
        global $conexion; // Usamos la conexión global a la base de datos
        // Preparamos la consulta para eliminar el seguimiento
        $stmt = $conexion->prepare("DELETE FROM artistas_seguidos WHERE id_usuario = ? AND id_artista = ?");
        $stmt->bind_param("ii", $id_usuario, $id_artista); // Vinculamos parámetros (dos enteros)
        $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para comprobar si un usuario sigue a un artista
    public static function esSeguido($id_usuario, $id_artista) {
        global $conexion; // Usamos la conexión global a la base de datos
        // Preparamos la consulta para verificar existencia del seguimiento
        $stmt = $conexion->prepare("SELECT 1 FROM artistas_seguidos WHERE id_usuario = ? AND id_artista = ?");
        $stmt->bind_param("ii", $id_usuario, $id_artista); // Vinculamos parámetros (dos enteros)
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->get_result()->num_rows > 0; // Retornamos true si existe al menos un resultado, false si no
    }
}
