<?php
// Inicia la sesión para poder manipularla
session_start();
// Destruye toda la información de la sesión actual (cierra sesión)
session_destroy();
// Redirige al usuario a la página de login después de cerrar sesión
header("Location: vista/vista_login.php");
// Termina la ejecución del script para asegurarse de que no se ejecuta nada más
exit();
?>
