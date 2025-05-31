<?php
// Iniciamos sesión para poder acceder a los datos guardados en ella
session_start();

// Intentamos obtener los valores "viejos" (inputs del formulario que se enviaron antes)
// y el mensaje de error guardados en la sesión, si es que existen.
// Si no existen, se asigna un array vacío o cadena vacía para evitar errores.
$old = $_SESSION['old'] ?? [];
$error = $_SESSION['error'] ?? '';

// Asignamos a variables individuales cada valor recuperado,
// o cadena vacía si no existe (para evitar warnings y poder rellenar los campos).
$email         = $old['email']         ?? '';
$usuario       = $old['usuario']       ?? '';
$fecha_nac     = $old['fecha_nac']     ?? '';
$genero        = $old['genero']        ?? '';
$pais          = $old['pais']          ?? '';
$codigo_postal = $old['codigo_postal'] ?? '';
$tipo_cuenta   = $old['tipo_cuenta']   ?? 'normal';  // Por defecto normal
$tarjeta       = $old['tarjeta']       ?? '';
$vencimiento   = $old['vencimiento']   ?? '';
$cvv           = $old['cvv']           ?? '';
$paypal        = $old['paypal']        ?? '';

// Después de obtener los datos, los borramos de la sesión para no mantenerlos siempre.
unset($_SESSION['old'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro</title>
    <!-- Bootstrap CSS desde CDN para estilos rápidos y responsivos -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    />
    <!-- Hoja de estilos personalizada -->
    <link rel="stylesheet" href="../styles/styles.css" />
</head>
<body class="bg-light">
    <div class="container mt-4">
        <!-- Card que contiene el formulario, con sombra y padding -->
        <div class="card shadow-lg p-3">
            <h2 class="text-center">Registro</h2>

            <!-- Formulario que envía los datos a ../controlador/registro.php vía POST -->
            <form action="../controlador/registro.php" method="POST">
                <div class="row">
                    <!-- Columna izquierda: campos básicos de usuario -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <!-- Campo email con valor pre-llenado desde $email para evitar pérdida de datos -->
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <!-- Campo usuario con valor pre-llenado -->
                            <input
                                type="text"
                                name="usuario"
                                class="form-control"
                                value="<?php echo htmlspecialchars($usuario); ?>"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <!-- Campo contraseña, no pre-llenado por seguridad -->
                            <input
                                type="password"
                                name="contrasena"
                                class="form-control"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <!-- Campo fecha de nacimiento con valor pre-llenado -->
                            <input
                                type="date"
                                name="fecha_nac"
                                class="form-control"
                                value="<?php echo htmlspecialchars($fecha_nac); ?>"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Género</label>
                            <!-- Select para género, con opción seleccionada basada en $genero -->
                            <select name="genero" class="form-select">
                                <option value="M" <?php if ($genero === 'M') echo 'selected'; ?>>Masculino</option>
                                <option value="F" <?php if ($genero === 'F') echo 'selected'; ?>>Femenino</option>
                                <option value="Otro" <?php if ($genero === 'Otro') echo 'selected'; ?>>Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">País</label>
                            <!-- Campo país pre-llenado -->
                            <input
                                type="text"
                                name="pais"
                                class="form-control"
                                value="<?php echo htmlspecialchars($pais); ?>"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código Postal</label>
                            <!-- Campo código postal pre-llenado -->
                            <input
                                type="text"
                                name="codigo_postal"
                                class="form-control"
                                value="<?php echo htmlspecialchars($codigo_postal); ?>"
                                required
                            />
                        </div>
                    </div>

                    <!-- Columna derecha: tipo de cuenta y datos de pago -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo de cuenta</label>
                            <!-- Select para tipo de cuenta con valor pre-seleccionado -->
                            <select name="tipo_cuenta" class="form-select" id="tipoCuenta">
                                <option value="normal" <?php if ($tipo_cuenta === 'normal') echo 'selected'; ?>>Normal</option>
                                <option value="premium" <?php if ($tipo_cuenta === 'premium') echo 'selected'; ?>>Premium</option>
                            </select>
                        </div>

                        <!-- Aviso para el usuario sobre los datos de pago -->
                        <div class="alert alert-danger mt-2">
                            <strong>Nota:</strong> Si eliges una cuenta Premium, debes ingresar los datos de pago.
                        </div>

                        <!-- Sección para datos de pago, que se muestra solo si el tipo de cuenta es 'premium' -->
                        <div
                            id="datosPago"
                            class="border p-3 bg-light"
                            style="display: <?php echo ($tipo_cuenta === 'premium') ? 'block' : 'none'; ?>;"
                        >
                            <h5 class="text-center">Datos de Pago</h5>
                            <div class="mb-3">
                                <label class="form-label">Tarjeta de Crédito</label>
                                <!-- Input para número de tarjeta con validación de 16 dígitos -->
                                <input
                                    type="text"
                                    name="tarjeta"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($tarjeta); ?>"
                                    placeholder="Número de tarjeta"
                                    pattern="[0-9]{16}"
                                    maxlength="16"
                                />
                                <small class="form-text text-muted">
                                    La tarjeta solo será verificada como correcta con 16 números.
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vencimiento</label>
                                <!-- Input para fecha de vencimiento de la tarjeta -->
                                <input
                                    type="month"
                                    name="vencimiento"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($vencimiento); ?>"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">CVV</label>
                                <!-- Input para CVV con patrón de 3 o 4 números -->
                                <input
                                    type="text"
                                    name="cvv"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cvv); ?>"
                                    maxlength="4"
                                    pattern="[0-9]{3,4}"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario PayPal</label>
                                <!-- Input para correo PayPal -->
                                <input
                                    type="email"
                                    name="paypal"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($paypal); ?>"
                                    placeholder="Correo PayPal"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para enviar el formulario -->
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Enlace para ir a iniciar sesión si ya se tiene cuenta -->
            <p class="text-center mt-3" style="color: #555;">
                ¿Ya tienes cuenta?
                <a href="../vista/vista_login.php" style="color: #667eea;">Inicia Sesión aquí</a>
            </p>
        </div>
    </div>

    <!-- Script para mostrar u ocultar los datos de pago según el tipo de cuenta -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoCuenta = document.getElementById('tipoCuenta');
            const datosPago = document.getElementById('datosPago');

            // Función que muestra datos de pago solo si se selecciona 'premium'
            function toggleDatosPago() {
                if (tipoCuenta.value === 'premium') {
                    datosPago.style.display = 'block';
                } else {
                    datosPago.style.display = 'none';
                }
            }

            // Ejecutamos al cargar la página para ajustar la visibilidad inicial
            toggleDatosPago();

            // Ejecutamos cada vez que cambia la selección del tipo de cuenta
            tipoCuenta.addEventListener('change', toggleDatosPago);
        });
    </script>
</body>
</html>
