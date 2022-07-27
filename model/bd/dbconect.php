<?php
// simple conexion a la base de datos
function connect(){
	//return new mysqli("127.0.0.1","root","","php_importarexcel");
    return new mysqli("127.0.0.1:3308","root","","test_correos");
}
$con = connect();
//echo "ENTRE AL SCRIPT dbconnect()"."<br>";
if (!$con->set_charset("utf8")) {//asignamos la codificación comprobando que no falle
       die("Error cargando el conjunto de caracteres utf8");
       //echo "NO HAY CONEXION CON LA BASE DE DATOS"."<br>";
}
?>

