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
        <button type="submit" class="btn btn-success w-100">Actualizar a Premium</button>
        <a href="normal.php" class="btn btn-secondary mt-3">Volver</a> 
    </form>

    <script>
    // Validación previa en JS al enviar el formulario
    document.getElementById('formPremium').addEventListener('submit', function(e) {
        const tarjeta = document.getElementById('tarjeta').value.trim();
        const vencimiento = document.getElementById('vencimiento').value.trim();
        const cvv = document.getElementById('cvv').value.trim();
        const paypal = document.getElementById('paypal').value.trim();

        // Validamos que se haya puesto tarjeta o paypal (al menos uno)
        if (!tarjeta && !paypal) {
            alert('Debes ingresar datos de tarjeta o un correo de PayPal.');
            e.preventDefault(); // Evita el envío
            return;
        }

        // Si hay tarjeta, validar formato tarjeta, vencimiento y cvv
        if (tarjeta) {
            if (!/^\d{16}$/.test(tarjeta)) {
                alert('La tarjeta debe contener exactamente 16 dígitos.');
                e.preventDefault();
                return;
            }
            if (!vencimiento) {
                alert('Debes ingresar la fecha de vencimiento de la tarjeta.');
                e.preventDefault();
                return;
            }
            if (!/^\d{3,4}$/.test(cvv)) {
                alert('El CVV debe contener 3 o 4 dígitos.');
                e.preventDefault();
                return;
            }
        }

        // Si hay paypal, validar formato de email
        if (paypal) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(paypal)) {
                alert('Introduce un correo electrónico de PayPal válido.');
                e.preventDefault();
                return;
            }
        }
    });
    </script>
</body>
</html>
