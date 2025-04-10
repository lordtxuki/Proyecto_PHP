<?php
class ModeloBase {
    protected $conexion;

    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "streaming");
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }
}
?>
