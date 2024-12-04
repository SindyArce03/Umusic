<?php
session_start();

// Datos de conexión a la base de datos
$host = 'aws-0-us-west-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$username = 'postgres.uckmivnmazucbzijmamc';
$password = 'umusicpass!!!';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verifica si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera los datos del formulario
    $nombre = $_POST['nombre'];
    $contraseña = $_POST['pass'];

    // Consulta SQL para verificar las credenciales
    $sql = "SELECT id, nombre, pass FROM usuarios WHERE nombre = :nombre"; // Asegúrate de seleccionar también el nombre
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->execute();

    // Si existe un usuario con ese nombre
    if ($stmt->rowCount() == 1) {
        // Recupera el id, nombre y la contraseña almacenada
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = $user['pass'];

        // Verificar la contraseña
        if (password_verify($contraseña, $hashed_password)) {
            // Usuario encontrado, iniciar sesión
            $_SESSION['usuario_id'] = $user['id']; // Guarda el id en la sesión
            $_SESSION['nombre'] = $user['nombre']; // Guarda el nombre en la sesión
        
            echo "Inicio de sesión exitoso. Usuario ID: " . $_SESSION['usuario_id'];
        
            // Redireccionar a la página principal
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y registro</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Fondo decorativo -->
    <div class="background"></div>
    <main>
        <div class="contenedor__todo">
            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar en la página</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera-register">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Regístrate para que puedas iniciar sesión</p>
                    <button id="btn__registrarse">Regístrarse</button>
                </div>
            </div>

            <!-- Formulario de Login y registro -->
            <div class="contenedor__login-register">
                <!-- Login -->
                <form action="login.php" method="POST" class="formulario__login">
                    <h2>Iniciar Sesión</h2>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="password" name="pass" placeholder="Contraseña" required>
                    <button type="submit">Entrar</button>
                </form>

                <!-- Register -->
                <form action="registro.php" method="POST" class="formulario__register">
                    <h2>Regístrarse</h2>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="correo" placeholder="Correo Electrónico" required>
                    <input type="password" name="pass" placeholder="Contraseña" required>
                    <button type="submit">Regístrarse</button>
                </form>
            </div>
        </div>
    </main>

    <script src="js/funciones.js"></script>
</body>
</html>
