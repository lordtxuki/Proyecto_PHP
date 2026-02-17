<?php
// Iniciamos la sesión para poder usar variables de sesión
session_start();

// Incluimos la conexión a la base de datos
include("conexion.php");

// Verificamos si el formulario fue enviado por método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recogemos los datos del formulario (usamos trim para eliminar espacios extra)
    $email         = trim($_POST["email"] ?? '');
    $usuario       = trim($_POST["usuario"] ?? '');
    $contrasena    = $_POST["contrasena"] ?? '';
    $fecha_nac     = $_POST["fecha_nac"] ?? null;
    $genero        = $_POST["genero"] ?? '';
    $pais          = trim($_POST["pais"] ?? '');
    $codigo_postal = trim($_POST["codigo_postal"] ?? '');
    $tipo_cuenta   = $_POST["tipo_cuenta"] ?? 'normal'; // Por defecto es cuenta normal
    $tarjeta       = trim($_POST["tarjeta"] ?? '');
    $vencimiento   = $_POST["vencimiento"] ?? '';
    $cvv           = trim($_POST["cvv"] ?? '');
    $paypal        = trim($_POST["paypal"] ?? '');

    // Guardamos los datos en sesión por si hay errores y hay que volver al formulario
    $_SESSION['old'] = [
        'email'         => $email,
        'usuario'       => $usuario,
        'fecha_nac'     => $fecha_nac,
        'genero'        => $genero,
        'pais'          => $pais,
        'codigo_postal' => $codigo_postal,
        'tipo_cuenta'   => $tipo_cuenta,
        'tarjeta'       => $tarjeta,
        'vencimiento'   => $vencimiento,
        'cvv'           => $cvv,
        'paypal'        => $paypal
    ];

    $error = ""; // Inicializamos variable de error

    // Verificamos que los campos obligatorios estén completos
    if (empty($email) || empty($usuario) || empty($contrasena) || empty($fecha_nac) || empty($pais) || empty($codigo_postal)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    }

    // Si es cuenta premium, comprobamos que haya un método de pago válido
    if (empty($error) && $tipo_cuenta === 'premium') {
        $tarjeta_valida = preg_match('/^\d{16}$/', $tarjeta); // Tarjeta debe tener 16 dígitos
        $paypal_valido  = filter_var($paypal, FILTER_VALIDATE_EMAIL); // Email de PayPal válido

        // Si no se ha proporcionado una forma de pago válida
        if (!$tarjeta_valida && !$paypal_valido) {
            $error = "Para cuentas premium debes introducir una tarjeta válida (16 dígitos) o un correo PayPal válido.";
        }
    }

    // Si no hay errores, continuamos con el registro
    if (empty($error)) {
        // Encriptamos la contraseña antes de guardarla
        $hash_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Preparamos la consulta para insertar al usuario
        $sql_usuario = "
            INSERT INTO Usuario 
                (email, contrasena, usuario, fecha_nac, genero, pais, codigo_postal)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt_usuario = $conexion->prepare($sql_usuario);

        if (!$stmt_usuario) {
            $error = "Error en la preparación de la consulta (Usuario): " . $conexion->error;
        } else {
            // Asociamos los valores a la consulta
            $stmt_usuario->bind_param(
                "sssssss",
                $email,
                $hash_password,
                $usuario,
                $fecha_nac,
                $genero,
                $pais,
                $codigo_postal
            );

            // Ejecutamos la consulta
            if ($stmt_usuario->execute()) {
                // Obtenemos el id del usuario recién registrado
                $id_usuario = $stmt_usuario->insert_id;

                // Si la cuenta es premium, registramos la suscripción y el pago
                if ($tipo_cuenta === 'premium') {

                    // Insertamos en tabla usuario_premium
                    $sql_premium = "INSERT INTO usuario_premium (id_usuario) VALUES (?)";
                    $stmt_premium = $conexion->prepare($sql_premium);

                    if (!$stmt_premium) {
                        $error = "Error en la preparación de la consulta (usuario_premium): " . $conexion->error;
                    } else {
                        $stmt_premium->bind_param("i", $id_usuario);
                        $stmt_premium->execute();
                        $id_usuario_premium = $stmt_premium->insert_id;

                        // Insertamos la suscripción activa desde hoy
                        $sql_suscrip = "
                            INSERT INTO Suscripciones (id_usuario_premium, fecha_inicio, estado) 
                            VALUES (?, NOW(), 'activa')
                        ";
                        $stmt_suscrip = $conexion->prepare($sql_suscrip);

                        if (!$stmt_suscrip) {
                            $error = "Error en la preparación de la consulta (Suscripciones): " . $conexion->error;
                        } else {
                            $stmt_suscrip->bind_param("i", $id_usuario_premium);
                            $stmt_suscrip->execute();
                            $id_suscripcion = $stmt_suscrip->insert_id;

                            // Datos del pago según el método
                            if (!empty($tarjeta)) {
                                $forma_pago     = "tarjeta";
                                $num_tarjeta    = $tarjeta;
                                $caducidad      = $vencimiento;
                                $codigo_seg     = $cvv;
                                $usuario_paypal = null;
                            } elseif (!empty($paypal)) {
                                $forma_pago     = "paypal";
                                $num_tarjeta    = null;
                                $caducidad      = null;
                                $codigo_seg     = null;
                                $usuario_paypal = $paypal;
                            } else {
                                // Sin datos (no debería pasar por validación)
                                $forma_pago     = "tarjeta";
                                $num_tarjeta    = null;
                                $caducidad      = null;
                                $codigo_seg     = null;
                                $usuario_paypal = null;
                            }

                            // Insertamos el pago
                            $sql_pago = "
                                INSERT INTO Pagos 
                                    (id_suscripcion, fecha_pago, cantidad, forma_pago, num_tarjeta, caducidad, codigo_seguridad, usuario_paypal)
                                VALUES 
                                    (?, NOW(), 9.99, ?, ?, ?, ?, ?)
                            ";
                            $stmt_pago = $conexion->prepare($sql_pago);

                            if (!$stmt_pago) {
                                $error = "Error en la preparación de la consulta (Pagos): " . $conexion->error;
                            } else {
                                $stmt_pago->bind_param(
                                    "isssss",
                                    $id_suscripcion,
                                    $forma_pago,
                                    $num_tarjeta,
                                    $caducidad,
                                    $codigo_seg,
                                    $usuario_paypal
                                );
                                $stmt_pago->execute();
                            }
                        }
                    }
                }

                // Si todo fue bien, guardamos datos de sesión y redirigimos
                    if (empty($error)) {

                        $_SESSION['usuario_id']  = $id_usuario;

                        $_SESSION['usuario']     = $usuario;
                        $_SESSION['tipo_cuenta'] = $tipo_cuenta;


                    // Borramos datos temporales
                    unset($_SESSION['old'], $_SESSION['error']);

                    // Redirigimos a la página principal correspondiente
                    if ($tipo_cuenta === 'premium') {
                        header("Location: ../vista/premium.php");
                    } else {
                        header("Location: ../vista/normal.php");
                    }
                    exit;
                }

            } else {
                $error = "Error al registrar el usuario: " . $stmt_usuario->error;
            }
        }
    }

    // Si hubo algún error, lo guardamos en sesión y redirigimos de vuelta al formulario
    $_SESSION['error'] = $error;
    header("Location: ../vista/vista_registro.php");
    exit;
}
?>
