<?php
require_once __DIR__.'/runtime.php';
if (!defined('APP_SITE')) define('APP_SITE', 'neokids');
if (PHP_SAPI !== 'cli') @set_time_limit(300);
// ini_set("session.cookie_lifetime","7200");
// ini_set("session.gc_maxlifetime","7200");

// Funciones
if (function_exists('isMobile')==false) {
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", app_value($_SERVER,'HTTP_USER_AGENT'));
	}
}

if (function_exists('comprobar_email')==false) {
	function comprobar_email($email){
    	$mail_correcto = 0;
    	//compruebo unas cosas primeras
    	if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
       	if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
          //miro si tiene caracter .
          if (substr_count($email,".")>= 1){
             //obtengo la terminacion del dominio
             $term_dom = substr(strrchr ($email, '.'),1);
             //compruebo que la terminación del dominio sea correcta
             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                //compruebo que lo de antes del dominio sea correcto
                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                if ($caracter_ult != "@" && $caracter_ult != "."){
                   $mail_correcto = 1;
                }
             }
          }
       }
    }
    if ($mail_correcto)
       return 1;
    else
       return 0;
	}
}
if (function_exists('my_bcmod')==false) {
	function my_bcmod( $x, $y ) { 
	    // how many numbers to take at once? carefull not to exceed (int)
    	$take = 5;    
	    $mod = '';

    	do {
	        $a = (int)$mod.substr( $x, 0, $take );
    	    $x = substr( $x, $take );
        	$mod = $a % $y;   
	    }
    	while ( strlen($x) );

	    return (int)$mod;
	} 
}
if (function_exists('calcularIban')==false) {
 function calcularIban( $_entidad, $_sucursal, $_dc, $_cuenta ) {

  $iban_ = -1;

  // CCC :      01280010120123456789
   // IBAN : [CONFIGURE_PRIVATE_IBAN]

  $codPais = "ES";
  $dc = "00"; // No sabemos el dígito de control del IBAN, ponemos 00

  $iban_ = $codPais."".$dc."".$_entidad."".$_sucursal."".$_dc."".$_cuenta;

  return $iban_;
 }
}

