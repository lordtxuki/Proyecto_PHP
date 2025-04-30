<?php
require_once '../controlador/conexion.php';

class CancionModelo {
    public static function registrarReproduccion($id_usuario, $id_cancion) {
        global $conexion;
        $stmt = $conexion->prepare("INSERT INTO usuario_canciones (id_usuario, id_cancion) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_cancion);
        $stmt->execute();

        $stmt = $conexion->prepare("UPDATE Canciones SET veces_reproducida = veces_reproducida + 1 WHERE id_cancion = ?");
        $stmt->bind_param("i", $id_cancion);
        $stmt->execute();
    }

    public static function obtener($id_cancion) {
        global $conexion;
        $stmt = $conexion->prepare("SELECT id_cancion, titulo, duracion, ruta FROM Canciones WHERE id_cancion = ?");
        $stmt->bind_param("i", $id_cancion);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function obtenerTodas() {
        global $conexion;
        $res = $conexion->query("SELECT id_cancion, titulo, duracion, ruta FROM Canciones");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
