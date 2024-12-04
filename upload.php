<?php
session_start();

$message = "";

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

// Verificar si el usuario está en sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: Debes iniciar sesión para subir archivos.");
}

$usuario_id = $_SESSION['usuario_id']; // Obtenemos el id del usuario en sesión

// Función para obtener la duración de un archivo de audio usando FFmpeg
function getAudioDuration($filePath)
{
    $ffmpegOutput = shell_exec("ffmpeg -i " . escapeshellarg($filePath) . " 2>&1");
    preg_match('/Duration: (\d+):(\d+):(\d+\.\d+)/', $ffmpegOutput, $matches);

    if (isset($matches[1]) && isset($matches[2]) && isset($matches[3])) {
        return sprintf('%02d:%02d:%05.2f', $matches[1], $matches[2], $matches[3]);
    }

    return '00:00:00'; // Valor predeterminado en caso de error
}

// Verifica si se ha enviado un archivo
if (isset($_FILES['file'])) {
    $fileName = basename($_FILES['file']['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileType != 'mp3') {
        $message = "Error: Solo se permiten archivos MP3.";
    } else {
        // Configuración de Supabase
        $bucket = 'Songs'; // Nombre de tu bucket
        $folder = $usuario_id; // Carpeta basada en el ID del usuario
        $filePath = "$folder/$fileName"; // Ruta dentro del bucket
        $supabaseUrl = "https://uckmivnmazucbzijmamc.supabase.co/storage/v1/object/$bucket/$filePath"; // URL para subir
        $supabaseApiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVja21pdm5tYXp1Y2J6aWptYW1jIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Mjk3OTU5NTksImV4cCI6MjA0NTM3MTk1OX0.OOqUMOwZ_yISPi7oYLskdiabmem5zoVd7KspD8WPAfc'; // Cambia por tu clave API

        // Iniciar cURL para subir el archivo a Supabase
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $supabaseApiKey",
            'Content-Type: audio/mpeg'
        ]);

        // Abrir el archivo
        $fileStream = fopen($_FILES['file']['tmp_name'], 'r');
        if (!$fileStream) {
            $message = "Error: No se pudo abrir el archivo para la subida.";
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fileStream, $_FILES['file']['size']));
            $response = curl_exec($ch);
            fclose($fileStream);

            if ($response === false) {
                $message = "Error: " . curl_error($ch);
            } else {
                $message = "El archivo " . htmlspecialchars($fileName) . " ha sido subido con éxito a Supabase.";

                // Obtener la duración real del archivo
                $duracion = getAudioDuration($_FILES['file']['tmp_name']);

                if ($duracion === '00:00:00') {
                    $message = "Error: No se pudo obtener la duración del archivo.";
                } else {
                    // Inserta el registro en la tabla canciones
                    $titulo = pg_escape_string($_POST['titulo']);
                    $artista = pg_escape_string($_POST['artista']);
                    $album = pg_escape_string($_POST['album']);
                    $year = intval($_POST['year']);

                    // URL pública del archivo
                    $fileUrl = "https://uckmivnmazucbzijmamc.supabase.co/storage/v1/object/$bucket/$filePath";

                    // Guarda la URL del archivo en la base de datos
                    $query = "INSERT INTO canciones (titulo, artista, album, year, duracion, archivo, usuario_id) VALUES ('$titulo', '$artista', '$album', $year, '$duracion', '$fileUrl', '$usuario_id')";
                    $result = pg_query($conn, $query);

                    if (!$result) {
                        $message = "Error: No se pudo guardar en la base de datos.";
                    }
                }
            }
        }

        curl_close($ch);
    }
} else {
    $message = "No se ha seleccionado ningún archivo.";
}

// Cierra la conexión y devuelve el mensaje
pg_close($conn);
echo $message;
?>