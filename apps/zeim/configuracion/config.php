<?php
require_once __DIR__.'/runtime.php';
if (!defined('APP_SITE')) define('APP_SITE', 'santpau');
if (PHP_SAPI !== 'cli') @set_time_limit(300); 
date_default_timezone_set("Europe/Madrid");

	
$permiso_entrar=false;
	
if ($_SERVER['REMOTE_ADDR']=='79.117.61.64') {	
        $permiso_entrar=true;	
}


// Funciones
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
             //compruebo que la terminaci?n del dominio sea correcta
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
if (function_exists('CalcularLetraNIF')==false) {
	function CalcularLetraNIF( $dni ) {
		
		// Mayusculas
		$dni=mb_convert_case($dni, MB_CASE_UPPER);
		
		// Extrae partes
		$parte1=substr($dni,0,1);
		$parte3=substr($dni,strlen($dni)-1,1);			
		$inicio_parte2=1;
		$numLetras=2;
		if (is_numeric($parte1)) {
				$parte1="";
				$inicio_parte2=0;
				$numLetras--;
		}
		if (is_numeric($parte3)) {
				$parte3="";
				$numLetras--;
		}			
		$parte2=substr($dni,$inicio_parte2,strlen($dni)-$numLetras);			
		
		// Determina si DNI o NIE
		$numeroDNI=$parte2;
		switch ($parte1) {
			
			case "X":
				$numeroDNI="0".$parte2;
			break;
			case "Y":
				$numeroDNI="1".$parte2;
			break;
			case "Z":
				$numeroDNI="2".$parte2;
			break;			
			
		}
		
		// Calcula letra
		$letras="TRWAGMYFPDXBNJZSQVHLCKE";
        $valor= (int) ($numeroDNI / 23);
        $valor *= 23;
        $valor= $numeroDNI - $valor;
        $letraNif= substr ($letras, $valor, 1);
		
		$resultado = array($parte1.$parte2,$letraNif);
		
		return $resultado;
	}
}
if (function_exists('comprobar_nif')==false) {
	function comprobar_nif($dni) {

		$dni="000000000".$dni;
		$dni=substr($dni,strlen($dni)-9,9);
	
		if(strlen($dni)<9) {
			return "DNI demasiado corto.";
		}
		
		$dni = strtoupper($dni);
	
		$letra = substr($dni, -1, 1);
		$numero = substr($dni, 0, 8);
			
		// Si es un NIE hay que cambiar la primera letra por 0, 1 ? 2 dependiendo de si es X, Y o Z.
		$numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);	
	
		$modulo = $numero % 23;
		$letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
		$letra_correcta = substr($letras_validas, $modulo, 1);
		
		if($letra_correcta!=$letra) {
			return "Letra incorrecta, la letra deber&iacute;a ser la $letra_correcta.";
		}
		else {
			return "OK";
		}
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
   // IBAN : IBAN_DE_EJEMPLO_NO_OPERATIVO

  $codPais = "ES";
  $dc = "00"; // No sabemos el d?gito de control del IBAN, ponemos 00

  $iban_ = $codPais."".$dc."".$_entidad."".$_sucursal."".$_dc."".$_cuenta;

  return $iban_;
 }
}

if (function_exists('generarDCInToIban')==false) {
function generarDCInToIban( $_iban ) {

  $ibanConDC_ = -1;

  // IBAN sin DC, DC = 00 : BE00999999999999
   // IBAN con DC, DC = 89 : BE89999999999999

  // Mover los cuatro primeros caracteres del n?mero IBAN a la derecha:
  $ibanConDC_ = substr($_iban,4)."".substr($_iban,0,4);


  // Convertir las letras a n?meros seg?n la siguiente tabla:
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


  // Aplicar la operación módulo 97 y restar al n?mero 98, el valor obtenido.
  // Si el resultado consta de s?lo un d?gito, insertar un cero a la izquierda.

  //$modulo97 = intval($ibanConDC_) % 97;
  //$modulo97 = $ibanConDC_ % 97;
  $modulo97 = my_bcmod($ibanConDC_,  97);

  $dc = 98 - $modulo97;

  // insertar 0 a la izquierda si fuera menor de dos d?gitos
  $dc = sprintf("%02d",$dc);

  // Sustituimos los d?gitos 2 y 4 por el $dc
  $ibanConDC_ =  substr($_iban,0,2).$dc.substr($_iban,4);


  return $ibanConDC_;
 }
}

// Comprueba si estamos en https y si no redirecciona

