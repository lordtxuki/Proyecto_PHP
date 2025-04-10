<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100" style="background: linear-gradient(135deg, #667eea, #764ba2);">
    <div class="container bg-white p-4 rounded shadow-lg" style="max-width: 500px;">
        <h2 class="text-center mb-4" style="color: #667eea;">Iniciar Sesión</h2>
        <form action="../controlador/login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label" style="color: #555;">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label" style="color: #555;">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <button class="btn" style="background-color: #667eea; color: white; width: 100%;" type="submit">Ingresar</button>
        </form>

        <?php
        if (isset($_SESSION['login_error'])) {
            echo "<p class='text-danger text-center'>" . $_SESSION['login_error'] . "</p>";
            unset($_SESSION['login_error']);
        }
        ?>

        <p class="text-center mt-3" style="color: #555;">¿No tienes cuenta? <a href="../vista/vista_registro.php" style="color: #667eea;">Regístrate aquí</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
