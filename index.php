<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Codificación de caracteres para que se muestren bien los caracteres especiales -->
    <meta charset="UTF-8">
    <!-- Título que aparece en la pestaña del navegador -->
    <title>Musickaly</title>
    <!-- Configuración para que la página sea responsiva en dispositivos móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlace al CSS de Bootstrap versión 5.3 desde CDN para estilos rápidos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace a tu hoja de estilos personalizada -->
    <link rel="stylesheet" href="styles/index.css">
</head>
<body class="bg-light">
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <!-- Contenedor fluido para que la barra ocupe todo el ancho -->
        <div class="container-fluid">
            <!-- Logo y nombre de la marca que actúa como enlace -->
            <a class="navbar-brand" href="#">
                <!-- Imagen del logo con tamaño fijo y alineación -->
                <img src="assets/logo.png" alt="Logo" width="60" height="60" class="d-inline-block align-text-top">
                Musickaly
            </a>
            <!-- Contenedor con botones alineados a la derecha -->
            <div class="d-flex ms-auto">
                <!-- Botón para ir a la página de login con estilo outline -->
                <a href="vista/vista_login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                <!-- Botón para ir a la página de registro con fondo blanco -->
                <a href="vista/vista_registro.php" class="btn btn-light">Registrarse</a>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal con margen arriba -->
    <div class="container mt-5">
        <!-- Título principal centrado con margen inferior -->
        <h1 class="text-center mb-4">Bienvenido a Musickaly</h1>
        <!-- Texto de presentación centrado -->
        <p class="text-center">Disfruta de contenido gratuito y explora lo que ofrecemos. Regístrate para tener acceso completo a todas las funcionalidades.</p>

        <!-- Carrusel de artistas usando Bootstrap -->
        <div id="carouselArtistas" class="carousel slide" data-bs-ride="carousel">
            <!-- Contenedor para los items del carrusel -->
            <div class="carousel-inner">
                <!-- Primer item activo (visible) con imagen de artista -->
                <div class="carousel-item active">
                    <img src="assets/artist1.jpg" class="d-block w-100" alt="Artista 1">
                </div>
                <!-- Segundo item -->
                <div class="carousel-item">
                    <img src="assets/artist2.jpg" class="d-block w-100" alt="Artista 2">
                </div>
                <!-- Tercer item -->
                <div class="carousel-item">
                    <img src="assets/artist3.jpg" class="d-block w-100" alt="Artista 3">
                </div>
                <!-- Cuarto item -->
                <div class="carousel-item">
                    <img src="assets/artist4.jpg" class="d-block w-100" alt="Artista 4">
                </div>
                <!-- Quinto item -->
                <div class="carousel-item">
                    <img src="assets/artist5.jpg" class="d-block w-100" alt="Artista 5">
                </div>
            </div>
            <!-- Botón para ir al slide anterior -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselArtistas" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <!-- Botón para ir al slide siguiente -->
            <button class="carousel-control-next" type="button" data-bs-target="#carouselArtistas" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Fila para tarjetas con testimonios -->
        <div class="row mt-5">
            <!-- Columna para primer testimonio -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <!-- Estrellas en texto con color amarillo -->
                        <h5 class="card-title text-warning">⭐⭐⭐⭐⭐</h5>
                        <!-- Texto del testimonio -->
                        <p class="card-text">"Musickaly es simplemente increíble. Todo lo que necesito está ahí, ¡y es gratis!"</p>
                        <!-- Autor del testimonio con texto gris y alineado a la derecha -->
                        <p class="text-muted text-end mb-0">– Laura M.</p>
                    </div>
                </div>
            </div>

            <!-- Columna para segundo testimonio -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-warning">⭐⭐⭐⭐⭐</h5>
                        <p class="card-text">"No esperaba tanto contenido sin pagar nada. Una app muy recomendable."</p>
                        <p class="text-muted text-end mb-0">– Javier R.</p>
                    </div>
                </div>
            </div>

            <!-- Columna para tercer testimonio -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-warning">⭐⭐⭐⭐⭐</h5>
                        <p class="card-text">"Una experiencia excelente, fácil de usar y muy entretenida. ¡Musickaly lo tiene todo!"</p>
                        <p class="text-muted text-end mb-0">– Marcos G.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Bootstrap para que funcionen componentes como el carrusel -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
