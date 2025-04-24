<?php

//El "isssss" en los bind param etc es por motivos de seguridad, para evitar inyecciones sql
//a pesar de no ser necesario me parece una buena practica y me permite comprobar si tengo ese conocimiento
// de ser necesario por errores u otros motivos, sera eliminado


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    $stmt = $conexion->prepare("SELECT id_usuario, contrasena FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];

            $stmt = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
            $stmt->bind_param("i", $usuario['id_usuario']);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                header("Location: ../vista/premium.php");
            } else {
                header("Location: ../vista/normal.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Contraseña incorrecta";
            header("Location: ../vista/vista_login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No existe una cuenta con este email";
        header("Location: ../vista/vista_login.php");
        exit();
    }
    $stmt->close();
    $conexion->close();
}
