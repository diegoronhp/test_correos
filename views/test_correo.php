<?php
//error_reporting(E_ALL);
error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../model/bd/configs.php';
require '../model/Correo.class.php';
require '../vendor/autoload.php';


function iniciar_test_correo(){
    echo "ENTRO AL METODO iniciar_test_correo"."<br>";
    $objCorreo = new Correo();
    $resultados_envio = array();
    $resultados_recepcion = array();
    $query = "SELECT id_cuenta_correo FROM cuenta_correo";
    echo "query = ".$query."<br>";
    $ids_cuentas = $objCorreo->consultar($query);

    echo "PRIMERO SE EJECUTA EL ENVIO TODOS LOS CORREOS DE PRUEBA"."<br>";
    while ($row = mysqli_fetch_array($ids_cuentas)){
        $id_origen = $row['id_cuenta_correo'];
        echo "id_origen = ".$id_origen."<br>";
        $resultados_envio_cuenta = ejecutar_envio_correos($id_origen,$objCorreo);
        $resultados_envio[$id_origen] = $resultados_envio_cuenta;
    }

    echo "RESULTADOS DE TODOS LOS CORREOS ENVIADOS:"."<br>";
    var_dump($resultados_envio);
    echo "DESPUES SE HACE LA PAUSA POR EL TIEMPO DE LATENCIA ESTABLECIDO"."<br>";
    pausar_tiempo_latencia($objCorreo);

    echo "LUEGO SE REALIZA LA LECTURA DE TODOS LOS CORREOS DE PRUEBA QUE HAYAN SIDO RECEPCIONADOS"."<br>";
    $query = "SELECT id_cuenta_correo FROM cuenta_correo";
    echo "query = ".$query."<br>";
    $ids_cuentas = $objCorreo->consultar($query);
    while ($row = mysqli_fetch_array($ids_cuentas)){
        $id_origen = $row['id_cuenta_correo'];
        echo "id_origen = ".$id_origen."<br>";
        $resultados_envio_cuenta = $resultados_envio[$id_origen];
        $resultados_recepcion_cuenta = comprobar_recepcion_correos_cuenta($id_origen,$resultados_envio_cuenta,$objCorreo);
        $resultados_recepcion[$id_origen] = $resultados_recepcion_cuenta;
    }
    echo "RESULTADOS DE TODOS LOS CORREOS RECIBIDOS:"."<br>";
    var_dump($resultados_recepcion);
    echo "HASTA ESTE PUNTO SE DEBERIAN EMPEZAR A IMPRIMIR LOS RESULTADOS EN LA PLANTILLA HTML"."<br>";
    envio_correo_resultados_pruebas($resultados_envio,$resultados_recepcion,$objCorreo);


}


function envio_correo_resultados_pruebas($resultados_envio,$resultados_recepcion,$objCorreo){
    echo "ENTRO AL METODO envio_correo_resultados_pruebas"."<br>";
    date_default_timezone_set("America/Bogota");
    $cadena_mensaje = "";
    //$cuenta_origen = "testcorreotigo@hotmail.com";
    $cuenta_origen = "noresponder@registraduria.gov.co";
    //$password_origen = "Test@correo1";
    $password_origen = "rnec4746";
    //$cuenta_destino = "testcorreotigo@gmail.com";
    $cuenta_destino = "dmmancera@registraduria.gov.co";
    $servidor_envio = "smtp.office365.com";
    $puerto_envio = 587;

    echo "EL CONTENIDO DE LOS ARRAYS CON TODOS LOS RESULTADOS DE **ENVIOS** SON = "."<br>";
    var_dump($resultados_envio);
    echo "EL CONTENIDO DE LOS ARRAYS CON TODOS LOS RESULTADOS DE **RECEPCION** SON = "."<br>";
    var_dump($resultados_recepcion);
    // Inicio
    $mail = new PHPMailer(true);
    $cadena_periodo = date("Y")."/".date("m")."/".date("d")." ".date("H").":00:00";
    $nombre_asunto = "RESULTADOS PRUEBAS DE CORREOS / FRANJA DE EJECUCION: ".$cadena_periodo;
    $nombre_destino = 'Mesa de ayuda Tigo';
    $cadena_mensaje .= "<html><head><meta charset='UTF-8'></head><body><p>".date('a')=='am'?'Buenos dias':'Buenas tardes'." Ingenieros:</p>
    <br><p>Reciban un cordial saludo,</p><br><p>Se realizan las siguientes pruebas de correo en la franja horaria de las : ".date('H').":00 HRS:"."</p>
    <br><table border='1'><tr><td colspan='15' style='text-align: center; font-weight: bold;'>Pruebas de correo ejecutadas en la franja: ".$cadena_periodo."</td></tr>
    <td style='text-align: center; font-weight: bold;'>Cuenta de correo de origen</td>
    <td style='text-align: center; font-weight: bold;'>Fecha envio</td>
    <td style='text-align: center; font-weight: bold;'>Diagnostico envio</td>
    <td style='text-align: center; font-weight: bold;'>Error envio</td>
    <td style='text-align: center; font-weight: bold;'>Fecha recepcion</td>
    <td style='text-align: center; font-weight: bold;'>Diagnostico recibo</td>
    <td style='text-align: center; font-weight: bold;'>Error recibo</td>
    <td style='text-align: center; font-weight: bold;'>Tiempo latencia (seg)</td>
    <td style='text-align: center; font-weight: bold;'>Fecha envio</td>
    <td style='text-align: center; font-weight: bold;'>Diagnostico envio</td>
    <td style='text-align: center; font-weight: bold;'>Error envio</td>
    <td style='text-align: center; font-weight: bold;'>Fecha recepcion</td>
    <td style='text-align: center; font-weight: bold;'>Diagnostico recibo</td>
    <td style='text-align: center; font-weight: bold;'>Error recibo</td>
    <td style='text-align: center; font-weight: bold;'>Tiempo latencia (seg)</td>";
    for($i=1;$i<=count($resultados_envio);$i++){
        $cadena_mensaje .= resultado_prueba_cuenta($resultados_envio[$i],$resultados_recepcion[$i],$i,$objCorreo);
    }
    $cadena_mensaje .= '</table><br><p>Lo anterior para su conocimiento y dem&aacute;s fines pertinentes.</p><br><p>Cordialmente</p><div><img src="data:image/png;base64,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" style="width=40%;"></div></body></html>';

    try{
        // Configuracion SMTP
        //$mail->SMTPDebug = 3;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->SMTPAuth = true;                                 // Enable SMTP authentication
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                                                      // Activar envio SMTP

        $mail->Host = $servidor_envio;
        $mail->SMTPAuth = true;
        $mail->Username = $cuenta_origen;  //Origen desde una cuenta de RNEC
        $mail->Password  = $password_origen;
        //$mail->SMTPSecure = 'tls';
        //$mail->Port  = 587;
        $mail->Port = $puerto_envio;
        $mail->setFrom($cuenta_origen, $nombre_asunto);

        // Destinatarios
        $mail->addAddress($cuenta_destino, $nombre_destino);  // Email y nombre del destinatario

        //cuentas adicionales a las que se envia el correo con los resultados de las pruebas
        //$mail->addAddress("infraestructuratigo@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("lanyseguridadtigo@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("jreyez@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("monitoreo7x24@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("mogomez@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("jacamargo@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("admunoz@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("csrengifo@registraduria.gov.co", $nombre_destino);
        //$mail->addAddress("planeta51@aol.com", $nombre_destino);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $nombre_asunto;
        $mail->Body = $cadena_mensaje;
        $mail->AltBody = 'Contenido del correo en texto plano para los clientes de correo que no soporten HTML';
        $mail->send();
        $resultado = true;
        echo 'El mensaje se ha enviado'."<br>";

    }catch(Exception $e){
        echo "El mensaje no se ha enviado. Mailer Error: {$mail->ErrorInfo}"."<br>";
    }


}


