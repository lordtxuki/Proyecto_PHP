<?php
require_once 'ModeloBase.php';

class Usuario extends ModeloBase {
    public function registrar($email, $password, $usuario, $fecha_nac, $genero, $pais, $codigo_postal) {
        $stmt = $this->conexion->prepare("INSERT INTO Usuario (email, contrasena, usuario, fecha_nac, genero, pais, codigo_postal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $email, $password, $usuario, $fecha_nac, $genero, $pais, $codigo_postal);
        return $stmt->execute();
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->conexion->prepare("SELECT * FROM Usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorId($id_usuario) {
        $stmt = $this->conexion->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
