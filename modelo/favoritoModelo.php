<?php
require_once '../controlador/conexion.php';

class FavoritoModelo {
    // Agrega un favorito según el tipo: canción, álbum, artista o playlist
    public static function agregar($id_usuario, $id, $tipo) {
        global $conexion;

        // Mapeo seguro de tipo → tabla y campo
        $mapa = [
            'cancion' => ['tabla' => 'canciones_favoritas', 'campo' => 'id_cancion'],
            'album'   => ['tabla' => 'albumes_favoritos',   'campo' => 'id_album'],
            'artista' => ['tabla' => 'artistas_favoritos',  'campo' => 'id_artista'],
            'playlist'=> ['tabla' => 'playlists_favoritas','campo' => 'id_playlist'],
        ];
        // Si $tipo no es una clave válida, salimos sin hacer nada
        if (!isset($mapa[$tipo])) {
            return;
        }

        $tabla = $mapa[$tipo]['tabla'];
        $campo = $mapa[$tipo]['campo'];

        // Comprobar si ya existe para no duplicar
        $check = $conexion->prepare("SELECT 1 FROM $tabla WHERE id_usuario = ? AND $campo = ?");
        $check->bind_param("ii", $id_usuario, $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
            // Insertar el favorito si no existe
            $stmt = $conexion->prepare("INSERT INTO $tabla (id_usuario, $campo) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id);
            $stmt->execute();
        }
    }

    // Quitar favorito según el tipo
    public static function quitar($id_usuario, $id, $tipo) {
        global $conexion;

        if ($tipo == 'cancion') {
            $stmt = $conexion->prepare("DELETE FROM canciones_favoritas WHERE id_usuario = ? AND id_cancion = ?");
        } elseif ($tipo == 'album') {
            $stmt = $conexion->prepare("DELETE FROM albumes_favoritos WHERE id_usuario = ? AND id_album = ?");
        } elseif ($tipo == 'artista') {
            $stmt = $conexion->prepare("DELETE FROM artistas_favoritos WHERE id_usuario = ? AND id_artista = ?");
        } elseif ($tipo == 'playlist') {
            $stmt = $conexion->prepare("DELETE FROM playlists_favoritas WHERE id_usuario = ? AND id_playlist = ?");
        } else {
            return; // tipo no válido, salimos
        }

        $stmt->bind_param("ii", $id_usuario, $id);
        $stmt->execute();
    }

    // Obtener todos los favoritos del usuario, agrupados por tipo
    public static function obtener($id_usuario) {
        global $conexion;
        $res = [
            'canciones' => [],
            'albumes'   => [],
            'artistas'  => [],
            'playlists' => []
        ];
        $res['canciones'] = $conexion
            ->query("SELECT * 
                    FROM canciones_favoritas cf 
                    JOIN Canciones c ON cf.id_cancion = c.id_cancion 
                    WHERE cf.id_usuario = $id_usuario")
            ->fetch_all(MYSQLI_ASSOC);
        $res['albumes'] = $conexion
            ->query("SELECT * 
                    FROM albumes_favoritos af 
                    JOIN Albumes a ON af.id_album = a.id_album 
                    WHERE af.id_usuario = $id_usuario")
            ->fetch_all(MYSQLI_ASSOC);
        $res['artistas'] = $conexion
            ->query("SELECT * 
                    FROM artistas_favoritos af 
                    JOIN Artistas a ON af.id_artista = a.id_artista 
                    WHERE af.id_usuario = $id_usuario")
            ->fetch_all(MYSQLI_ASSOC);
        $res['playlists'] = $conexion
            ->query("SELECT * 
                    FROM playlists_favoritas pf 
                    JOIN Playlist p ON pf.id_playlist = p.id_playlist 
                    WHERE pf.id_usuario = $id_usuario")
            ->fetch_all(MYSQLI_ASSOC);
        return $res;
    }

    // Comprobar si un artista está marcado como favorito por un usuario
    public static function esFavorito($id_usuario, $id_artista, $tipo) {
        global $conexion;

        if ($tipo === 'artista') {
            $stmt = $conexion->prepare("SELECT 1 FROM artistas_favoritos WHERE id_usuario = ? AND id_artista = ?");
            $stmt->bind_param("ii", $id_usuario, $id_artista);
            $stmt->execute();
            return $stmt->get_result()->num_rows > 0;
        }

        return false;
    }

    // Obtener todos los artistas favoritos de un usuario
    public static function obtenerArtistasFavoritos($id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("
            SELECT a.id_artista, a.nombre, a.imagen
            FROM artistas_favoritos af
            INNER JOIN Artistas a ON af.id_artista = a.id_artista
            WHERE af.id_usuario = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
