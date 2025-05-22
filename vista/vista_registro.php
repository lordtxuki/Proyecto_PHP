<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../modelo/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $correo = $_POST["email"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $fecha_nac = $_POST["fecha_nac"];
    $genero = $_POST["genero"];
    $pais = $_POST["pais"];
    $codigo_postal = $_POST["codigo_postal"];
    $tipo_cuenta = $_POST["tipo_cuenta"];
    $rol = ($tipo_cuenta == 'premium') ? 'premium' : 'usuario';

    $sql = "INSERT INTO Usuario (nombre_usuario, correo, contrasena, fecha_nacimiento, genero, pais, codigo_postal, rol, fecha_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $usuario, $correo, $contrasena, $fecha_nac, $genero, $pais, $codigo_postal, $rol);

    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;

        if ($rol == 'premium') {
            $tarjeta = $_POST["tarjeta"];
            $vencimiento = $_POST["vencimiento"];
            $cvv = $_POST["cvv"];
            $paypal = $_POST["paypal"];
            $metodo_pago = (!empty($tarjeta)) ? 'Tarjeta' : ((!empty($paypal)) ? 'PayPal' : 'Desconocido');

            $sql_premium = "INSERT INTO usuario_premium (id_usuario, metodo_pago, fecha_inicio) 
                            VALUES (?, ?, NOW())";
            $stmt_premium = $conn->prepare($sql_premium);
            $stmt_premium->bind_param("is", $id_usuario, $metodo_pago);
            $stmt_premium->execute();

            $sql_suscripcion = "INSERT INTO Suscripciones (id_usuario, tipo, fecha_inicio, activa) 
                                VALUES (?, 'premium', NOW(), 1)";
            $stmt_suscripcion = $conn->prepare($sql_suscripcion);
            $stmt_suscripcion->bind_param("i", $id_usuario);
            $stmt_suscripcion->execute();

            $sql_pago = "INSERT INTO Pagos (id_usuario, metodo_pago, fecha_pago, monto) 
                            VALUES (?, ?, NOW(), 9.99)";
            $stmt_pago = $conn->prepare($sql_pago);
            $stmt_pago->bind_param("is", $id_usuario, $metodo_pago);
            $stmt_pago->execute();
        }

        $_SESSION["id_usuario"] = $id_usuario;
        $_SESSION["usuario"] = $usuario;
        $_SESSION["rol"] = $rol;

        if ($rol == 'premium') {
            header("Location: ../vista/premium.php");
        } else {
            header("Location: ../vista/normal.php");
        }
        exit;
    } else {
        echo "Error al registrar el usuario: " . $conn->error;
    }
}
?>
