<?php
//error_reporting(E_ALL);
error_reporting(0);

//require '../model/Correo.class.php';
require '../views/test_correo.php';

if(isset($_POST["ejecutar"])){
    date_default_timezone_set("America/Bogota");
    $formato = 'Y-m-d H:i:s';
    $mensaje = "El test de correo ha sido ejecutado con exito durante un tiempo de ";
    $antes = date($formato);
    //LLAMAMOS AL METODO QUE EJECUTA EL TEST DE CORREO
    iniciar_test_correo();
    $despues = date($formato);
    $dif_segundos = calcular_diferencia_segundos($antes,$despues);
    $mensaje .= $dif_segundos." segundos, ahora puede consultar los resultados obtenidos de las pruebas en su cuenta de correo";
    $respuesta = array("mensaje"=>$mensaje);
    echo json_encode($respuesta);
}

if(isset($_POST["sltiempo"])){
    $objCorreo = new Correo();
    $query = "SELECT tiempo_limite FROM prueba_envio_recepcion WHERE id_prueba_env_rec = 1";
    //echo "query = ".$query."<br>";
    $resultado = $objCorreo->consultar_campos($query);
    $tiempo_latencia = $resultado["tiempo_limite"];
    //echo "tiempo_latencia = ".$tiempo_latencia."<br>";
    echo json_encode($tiempo_latencia);
}

if(isset($_POST["uptiempo"])){
    //echo "DETECTO EL EVENTO POST DEL FORMULARIO EN EL QUE SE EDITA EL TIEMPO DE LATENCIA"."<br>";
    $latencia = intval($_POST["latencia"]);
    //echo "latencia = ".$latencia."<br>";
    $objCorreo = new Correo();
    $query = "UPDATE prueba_envio_recepcion SET tiempo_limite = '".$latencia."'";
    //echo "query = ".$query."<br>";
    $resultado = $objCorreo->actualizar($query);
    $mensaje = "El tiempo de latencia para todas las pruebas de correo ha sido establecido a ".$latencia." segundos";
    $respuesta = array("mensaje"=>$mensaje);
    echo json_encode($respuesta);
}


if(isset($_POST["consultar"])){
    $objCorreo = new Correo();
    $cadena_html = "<table border='1'><thead><tr><td>ID</td><td>ALIAS</td><td>E-MAIL</td><td>PASSWORD</td><td>SERVIDOR SMTP</td><td>PUERTO SMTP</td><td>SERVIDOR IMAP</td><td>PUERTO IMAP</td><td colspan='2'>ACCION</td></tr></thead><tbody>";
    $query = "SELECT * FROM cuenta_correo";
    $resultado = $objCorreo->consultar($query);
    while ($row = mysqli_fetch_array($resultado)){
        $id_cuenta = $row['id_cuenta_correo'];
        $alias_cuenta = $row['alias_cuenta_correo'];
        $direccion_email = $row['direccion_email'];
        $password_email = $row['password_email'];
        $servidor_smtp = $row['servidor_smtp'];
        $puerto_smtp = $row['puerto_smtp'];
        //$servidor_imap = $row['servidor_imap'];
        $llaves = array("{","}");
        $servidor_imap = str_replace($llaves," ",$row['servidor_imap']);
        $puerto_imap = $row['puerto_imap'];
        $cadena_html .= "<tr>";
        $cadena_html .= "<td>".$id_cuenta."</td>";
        $cadena_html .= "<td><input type='text' id='als_".$id_cuenta."' value='".$alias_cuenta."' disabled></td>";
        $cadena_html .= "<td><input type='email' id='drc_".$id_cuenta."' value='".$direccion_email."' disabled></td>";
        $cadena_html .= "<td><input type='text' id='psw_".$id_cuenta."' value='".$password_email."' disabled></td>";
        $cadena_html .= "<td><input type='text' id='smt_".$id_cuenta."' value='".$servidor_smtp."' disabled></td>";
        $cadena_html .= "<td><input type='number' id='pts_".$id_cuenta."' value='".$puerto_smtp."' disabled></td>";
        $cadena_html .= "<td><input type='text' id='imp_".$id_cuenta."' value='".$servidor_imap."' disabled></td>";
        $cadena_html .= "<td><input type='number' id='pti_".$id_cuenta."' value='".$puerto_imap."' disabled></td>";
        $cadena_html .= "<td><input type='button' id='reg_".$id_cuenta."' class='act' value='Editar'></td>";
        $cadena_html .= "<td><input type='button' id='reg_".$id_cuenta."' class='del' value='Eliminar'></td>";
        $cadena_html .= "</tr>";
    }
    $cadena_html .= "</tbody></table>";
    echo json_encode($cadena_html);
}


