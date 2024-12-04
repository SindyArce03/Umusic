<?php
// Conectar a la base de datos
$host = "aws-0-us-west-1.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$user = "postgres.uckmivnmazucbzijmamc";
$password = "umusicpass!!!";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos.");
}

// Iniciar sesión al principio
session_start();

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['pass'];

    // Validar que el correo no esté ya registrado
    $result = pg_query_params($conn, "SELECT * FROM usuarios WHERE correo = $1", array($correo));

    if (pg_num_rows($result) > 0) {
        // El correo ya está registrado
        echo "<script>alert('El usuario ya está registrado.'); window.location.href='index.php';</script>";
    } else {
        // Insertar nuevo usuario
        $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT); // Encriptar la contraseña
        $insert_query = "INSERT INTO usuarios (nombre, correo, pass, fecha_registro) VALUES ($1, $2, $3, NOW()) RETURNING id"; // Añadir RETURNING
        $insert_result = pg_query_params($conn, $insert_query, array($nombre, $correo, $hashed_password));

        if ($insert_result) {
            // Obtener el ID del usuario recién creado
            $user_id = pg_fetch_result($insert_result, 0, 'id'); // Obtener el ID
            $_SESSION['usuario_id'] = $user_id; // Guardar el ID del usuario en la sesión
            $_SESSION['nombre'] = $nombre; // Guardar el nombre en la sesión
            header("Location: index.php"); // Redirigir a index.php
            exit();
        } else {
            echo "<script>alert('Error al registrar el usuario.'); window.location.href='login.php';</script>";
        }
    }
}

pg_close($conn);
?>
