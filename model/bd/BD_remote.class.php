<?php

include_once "configs.php";
// Conexión a bases de datos

// Conexión a SQL
class Database_Remote {	
    private $db_host;       // Host al que conectar
    private $db_nombre;     // Nombre de la Base de Datos que se desea utilizar
    private $db_user;       // Nombre del usuario con permisos para acceder
    private $db_pass;       // Contraseña de dicho usuario
    private $con_remote;
    private static $instance = NULL;


    //Constructor
    private function __construct() {
        $this->db_nombre = BD_NAME_REMOTE;
        $this->db_host = BD_HOST_REMOTE;
        $this->db_user = BD_USER_REMOTE;
        $this->db_pass = BD_PASS_REMOTE;
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

//conexion de prueba
   function conectar() {
        $this->con_remote=mysqli_connect($this->db_host, $this->db_user, $this->db_pass);
        
        // Revisa errores de conexion
        if($this->con_remote==false) {
            //header("Location:error.php");
            echo mysqli_error($this->con_remote). "\n";
            echo "no entro error";
            exit;
        }
        mysqli_query ($this->con_remote,"SET NAMES 'utf8'");

    }






//conexio prueba 3

  /*  

function conectar() {
        $this->con_remote=@mysqli_connect($this->db_host, $this->db_user, $this->db_pass);
        
        // Revisa errores de conexion
        if($this->con_remote==false) {
            //header("Location:error.php");
            echo mysqli_error($this->con_remote). "\n";
            echo "no ingreso al sistema-PROBLEMAS EN LA BD-DESARROLLO TIGO";
            exit;
        }elseif(@mysqli_select_db($this->db_nombre ,$this->con_remote)==false) {
            //header("Location:error.php");         
            echo mysqli_error($this->con_remote). "\n";
          //  echo "ingreso correctamente ";
            exit;
        }
        mysqli_query ("SET NAMES 'utf8'",$this->con_remote);
    }
 */
    
//conexion d eprueba 2





//////////////////conexion de antes

/*
   function conectar() {
        $this->con_remote = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_nombre);
        
        // Revisa errores de conexion
        if(mysqli_connect_errno()) {
            //header("Location:error.php");
            echo mysqli_error($this->con_remote). "\n";
            echo "No conecto...";
            exit;
        }
        //mysqli_query ("SET NAMES 'utf8'",$this->con);
        mysqli_set_charset($this->con_remote,"utf8");
    }

*/
//////////////conexion de antes

    function query($query) {
        $rs = mysqli_query($this->con_remote, $query);
        return $rs;
    }

    function consulta_punt($query) {
        $rs = mysqli_query($this->con_remote, $query);
        $num_rows = mysqli_num_rows($rs);
        $row = ($num_rows != 0)?mysqli_fetch_array($rs):"";
        return $row;
    }

    function actualiza($actualiza) {
        $rs=mysqli_query($actualiza,$this->con_remote);
        return $rs;
    }

    function cerrar() {
        mysqli_close($this->con_remote);
    }
    
    function consulta_cant($consulta) {
        $rs = mysqli_query($consulta,$this->con_remote);
        $num_rows=mysqli_num_rows($rs);
        return $num_rows;
    }
    
    public function insert($query){
        mysqli_query($query);
    }
    
    public function select($query){
        $this->result = mysqli_query($this->con_remote,$query);
        //$this->cerrar();
       
        return $this->result;
    }

    public function update($query){
        mysqli_query($query);
    }

    public function delete($query){
        mysqli_query($query);
    }
}
?>
