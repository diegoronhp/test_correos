<?php
/*
CLASE PARA LA EJECUCION DE LAS PRUEBAS DE CORREO
*/
require_once "../model/bd/BD.class.php";

class Correo{
    private $bd;
    private $correo;

    public function __construct() {
        $this->bd = Database::getInstance();
        $this->correo = array();
    }

    public function consultar($query){
        return $this->bd->select($query);
    }

    public function contar_filas($query){
        return $this->bd->consulta_cant($query);
    }

    public function insertar($query){
        return $this->bd->insert($query);
    }

    public function actualizar($query){
        return $this->bd->update($query);
    }

    public function eliminar($query){
        return $this->bd->delete($query);
    }

    function consultar_campos($query) {
        return $this->bd->consulta_punt($query);
    }

    function get_conection(){
        return $this->bd->get_con();
    }

}

?>