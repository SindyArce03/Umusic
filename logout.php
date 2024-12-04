<?php
// Verifica que la sesión esta iniciada
session_start();

// Elimina todas las variables de inicio de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Regresa a la página de inicio
header("Location: login.php");
exit();
?>
