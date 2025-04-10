<?php
// Mantener los datos si el registro falló
$email = isset($_POST['email']) ? $_POST['email'] : '';
$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$fecha_nac = isset($_POST['fecha_nac']) ? $_POST['fecha_nac'] : '';
$genero = isset($_POST['genero']) ? $_POST['genero'] : '';
$pais = isset($_POST['pais']) ? $_POST['pais'] : '';
$codigo_postal = isset($_POST['codigo_postal']) ? $_POST['codigo_postal'] : '';
$tipo_cuenta = isset($_POST['tipo_cuenta']) ? $_POST['tipo_cuenta'] : '';
$tarjeta = isset($_POST['tarjeta']) ? $_POST['tarjeta'] : '';
$vencimiento = isset($_POST['vencimiento']) ? $_POST['vencimiento'] : '';
$cvv = isset($_POST['cvv']) ? $_POST['cvv'] : '';
$paypal = isset($_POST['paypal']) ? $_POST['paypal'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="card shadow-lg p-3">
            <h2 class="text-center">Registro</h2>
            <form action="../controlador/registro.php" method="POST">
                <div class="row">
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="usuario" class="form-control" value="<?php echo $usuario; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nac" class="form-control" value="<?php echo $fecha_nac; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Género</label>
                            <select name="genero" class="form-select">
                                <option value="M" <?php if ($genero == 'M') echo 'selected'; ?>>Masculino</option>
                                <option value="F" <?php if ($genero == 'F') echo 'selected'; ?>>Femenino</option>
                                <option value="Otro" <?php if ($genero == 'Otro') echo 'selected'; ?>>Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" class="form-control" value="<?php echo $pais; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="codigo_postal" class="form-control" value="<?php echo $codigo_postal; ?>" required>
                        </div>
                    </div>

                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo de cuenta</label>
                            <select name="tipo_cuenta" class="form-select">
                                <option value="normal" <?php if ($tipo_cuenta == 'normal') echo 'selected'; ?>>Normal</option>
                                <option value="premium" <?php if ($tipo_cuenta == 'premium') echo 'selected'; ?>>Premium</option>
                            </select>
                        </div>

                        <div class="alert alert-danger mt-2">
                            <strong>Nota:</strong> Si eliges una cuenta Premium, debes ingresar los datos de pago.
                        </div>

                        <div class="border p-3 bg-light">
                            <h5 class="text-center">Datos de Pago</h5>
                            <div class="mb-3">
                                <label class="form-label">Tarjeta de Crédito</label>
                                <input type="text" name="tarjeta" class="form-control" value="<?php echo $tarjeta; ?>" placeholder="Número de tarjeta" pattern="\d{16}" maxlength="16">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vencimiento</label>
                                <input type="month" name="vencimiento" class="form-control" value="<?php echo $vencimiento; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">CVV</label>
                                <input type="text" name="cvv" class="form-control" value="<?php echo $cvv; ?>" maxlength="4" pattern="\d{3,4}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario PayPal</label>
                                <input type="email" name="paypal" class="form-control" value="<?php echo $paypal; ?>" placeholder="Correo PayPal">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
            </form>
            <p class="text-center mt-3" style="color: #555;">¿Ya tienes cuenta? <a href="../vista/vista_login.php" style="color: #667eea;">Inicia Sesión aquí</a></p>
        </div>
    </div>

</body>
</html>
