<?php
session_start();
session_destroy();
header("Location: vista/vista_login.php");
exit();

?>