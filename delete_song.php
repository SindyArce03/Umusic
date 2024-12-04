<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['error'] = "Error: Debes iniciar sesión para eliminar archivos.";
    header("Location: index.php");  // Redirige a la página principal si no está logueado
    exit();
}

// Configuración de conexión a la base de datos
$host = 'aws-0-us-west-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$username = 'postgres.uckmivnmazucbzijmamc';
$password = 'umusicpass!!!';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$username password=$password");

if (!$conn) {
    $_SESSION['error'] = "Error: No se puede conectar a la base de datos.";
    header("Location: index.php");  // Redirige si la conexión falla
    exit();
}

// Verifica que se haya recibido el id de la canción
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $_SESSION['error'] = "Error: ID de canción inválido.";
    header("Location: index.php");
    exit();
}

$cancion_id = $_POST['id'];  // Aquí se toma el ID de la canción del formulario

// Consulta para eliminar la canción asegurando que el usuario es el propietario
$sql = "DELETE FROM canciones WHERE id = $1 AND usuario_id = $2";
$result = pg_query_params($conn, $sql, array($cancion_id, $_SESSION['usuario_id']));

if ($result) {
    $_SESSION['success'] = "Canción eliminada exitosamente.";
} else {
    $_SESSION['error'] = "Error al eliminar la canción: " . pg_last_error($conn);
}


// Cierra la conexión
pg_close($conn);

// Redirige a la página principal para mostrar el mensaje
header("Location: index.php");
exit();
?>
