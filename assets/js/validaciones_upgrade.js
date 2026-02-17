document.addEventListener("DOMContentLoaded", function () {

    const bloqueTarjeta = document.getElementById("bloqueTarjeta");
    const bloquePaypal = document.getElementById("bloquePaypal");

    const tarjeta = document.getElementById("tarjeta");
    const paypal = document.getElementById("paypal");
    const cvv = document.getElementById("cvv");

    /* ============================
        TARJETA OCULTA PAYPAL
    ============================ */
    tarjeta.addEventListener("input", function () {

        const valor = this.value.replace(/\D/g, "");

        if (valor.length > 0) {
            bloquePaypal.style.display = "none";
        } else {
            bloquePaypal.style.display = "block";
        }
    });

    /* ============================
        SOLO NUMEROS CVV
    ============================ */
    cvv.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").substring(0, 4);
    });

    /* ============================
        PAYPAL OCULTA TARJETA
    ============================ */
    paypal.addEventListener("input", function () {

        if (this.value.trim() !== "") {
            bloqueTarjeta.style.display = "none";
        } else {
            bloqueTarjeta.style.display = "block";
        }
    });

});
