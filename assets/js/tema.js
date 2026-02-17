document.addEventListener("DOMContentLoaded", function () {

    const boton = document.getElementById("toggleTema");

    if (!boton) return;

    // Aplicar tema guardado
    if (localStorage.getItem("tema") === "oscuro") {
        document.body.classList.add("modo-oscuro");
        boton.textContent = "☀️";
    } else {
        boton.textContent = "🌙";
    }

    boton.addEventListener("click", function () {

        document.body.classList.toggle("modo-oscuro");

        if (document.body.classList.contains("modo-oscuro")) {
            localStorage.setItem("tema", "oscuro");
            boton.textContent = "☀️";
        } else {
            localStorage.setItem("tema", "claro");
            boton.textContent = "🌙";
        }

    });

});
