<?php
require_once '../controlador/conexion.php';

class RecomendacionModelo {

    public static function obtenerRecomendaciones($id_usuario) {
        global $conexion;

        //  Obtener artistas seguidos
        $stmt = $conexion->prepare("
            SELECT id_artista 
            FROM artistas_seguidos 
            WHERE id_usuario = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $artistas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $artistas[] = $fila['id_artista'];
        }

        if (empty($artistas)) {
            return [];
        }

        $recomendaciones = [];

        //  Obtener hasta 3 álbumes por cada artista seguido
        foreach ($artistas as $id_artista) {

            $stmt2 = $conexion->prepare("
                SELECT a.*, ar.nombre AS artista
                FROM Albumes a
                JOIN Artistas ar ON a.id_artista = ar.id_artista
                WHERE a.id_artista = ?
                ORDER BY a.año_publicacion DESC
                LIMIT 3
            ");

            $stmt2->bind_param("i", $id_artista);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            while ($fila2 = $res2->fetch_assoc()) {
                $recomendaciones[] = $fila2;
            }
        }

        // Mezclar resultados para que no salgan agrupados
        shuffle($recomendaciones);

        //  Limitar a máximo 10 recomendaciones
        return array_slice($recomendaciones, 0, 10);
    }
}
