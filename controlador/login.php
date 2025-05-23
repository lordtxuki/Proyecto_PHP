<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    $stmt = $conexion->prepare("SELECT id_usuario, contrasena, rol FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] === 'admin') {
                header("Location: ../vista/admin.php");
            } elseif ($usuario['rol'] === 'premium') {
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
?>