function resultado_prueba_cuenta($resultados_envio_cuenta,$resultados_recepcion_cuenta,$id_origen,$objCorreo){
    echo "ENTRO AL METODO resultado_prueba_cuenta"."<br>";
    $cadena_cuenta = "";
    $cant_cols = 2;
    $vacio = array();
    echo "RESULTADOS **ENVIO** ASOCIADOS A LA CUENTA CON id_origen = ".$id_origen."<br>";
    var_dump($resultados_envio_cuenta);
    echo "RESULTADOS **RECEPCION** ASOCIADOS A LA CUENTA CON id_origen = ".$id_origen."<br>";
    var_dump($resultados_recepcion_cuenta);
    $query = "SELECT direccion_email, alias_cuenta_correo FROM cuenta_correo WHERE id_cuenta_correo = ".$id_origen."";
    $resultado = $objCorreo->consultar_campos($query);
    $cuenta_origen = $resultado['direccion_email'];
    $alias_cuenta = $resultado['alias_cuenta_correo'];
    $query = "SELECT count(*) AS cantidad FROM cuenta_correo";
    $resultado = $objCorreo->consultar_campos($query);
    $cant_cuentas = intval($resultado['cantidad']);
    if($cant_cuentas > 2){
        $cant_cols = intval($cant_cuentas % 2) == 0 ? $cant_cuentas : $cant_cuentas - 1;
    }
    echo "cant_cols = ".$cant_cols."<br>";
    $cadena_cuenta .= "<tr><td rowspan='$cant_cols' style='text-align: center; font-weight: bold;'>".$cuenta_origen."<br>(".$alias_cuenta.")"."</td>";
    echo "cadena_cuenta (solo cuenta origen) = ".$cadena_cuenta."<br>";
    for($i=0,$j=1 ; $i<count($resultados_envio_cuenta) ; $i+=2,$j+=2){
        echo "----------------------------------------------";
        echo "ENVIANDO LOS RESULTADOS DE LA PRIMERA COLUMNA CON EL CONTADOR i = ".$i."<br>";
        var_dump($resultados_envio_cuenta[$i]);
        var_dump($resultados_recepcion_cuenta[$i]);
        echo "----------------------------------------------";
        echo "ENVIANDO LOS RESULTADOS DE LA SEGUNDA COLUMNA CON EL CONTADOR j = ".$j."<br>";
        var_dump($resultados_envio_cuenta[$j]);
        var_dump($resultados_recepcion_cuenta[$j]);
        echo "----------------------------------------------";
        $cadena_cuenta .= resultado_envio_recepcion($resultados_envio_cuenta[$i],$resultados_recepcion_cuenta[$i],$resultados_envio_cuenta[$j],$resultados_recepcion_cuenta[$j]);
    }
    echo "cadena_cuenta (con resultados pruebas) = ".$cadena_cuenta."<br>";
    return $cadena_cuenta;
}



