<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../controlador/conexion.php");

$email = '';
$usuario = '';
$fecha_nac = '';
$genero = '';
$pais = '';
$codigo_postal = '';
$tipo_cuenta = '';
$tarjeta = '';
$vencimiento = '';
$cvv = '';
$paypal = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $usuario = trim($_POST["usuario"]);
    $contrasena_raw = $_POST["contrasena"];
    $fecha_nac = $_POST["fecha_nac"];
    $genero = $_POST["genero"];
    $pais = trim($_POST["pais"]);
    $codigo_postal = trim($_POST["codigo_postal"]);
    $tipo_cuenta = $_POST["tipo_cuenta"];

    $tarjeta = isset($_POST["tarjeta"]) ? trim($_POST["tarjeta"]) : '';
    $vencimiento = isset($_POST["vencimiento"]) ? $_POST["vencimiento"] : '';
    $cvv = isset($_POST["cvv"]) ? trim($_POST["cvv"]) : '';
    $paypal = isset($_POST["paypal"]) ? trim($_POST["paypal"]) : '';

    if (empty($email) || empty($usuario) || empty($contrasena_raw) || empty($fecha_nac) || empty($pais) || empty($codigo_postal)) {
        $error = "Por favor completa todos los campos obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } else {
        $sql_check = "SELECT id_usuario FROM Usuario WHERE email = ? OR usuario = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("ss", $email, $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "El email o usuario ya están registrados.";
        }
    }

    if ($error === '') {
        $contrasena = password_hash($contrasena_raw, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Usuario (usuario, email, contrasena, fecha_nac, genero, pais, codigo_postal)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssss", $usuario, $email, $contrasena, $fecha_nac, $genero, $pais, $codigo_postal);

        if ($stmt->execute()) {
            $id_usuario = $stmt->insert_id;

            if ($tipo_cuenta === 'premium') {
                $sql_premium = "INSERT INTO usuario_premium (id_usuario) VALUES (?)";
                $stmt_prem = $conexion->prepare($sql_premium);
                $stmt_prem->bind_param("i", $id_usuario);
                $stmt_prem->execute();
                $id_usuario_premium = $conexion->insert_id;

                $sql_sus = "INSERT INTO Suscripciones (id_usuario_premium, fecha_inicio, estado) VALUES (?, CURDATE(), 'activa')";
                $stmt_sus = $conexion->prepare($sql_sus);
                $stmt_sus->bind_param("i", $id_usuario_premium);
                $stmt_sus->execute();
                $id_suscripcion = $conexion->insert_id;

                $forma_pago = !empty($tarjeta) ? 'tarjeta' : (!empty($paypal) ? 'paypal' : null);

                if ($forma_pago === null) {
                    $error = "Debes ingresar un método de pago válido para la cuenta Premium.";
                    $conexion->query("DELETE FROM Usuario WHERE id_usuario = $id_usuario");
                } else {
                    $num_tarjeta = ($forma_pago === 'tarjeta') ? $tarjeta : null;
                    $caducidad = ($forma_pago === 'tarjeta') ? $vencimiento : null;
                    $codigo_seguridad = ($forma_pago === 'tarjeta') ? $cvv : null;
                    $usuario_paypal = ($forma_pago === 'paypal') ? $paypal : null;

                    if ($error === '') {
                        $sql_pago = "INSERT INTO Pagos
                            (id_suscripcion, fecha_pago, cantidad, forma_pago, num_tarjeta, caducidad, codigo_seguridad, usuario_paypal)
                            VALUES (?, CURDATE(), 9.99, ?, ?, ?, ?, ?)";

                        $stmt_pago = $conexion->prepare($sql_pago);
                        $stmt_pago->bind_param("isssss", $id_suscripcion, $forma_pago, $num_tarjeta, $caducidad, $codigo_seguridad, $usuario_paypal);
                        $stmt_pago->execute();

                        $_SESSION["tipo_cuenta"] = "premium";
                    }
                }
            } else {
                $_SESSION["tipo_cuenta"] = "normal";
            }

            if ($error === '') {
                $_SESSION["usuario_id"] = $id_usuario;
                $_SESSION["usuario"] = $usuario;

                if ($_SESSION["tipo_cuenta"] === "premium") {
                    header("Location: ../vista/premium.php");
                } else {
                    header("Location: ../vista/normal.php");
                }
                exit;
            }
        } else {
            $error = "Error al registrar el usuario: " . $conexion->error;
        }
    }
}

$_SESSION['registro_error'] = $error;
$_SESSION['registro_datos'] = [
    'email' => $email,
    'usuario' => $usuario,
    'fecha_nac' => $fecha_nac,
    'genero' => $genero,
    'pais' => $pais,
    'codigo_postal' => $codigo_postal,
    'tipo_cuenta' => $tipo_cuenta,
    'tarjeta' => $tarjeta,
    'vencimiento' => $vencimiento,
    'cvv' => $cvv,
    'paypal' => $paypal
];

header("Location: ../vista/vista_registro.php");
exit;
?>
