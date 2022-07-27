<?php

include_once "configs.php";
// Conexión a bases de datos

// Conexión a SQL
class Database_Remote_SQLServer {	
    private $db_host;       // Host al que conectar
    private $db_nombre;     // Nombre de la Base de Datos que se desea utilizar
    private $db_user;       // Nombre del usuario con permisos para acceder
    private $db_pass;       // Contraseña de dicho usuario
    private $connectionInfo; //array de conexion
    private $serverName;// nombre de la conexion
    private $con_remote;
    private static $instance;
  
    //Constructor
    private function __construct() {
        $this->db_nombre = BD_NAME_REMOTE_SQLServer;
        $this->db_host = BD_HOST_REMOTE_SQLServer;
        $this->db_user = BD_USER_REMOTE_SQLServer;
        $this->db_pass = BD_PASS_REMOTE_SQLServer;
        $this->connectionInfo = array ("Database" => $this->db_nombre, "UID" => $this->db_user, "PWD" => $this->db_pass);
        $this->serverName = "{$this->db_host}\\{$this->db_nombre}, 1433";
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
        $this->con_remote = sqlsrv_connect($this->serverName, $this->connectionInfo);        
        // Revisa errores de conexion
        echo "aca funciona algo";
        if($this->con_remote === false) {  

            //header("Location:error.php");
            echo "aca falla algo";
            die( print_r( sqlsrv_errors(), true));           
        }
        //mysql_query ("SET NAMES 'utf8'",$this->con_remote);
    }
    










    function query($query) {
        $rs = sqlsrv_query( $this->con_remote, $query);
        if( $rs === false ) {
            var_dump($query);
             die( print_r( sqlsrv_errors(), true));
        }

        //$rs = mysql_query($query,$this->con_remote);
		//$this->cerrar();
        return $rs;
    }

    function consulta_punt($query) {
        $rs = mysql_query($query,$this->con_remote);
        $num_rows = mysqli_num_rows($rs);
        $row = ($num_rows != 0)?mysqli_fetch_array($rs):"";
        return $row;
    }

    function actualiza($actualiza) {
        $rs=mysql_query($actualiza,$this->con_remote);
        return $rs;
    }

    function cerrar() {
        mysql_close($this->con_remote);
    }
	
	function consulta_cant($consulta) {
        $rs = mysql_query($consulta,$this->con_remote);
        $num_rows=mysqli_num_rows($rs);
        return $num_rows;
    }
    
    public function insert($query){
        mysql_query($query);
    }
	
    public function select($query){
        return $this->result = mysql_query($query);
    }

    public function update($query){
        mysql_query($query);
    }

    public function delete($query){
        mysql_query($query);
    }
}
?>