if(isset($_POST["eliminar"])){
    //echo "DETECTO EL EVENTO POST DEL FORMULARIO EN EL QUE SE ELIMINA UNA CUENTA DE PRUEBA EXISTENTE"."<br>";
    $id_reg = $_POST["id_reg"];
    $alias_email = $_POST["alias_email"];
    $dir_email = $_POST["dir_email"];
    //echo "id_reg = ".$id_reg."<br>";
    //echo "alias_email = ".$alias_email."<br>";
    //echo "dir_email = ".$dir_email."<br>";
    $mensaje = eliminar_registro_cuenta_existente($id_reg,$dir_email,$alias_email);
    $respuesta = array("mensaje"=>$mensaje);
    echo json_encode($respuesta);
}


if(isset($_POST["actualizar"])){
    //echo "DETECTO EL EVENTO POST DEL FORMULARIO EN EL QUE SE ACTUALIZA UNA CUENTA DE PRUEBA EXISTENTE"."<br>";
    $mensaje = "";
    $id_reg = $_POST["id_reg"];
    $dir_email = strtolower($_POST["dir_email"]);
    $alias_email = strtoupper($_POST["alias_email"]);
    $pswd_email = $_POST["pswd_email"];
    $serv_smtp = $_POST["serv_smtp"];
    $port_smtp = $_POST["port_smtp"];
    $serv_imap = "{".$_POST["serv_imap"]."}";
    $port_imap = $_POST["port_imap"];
    //echo "id_reg = ".$id_reg."<br>";
    //echo "dir_email = ".$dir_email."<br>";
    //echo "alias_email = ".$alias_email."<br>";
    //echo "pswd_email = ".$pswd_email."<br>";
    //echo "serv_smtp = ".$serv_smtp."<br>";
    //echo "port_smtp = ".$port_smtp."<br>";
    //echo "serv_imap = ".$serv_imap."<br>";
    //echo "port_imap = ".$port_imap."<br>";
    $alias_anterior = consultar_alias_cuenta_anterior($id_reg);
    $mensaje = actualizar_registro_cuenta_existente($id_reg,$dir_email,$alias_email,$pswd_email,$serv_smtp,$port_smtp,$serv_imap,$port_imap,$alias_anterior);
    $respuesta = array("mensaje"=>$mensaje);
    echo json_encode($respuesta);
}



