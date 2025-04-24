<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Musickaly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="assets/logo.png" alt="Logo" width="60" height="60" class="d-inline-block align-text-top">
                Musickaly
            </a>
            <div class="d-flex ms-auto">
                <a href="vista/vista_login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                <a href="vista/vista_registro.php" class="btn btn-light">Registrarse</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Bienvenido a Musickaly</h1>
        <p class="text-center">Disfruta de contenido gratuito y explora lo que ofrecemos. Regístrate para tener acceso completo a todas las funcionalidades.</p>

        <div id="carouselArtistas" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/artist1.jpg" class="d-block w-100" alt="Artista 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/artist2.jpg" class="d-block w-100" alt="Artista 2">
                </div>
                <div class="carousel-item">
                    <img src="assets/artist3.jpg" class="d-block w-100" alt="Artista 3">
                </div>
                <div class="carousel-item">
                    <img src="assets/artist4.jpg" class="d-block w-100" alt="Artista 4">
                </div>
                <div class="carousel-item">
                    <img src="assets/artist5.jpg" class="d-block w-100" alt="Artista 5">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselArtistas" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselArtistas" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-warning">⭐⭐⭐⭐⭐</h5>
                        <p class="card-text">"Musickaly es simplemente increíble. Todo lo que necesito está ahí, ¡y es gratis!"</p>
                        <p class="text-muted text-end mb-0">– Laura M.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-warning">⭐⭐⭐⭐⭐</h5>
                        <p class="card-text">"No esperaba tanto contenido sin pagar nada. Una app muy recomendable."</p>
                        <p class="text-muted text-end mb-0">– Javier R.</p>
                    </div>
                </div>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>