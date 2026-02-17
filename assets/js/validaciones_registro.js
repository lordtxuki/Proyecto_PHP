document.addEventListener("DOMContentLoaded", function () {

    const tipoCuenta = document.getElementById("tipoCuenta");
    const datosPago = document.getElementById("datosPago");

    const tarjeta = document.getElementById("tarjeta");
    const paypal = document.getElementById("paypal");

    const bloqueTarjeta = document.getElementById("bloqueTarjeta");
    const bloquePaypal = document.getElementById("bloquePaypal");

    const vencimiento = document.getElementById("vencimiento");
    const cvv = document.getElementById("cvv");

    /* ============================
        MOSTRAR DATOS PAGO
    ============================ */
    if (tipoCuenta) {
        tipoCuenta.addEventListener("change", function () {
            datosPago.style.display = this.value === "premium" ? "block" : "none";
        });
    }

    /* ============================
        FORMATEAR TARJETA
    ============================ */
    if (tarjeta) {
        tarjeta.addEventListener("input", function () {

            let valor = this.value.replace(/\D/g, "").substring(0, 16);
            const partes = valor.match(/.{1,4}/g);
            this.value = partes ? partes.join(" ") : "";

            if (valor.length > 0) {
                bloquePaypal.style.display = "none";
            } else {
                bloquePaypal.style.display = "block";
            }

        });
    }

    /* ============================
        SOLO NÚMEROS CVV
    ============================ */
    if (cvv) {
        cvv.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").substring(0, 4);
        });
    }

    /* ============================
        PAYPAL OCULTA TARJETA
    ============================ */
    if (paypal) {
        paypal.addEventListener("input", function () {

            if (this.value.trim() !== "") {
                bloqueTarjeta.style.display = "none";
            } else {
                bloqueTarjeta.style.display = "block";
            }

        });
    }

});
