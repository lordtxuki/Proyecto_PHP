<?php
class ModeloBase {
    protected $conexion; // Variable para almacenar la conexión a la base de datos

    public function __construct() {
        // Crear conexión a la base de datos MySQL usando mysqli
        $this->conexion = new mysqli("localhost", "root", "", "streaming");

        // Comprobar si hubo un error al conectar
        if ($this->conexion->connect_error) {
            // Detener la ejecución y mostrar el error de conexión
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }
}
?>