if (function_exists('generarDCInToIban')==false) {
function generarDCInToIban( $_iban ) {

  $ibanConDC_ = -1;

  // IBAN sin DC, DC = 00 : BE00999999999999
   // IBAN con DC, DC = 89 : BE89999999999999

  // Mover los cuatro primeros caracteres del número IBAN a la derecha:
  $ibanConDC_ = substr($_iban,4)."".substr($_iban,0,4);


  // Convertir las letras a números según la siguiente tabla:
  // A=10 G=16 M=22  S=28 Y=34
  // B=11 H=17 N=23  T=29 Z=35
  // C=12 I=18 O=24 U=30
  // D=13 J=19 P=25 V=31
  // E=14 K=20 Q=26 W=32
  // F=15 L=21 R=27 X=33
  $letras_array = array("A","B","C","D","E","F","G","H","I","J","K","L",
        "M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
  $numeros_array = array("10","11","12","13","14","15","16","17","18","19","20","21",
        "22","23","24","25","26","27","28","29","30","31","32","33","34","35");


  $ibanConDC_ = str_replace( $letras_array, $numeros_array, $ibanConDC_);


  // Aplicar la operación módulo 97 y restar al número 98, el valor obtenido.
  // Si el resultado consta de sólo un dígito, insertar un cero a la izquierda.

  //$modulo97 = intval($ibanConDC_) % 97;
  //$modulo97 = $ibanConDC_ % 97;
  $modulo97 = my_bcmod($ibanConDC_,  97);

  $dc = 98 - $modulo97;

  // insertar 0 a la izquierda si fuera menor de dos dígitos
  $dc = sprintf("%02d",$dc);

  // Sustituimos los dígitos 2 y 4 por el $dc
  $ibanConDC_ =  substr($_iban,0,2).$dc.substr($_iban,4);


  return $ibanConDC_;
 }
}

// Comprueba si estamos en https y si no redirecciona

if (app_value($_SERVER,'HTTPS')!="on") {
        // header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        }

$desactivar_fichar=true;


// Base de datos Alumnos centros externos
$baseDatosOrigenExternos = "gestionguarderias";

//Datos que se deben modificar si se cambia la página de servidor o de carpeta

$trimestre=3;
$periodo=4;

$fechalimite=strtotime(date("Y-m-d"))-(86400*15);

$tipo_usuario[0]="Alumno";
$tipo_usuario[1]="Padre";
$tipo_usuario[2]="Educadora";
$tipo_usuario[3]="Coordinador de centro";
$tipo_usuario[4]="Administrador";
$tipo_usuario[10]="Cocina";
$tipo_usuario[11]="Personal no docente";

$tipos_proveedores[0][0]="Comerciales";
$tipos_proveedores[0][1]="Comercial";
$tipos_proveedores[1][0]="Internos";
$tipos_proveedores[1][1]="Interno";

$nombreDirectora=(getenv('NEOKIDS_DIRECTOR_NAME') ?: '');

$IBAN_IngresoFacturas=(getenv('NEOKIDS_INVOICE_BANK_DETAILS') ?: '');

// Inicio mensajes

// $url_mensajes="http://www.escuelas-infantiles.com";

$url_mensajes="";

// Fin mensajes

$dominio_actual = "http://gestion.neo-kids.es" ;
$url_web = "" ;
$web_path = dirname(__DIR__);
$upload_path = dirname(__DIR__).'/fotos_usuarios';
$upload_path_dni = dirname(__DIR__).'/dni_usuarios';
$upload_path_firma = dirname(__DIR__).'/firma_usuarios';
$upload_path_platos = dirname(__DIR__) . '/fotos_platos';
$url_root = "" ;
$root_path = dirname(__DIR__);
$config_path = $dominio_actual.$url_root."/configuracion/" ;
$access_database="gestionpiscina";
$ano_inicio_actividad=2005;

$carpeta_documentacion = dirname(__DIR__).'/.private/documentacion';

$galeria_fotos_path = dirname(__DIR__).'/galeria';
$descarga_docs_path = dirname(__DIR__).'/descargas';

$limite_de_edad=10;

$nombre_pagina_completo=$_SERVER['PHP_SELF'];

$nombre_pagina=explode("/",$nombre_pagina_completo);

$nombre_script=str_replace(".php","",$nombre_pagina[count($nombre_pagina)-1]);

if ($nombre_script=="index") {
	$titulo_web = "Gestión de la Piscina de Neokids" ;
} else {
	$titulo_web = ucwords(str_replace("_"," ",$nombre_script));
}

$nombre_centro = "NEOKIDS" ;

//Configuración CHAT
$servidor_irc = "irc.empresa.com" ;
$canal_irc = "NEOKIDS" ;

//Configuracion correo
$servidor_de_correo = "mail.empresa.com" ;
$servidor_de_correo_saliente = $servidor_de_correo ;

$puerto_correo = 25 ;

$mail_principal = "webmaster@neo-kids.es" ;

//Aspecto de menus, noticias y dimensiones de tablas
$numero_columnas_menu = 5 ;

$ancho_pagina_total = 775 ;

$nombre_cookie = "Gestion_neokids" ;

//Numero máximo de cursos mostrados en la página inicial
$cursos_inicio = 5 ;

//Longitud de las noticias de la barra lateral
$lonfitud_not = 100 ;

//Numero de noticias en buscador
$num_not = 10 ;

// Opcion seleccionada en el menú
$submenu_activo=app_value($_GET,'Submenu');

//Dias de la semana y meses en castellano
$dia_sem[1]="L";
$dia_sem[2]="M";
$dia_sem[3]="X";
$dia_sem[4]="J";
$dia_sem[5]="V";
$dia_sem[6]="S";
$dia_sem[7]="D";
$dia_sem_largo[1]="Lunes";
$dia_sem_largo[2]="Martes";
$dia_sem_largo[3]="Miércoles";
$dia_sem_largo[4]="Jueves";
$dia_sem_largo[5]="Viernes";
$dia_sem_largo[6]="Sábado";
$dia_sem_largo[7]="Domingo";
$dia_sem_largo_plural[1]="lunes";
$dia_sem_largo_plural[2]="martes";
$dia_sem_largo_plural[3]="miércoles";
$dia_sem_largo_plural[4]="jueves";
$dia_sem_largo_plural[5]="viernes";
$dia_sem_largo_plural[6]="sábados";
$dia_sem_largo_plural[7]="domingos";
$nombre_mes[1]="Enero";
$nombre_mes[2]="Febrero";
$nombre_mes[3]="Marzo";
$nombre_mes[4]="Abril";
$nombre_mes[5]="Mayo";
$nombre_mes[6]="Junio";
$nombre_mes[7]="Julio";
$nombre_mes[8]="Agosto";
$nombre_mes[9]="Septiembre";
$nombre_mes[10]="Octubre";
$nombre_mes[11]="Noviembre";
$nombre_mes[12]="Diciembre";
$nombre_mes_corto[1]="Ene.";
$nombre_mes_corto[2]="Feb.";
$nombre_mes_corto[3]="Mar.";
$nombre_mes_corto[4]="Abr.";
$nombre_mes_corto[5]="May.";
$nombre_mes_corto[6]="Jun.";
$nombre_mes_corto[7]="Jul.";
$nombre_mes_corto[8]="Ago.";
$nombre_mes_corto[9]="Sep.";
$nombre_mes_corto[10]="Oct.";
$nombre_mes_corto[11]="Nov.";
$nombre_mes_corto[12]="Dic.";

//Colores y fuentes

$fondo_at = "#9acdec" ;
$color_fuente_opciones = "#00529C" ;
$fondo_web = "#FFFFFF";
$color_fuente = "#000000" ;
$color_fuente_link = "#343994";
$fuente_general = "Arial, Helvetica";
$fondo_ventana = "#777777";
$color_lat = "#cccccc";
$color_lat2 = "#dddddd";
$color_menu = "#ededed" ;
$empresa_curso = "NEOKIDS, S.L.";
$domicilio_empresa = "Avenida General Avilés, 54";
$domicilio_empresa_corto = "Avenida General Avilés, 54";
$poblacion_empresa = "Valencia";
$provincia_empresa = "Valencia";
$cp_empresa = "46015";
$fax_empresa = "";
$telefono_empresa = "";
$telefono1_empresa = "";
$mail_contacto_empresa="info@neo-kids.es";
$cif_empresa="";
$texto_inicio = "";
$texto_pie_pagina = "Gestión de la Piscina de Neokids";


//Nombre de la sesion
$usuarios_sesion="neokids_personal";

//Informacion para conectar a la base de datos

$sql_host=app_db_settings('sql_host');
$sql_db=app_db_settings('sql_db');
$sql_usuario=app_db_settings('sql_usuario');
$sql_pass=app_db_settings('sql_pass');
$sql_blog_ss = "blog";
$sql_blog_pass = (getenv('NEOKIDS_BLOG_DB_PASSWORD') ?: '');

foreach (array('sql_host'=>'APP_DB_HOST','sql_db'=>'APP_DB_NAME','sql_usuario'=>'APP_DB_USER','sql_pass'=>'APP_DB_PASSWORD') as $key=>$env) { if (getenv($env)!==false) $$key=getenv($env); }

//Tablas de la base de datos
//Tabla de usuarios
$sql_tabla = "usuarios";

//Variable para saber en todo momento donde estamos
$path_index=$_SERVER['PHP_SELF'];

//META TAGS para buscadores
$meta_description="";
$meta_keywords="";

//Inicia la sesion
app_start_session($usuarios_sesion);

$_SESSION['$url_web']=$url_web;

// -------- Chequear si existe la sesión -------

// Horarios del centro por defecto
$hora_entrada_centro_man="09:00:00";
$hora_salida_centro_man="13:00:00";
$hora_entrada_centro_tar="15:00:00";
$hora_salida_centro_tar="17:30:00";

$hora_entrada_centro_man_mj="09:00:00";
$hora_salida_centro_man_mj="15:30:00";
$hora_entrada_centro_tar_mj="13:00:00";
$hora_salida_centro_tar_mj="17:30:00";

require __DIR__.'/session-context.php';
$db_validacion=db_connect($sql_host,$sql_usuario,$sql_pass);
db_select_db($sql_db,$db_validacion);

// Nombres cursos

$array_nombres_cursos=array();
$busca_lista_cursos=$permitido ? db_query("SELECT id_curso,nombre FROM cursos_academicos") : false;
while($busca_lista_cursos && ($lista_cursos=db_fetch_array($busca_lista_cursos))) {
        $array_nombres_cursos[$lista_cursos["id_curso"]]=$lista_cursos["nombre"];
        }

//Introduce en tabla de estadisticas
$info_adicional=app_value($_SERVER,'QUERY_STRING');
$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
$HTTP_ACCEPT_LANGUAGE=app_value($_SERVER,'HTTP_ACCEPT_LANGUAGE');
$HTTP_USER_AGENT=app_value($_SERVER,'HTTP_USER_AGENT');
// mysql_query("INSERT INTO estadisticas1 (Sesion, Fecha, Hora, REMOTE_ADDR, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, Pagina_visitada, usuario, info_adicional) VALUES ('".session_id()."', '".date("Y/m/d")."', '".date("H:i")."', '".$REMOTE_ADDR."', '".$HTTP_ACCEPT_LANGUAGE."', '".$HTTP_USER_AGENT."', '".$path_index."', '$nombre_completo', '$info_adicional')") or die(mysql_error());

$ip_segura=true;

if ($REMOTE_ADDR=="10.10.1.25") {
        $ip_segura=true;
        }
if ($REMOTE_ADDR=="10.10.1.20") {
        $ip_segura=true;
        }
if ($REMOTE_ADDR=="10.10.1.102") {
        $ip_segura=true;
        }

if (strlen(app_value($_GET,'id_aula'))!=0) {
	$id_aula=(int)app_value($_GET,'id_aula',0);
	}
	
// Comprueba si hay que hacer copia del registro
/*
$comprueba_tam=mysql_query("SELECT * FROM estadisticas1");
if (mysql_num_rows($comprueba_tam)>50000) {
        // include("inc/guardar_registro_accesos.php");
        }
 */
 
// Colores subniveles natación
$colores_subniveles=array("#cecece","#9ed09c","#589b56","#135012","#9cbdd0","#43708a","#123b53","#d0af9c","#a16745","#5b2709"); 
$nombres_subniveles=array("0","1.1","1.2","1.3","2.1","2.2","2.3","3.1","3.2","3.3");
?>