function resultado_envio_recepcion($resultado_prueba_envio_1,$resultado_prueba_recepcion_1,$resultado_prueba_envio_2 = null,$resultado_prueba_recepcion_2 = null){
    echo "ENTRO AL METODO resultado_envio_recepcion"."<br>";
    $fila_completa = "";
    $cadena_pruebas = "";
    $color_prueba_envio_2 = "";
    $color_prueba_recepcion_2 = "";
    $color_prueba_envio_1 = $resultado_prueba_envio_1[11] == "Envio exitoso" ? "#3CFF33" :"#FF5833";

    echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
    echo "RESULTADO DE LA PRUEBA DE **ENVIO** PRIMERA COLUMNA **PARA IMPRIMIR**"."<br>";
    var_dump($resultado_prueba_envio_1);
    echo "RESULTADO DE LA PRUEBA DE **RECEPCION** PRIMERA COLUMNA **PARA IMPRIMIR**"."<br>";
    var_dump($resultado_prueba_recepcion_1);
    echo "RESULTADO DE LA PRUEBA DE **ENVIO** SEGUNDA COLUMNA **PARA IMPRIMIR**"."<br>";
    var_dump($resultado_prueba_envio_2);
    echo "RESULTADO DE LA PRUEBA DE **RECEPCION** SEGUNDA COLUMNA **PARA IMPRIMIR**"."<br>";
    var_dump($resultado_prueba_recepcion_2);
    echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";

    if($resultado_prueba_recepcion_1[3] == "NO ESTABLECIDA"){
        $color_prueba_recepcion_1 = "#FF5833";
    }else{
        $color_prueba_recepcion_1 = "#3CFF33";
    }

    if($resultado_prueba_envio_2 != null){
        echo "EL SEGUNDO ARRAY **NO** ESTA VACIO"."<br>";
        $fila_completa = true;
        $color_prueba_envio_2 = $resultado_prueba_envio_2[11] == "Envio exitoso" ? "#3CFF33" :"#FF5833";
        if($resultado_prueba_recepcion_2[3] == "NO ESTABLECIDA"){
            $color_prueba_recepcion_2 = "#FF5833";
        }else{
            $color_prueba_recepcion_2 = "#3CFF33";
        }
    }else{
        echo "EL SEGUNDO ARRAY **SI** ESTA VACIO"."<br>";
        $fila_completa = false;
    }

    echo "FILA COMPLETA ? ";
    echo $fila_completa == true ? "TRUE"."<br>":"FALSE"."<br>";

    if($fila_completa == true){
        echo "ENCONTRE UNA FILA **COMPLETA** CON LOS RESULTADOS DE LAS DOS PRUEBAS EJECUTADAS"."<br>";
        /***************************************************************PRIMERA FILA*******************************************************************************************************/
        $cadena_pruebas .= "<td colspan='7' style='text-align: center; font-weight: bold;'>".$resultado_prueba_envio_1[3]."<br>"."(".$resultado_prueba_envio_1[13].")"."</td>";  //ENCABEZADO DE LAS 7 COLUMNAS (1-7) CON LA CUENTA DE DESTINO 1
        $cadena_pruebas .= "<td colspan='7' style='text-align: center; font-weight: bold;'>".$resultado_prueba_envio_2[3]."<br>"."(".$resultado_prueba_envio_2[13].")"."</td>";  //ENCABEZADO DE LAS 7 COLUMNAS (8-14) CON LA CUENTA DE DESTINO 2
        $cadena_pruebas .= "</tr>";
        /***************************************************************SEGUNDA FILA*******************************************************************************************************/
        $cadena_pruebas .= "<tr><td style='text-align: center;'>".$resultado_prueba_envio_1[6]."</td>";  //DATO CON LA FECHA DE ENVIO PARA LA CUENTA DE DESTINO 1 (COL-1)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_envio_1.";'>".$resultado_prueba_envio_1[11]."</td>";  //DATO CON EL DIAGNOSTICO DE ENVIO PARA LA CUENTA DE DESTINO 1 (COL-2)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_envio_1.";'>".$resultado_prueba_envio_1[7]."</td>";    // DATO CON EL ERROR DE ENVIO PARA LA CUENTA DE DESTINO 1 (COL-3)
        $cadena_pruebas .= "<td style='text-align: center;'>".$resultado_prueba_recepcion_1[3]."</td>"; //DATO CON LA FECHA DE RECEPCION PARA LA CUENTA DE DESTINO 1 (COL-4)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[5]."</td>"; //DATO CON EL DIAGNOSTICO DE RECIBO PARA LA CUENTA DE DESTINO 1 (COL-5)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[4]."</td>"; //DATO CON EL ERROR DE RECIBO PARA LA CUENTA DE DESTINO 1 (COL-6)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[2]."</td>"; //DATO CON EL TIEMPO DE LATENCIA ESTABLECIDO PARA LA CUENTA DE DESTINO 1 (COL-7)
        $cadena_pruebas .= "<td style='text-align: center;'>".$resultado_prueba_envio_2[6]."</td>";  //DATO CON LA FECHA DE ENVIO PARA LA CUENTA DE DESTINO 2 (COL-8)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_envio_2.";'>".$resultado_prueba_envio_2[11]."</td>";  //DATO CON EL DIAGNOSTICO DE ENVIO PARA LA CUENTA DE DESTINO 2 (COL-9)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_envio_2.";'>".$resultado_prueba_envio_2[7]."</td>";  // DATO CON EL ERROR DE ENVIO PARA LA CUENTA DE DESTINO 2 (COL-10)
        $cadena_pruebas .= "<td style='text-align: center;'>".$resultado_prueba_recepcion_2[3]."</td>"; //DATO CON LA FECHA DE RECEPCION PARA LA CUENTA DE DESTINO 2 (COL-11)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_2.";'>".$resultado_prueba_recepcion_2[5]."</td>"; //DATO CON EL DIAGNOSTICO DE RECIBO PARA LA CUENTA DE DESTINO 2 (COL-12)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_recepcion_2.";'>".$resultado_prueba_recepcion_2[4]."</td>"; //DATO CON EL ERROR DE RECIBO PARA LA CUENTA DE DESTINO 2 (COL-13)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_2.";'>".$resultado_prueba_recepcion_2[2]."</td></tr>"; //DATO CON EL TIEMPO DE LATENCIA ESTABLECIDO PARA LA CUENTA DE DESTINO 2 (COL-14)
        $cadena_pruebas .= "</tr>";
    }else{
        echo "ENCONTRE UNA FILA **INCOMPLETA** CON LOS RESULTADOS DE UNA SOLA PRUEBA EJECUTADA"."<br>";
        /***************************************************************PRIMERA FILA*******************************************************************************************************/
        $cadena_pruebas .= "<td colspan='7' style='text-align: center; font-weight: bold;'>".$resultado_prueba_envio_1[3]."<br>"."(".$resultado_prueba_envio_1[13].")"."</td>";  //ENCABEZADO DE LAS 7 COLUMNAS (1-7) CON LA CUENTA DE DESTINO 1
        $cadena_pruebas .= "<td colspan='7'></td>";  //ENCABEZADO DE LAS 7 COLUMNAS (8-14) SIN DATO ASOCIADO
        $cadena_pruebas .= "</tr>";
        /***************************************************************SEGUNDA FILA*******************************************************************************************************/
        $cadena_pruebas .= "<tr><td style='text-align: center;'>".$resultado_prueba_envio_1[6]."</td>";  //DATO CON LA FECHA DE ENVIO PARA LA CUENTA DE DESTINO 1 (COL-1)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_envio_1.";'>".$resultado_prueba_envio_1[11]."</td>";  //DATO CON EL DIAGNOSTICO DE ENVIO PARA LA CUENTA DE ORIGEN 1 (COL-2)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_envio_1.";'>".$resultado_prueba_envio_1[7]."</td>";    // DATO CON EL ERROR DE ENVIO PARA LA CUENTA DE ORIGEN 1 (COL-3)
        $cadena_pruebas .= "<td style='text-align: center;'>".$resultado_prueba_recepcion_1[3]."</td>"; //DATO CON LA FECHA DE RECEPCION PARA LA CUENTA DE DESTINO 1 (COL-4)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[5]."</td>"; //DATO CON EL DIAGNOSTICO DE RECIBO PARA LA CUENTA DE DESTINO 1 (COL-5)
        $cadena_pruebas .= "<td style='background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[4]."</td>"; //DATO CON EL ERROR DE RECIBO PARA LA CUENTA DE DESTINO 1 (COL-6)
        $cadena_pruebas .= "<td style='text-align: center; background-color: ".$color_prueba_recepcion_1.";'>".$resultado_prueba_recepcion_1[2]."</td>"; //DATO CON EL TIEMPO DE LATENCIA ESTABLECIDO PARA LA CUENTA DE DESTINO 1 (COL-7)
        $cadena_pruebas .= "<td></td>";  //DATO CON LA FECHA DE ENVIO PARA LA CUENTA DE DESTINO 2 (COL-8) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td>";  //DATO CON EL DIAGNOSTICO DE ENVIO PARA LA CUENTA DE ORIGEN 2 (COL-9) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td>";  // DATO CON EL ERROR DE ENVIO PARA LA CUENTA DE ORIGEN 2 (COL-10) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td>"; //DATO CON LA FECHA DE RECEPCION PARA LA CUENTA DE DESTINO 2 (COL-11) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td>"; //DATO CON EL DIAGNOSTICO DE RECIBO PARA LA CUENTA DE DESTINO 2 (COL-12) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td>"; //DATO CON EL ERROR DE RECIBO PARA LA CUENTA DE DESTINO 2 (COL-13) SIN DATO ASOCIADO
        $cadena_pruebas .= "<td></td></tr>"; //DATO CON EL TIEMPO DE LATENCIA ESTABLECIDO PARA LA CUENTA DE DESTINO 2 (COL-14) SIN DATO ASOCIADO
        $cadena_pruebas .= "</tr>";
    }


    echo "cadena_pruebas (fila completa) = ".$cadena_pruebas."<br>";
    return $cadena_pruebas;
}



