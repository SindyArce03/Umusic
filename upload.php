<?php
// Verifica si se ha enviado un archivo
if (isset($_FILES['file'])) {
    // Directorio donde se guardarán los archivos
    $uploadDir = 'uploads/';

    // Crea el directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Nombre original del archivo
    $fileName = basename($_FILES['file']['name']);

    // Ubicación completa donde se guardará el archivo
    $uploadFile = $uploadDir . $fileName;

    // Verifica el tipo de archivo (solo permite MP3)
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    if ($fileType != 'mp3') {
        echo "Error: Solo se permiten archivos MP3.";
    } else {
        // Mueve el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            echo "El archivo " . htmlspecialchars($fileName) . " ha sido subido con éxito.";
        } else {
            echo "Error: Hubo un problema al subir tu archivo.";
        }
    }
} else {
    echo "No se ha seleccionado ningún archivo.";
}
?>
