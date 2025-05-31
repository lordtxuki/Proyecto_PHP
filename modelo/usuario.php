<?php
require_once 'ModeloBase.php';

class Usuario extends ModeloBase {
    public function registrar($email, $password, $usuario, $fecha_nac, $genero, $pais, $codigo_postal) {
        // Preparar la consulta para insertar un nuevo usuario en la tabla Usuario
        $stmt = $this->conexion->prepare("INSERT INTO Usuario (email, contrasena, usuario, fecha_nac, genero, pais, codigo_postal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Asociar los parámetros (todos strings)
        $stmt->bind_param("sssssss", $email, $password, $usuario, $fecha_nac, $genero, $pais, $codigo_postal);
        // Ejecutar la consulta y devolver resultado (true si tuvo éxito, false si no)
        return $stmt->execute();
    }

    public function obtenerPorEmail($email) {
        // Preparar consulta para obtener usuario por email
        $stmt = $this->conexion->prepare("SELECT * FROM Usuario WHERE email = ?");
        // Asociar parámetro email (string)
        $stmt->bind_param("s", $email);
        // Ejecutar consulta
        $stmt->execute();
        // Obtener y devolver resultado como array asociativo con datos del usuario
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorId($id_usuario) {
        // Preparar consulta para obtener usuario por id_usuario
        $stmt = $this->conexion->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
        // Asociar parámetro id_usuario (int)
        $stmt->bind_param("i", $id_usuario);
        // Ejecutar consulta
        $stmt->execute();
        // Obtener y devolver resultado como array asociativo con datos del usuario
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
