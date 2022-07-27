<?php
require '../libs/Configs.php';
//echo "ANTES DEL getInstance"."<br>";
$config = Configs::getInstance();
//echo "DESPUES DEL getInstance"."<br>";
//Carpeta de los Controladores
$config->set('carpetaControlador','controller/');
//Carpeta de las Clases de nuestro Modelo
$config->set('carpetaModelo','model/');
//Carpeta de las vistas
$config->set('carpetaVista','views/');
//Carpeta de las vistas de contenido
$config->set('contenido','views/content/');
//Carpeta de los CSS
$config->set('carpetaCss','views/default/css/');
//Carpeta de los JavaScripts
$config->set('carpetaJS','views/default/js/');
//Nombre aplicativo
$config->set('APP_NOMBRE','HELP_DESK');
//Nombre carpeta aplicativo
$config->set('C_ROOT','/union/');
?>