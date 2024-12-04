<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    //echo "Usuario ID en sesión: " . $_SESSION['usuario_id'];
} else {
    echo "No hay usuario en sesión.";
}

// Configuración de conexión a la base de datos
$host = 'aws-0-us-west-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$username = 'postgres.uckmivnmazucbzijmamc';
$password = 'umusicpass!!!';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$username password=$password");

if (!$conn) {
    die("Error: No se puede conectar a la base de datos.");
}

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    echo ("Error: Debes iniciar sesión para subir archivos.");
    exit(); // Termina la ejecución si el usuario no está autenticado
}

$usuario_id = $_SESSION['usuario_id']; // Asegúrate de obtener el id del usuario en sesión correctamente

// Inicializa la variable $canciones
$canciones = [];

// Verifica que $usuario_id sea un entero
if (!is_numeric($usuario_id)) {
    echo "Error: ID de usuario inválido.";
    exit();
}

// Consulta para obtener todas las canciones (sin filtrar por usuario)
$sql = "SELECT * FROM canciones";
$result = pg_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conn));
}

// Obtener todas las canciones
$canciones = pg_fetch_all($result);

// Verificar si se encontraron canciones
if (empty($canciones)) {
    echo " No hay canciones disponibles para este usuario.";
} else {
    // Aquí puedes procesar y mostrar las canciones
    foreach ($canciones as $cancion) {
        //echo "Título: " . htmlspecialchars($cancion['titulo']) . "<br>";
    }
}

