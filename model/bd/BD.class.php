<?php

include_once "configs.php";
// Conexión a bases de datos

// Conexión a SQL
class Database {    
    private $db_host;       // Host al que conectar
    private $db_nombre;     // Nombre de la Base de Datos que se desea utilizar
    private $db_user;       // Nombre del usuario con permisos para acceder
    private $db_pass;       // Contraseña de dicho usuario
    private $con;
    private static $instance;


    //Constructor
    private function __construct() {
        $this->db_nombre = BD_NAME;
        $this->db_host = BD_HOST;
        $this->db_user = BD_USER;
        $this->db_pass = BD_PASS;
        $this->conectar();
    }

    // Singleton
    /*Función encargada de crear, si es necesario, el objeto. Esta es la función que se debe llamar para instanciar el objeto, y así, poder utilizar sus métodos*/ 
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance=new $c;
        }
        return self::$instance;
    }


    function conectar() {
        $this->con = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_nombre);
        
        // Revisa errores de conexion
        if(mysqli_connect_errno()) {
            //header("Location:error.php");
            echo mysqli_error($this->con). "\n";
            exit;
        }
        //mysqli_query ("SET NAMES 'utf8'",$this->con);
        mysqli_set_charset($this->con,"utf8");
    }


    function query($query) {
        //var_dump($query);
        $rs = mysqli_query($this->con, $query);
        return $rs;
        $rs =$mysqli->escape_string($rs);   //haciendo un escapestring posible ataque inyeccion sql
    }

    function consulta_punt($query) {
        $rs = mysqli_query($this->con, $query);
        $num_rows = mysqli_num_rows($rs);
        $row = ($num_rows != 0)?mysqli_fetch_array($rs):"";
        return $row;
    }

    function actualiza($actualiza) {
        $rs=mysqli_query($this->con, $actualiza);
        return $rs;
    }

    function cerrar() { 
        mysqli_close($this->con);
    }
    
    function consulta_cant($consulta) {
        $rs = mysqli_query($this->con, $consulta);
        $num_rows=mysqli_num_rows($rs);
        return $num_rows;
    }
    
    public function insert($query){
        //echo "ENTRO AL METODO insert (BD) CON EL query = ".$query."<br>";
        //mysqli_query($query);
        //var_dump($query);
        $rs = mysqli_query($this->con, $query);
        return $rs;
    }
    
    public function select($query){
        //var_dump($query);
        return $this->result = mysqli_query($this->con, $query);
    }

    public function update($query){
        //print_r($query);
        //mysqli_query($this->con, $query);
        $rs=mysqli_query($this->con, $query);
        return $rs;
    }

    public function delete($query){
        //mysqli_query($this->con,$query);
        $rs=mysqli_query($this->con, $query);
        return $rs;
    }

    public function error(){
        mysqli_error($this->con);
    }

    public function get_con(){
        return $this->con;
    }

}
?>