function comprobar_recepcion_correos_cuenta($id_origen,$resultados_envio_cuenta,$objCorreo){
    echo "ENTRO AL METODO comprobar_recepcion_correos_cuenta"."<br>";
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    echo "id_origen = ".$id_origen."<br>";
    $resultados_pruebas_recepcion = array();
    $cadena_ast = "*****************************************************************************************************************************************************";
    for($i=0;$i<count($resultados_envio_cuenta);$i++){
        imprimir_cadena_astericos($cadena_ast,2);
        $resultados_envio = $resultados_envio_cuenta[$i];
        echo "EL RESULTADO (".$i.") PARA LA CUENTA CON id_origen = (".$id_origen.") TIENE LAS SIGUIENTES VARIABLES = "."<br>";
        $resultado_recepcion = recepcion_correo_cuenta($resultados_envio,$objCorreo);
        var_dump($resultado_recepcion);
        array_push($resultados_pruebas_recepcion,$resultado_recepcion);
        echo "RESULTADO PRUEBA RECEPCION = ".$resultado_recepcion[6];
        echo $resultado_recepcion[0] == true ? " EXITOSA"."<br>" : " FALLIDA"."<br>";
        echo "MENSAJE DE RECEPCION DEL CORREO = ".$resultado_recepcion[1]."<br>";
        echo "TIEMPO DE LATENCIA: ".$resultado_recepcion[2]." SEGUNDOS"."<br>";
        echo "FECHA Y HORA DE EJECUCION DE LA PRUEBA DE RECEPCION: ".$resultado_recepcion[3]."<br>";
        imprimir_cadena_astericos($cadena_ast,2);
    }
    return $resultados_pruebas_recepcion;
}


