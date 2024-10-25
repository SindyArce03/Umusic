<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umusic</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">       
</head>
<body>

    <!-- Fondo decorativo -->
    <div class="background"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Umusic</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Perfil</a>
                </li>
                <!-- Menú desplegable para Subir Contenido -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Subir Contenido
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <form class="dropdown-form px-4 py-3" action="upload.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file">Seleccionar archivo:</label>
                                <input type="file" class="form-control-file" id="file" name="file">
                            </div>
                            <button type="submit" class="btn btn-primary">Subir Archivo</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Bienvenido a Umusic</h1>
            <p>Explora, comparte y disfruta de tus canciones favoritas</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <h2 class="color">Explorar Contenidos</h2>
            <div class="row">
                <?php
                $dir = 'uploads/';
                if (is_dir($dir)) {
                    if ($dh = opendir($dir)) {
                        while (($file = readdir($dh)) !== false) {
                            if ($file != '.' && $file != '..') {
                                echo "<div class='col-md-4'>
                                        <div class='card'>
                                            <div class='card-body'>
                                                <h5 class='card-title'>" . htmlspecialchars($file) . "</h5>
                                                <audio controls>
                                                    <source src='" . $dir . $file . "' type='audio/mp3'>
                                                    Tu navegador no soporta el reproductor de audio.
                                                </audio>
                                            </div>
                                        </div>
                                      </div>";
                            }
                        }
                        closedir($dh);
                    }
                }
                ?>
            </div>
        </div>
    </section>
    
    <div class="button-container">
        <button class="btn btn-light mx-2">⏮ Anterior</button>
        <button class="btn btn-light mx-2">⏯ Reproducir</button>
        <button class="btn btn-light mx-2">⏭ Siguiente</button>
    </div>
</div>
    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
