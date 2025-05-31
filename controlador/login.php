<?php
// Inicio la sesión si no está activa para manejar al usuario que intenta loguearse
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluyo la conexión a la base de datos para poder hacer consultas
require_once '../controlador/conexion.php';

// Compruebo que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recojo el email y la contraseña que escribió el usuario
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    // Preparo una consulta para buscar al usuario por su email
    $stmt = $conexion->prepare("SELECT id_usuario, contrasena FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Si encuentro un usuario con ese email
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verifico que la contraseña escrita coincide con la guardada en la base de datos
        if (password_verify($password, $usuario['contrasena'])) {
            // Guardo el id del usuario en la sesión para mantenerlo logueado
            $_SESSION['usuario_id'] = $usuario['id_usuario'];

            // Compruebo si ese usuario es premium buscando en la tabla usuario_premium
            $stmt2 = $conexion->prepare("SELECT id_usuario FROM usuario_premium WHERE id_usuario = ?");
            $stmt2->bind_param("i", $usuario['id_usuario']);
            $stmt2->execute();
            $resPremium = $stmt2->get_result();

            // Si es premium, lo mando a la página premium
            if ($resPremium->num_rows > 0) {
                header("Location: ../vista/premium.php");
            } else {
                // Si no es premium, lo mando a la página normal
                header("Location: ../vista/normal.php");
            }
            $stmt2->close();
            exit();
        } else {
            // Si la contraseña no coincide, guardo un mensaje de error en la sesión y redirijo al login
            $_SESSION['login_error'] = "Contraseña incorrecta";
            header("Location: ../vista/vista_login.php");
            exit();
        }
    } else {
        // Si no existe usuario con ese email, guardo el error y redirijo al login
        $_SESSION['login_error'] = "No existe una cuenta con este email";
        header("Location: ../vista/vista_login.php");
        exit();
    }
    $stmt->close();
    $conexion->close();
}
?>
