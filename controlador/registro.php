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
    $password = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
    $usuario = $_POST['usuario'];
    $fecha_nac = $_POST['fecha_nac'];
    $genero = $_POST['genero'];
    $pais = $_POST['pais'];
    $codigo_postal = $_POST['codigo_postal'];
    $tipo_cuenta = $_POST['tipo_cuenta'];

    $fecha_nac_obj = DateTime::createFromFormat('d/m/Y', $fecha_nac);
    $fecha_nac_formateada = $fecha_nac_obj ? $fecha_nac_obj->format('Y-m-d') : null;

    if ($tipo_cuenta == "premium") {
        $tarjeta = $_POST['tarjeta'];
        $vencimiento = $_POST['vencimiento'];
        $cvv = $_POST['cvv'];
        $paypal = $_POST['paypal'];

        if (empty($tarjeta) && empty($paypal)) {
            $error = "Por favor, ingresa los datos de tarjeta o PayPal para la cuenta Premium.";
        } elseif (!empty($tarjeta) && !preg_match('/^\d{16}$/', $tarjeta)) {
            $error = "El número de tarjeta debe tener 16 dígitos.";
        } elseif (!empty($cvv) && !preg_match('/^\d{3,4}$/', $cvv)) {
            $error = "El CVV debe tener entre 3 y 4 dígitos.";
        } elseif (!empty($paypal) && !filter_var($paypal, FILTER_VALIDATE_EMAIL)) {
            $error = "El correo de PayPal no es válido.";
        }
    }

    if (!isset($error)) {
        $stmt = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El usuario ya existe. Por favor, elige otro nombre de usuario.";
        } else {
            $stmt = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "El correo electrónico ya está registrado. Por favor, usa otro.";
            } else {
                $stmt = $conexion->prepare("INSERT INTO Usuario (email, contrasena, usuario, fecha_nac, genero, pais, codigo_postal) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $email, $password, $usuario, $fecha_nac_formateada, $genero, $pais, $codigo_postal);

                if ($stmt->execute()) {
                    $id_usuario = $conexion->insert_id;

                    if ($tipo_cuenta == "premium") {
                        $stmt = $conexion->prepare("INSERT INTO usuario_premium (id_usuario) VALUES (?)");
                        $stmt->bind_param("i", $id_usuario);
                        $stmt->execute();
                        $id_usuario_premium = $conexion->insert_id;

                        $fecha_inicio = date('Y-m-d');
                        $fecha_renovacion = date('Y-m-d', strtotime("+1 month"));
                        $estado = 'activa';

                        $stmt = $conexion->prepare("INSERT INTO Suscripciones (id_usuario_premium, fecha_inicio, fecha_renovacion, estado) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $id_usuario_premium, $fecha_inicio, $fecha_renovacion, $estado);
                        $stmt->execute();
                        $id_suscripcion = $conexion->insert_id;

                        $cantidad = 9.99;

                        if (!empty($tarjeta)) {
                            $forma_pago = 'tarjeta';
                            $stmt = $conexion->prepare("INSERT INTO Pagos (id_suscripcion, fecha_pago, cantidad, forma_pago, num_tarjeta, caducidad, codigo_seguridad) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("issssss", $id_suscripcion, $fecha_inicio, $cantidad, $forma_pago, $tarjeta, $vencimiento, $cvv);
                            $stmt->execute();
                        } elseif (!empty($paypal)) {
                            $forma_pago = 'paypal';
                            $stmt = $conexion->prepare("INSERT INTO Pagos (id_suscripcion, fecha_pago, cantidad, forma_pago, usuario_paypal) VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("issss", $id_suscripcion, $fecha_inicio, $cantidad, $forma_pago, $paypal);
                            $stmt->execute();
                        }
                    }

                    // Redirección corregida
                    header("Location: /Recuperacion_Php/vista/vista_login.php?exito1");
                    exit();
                } else {
                    $error = "Error en el registro. Por favor, intenta nuevamente.";
                }
            }
        }
        $stmt->close();
    }

    $conexion->close();
}
?>