// Cerrar la conexión
pg_close($conn);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umusic</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
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
                    <a class="nav-link" href="javascript:location.reload()">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Perfil'; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="perfilDropdown">
                        <?php if (isset($_SESSION['usuario_id'])): // Cambié a usuario_id para verificar la sesión 
                        ?>
                            <button class="dropdown-item text-center" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php">Iniciar Sesión</a>
                        <?php endif; ?>
                    </div>
                </li>

                <!-- Menú desplegable para Subir Contenido -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Subir Contenido
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <form id="uploadForm" class="dropdown-form px-4 py-3" action="upload.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file">Seleccionar archivo:</label>
                                <input type="file" class="form-control-file" id="file" name="file" required>
                            </div>
                            <div class="form-group">
                                <label for="titulo">Título:</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="form-group">
                                <label for="artista">Artista:</label>
                                <input type="text" class="form-control" id="artista" name="artista" required>
                            </div>
                            <div class="form-group">
                                <label for="album">Álbum:</label>
                                <input type="text" class="form-control" id="album" name="album">
                            </div>
                            <div class="form-group">
                                <label for="year">Año:</label>
                                <input type="number" class="form-control" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Subir Archivo</button>
                        </form>


                        <!-- Contenedor de mensaje -->
                        <div id="message-box" class="message-box mt-3"></div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Bienvenido a Umusic</h1>
            <p>Explora y disfruta de tus canciones favoritas</p>

            <!--Carrusel de imagenes-->
            <div class="carousel">
                <div class="carousel-inner">
                    <?php
                    $dir = 'imagenes/'; // Ruta de la carpeta de imágenes
                    $images = glob($dir . "*.{jpg}", GLOB_BRACE); // Busca imágenes con extensiones .jpg
                    $active = 'active';

                    foreach ($images as $image) {
                        echo '<div class="carousel-item ' . $active . '">';
                        echo '<img src="' . $image . '" alt="Imagen del Carrusel">';
                        echo '</div>';
                        $active = ''; // Solo la primera imagen lleva la clase 'active'
                    }
                    ?>
                </div>
                <span class="prev" onclick="moveSlide(-1)">&#10094;</span>
                <span class="next" onclick="moveSlide(1)">&#10095;</span>
            </div>
        </div>
    </section>
    <script src="js/scripts.js"></script>

    <?php
    // Mostrar el mensaje de éxito si está presente en la sesión
    if (isset($_SESSION['success'])): ?>
        <div id="message" class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); // Borrar el mensaje después de mostrarlo 
        ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div id="message" class="alert alert-danger">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); // Borrar el mensaje después de mostrarlo 
        ?>
    <?php endif; ?>

    <section class="content-section">
        <div class="container">
            <h2 class="color">Explorar Contenidos</h2>
            <div class="row">
                <?php foreach ($canciones as $cancion): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($cancion['titulo']) ?> - <?= htmlspecialchars($cancion['artista']) ?></h5>
                                <audio controls>
                                    <source src="<?= htmlspecialchars($cancion['archivo']) ?>" type="audio/mp3">
                                    Tu navegador no soporta el reproductor de audio.
                                </audio>
                                <!-- Botón de eliminación -->
                                <form method="post" action="delete_song.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta canción?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($cancion['id']) ?>">
                                    <button type="submit" class="btn btn-light mx-2">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <?php
    // Cerrar conexión
    $pdo = null;
    ?>


    <div class="button-container">
        <button id="btn-prev" class="btn btn-light mx-2">⏮ Anterior</button>
        <button id="btn-play-pause" class="btn btn-light mx-2">⏯ Reproducir</button>
        <button id="btn-next" class="btn btn-light mx-2">⏭ Siguiente</button>
    </div>
    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Selección de todos los elementos de audio
        const audios = document.querySelectorAll("audio");
        let currentAudioIndex = 0;

        // Seleccionar botones
        const playPauseButton = document.getElementById("btn-play-pause");
        const prevButton = document.getElementById("btn-prev");
        const nextButton = document.getElementById("btn-next");

        // Función para reproducir o pausar el audio actual
        function togglePlayPause() {
            const currentAudio = audios[currentAudioIndex];

            if (currentAudio.paused) {
                currentAudio.play();
                playPauseButton.textContent = "⏸ Pausar";
            } else {
                currentAudio.pause();
                playPauseButton.textContent = "⏯ Reproducir";
            }
        }

        // Función para ir al siguiente audio
        function playNextAudio() {
            if (audios.length === 0) return; // Evitar errores si no hay audios

            audios[currentAudioIndex].pause(); // Pausar el actual
            audios[currentAudioIndex].currentTime = 0; // Reiniciar tiempo

            currentAudioIndex = (currentAudioIndex + 1) % audios.length; // Avanzar y hacer ciclo si es necesario
            audios[currentAudioIndex].play();
            playPauseButton.textContent = "⏸ Pausar";
        }

        // Función para ir al audio anterior
        function playPrevAudio() {
            if (audios.length === 0) return; // Evitar errores si no hay audios

            audios[currentAudioIndex].pause();
            audios[currentAudioIndex].currentTime = 0;

            currentAudioIndex = (currentAudioIndex - 1 + audios.length) % audios.length; // Retroceder y ciclar
            audios[currentAudioIndex].play();
            playPauseButton.textContent = "⏸ Pausar";
        }

        // Event Listeners para botones
        playPauseButton.addEventListener("click", togglePlayPause);
        nextButton.addEventListener("click", playNextAudio);
        prevButton.addEventListener("click", playPrevAudio);
    </script>

    <!-- JavaScript para manejo de la carga de archivos -->
    <script>
        document.getElementById("uploadForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const messageBox = document.getElementById("message-box");

            fetch("upload.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    messageBox.textContent = data;
                    if (data.includes("ha sido subido con éxito")) {
                        messageBox.classList.add("alert", "alert-success");
                        setTimeout(() => location.reload(), 1500); // Recarga después de 1.5 segundos
                    } else {
                        messageBox.classList.add("alert", "alert-danger");
                    }
                })
                .catch(error => {
                    messageBox.textContent = "Error al subir el archivo.";
                    messageBox.classList.add("alert", "alert-danger");
                });
        });
    </script>

    <script>
        // Si existe el mensaje, ocúltalo después de 2 segundos
        window.onload = function() {
            var message = document.getElementById('message');
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none'; // Oculta el mensaje
                }, 2000);
            }
        };
    </script>

</body>

</html>