if (app_value($_SERVER,'HTTPS')!="on") {
        // header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        }

// Usuarios permiso acceso restringido
$usuariosPermitidos = array(1,29,938);

$desactivar_fichar=true;

//Datos que se deben modificar si se cambia la página de servidor o de carpeta

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

// Inicio mensajes

// $url_mensajes="http://www.escuelas-infantiles.com";

$url_mensajes="";

// Fin mensajes

$dominio_actual = "http://gestion.escuelainfantilsantpau.com" ;
$url_web = "" ;
$web_path = dirname(__DIR__);
$upload_path = dirname(__DIR__).'/fotos_usuarios';
$upload_path_dni = dirname(__DIR__).'/dni_usuarios';
$upload_path_firma = dirname(__DIR__).'/firma_usuarios';
$url_root = "" ;
$root_path = dirname(__DIR__);
$config_path = $dominio_actual.$url_root."/configuracion/" ;
$access_database="gestionguarderias";
$ano_inicio_actividad=2005;

$carpeta_documentacion = dirname(__DIR__).'/.private/documentacion';

$galeria_fotos_path = dirname(__DIR__).'/galeria';
$descarga_docs_path = dirname(__DIR__).'/descargas';

$limite_de_edad=10;

$titulo_web = "ZEIM - Escuelas municipales de Zaebsal" ;
$nombre_centro = "Escuela Infantil Municipal" ;

//Configuraci?n CHAT
$servidor_irc = "irc.empresa.com" ;
$canal_irc = "santpau" ;

//Configuracion correo
$servidor_de_correo = "mail.empresa.com" ;
$servidor_de_correo_saliente = $servidor_de_correo ;

$puerto_correo = 25 ;

$mail_principal = "webmaster@empresa.com" ;

//Aspecto de menus, noticias y dimensiones de tablas
$numero_columnas_menu = 5 ;

$ancho_pagina_total = 775 ;

$nombre_cookie = "Gestion_guarderias" ;

//Numero m?ximo de cursos mostrados en la p?gina inicial
$cursos_inicio = 5 ;

//Longitud de las noticias de la barra lateral
$lonfitud_not = 100 ;

//Numero de noticias en buscador
$num_not = 10 ;

// Opcion seleccionada en el men?
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
$dia_sem_largo[3]="Miercoles";
$dia_sem_largo[4]="Jueves";
$dia_sem_largo[5]="Viernes";
$dia_sem_largo[6]="Sábado";
$dia_sem_largo[7]="Domingo";
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

$fondo_at = "#c1d100" ;
$color_fuente_opciones = "#00529C" ;
$fondo_web = "#FFFFFF";
$color_fuente = "#000000" ;
$color_fuente_link = "#343994";
$fuente_general = "Arial, Helvetica";
$fondo_ventana = "#777777";
$color_lat = "#cccccc";
$color_lat2 = "#dddddd";
$color_menu = "#ededed" ;
$empresa_curso = "ZEIM Escuelas Infantils";
$domicilio_empresa = "Domicilio de la empresa";
$domicilio_empresa_corto = "Domicilio de la empresa (corto)";
$poblacion_empresa = "Poblacion";
$provincia_empresa = "Provincia";
$cp_empresa = "46XXX";
$fax_empresa = "96 XXX XX XX";
$telefono_empresa = "96 XXX XX XX";
$telefono1_empresa = "96 XXX XX XX";
$mail_contacto_empresa="info@empresa.com";
$cif_empresa="B12345678";
$texto_inicio = "";
$texto_pie_pagina = "Gestión de Escuelas Infantiles";


//Nombre de la sesion
$usuarios_sesion="santpau_personal";

//Informacion para conectar a la base de datos

// $sql_host = "88.26.220.146";
$sql_host=app_db_settings('sql_host');
$sql_db=app_db_settings('sql_db');
$sql_usuario=app_db_settings('sql_usuario');
$sql_pass=app_db_settings('sql_pass');
$sql_blog_ss = (getenv('ZEIM_BLOG_USER_01') ?: '');
$sql_blog_pass = (getenv('ZEIM_BLOG_PASSWORD_01') ?: '');

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

// Alergias y Alimentos no introducidos
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_frutossecos","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos","alergias_cacahuetes","alergias_altramuces","alergias_apio","alergias_mostaza","alergias_sesamo","alergias_crustaceos","alergias_moluscos","alergias_proternera","alergias_pielmelocoton","alergias_cerdo","alergias_berenjena");
$array_nombres_alergias=array("Leche y sus derivados","Huevo","Frutos de cáscara","Gluten","Pescado","Soja","Sulfitos","Frutos secos sin cáscara","Altramuces","Apio","Mostaza","Sésamo","Crustaceos","Moluscos","Intolerancia a la Proteina Ternera","Intolerancia a la Piel Melocotón","Intolerancia al  Cerdo","Intolerancia al la berenjena");
$nombres_alergias=array("leche y sus derivados","huevo","frutos de cáscara","gluten","pescado","soja","sulfitos","frutos secos sin cáscara","altramuces","apio","mostaza","sésamo","crustaceos","moluscos","intolerancia a la proteina ternera","intolerancia a la piel melocotón","intolerancia al Cerdo","Intolerancia al la Berenjena");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");
$nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");



// -------- Chequear si existe la sesi?n -------

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

$array_path_index=explode('/',$path_index);

if (($identificador=="ayuntamiento")) {
if ((($array_path_index[2]!="editar_servicios.php") and ($array_path_index[1]=="facturacion")) or ($array_path_index[1]=="gestion_gastos") or ($array_path_index[1]=="caja") or ($array_path_index[1]=="almacen") or (($array_path_index[1]=="gestion") and ($array_path_index[2]!="centros"))) {
        die();
        }
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
?>
