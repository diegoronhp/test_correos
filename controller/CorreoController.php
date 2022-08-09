<?php
//error_reporting(E_ALL);
error_reporting(0);

require '../model/Correo.class.php';

if((isset($_POST["dir_email"]))&&(isset($_POST["alias_email"]))&&(isset($_POST["pswd_email"]))&&(isset($_POST["serv_smtp"]))&&(isset($_POST["port_smtp"]))&&(isset($_POST["serv_imap"]))&&(isset($_POST["port_imap"]))){
    //echo "DETECTO EL EVENTO POST DEL FORMULARIO EN EL QUE SE AGREGA LA NUEVA CUENTA DE PRUEBA"."<br>";
    $mensaje = "";
    $dir_email = strtolower($_POST["dir_email"]);
    $alias_email = strtoupper($_POST["alias_email"]);
    $pswd_email = $_POST["pswd_email"];
    $serv_smtp = $_POST["serv_smtp"];
    $port_smtp = $_POST["port_smtp"];
    $serv_imap = "{".$_POST["serv_imap"]."}";
    $port_imap = $_POST["port_imap"];
    //echo "dir_email = ".$dir_email."<br>";
    //echo "alias_email = ".$alias_email."<br>";
    //echo "pswd_email = ".$pswd_email."<br>";
    //echo "serv_smtp = ".$serv_smtp."<br>";
    //echo "port_smtp = ".$port_smtp."<br>";
    //echo "serv_imap = ".$serv_imap."<br>";
    //echo "port_imap = ".$port_imap."<br>";
    $existe = comprobar_cuenta_correo($dir_email,$alias_email);
    if($existe){
        //echo "LA CUENTA DE CORREO = ".$dir_email." O EL ALIAS DE LA CUENTA **SI** EXISTEN PARA ALGUNA REGISTRO EN LA BD"."<br>";
        $mensaje = "La cuenta de correo (".$dir_email.") o el alias de cuenta (".$alias_email.") ya existen para algun registro de la base de datos, por favor intente de nuevo con datos válidos para la cuenta por agregar";
    }else{
        //echo "LA CUENTA DE CORREO = ".$dir_email." O EL ALIAS DE LA CUENTA **NO** EXISTEN EN LA BASE DE DATOS"."<br>";
        $mensaje = insertar_nueva_cuenta_correo($dir_email,$alias_email,$pswd_email,$serv_smtp,$port_smtp,$serv_imap,$port_imap);
    }

    $respuesta = array("mensaje"=>$mensaje);
    echo json_encode($respuesta);
}



