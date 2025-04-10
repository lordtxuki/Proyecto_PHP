<?php

class Usuario{
    protected $id;
    protected $email;
    protected $contrasena;
    protected $nombre_usuario;
    protected $fecha_nac;
    protected $genero;
    protected $pais;
    protected $codigo_postal;
    protected $imagen_perfil;

    public function __construct($id,$email,$contrasena,$nombre_usuario,$fecha_nac,$genero,$pais,$codigo_postal,$imagen_perfil){
        $this->id = $id;
        $this->email = $email;
        $this->contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
        $this->usuario = $usuario;
        $this->fecha_nac = $fecha_nac;
        $this->genero = $genero;
        $this->pais = $pais;
        $this->codigo_postal = $codigo_postal;
        $this->imagen_perfil = $imagen_perfil;
    }

    public function obtenerID() {
        return $this->id;
    }

    
}


?>