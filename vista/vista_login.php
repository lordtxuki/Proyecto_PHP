<?php
// Iniciamos sesión si no está iniciada para poder usar variables de sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Para que la página sea responsive en dispositivos móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Enlace al CSS de Bootstrap 5.3 para estilos rápidos y responsivos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <!-- Enlace a tu CSS personalizado para estilos propios -->
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100" 
        style="background: linear-gradient(135deg, #667eea, #764ba2);">
    <!-- Contenedor centrado con fondo blanco, padding y sombra -->
    <div class="container bg-white p-4 rounded shadow-lg" style="max-width: 500px;">
        <!-- Título del formulario -->
        <h2 class="text-center mb-4" style="color: #667eea;">Iniciar Sesión</h2>
        <!-- Formulario que envía datos por método POST al controlador login.php -->
        <form action="../controlador/login.php" method="POST">
            <!-- Campo email, obligatorio y tipo email para validación HTML -->
            <div class="mb-3">
                <label for="email" class="form-label" style="color: #555;">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <!-- Campo contraseña, obligatorio y tipo password para ocultar texto -->
            <div class="mb-3">
                <label for="contrasena" class="form-label" style="color: #555;">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <!-- Botón para enviar el formulario -->
            <button class="btn" style="background-color: #667eea; color: white; width: 100%;" type="submit">Ingresar</button>
        </form>

        <?php
        // Mostrar mensaje de error de inicio de sesión guardado en sesión si existe
        if (isset($_SESSION['login_error'])) {
            // Se muestra el mensaje y luego se elimina para que no aparezca siempre
            echo "<p class='text-danger text-center'>" . $_SESSION['login_error'] . "</p>";
            unset($_SESSION['login_error']);
        }
        ?>

        <!-- Enlace para ir a la página de registro si el usuario no tiene cuenta -->
        <p class="text-center mt-3" style="color: #555;">
            ¿No tienes cuenta? <a href="../vista/vista_registro.php" style="color: #667eea;">Regístrate aquí</a>
        </p>
    </div>

    <!-- Scripts JS de Bootstrap para funcionalidades como modal, dropdown, etc. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
