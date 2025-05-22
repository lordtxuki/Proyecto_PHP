<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../modelo/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $correo = $_POST["correo"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $tipo_cuenta = $_POST["tipo_cuenta"];
    $rol = ($tipo_cuenta == 'premium') ? 'premium' : 'usuario';

    $sql_usuario = "INSERT INTO Usuario (nombre_usuario, correo, contrasena, rol, fecha_registro) 
                    VALUES (?, ?, ?, ?, NOW())";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("ssss", $usuario, $correo, $contrasena, $rol);
    
    if ($stmt_usuario->execute()) {
        $id_usuario = $stmt_usuario->insert_id;

        if ($tipo_cuenta == 'premium') {
            $tarjeta = $_POST["tarjeta"];
            $fecha_exp = $_POST["fecha_exp"];
            $cvv = $_POST["cvv"];
            $metodo_pago = "Tarjeta";

            $sql_premium = "INSERT INTO usuario_premium (id_usuario, metodo_pago, fecha_inicio) 
                            VALUES (?, ?, NOW())";
            $stmt_premium = $conn->prepare($sql_premium);
            $stmt_premium->bind_param("is", $id_usuario, $metodo_pago);
            $stmt_premium->execute();

            $sql_suscripcion = "INSERT INTO Suscripciones (id_usuario, tipo, fecha_inicio, activa) 
                                VALUES (?, ?, NOW(), 1)";
            $stmt_suscripcion = $conn->prepare($sql_suscripcion);
            $stmt_suscripcion->bind_param("is", $id_usuario, $tipo_cuenta);
            $stmt_suscripcion->execute();

            $sql_pago = "INSERT INTO Pagos (id_usuario, metodo_pago, fecha_pago, monto) 
                         VALUES (?, ?, NOW(), 9.99)";
            $stmt_pago = $conn->prepare($sql_pago);
            $stmt_pago->bind_param("is", $id_usuario, $metodo_pago);
            $stmt_pago->execute();
        }

        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $rol;

        if ($rol == 'premium') {
            header("Location: ../vista/premium.php");
        } else {
            header("Location: ../vista/normal.php");
        }
        exit;
    } else {
        echo "Error al registrar el usuario.";
    }
}
?>