function recepcion_correo_cuenta($resultado_prueba_envio,$objCorreo){
    echo "ENTRO AL METODO recepcion_correo_cuenta"."<br>";
    $formato = 'Y-m-d H:i:s';
    date_default_timezone_set("America/Bogota");
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    echo "nombre_prueba = ".$resultado_prueba_envio[1]."<br>";
    echo "servidor_imap = ".$resultado_prueba_envio[8]."<br>";
    echo "cuenta_origen = ".$resultado_prueba_envio[2]."<br>";
    echo "cuenta_destino = ".$resultado_prueba_envio[3]."<br>";
    echo "password_destino = ".$resultado_prueba_envio[4]."<br>";
    echo "asunto = ".$resultado_prueba_envio[5]."<br>";
    echo "fecha_hora_envio = ".$resultado_prueba_envio[6]."<br>";
    echo "error_envio = ".$resultado_prueba_envio[7]."<br>";
    echo "tiempo_limite = ".$resultado_prueba_envio[9]."<br>";
    echo "id_prueba_env_rec = ".$resultado_prueba_envio[10]."<br>";
    echo "diagnostico_envio = ".$resultado_prueba_envio[11]."<br>";
    $hostname = $resultado_prueba_envio[8];
    $username = $resultado_prueba_envio[3];
    $password = $resultado_prueba_envio[4];
    $asunto = $resultado_prueba_envio[5];
    $fecha_envio = $resultado_prueba_envio[6];
    $error_envio = $resultado_prueba_envio[7];
    $tiempo_limite = $resultado_prueba_envio[9];
    $id_prueba = $resultado_prueba_envio[10];
    $diagnostico_envio = $resultado_prueba_envio[11];
    $cuenta_origen = $resultado_prueba_envio[2];
    $nombre_prueba = $resultado_prueba_envio[1];
    $resultado_recepcion = false;
    $mensaje = "";
    $diagnostico_recibo = "Recibo exitoso";
    //$tiempo_latencia = 0;
    $cadena_periodo = "";
    $correo_valido = false;
    $fecha_hora_arrivo = "";
    //$intervalo = 10;
    //$tiempo_fraccion = 0;
    //$arrivo_correo = "";
    $error_conexion = "";
    $error_recibo = "Sin error de recibo";
    $error_imap = "";
    $inbox = false;


    try{
        $inbox = imap_open($hostname,$username,$password);
        $error_imap = imap_last_error();
        echo "HASTA ESTE PUNTO HAY ALGUN ERROR A LA CONEXION IMAP? ".$error_imap."<br>";
        echo "¿ES POSIBLE ABRIR EL INBOX DEL SERVIDOR = ".$hostname."? ";
        echo $inbox == true ? "TRUE"."<br>" : "FALSE"."<br>";
        $emails = imap_search($inbox, 'SUBJECT "TEST DE CORREO" UNSEEN'); //ESTE FILTRO FUNCIONA
        echo "¿HAY CORREOS SIN LEER CON EL ASUNTO TEST DE CORREO? ";
        echo $emails == true ? "TRUE"."<br>" : "FALSE"."<br>";
        print_r($emails);

        if($emails){
            echo "**SI** HAY NUEVOS CORREOS EN LA BANDEJA DE ENTRADA POR LO QUE AHORA PROCEDO A BUSCAR EL CORREO COINCIDENTE DE PRUEBA QUE HABIA SIDO ENVIADO"."<br>";
            $correo_valido = buscar_correo_coincidente($emails,$inbox,$asunto);
            if($correo_valido == true){
                echo "**SI** ENCONTRE EL CORREO DE PRUEBA COINCIDENTE QUE ESTABA BUSCANDO DESPUES DE QUE HAN TRANSCURRIDO (".$tiempo_limite.") SEGUNDOS"."<br>";
                $fecha_hora_arrivo = date($formato);
                $mensaje = "El mensaje de correo proveniente de la cuenta (".$cuenta_origen.") ha sido recibido con exito para la prueba ejecutada en la fecha (".$fecha_envio.")";
                $resultado_recepcion = true;
            }else{
                echo "**NO** ENCONTRE EL CORREO COINCIDENTE QUE ESTABA BUSCANDO DESPUES DE HABER ESPERADO POR ".$tiempo_limite." SEGUNDOS"."<br>";
            }
        }else{
            echo "**NO** HAY CORREOS NUEVOS EN LA BANDEJA DE ENTRADA DESPUES DE ESPERAR POR".$tiempo_limite." SEGUNDOS"."<br>";
        }

    }catch(Exception $e){
        echo "**NO** HAY CONEXION CON EL BUZON DE LA CUENTA CON LAS CREDENCIALES username = ".$username." / password = ".$password." MEDIANTE EL SERVIDOR hostname = ".$hostname."<br>";
        $error_conexion = "Se detecta el siguiente error: ".imap_last_error();
        echo "EL ERROR DETECTADO DURANTE LA CONEXION error_conexion = ".$error_conexion."<br>";

        //MANEJO DE LA EXCEPCION ANTE LA DETECCION DEL ERROR: too many login failures A CAUSA DE NO PODER ESTABLECER CONEXION CON EL SERVIDOR IMAP
        if((imap_last_error() == 'Too many login failures')&&($diagnostico_envio == "Envio exitoso")){
            echo "HA SIDO DETECTADA UNA EXCEPCION CON EL ERROR (Too many login failures) PERO EL CORREO DE PRUEBA **SI** FUE ENVIADO, POR LO TANTO SE MANEJA EL ERROR PARA APROBAR LA PRUEBA";
            $fecha_hora_arrivo = date($formato);
            $mensaje = "El mensaje de correo proveniente de la cuenta (".$cuenta_origen.") ha sido enviado con exito para la prueba ejecutada en la fecha (".$fecha_envio."), sin embargo no fue posible acceder al buzon de la cuenta (".$username.") por error en el login al servidor imap (".$hostname.")";
            $resultado_recepcion = true;
        }
    }

    echo "¿CORREO ENCONTRADO? ";
    echo $resultado_recepcion == true ? "TRUE"."<br>" : "FALSE"."<br>";


    //MANEJO DE LA EXCEPCION ANTE LA DETECCION DEL ERROR: too many login failures A CAUSA DE NO PODER ESTABLECER CONEXION CON EL SERVIDOR IMAP
    if(($error_imap == 'Too many login failures') && ($inbox == false) && ($diagnostico_envio == "Envio exitoso")){
        echo "NO HA SIDO POSIBLE ACCEDER AL BUZON DE LA CUENTA: ".$username." COMO CONSECUENCIA DE ALGUNA RESTRICCION EN EL LIMITE DE LOGINS EN EL SERVIDOR imap DE ESTA CUENTA, NO OBSTANTE EL CORREO DE PRUEBA ENVIADO DESDE LA CUENTA: ".$cuenta_origen." FUE ENVIADO EXITOSAMENTE"."<br>";
        $fecha_hora_arrivo = date($formato);
        $mensaje = "El mensaje de correo proveniente de la cuenta (".$cuenta_origen.") ha sido enviado con exito para la prueba ejecutada en la fecha (".$fecha_envio."), sin embargo no fue posible acceder al buzon de la cuenta (".$username.") a causa del error en el login al servidor imap (".$hostname.")";
        $resultado_recepcion = true;
    }

    if($resultado_recepcion == false){
        echo "DESPUES DE ESPERAR POR ".$tiempo_limite." SEGUNDOS SE COMPRUEBA QUE NO HA LLEGADO EL CORREO DE PRUEBA ESPERADO, ASI QUE NOTIFICO ESTE ERROR"."<br>";
        $diagnostico_recibo = "Recibo fallido";
        $fecha_hora_arrivo = "NO ESTABLECIDA";
        if($error_conexion != ""){
            $error_recibo = $error_conexion;
            $mensaje = "El mensaje de correo proveniente de la cuenta (".$cuenta_origen.") no ha sido recibido para la prueba ejecutada en la fecha (".$fecha_envio.") debido al siguiente error de conexion al buzon: ".$error_recibo;
        }else{
            $error_recibo = "No ha llegado el correo de prueba dentro del limite de tiempo de latencia establecido que corresponde a (".$tiempo_limite.") segundos";
            $mensaje = "El mensaje de correo proveniente de la cuenta (".$cuenta_origen.") no ha sido recibido para la prueba ejecutada en la fecha (".$fecha_envio.")";

        }
    }

    $cadena_periodo = date("Y")."/".date("m")."/".date("d")." ".date("H").":00:00";
    echo "AL FINAL DEBO INSERTAR EL REGISTRO DE LA PRUEBA EJECUTADA EN LA BASE DE DATOS CON LOS SIGUIENTES RESULTADOS DE LA RECEPCION:"."<br>";
    echo "cadena_periodo = ".$cadena_periodo."<br>";
    echo "fecha_hora_envio = ".$fecha_envio."<br>";
    echo "diagnostico_envio = ".$diagnostico_envio."<br>";
    echo "error_envio = ".$error_envio."<br>";
    echo "fecha_recepcion = ".$fecha_hora_arrivo."<br>";
    echo "diagnostico_recibo = ".$diagnostico_recibo."<br>";
    echo "error_recibo = ".$error_recibo."<br>";
    echo "tiempo_latencia = ".$tiempo_limite."<br>";
    echo "mensaje = ".$mensaje."<br>";
    echo "id_prueba = ".$id_prueba."<br>";
    echo "nombre_prueba = ".$nombre_prueba."<br>";
    insertar_prueba_ejecutada($objCorreo,$cadena_periodo,$fecha_envio,$error_envio,$fecha_hora_arrivo,$tiempo_limite,$mensaje,$id_prueba,$error_recibo,$diagnostico_envio,$diagnostico_recibo);
    $resultado_prueba = array($resultado_recepcion,$mensaje,$tiempo_limite,$fecha_hora_arrivo,$error_recibo,$diagnostico_recibo,$nombre_prueba);
    return $resultado_prueba;
}