if(isset($_POST["agregar"])){
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


function eliminar_registro_cuenta_existente($id_reg,$dir_email,$alias_email){
    //echo "ENTRO AL METODO eliminar_registro_cuenta_existente"."<br>";
    $mensaje = "";
    $objCorreo = new Correo();
    $query = "DELETE FROM cuenta_correo WHERE id_cuenta_correo = '".$id_reg."' AND direccion_email = '".$dir_email."'";
    //echo "query = ".$query."<br>";
    $eliminado = $objCorreo->eliminar($query);

    //echo "ELIMINADO REGISTRO ? "."<br>";
    //echo $eliminado == true ? "TRUE"."<br>":"FALSE"."<br>";

    if($eliminado){
        eliminar_pruebas_asociadas($id_reg);
        $mensaje = "La cuenta de correo de prueba (".$dir_email.") identificada con alias (".$alias_email.") ha sido eliminada en la base de datos.";
    }else{
        $mensaje = "La cuenta de correo de prueba (".$dir_email.") no ha podido ser eliminada en la base de datos. Por favor intente nuevamente";
    }
    //echo "mensaje = ".$mensaje."<br>";
    return $mensaje;
}

function actualizar_registro_cuenta_existente($id_reg,$dir_email,$alias_email,$pswd_email,$serv_smtp,$port_smtp,$serv_imap,$port_imap,$alias_anterior){
    //echo "ENTRO AL METODO actualizar_registro_cuenta_existente"."<br>";
    $mensaje = "";
    $objCorreo = new Correo();
    $query = "UPDATE cuenta_correo SET alias_cuenta_correo = '".$alias_email."', direccion_email = '".$dir_email."', password_email = '".$pswd_email."', servidor_smtp = '".$serv_smtp."', puerto_smtp = '".$port_smtp."', servidor_imap = '".$serv_imap."', puerto_imap = '".$port_imap."' WHERE id_cuenta_correo = '".$id_reg."'";
    //echo "query = ".$query."<br>";
    $actualizado = $objCorreo->actualizar($query);

    //echo "ACTUALIZADO REGISTRO ? "."<br>";
    //echo $actualizado == true ? "TRUE"."<br>":"FALSE"."<br>";

    if($actualizado){
        actualizar_alias_pruebas($alias_email,$alias_anterior);
        $mensaje = "La cuenta de correo de prueba (".$dir_email.") identificada con alias (".$alias_email.") ha sido actualizada en la base de datos.";
    }else{
        $mensaje = "La cuenta de correo de prueba (".$dir_email.") no ha podido ser actualizada en la base de datos. Por favor intente nuevamente";
    }
    //echo "mensaje = ".$mensaje."<br>";
    return $mensaje;
}


function eliminar_pruebas_asociadas($id_reg){
    //echo "ENTRO AL METODO eliminar_pruebas_asociadas"."<br>";
    $objCorreo = new Correo();
    $query = "DELETE FROM prueba_envio_recepcion where id_correo_origen = '".$id_reg."' OR id_correo_destino = '".$id_reg."'";
    //echo "query = ".$query."<br>";
    $eliminadas = $objCorreo->eliminar($query);
    //echo "ELIMINADAS ASOCIADAS ? "."<br>";
    //echo $eliminadas == true ? "TRUE"."<br>":"FALSE"."<br>";
}


function actualizar_alias_pruebas($alias_nuevo,$alias_anterior){
    //echo "ENTRO AL METODO actualizar_alias_pruebas"."<br>";
    $objCorreo = new Correo();
    $query = "SELECT id_prueba_env_rec, nombre_prueba FROM prueba_envio_recepcion WHERE nombre_prueba like '%".$alias_anterior."%'";
    //echo "query = ".$query."<br>";
    $resultado = $objCorreo->consultar($query);
    while ($row = mysqli_fetch_array($resultado)){
        $id_prueba = $row['id_prueba_env_rec'];
        $nombre_anterior = $row['nombre_prueba'];
        //echo "id_prueba = ".$id_prueba."<br>";
        //echo "nombre_anterior = ".$nombre_anterior."<br>";
        $nombre_nuevo = str_replace($alias_anterior,$alias_nuevo,$nombre_anterior);
        //echo "nombre_nuevo = ".$nombre_nuevo."<br>";
        $query = "UPDATE prueba_envio_recepcion SET nombre_prueba = '".$nombre_nuevo."' WHERE id_prueba_env_rec = '".$id_prueba."'";
        //echo "query = ".$query."<br>";
        $actualizado = $objCorreo->actualizar($query);
        //echo "ACTUALIZADO ALIAS ? "."<br>";
        //echo $actualizado == true ? "TRUE"."<br>":"FALSE"."<br>";
    }
}


function consultar_alias_cuenta_anterior($id_cuenta){
    //echo "ENTRO AL METODO consultar_alias_cuenta_anterior"."<br>";
    $objCorreo = new Correo();
    $query = "SELECT alias_cuenta_correo FROM cuenta_correo WHERE id_cuenta_correo = '".$id_cuenta."'";
    //echo "query = ".$query."<br>";
    $resultado = $objCorreo->consultar_campos($query);
    $alias_email = $resultado['alias_cuenta_correo'];
    //echo "alias_emial = ".$alias_email."<br>";
    return $alias_email;
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