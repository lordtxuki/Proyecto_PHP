<?php
// Incluye el archivo donde se hace la conexión a la base de datos
require_once '../controlador/conexion.php';

// Creamos una clase llamada RecomendacionModelo
class RecomendacionModelo {

    // Esta función va a obtener las recomendaciones para un usuario en concreto
    public static function obtenerRecomendaciones($id_usuario) {
        // Usamos la variable de conexión a la base de datos que ya estaba creada
        global $conexion;

        // 1) Buscamos los artistas que sigue el usuario
        $sql = "SELECT id_artista FROM artistas_seguidos WHERE id_usuario = $id_usuario";
        $res = $conexion->query($sql); // Ejecutamos la consulta

        // Guardamos los IDs de los artistas en un array
        $artistas = [];
        while ($fila = $res->fetch_assoc()) {
            $artistas[] = $fila['id_artista']; // Añadimos cada ID al array
        }

        // Si el usuario no sigue a ningún artista, no recomendamos nada
        if (empty($artistas)) return [];

        // Convertimos el array de IDs en una lista separada por comas (ej: 2,4,5)
        $ids = implode(',', $artistas);

        // 2) Buscamos los álbumes de esos artistas
        $sql2 = "
            SELECT a.*, ar.nombre AS artista, a.imagen_portada
            FROM Albumes a
            JOIN Artistas ar ON a.id_artista = ar.id_artista
            WHERE a.id_artista IN ($ids)
            ORDER BY a.año_publicacion DESC
            LIMIT 10
        ";

        // Ejecutamos la segunda consulta
        $res2 = $conexion->query($sql2);

        // Devolvemos todos los resultados como un array asociativo
        return $res2->fetch_all(MYSQLI_ASSOC);
    }
}