function pausar_tiempo_latencia($objCorreo){
    echo "ENTRO AL METODO pausar_tiempo_latencia"."<br>";
    $query = "SELECT tiempo_limite FROM prueba_envio_recepcion WHERE id_prueba_env_rec = 1";
    $resultado = $objCorreo->consultar_campos($query);
    $tiempo_latencia = $resultado['tiempo_limite'];
    echo "tiempo_latencia = ".$tiempo_latencia."<br>";
    echo "**ANTES** DE ESPERAR POR ".$tiempo_latencia." SEGUNDOS"."<br>";
    sleep($tiempo_latencia);
    echo "**DESPUES** DE ESPERAR POR ".$tiempo_latencia." SEGUNDOS"."<br>";
}




function ejecutar_envio_correos($id_origen,$objCorreo){
    echo "ENTRO AL METODO ejecutar_envio_correos"."<br>";
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    echo "id_origen = ".$id_origen."<br>";
    $resultados_pruebas_envio = array();
    $cadena_ast = "*****************************************************************************************************************************************************";
    $ids_pruebas = consultar_ids_otras_cuentas($id_origen,$objCorreo);
    for($i=0;$i<count($ids_pruebas);$i++){
        imprimir_cadena_astericos($cadena_ast,2);
        $id_destino = $ids_pruebas[$i];
        $datos_prueba = consultar_datos_prueba($id_origen,$id_destino,$objCorreo);
        $resultado_envio = enviar_correo_prueba($datos_prueba,$objCorreo);
        array_push($resultados_pruebas_envio,$resultado_envio);
        echo "RESULTADO PRUEBA ENVIO = ".$resultado_envio[1];
        echo $resultado_envio[0] == true ? " EXITOSA"."<br>" : " FALLIDA"."<br>";
        echo "CUENTA ORIGEN = ".$resultado_envio[2]."<br>";
        echo "CUENTA DESTINO = ".$resultado_envio[3]."<br>";
        echo "ASUNTO DEL CORREO DE PRUEBA = ".$resultado_envio[5]."<br>";
        echo "FECHA Y HORA DE EJECUCION DE LA PRUEBA DE ENVIO: ".$resultado_envio[6]."<br>";
        echo "ERROR DETECTADO: ".$resultado_envio[7]."<br>";
        imprimir_cadena_astericos($cadena_ast,2);
    }
    return $resultados_pruebas_envio;
}




function consultar_ids_otras_cuentas($id_origen,$objCorreo){
    echo "ENTRO AL METODO consultar_ids_otras_cuentas"."<br>";
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    echo "id_origen = ".$id_origen."<br>";
    $ids_destino = array();
    $query = "SELECT id_cuenta_correo FROM cuenta_correo WHERE id_cuenta_correo NOT IN(".$id_origen.")";
    echo "query = ".$query."<br>";
    $resultado = $objCorreo->consultar($query);

    while ($row = mysqli_fetch_array($resultado)){
        $id_destino = $row['id_cuenta_correo'];
        echo "id_destino = ".$id_destino."<br>";
        array_push($ids_destino,$id_destino);
    }
    return $ids_destino;
}


function consultar_datos_prueba($id_origen,$id_destino,$objCorreo){
    echo "ENTRO AL METODO consultar_datos_prueba"."<br>";
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    echo "id_origen = ".$id_origen."<br>";
    echo "id_destino = ".$id_destino."<br>";
    $query = "SELECT * FROM prueba_envio_recepcion WHERE id_correo_origen = '".$id_origen."' AND id_correo_destino = '".$id_destino."'";
    $resultado = $objCorreo->consultar_campos($query);
    $id_prueba_env_rec = $resultado['id_prueba_env_rec'];
    $nombre_prueba = $resultado['nombre_prueba'];
    $tiempo_limite = $resultado['tiempo_limite'];
    echo "id_prueba_env_rec = ".$id_prueba_env_rec."<br>";
    echo "nombre_prueba = ".$nombre_prueba."<br>";
    echo "tiempo_limite = ".$tiempo_limite."<br>";
    $respuesta = array($id_prueba_env_rec,$nombre_prueba,$tiempo_limite,$id_origen,$id_destino);
    return $respuesta;
}



