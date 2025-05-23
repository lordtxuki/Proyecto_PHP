<?php
require_once '../controlador/conexion.php';

class FavoritoModelo {
    public static function agregar($id_usuario, $id, $tipo) {
        global $conexion;

        $tabla = "";
        $campo = "";

        if ($tipo == 'cancion') {
            $tabla = "canciones_favoritas";
            $campo = "id_cancion";
        } elseif ($tipo == 'album') {
            $tabla = "albumes_favoritos";
            $campo = "id_album";
        } elseif ($tipo == 'artista') {
            $tabla = "artistas_favoritos";
            $campo = "id_artista";
        } elseif ($tipo == 'playlist') {
            $tabla = "playlists_favoritas";
            $campo = "id_playlist";
        }

        $check = $conexion->prepare("SELECT * FROM $tabla WHERE id_usuario = ? AND $campo = ?");
        $check->bind_param("ii", $id_usuario, $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conexion->prepare("INSERT INTO $tabla (id_usuario, $campo) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id);
            $stmt->execute();
        }
    }

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
        }
        $stmt->bind_param("ii", $id_usuario, $id);
        $stmt->execute();
    }

    public static function obtener($id_usuario) {
        global $conexion;
        $res = [
            'canciones' => [],
            'albumes' => [],
            'artistas' => [],
            'playlists' => []
        ];
        $res['canciones'] = $conexion->query("SELECT * FROM canciones_favoritas cf JOIN Canciones c ON cf.id_cancion = c.id_cancion WHERE cf.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        $res['albumes'] = $conexion->query("SELECT * FROM albumes_favoritos af JOIN Albumes a ON af.id_album = a.id_album WHERE af.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        $res['artistas'] = $conexion->query("SELECT * FROM artistas_favoritos af JOIN Artistas a ON af.id_artista = a.id_artista WHERE af.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        $res['playlists'] = $conexion->query("SELECT * FROM playlists_favoritas pf JOIN Playlist p ON pf.id_playlist = p.id_playlist WHERE pf.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        return $res;
    }

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

    public static function obtenerArtistasFavoritos($id_usuario) {
        global $conexion;
        $stmt = $conexion->prepare("
            SELECT artistas.id_artista, artistas.nombre, artistas.imagen
            FROM artistas_favoritos
            INNER JOIN artistas ON artistas_favoritos.id_artista = artistas.id_artista
            WHERE artistas_favoritos.id_usuario = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
