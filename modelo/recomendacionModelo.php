<?php
require_once '../controlador/conexion.php';

class RecomendacionModelo {
    public static function obtenerRecomendaciones($id_usuario) {
        global $conexion;

        // 1) Obtener artistas seguidos
        $sql = "SELECT id_artista FROM artistas_seguidos WHERE id_usuario = $id_usuario";
        $res = $conexion->query($sql);
        $artistas = [];
        while ($fila = $res->fetch_assoc()) {
            $artistas[] = $fila['id_artista'];
        }
        if (empty($artistas)) return [];

        $ids = implode(',', $artistas);
        $sql2 = "
            SELECT a.*, ar.nombre AS artista, a.imagen_portada
            FROM Albumes a
            JOIN Artistas ar ON a.id_artista = ar.id_artista
            WHERE a.id_artista IN ($ids)
            ORDER BY a.año_publicacion DESC
            LIMIT 10
        ";
        $res2 = $conexion->query($sql2);
        return $res2->fetch_all(MYSQLI_ASSOC);
    }
}