function enviar_correo_prueba($datos_prueba,$objCorreo){
    echo "ENTRO AL METODO envio_correo"."<br>";
    echo "RECIBO LAS SIGUIENTES VARIABLES:"."<br>";
    $id_prueba_env_rec = $datos_prueba[0];
    $nombre_prueba = $datos_prueba[1];
    $tiempo_limite = $datos_prueba[2];
    $id_origen = $datos_prueba[3];
    $id_destino = $datos_prueba[4];
    echo "id_prueba_env_rec = ".$id_prueba_env_rec."<br>";
    echo "nombre_prueba = ".$nombre_prueba."<br>";
    echo "tiempo_limite = ".$tiempo_limite."<br>";
    echo "id_origen = ".$id_origen."<br>";
    echo "id_destino = ".$id_destino."<br>";

    $datos_envio = consultar_cuenta_correo($id_origen,$objCorreo);
    $datos_recepcion = consultar_cuenta_correo($id_destino,$objCorreo);

    $alias_cuenta_envio = $datos_envio[0];
    $cuenta_origen = $datos_envio[1];
    $password_origen = $datos_envio[2];
    $servidor_envio = $datos_envio[3];
    $puerto_envio = $datos_envio[4];
    $alias_cuenta_recepcion = $datos_recepcion[0];
    $cuenta_destino = $datos_recepcion[1];
    $password_destino = $datos_recepcion[2];
    $servidor_recepcion = $datos_recepcion[5];
    $puerto_recepcion = $datos_recepcion[6];
    echo "alias_cuenta_envio = ".$alias_cuenta_envio."<br>";
    echo "cuenta_origen = ".$cuenta_origen."<br>";
    echo "password_origen = ".$password_origen."<br>";
    echo "servidor_envio = ".$servidor_envio."<br>";
    echo "puerto_envio = ".$puerto_envio."<br>";
    echo "alias_cuenta_recepcion = ".$alias_cuenta_recepcion."<br>";
    echo "cuenta_destino = ".$cuenta_destino."<br>";
    echo "password_destino = ".$password_destino."<br>";
    echo "servidor_recepcion = ".$servidor_recepcion."<br>";
    echo "puerto_recepcion = ".$puerto_recepcion."<br>";

    $mail = new PHPMailer(true);
    $resultado = false;
    $nombre_destino = 'Mesa de ayuda Tigo';
    $fecha_hora_envio = "";
    $asunto = "";
    $diagnostico_envio = "Envio exitoso";
    $error_detectado = "Sin error de envio";

    try{
        $mail->isSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Host = $servidor_envio;
        $mail->SMTPAuth = true;
        $mail->Username = $cuenta_origen;
        $mail->Password  = $password_origen;
        //$mail->SMTPSecure = 'tls';
        $mail->Port = $puerto_envio;
        $mail->setFrom($cuenta_origen, $nombre_prueba);

        // Destinatarios
        $mail->addAddress($cuenta_destino, $nombre_destino);

        //Fecha actual
        date_default_timezone_set("America/Bogota");
        $fecha_hora_envio = date("Y-m-d H:i:s");
        $asunto = $nombre_prueba.' / '.$fecha_hora_envio;

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = '
        <html>
        <head>
        </head>
        <style>
        h1,h2,h3,h4 {text-align: center;}
        </style>
        <body>
        <h1><img src="data:image/png;base64,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"  style="width: 210px;"></h1>
        <h2>ESTA ES UNA PRUEBA AUTOM&AacuteTICA PARA LA COMPROBACI&OacuteN DEL ENVIO Y RECEPCI&OacuteN DE CORREOS</h2>
        <h3>La prueba ejecutada es '.$nombre_prueba.' y los datos de envio del correo de prueba son los siguientes:</h3>
        <h3>La cuenta de correo origen es: '.$cuenta_origen.'</h3>
        <h3>La cuenta de correo de destino es: '.$cuenta_destino.'</h3>
        <h3>La prueba fue ejecutada en la fecha: '.$fecha_hora_envio.'</h3>
        <footer>
          <h4><p>Este es un mensaje autom&aacutetico y no es necesario responder</p></h4>
        </footer>
        </body>
        </html>';
        $mail->AltBody = 'Contenido del correo en texto plano para los clientes de correo que no soporten HTML';
        $mail->send();
        echo 'El mensaje se ha enviado'."<br>";
        $resultado = true;
    }catch(Exception $e){
        $diagnostico_envio = "Envio fallido";
        $error_detectado = "Se detecta el siguiente error: ".$mail->ErrorInfo;
        echo "El mensaje no se ha enviado. Mailer Error: {$mail->ErrorInfo}"."<br>";
    }

    $resultado_prueba = array($resultado,$nombre_prueba,$cuenta_origen,$cuenta_destino,$password_destino,$asunto,$fecha_hora_envio,$error_detectado,$servidor_recepcion,$tiempo_limite,$id_prueba_env_rec,$diagnostico_envio,$alias_cuenta_envio,$alias_cuenta_recepcion);
    return $resultado_prueba;

}



function consultar_cuenta_correo($id_cuenta,$objCorreo){
    echo "ENTRO AL METODO consultar_cuenta_correo"."<br>";
    echo "RECIBO EL id_cuenta = ".$id_cuenta."<br>";
    $query = "SELECT * FROM cuenta_correo WHERE id_cuenta_correo = '".$id_cuenta."'";
    echo "query = ".$query."<br>";
    $resultado = $objCorreo->consultar_campos($query);
    $alias_cuenta = $resultado['alias_cuenta_correo'];
    $email = $resultado['direccion_email'];
    $pswd = $resultado['password_email'];
    $smtp = $resultado['servidor_smtp'];
    $pt_smtp = $resultado['puerto_smtp'];
    $imap = $resultado['servidor_imap'];
    $pt_imap = $resultado['puerto_imap'];
    echo "alias_cuenta = ".$alias_cuenta."<br>";
    echo "email = ".$email."<br>";
    echo "pswd = ".$pswd."<br>";
    echo "smtp = ".$smtp."<br>";
    echo "pt_smtp = ".$pt_smtp."<br>";
    echo "imap = ".$imap."<br>";
    echo "pt_imap = ".$pt_imap."<br>";
    $respuesta = array($alias_cuenta,$email,$pswd,$smtp,$pt_smtp,$imap,$pt_imap);
    return $respuesta;
}