function insertar_nueva_cuenta_correo($email,$alias,$pswd,$serv_smtp,$port_smtp,$serv_imap,$port_imap){
    //echo "ENTRO AL METODO insertar_nueva_cuenta_correo"."<br>";
    //echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    //echo "email = ".$email."<br>";
    //echo "alias = ".$alias."<br>";
    //echo "pswd = ".$pswd."<br>";
    //echo "serv_smtp = ".$serv_smtp."<br>";
    //echo "port_smtp = ".$port_smtp."<br>";
    //echo "serv_imap = ".$serv_imap."<br>";
    //echo "port_imap = ".$port_imap."<br>";
    $mensaje = "";
    $cont = 0;
    $objCorreo = new Correo();
    $query = "INSERT INTO cuenta_correo(alias_cuenta_correo,direccion_email,password_email,servidor_smtp,puerto_smtp,servidor_imap,puerto_imap) VALUES('".$alias."','".$email."','".$pswd."','".$serv_smtp."','".$port_smtp."','".$serv_imap."','".$port_imap."')";
    $insertado = $objCorreo->insertar($query);

    //echo "REGISTRO INSERTADO ? ";
    //echo $insertado ? "TRUE"."<br>":"FALSE"."<br>";

    if($insertado){
        //echo "AHORA BUSCO EL DATO DEL id_cuenta_correo ASIGNADO PARA LA CUENTA = ".$email."<br>";
        $query = "SELECT id_cuenta_correo FROM cuenta_correo WHERE alias_cuenta_correo = '".$alias."' AND direccion_email = '".$email."'";
        //echo "query = ".$query."<br>";
        $resultado = $objCorreo->consultar_campos($query);
        $id_cuenta_correo = $resultado['id_cuenta_correo'];
        //echo "id_cuenta_correo = ".$id_cuenta_correo."<br>";
        //echo "AHORA BUSCO EL TIEMPO LIMITE ESTABLECIDO COMO LATENCIA SOLO PARA LA PRIMERA PRUEBA PARA ASIGNARLO A LAS NUEVAS PRUEBAS POR INSERTAR EN LA TABLA prueba_envio_recepcion"."<br>";
        $query = "SELECT tiempo_limite FROM prueba_envio_recepcion WHERE id_prueba_env_rec = 1";
        //echo "query = ".$query."<br>";
        $resultado = $objCorreo->consultar_campos($query);
        $tiempo_limite = $resultado['tiempo_limite'];
        //echo "tiempo_limite = ".$tiempo_limite."<br>";
        //echo "AHORA BUSCO LAS DEMAS CUENTAS DISTINTAS A LA CUENTA = ".$email." PARA CREAR LAS PRUEBAS ASOCIADAS A ESTA CUENTA"."<br>";
        $query = "SELECT id_cuenta_correo, alias_cuenta_correo FROM cuenta_correo WHERE id_cuenta_correo <> '".$id_cuenta_correo."'";
        //echo "query = ".$query."<br>";
        $resultado = $objCorreo->consultar($query);
        while ($row = mysqli_fetch_array($resultado)){
            $id_cuenta = $row['id_cuenta_correo'];
            $alias_cuenta = $row['alias_cuenta_correo'];
            //echo "PRIMERA PRUEBA CREADA PARA id_cuenta = ".$id_cuenta." / alias_cuenta = ".$alias_cuenta."<br>";
            $nombre_prueba = "TEST DE CORREO (".$alias." => ".$alias_cuenta.")";
            //echo "nombre_prueba = ".$nombre_prueba."<br>";
            $query = "INSERT INTO prueba_envio_recepcion(nombre_prueba,id_correo_origen,id_correo_destino,tiempo_limite) VALUES('".$nombre_prueba."','".$id_cuenta_correo."','".$id_cuenta."','".$tiempo_limite."')";
            //echo "query = ".$query."<br>";
            $insertado = $objCorreo->insertar($query);
            //echo "REGISTRO DE PRIMERA PRUEBA INSERTADO ? ";
            //echo $insertado ? "TRUE"."<br>":"FALSE"."<br>";
            //echo "SEGUNDA PRUEBA CREADA PARA id_cuenta = ".$id_cuenta." / alias_cuenta = ".$alias_cuenta."<br>";
            $nombre_prueba = "TEST DE CORREO (".$alias_cuenta." => ".$alias.")";
            //echo "nombre_prueba = ".$nombre_prueba."<br>";
            $query = "INSERT INTO prueba_envio_recepcion(nombre_prueba,id_correo_origen,id_correo_destino,tiempo_limite) VALUES('".$nombre_prueba."','".$id_cuenta."','".$id_cuenta_correo."','".$tiempo_limite."')";
            //echo "query = ".$query."<br>";
            $insertado = $objCorreo->insertar($query);
            //echo "REGISTRO DE SEGUNDA PRUEBA INSERTADO ? ";
            //echo $insertado ? "TRUE"."<br>":"FALSE"."<br>";
            $cont+=2;
        }
        $mensaje = "La cuenta de correo de prueba (".$email.") identificada con alias (".$alias.") ha sido agregada en la base de datos. Por lo tanto, ahora seran ejecutadas (".$cont.") nuevas pruebas asociadas a esta cuenta";

    }else{
        $mensaje = "La cuenta de correo de prueba (".$email.") no ha podido ser agregada en la base de datos. Por favor intente nuevamente";
    }
    //echo "mensaje = ".$mensaje."<br>";
    return $mensaje;
}



function comprobar_cuenta_correo($direccion,$alias){
    //echo "ENTRO AL METODO comprobar_cuenta_correo"."<br>";
    //echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    //echo "direccion = ".$direccion."<br>";
    //echo "alias = ".$alias."<br>";
    $existe = false;
    $objCorreo = new Correo();
    $query = "SELECT * FROM cuenta_correo WHERE direccion_email like '".$direccion."' OR alias_cuenta_correo like '".$alias."'";
    //echo "query = "."<br>";
    $num_rows = $objCorreo->contar_filas($query);
    if($num_rows > 0){
        $existe = true;
    }

    //echo "EXISTE LA CUENTA ? ";
    //echo $existe == true ? "TRUE"."<br>":"FALSE"."<br>";

    return $existe;
}




?>