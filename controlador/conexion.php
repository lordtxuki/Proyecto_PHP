<?php
// Creo la conexión a la base de datos usando MySQLi con los datos del servidor local
$conexion = new mysqli("localhost", "root", "", "streaming");

// Compruebo si hubo un error al conectar y si es así paro la ejecución mostrando el error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
