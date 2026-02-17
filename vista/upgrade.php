<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Actualizar a Premium</title>
    <link rel="stylesheet" href="../styles/upgrade.css" />
</head>
<body>

    <!-- Formulario para actualizar a usuario Premium -->
    <form id="formPremium" action="../controlador/upgrade.php" method="POST" novalidate>

        <!-- BLOQUE TARJETA -->
        <div id="bloqueTarjeta">

            <div class="mb-3">
                <!-- Patrón para 16 dígitos -->
                <label class="form-label">Tarjeta de crédito (16 dígitos)</label>
                <input
                    type="text"
                    name="tarjeta"
                    id="tarjeta"
                    pattern="^\d{16}$"
                    maxlength="16"
                    class="form-control"
                    placeholder="Ej: 1234123412341234"
                    title="Debe contener exactamente 16 dígitos"
                    inputmode="numeric"
                    autocomplete="cc-number"
                >
            </div>

            <div class="mb-3">
                <!-- Campo mes y año de vencimiento de la tarjeta -->
                <label class="form-label">Vencimiento</label>
                <input
                    type="month"
                    name="vencimiento"
                    id="vencimiento"
                    class="form-control"
                    autocomplete="cc-exp"
                >
            </div>

            <div class="mb-3">
                <!-- CVV: 3 o 4 dígitos -->
                <label class="form-label">CVV</label>
                <input
                    type="text"
                    name="cvv"
                    id="cvv"
                    pattern="^\d{3,4}$"
                    maxlength="4"
                    class="form-control"
                    placeholder="Ej: 123"
                    title="Debe contener 3 o 4 dígitos"
                    inputmode="numeric"
                    autocomplete="cc-csc"
                >
            </div>

        </div>
        <!-- FIN BLOQUE TARJETA -->


        <!-- BLOQUE PAYPAL -->
        <div id="bloquePaypal">
            <div class="mb-3">
                <!-- Email de PayPal opcional -->
                <label class="form-label">PayPal (opcional)</label>
                <input
                    type="email"
                    name="paypal"
                    id="paypal"
                    class="form-control"
                    placeholder="Correo de PayPal"
                    pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                    title="Introduce un correo electrónico válido"
                >
            </div>
        </div>
        <!-- FIN BLOQUE PAYPAL -->


        <button type="submit" class="btn btn-success w-100">
            Actualizar a Premium
        </button>

        <a href="normal.php" class="btn btn-secondary mt-3">
            Volver
        </a>

    </form>

    <script src="../assets/js/validaciones_upgrade.js"></script>

</body>
</html>
