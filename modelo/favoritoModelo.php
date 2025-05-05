<?php
require_once '../controlador/conexion.php';

class FavoritoModelo {
    public static function agregar($id_usuario, $id, $tipo) {
        global $conexion;
        if ($tipo == 'cancion') {
            $stmt = $conexion->prepare("INSERT INTO canciones_favoritas (id_usuario, id_cancion) VALUES (?, ?)");
        } else {
            $stmt = $conexion->prepare("INSERT INTO albumes_favoritos (id_usuario, id_album) VALUES (?, ?)");
        }
        $stmt->bind_param("ii", $id_usuario, $id);
        $stmt->execute();
    }

    public static function quitar($id_usuario, $id, $tipo) {
        global $conexion;
        if ($tipo == 'cancion') {
            $stmt = $conexion->prepare("DELETE FROM canciones_favoritas WHERE id_usuario = ? AND id_cancion = ?");
        } else {
            $stmt = $conexion->prepare("DELETE FROM albumes_favoritos WHERE id_usuario = ? AND id_album = ?");
        }
        $stmt->bind_param("ii", $id_usuario, $id);
        $stmt->execute();
    }

    public static function obtener($id_usuario) {
        global $conexion;
        $res = [
            'canciones' => [],
            'albumes' => []
        ];
        $res['canciones'] = $conexion->query("SELECT * FROM canciones_favoritas cf JOIN Canciones c ON cf.id_cancion = c.id_cancion WHERE cf.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        $res['albumes'] = $conexion->query("SELECT * FROM albumes_favoritos af JOIN Albumes a ON af.id_album = a.id_album WHERE af.id_usuario = $id_usuario")->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
}