function buscar_correo_coincidente($emails,$inbox,$asunto){
    echo "ENTRO AL METODO buscar_correo_coincidente"."<br>";
    //$formato = 'Y-m-d H:i:s';
    //date_default_timezone_set("America/Bogota");
    $correo_valido = "";
    $correo_encontrado = false;
    //$cadena_asunto = "";
    //$fecha_hora = "";
    //$fecha_hora_arrivo = "";
    //$tiempo_latencia = "";
    $j = 1;

    foreach($emails as $email_number){
        echo "CORREO # ".$j."<br>";
        $header = imap_headerinfo($inbox,$email_number);
        $subject = $header->subject;
        echo "EL CORREO **ENTRANTE** TIENE EL SIGUIENTE ASUNTO: ".$subject."<br>";
        echo "EL CORREO **ENVIADO** TIENE EL SIGUIENTE ASUNTO:".$asunto."<br>";
        $correo_valido = strcmp($subject,$asunto);

        //COMPRUEBO SI EL ASUNTO DEL CORREO ENTRANTE TIENE EL MISMO ASUNTO DEL CORREO ENVIADO DURANTE LA ULTIMA PRUEBA EJECUTADA
        if($correo_valido === 0){
            echo "EL CORREO RECIBIDO TIENE UN ASUNTO **IDENTICO** AL DEL ULTIMO CORREO ENVIADO DE PRUEBA, POR LO TANTO CONCLUYO QUE EL CORREO DE PRUEBA **SI** LLEGO"."<br>";
            $correo_encontrado = true;
            //$cadena_asunto = explode("/",$subject);
            //$fecha_hora = $cadena_asunto[1];
            echo "ENTONCES CAPTURO LA HORA DE LLEGADA DEL CORREO COINCIDENTE Y CALCULO LA DIFERENCIA EN SEGUNDOS ENTRE LA HORA DE ENVIO Y LA HORA DE RECEPCION PARA OBTENER LA LATENCIA"."<br>";
            //$fecha_hora_arrivo = date($formato);
            //$tiempo_latencia = calcular_diferencia_segundos($fecha_hora,$fecha_hora_arrivo);
            echo "AL FINAL DEJO EL CORREO COMO LEIDO Y LO ENVIO A LA CARPETA DE INSERTADOS"."<br>";
            $leido = imap_setflag_full($inbox, $email_number, "\\Seen "); //banderas en los msjs - deja el mensaje como leido
            echo "¿CORREO LEIDO EN LA BANDEJA DE ENTRADA? ";
            echo $leido == true ? "TRUE"."<br>" : "FALSE"."<br>";
            $movido = imap_mail_copy($inbox, 1, 'Insertado'); //copia el correo desde la bandeja de entrada a la carpeta insertado
            echo "¿CORREO MOVIDO A LA CARPETA INSERTADO? ";
            echo $movido == true ? "TRUE"."<br>" : "FALSE"."<br>";
            $borrado = imap_delete($inbox, 1); //elimina el correo de la bandeja de entrada
            echo "¿CORREO BORRADO DE LA BANDEJA DE ENTRADA? ";
            echo $borrado == true ? "TRUE"."<br>" : "FALSE"."<br>";
            $cerrado = imap_close($inbox);  //cierra el acceso al correo
            echo "¿BANDEJA DE CORREO CERRADA? ";
            echo $cerrado == true ? "TRUE"."<br>" : "FALSE"."<br>";
            break;
        }else{
            echo "EL CORREO RECIBIDO TIENE UN ASUNTO **DIFERENTE** AL DEL ULTIMO CORREO ENVIADO DE PRUEBA ENTONCES DEBO SEGUIR BUSCANDO EN LOS DEMAS CORREOS NUEVOS"."<br>";
        }
        $j++;
    }

    echo "¿CORREO DE PRUEBA ENCONTRADO CON EXITO?";
    echo $correo_encontrado == true ? "TRUE"."<br>":"FALSE"."<br>";
    //echo "DEVUELVO LAS SIGUIENTES VARIABLES: "."<br>";
    //echo "fecha_hora_arrivo = ".$fecha_hora_arrivo."<br>";
    //echo "tiempo_latencia = ".$tiempo_latencia."<br>";
    //$respuesta = array($correo_encontrado,$fecha_hora_arrivo,$tiempo_latencia);
    //return $respuesta;
    return $correo_encontrado;
}


function calcular_diferencia_segundos($fecha_menor, $fecha_mayor){
    date_default_timezone_set("America/Bogota");
    $formato = 'Y-m-d H:i:s';
    echo "ESTOY EN EL METODO calcular_diferencia_segundos"."<br>";
    echo "RECIBO LAS SIGUIENTES FECHAS"."<br>";
    echo "fecha_menor = ".$fecha_menor."<br>";
    echo "fecha_mayor = ".$fecha_mayor."<br>";
    $date1 = new DateTime($fecha_menor);
    //echo "date1 = ".date_format($date1,$formato)."<br>";
    $date2 = new DateTime($fecha_mayor);
    //echo "date2 = ".date_format($date2,$formato)."<br>";

    $diff = $date1->diff($date2);
    $segundos = ( ($diff->days * 24 ) * 60 ) + ( $diff->i * 60 ) + $diff->s;
    echo "LA DIFERENCIA ENTRE LAS DOS FECHAS EQUIVALE A ".$segundos." SEGUNDOS"."<br>";
    return $segundos;

}



function insertar_prueba_ejecutada($objCorreo,$franja,$envio,$error_envio,$recepcion,$latencia,$observaciones,$id_prueba,$error_recibo,$diagnostico_envio,$diagnostico_recibo){
    echo "ENTRO AL METODO insertar_prueba_ejecutada"."<br>";
    if($recepcion == "NO ESTABLECIDA"){
        $query = "INSERT INTO prueba_ejecutada(franja_ejecucion,fecha_envio,diagnostico_envio,error_envio,fecha_recepcion,diagnostico_recibo,error_recibo,tiempo_latencia,observaciones_prueba,id_prueba_env_rec)
        VALUES('".$franja."','".$envio."','".$diagnostico_envio."','".$error_envio."',null,'".$diagnostico_recibo."','".$error_recibo."','".$latencia."','".$observaciones."','".$id_prueba."')";
    }else{
        $query = "INSERT INTO prueba_ejecutada(franja_ejecucion,fecha_envio,diagnostico_envio,error_envio,fecha_recepcion,diagnostico_recibo,error_recibo,tiempo_latencia,observaciones_prueba,id_prueba_env_rec)
        VALUES('".$franja."','".$envio."','".$diagnostico_envio."','".$error_envio."','".$recepcion."','".$diagnostico_recibo."','".$error_recibo."','".$latencia."','".$observaciones."','".$id_prueba."')";
    }
    echo "query = ".$query."<br>";
    $resultado = $objCorreo->insertar($query);
    echo "INSERTADO? ";
    echo $resultado == true ? "TRUE"."<br>":"FALSE"."<br>";
}



function imprimir_cadena_astericos($cadena,$cant){
    for($i=0;$i<$cant;$i++){
        echo $cadena;
    }
}


?>