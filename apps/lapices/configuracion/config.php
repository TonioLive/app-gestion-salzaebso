<?php
require_once __DIR__ . '/deployment-config.php';
require_once __DIR__ . '/runtime.php';
if (PHP_SAPI !== 'cli') {
    // Evita que una peticion web bloqueada permanezca ejecutandose sin limite.
    @set_time_limit(300);
}
date_default_timezone_set("Europe/Madrid");
// ini_set("session.cookie_lifetime","3600");
// ini_set("session.gc_maxlifetime","3600");

// Funciones
if (function_exists('set_and_enum_values')==false) {
	function set_and_enum_values( $table , $field ){
	    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $field)) {
        return array();
    }
    $query = "SHOW COLUMNS FROM `$table` LIKE '$field'";
	    $result = db_query( $query ) or die( 'Error getting Enum/Set field ' . db_error() );
	    $row = db_fetch_array($result);
	    if(stripos(".".$row[1],"enum(") > 0) $row[1]=str_replace("enum('","",$row[1]);
	        else $row[1]=str_replace("set('","",$row[1]);
	    $row[1]=str_replace("','","\n",$row[1]);
	    $row[1]=str_replace("')","",$row[1]);
	    $ar = explode("\n",$row[1]);
	    for ($i=0;$i<count($ar);$i++) $arOut[str_replace("''","'",$ar[$i])]=str_replace("''","'",$ar[$i]);
	    return $arOut ;
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
			
		// Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
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

  // CCC :      [configure privately]
   // IBAN : [configure privately]

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
if (function_exists('generarDCIdelIban')==false) {
        function generarDCIdelIban( $_iban ) {

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


        return $dc;
        }
}
// Comprueba si estamos en https y si no redirecciona

if (!app_is_https()) {
        // header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        }

// Usuarios permiso acceso restringido
$usuariosPermitidos = array(1,29,938);

$desactivar_fichar=true;
$mostrar_alertas_mensajes=true;

$incluir_santpau_cocina=true;
$dia_configuracion=app_request_int($_GET, 'dia', 0);
if ($dia_configuracion>0) {
        if (date('Y-m-d',$dia_configuracion)<'2023-09-01') {
                $incluir_santpau_cocina=true;
        }
}

// Programacion inglés

$version_actual_prog_ingles=2;

// Tipos de proveedores
$subtipos_proveedor = array('Material fungible','Material no fungible','Alimentación','Limpieza','Otros');

//Datos que se deben modificar si se cambia la página de servidor o de carpeta
$onload_script="";
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

// Inicio mensajes

// $url_mensajes="http://www.escuelas-infantiles.com";

$url_mensajes="";

// Fin mensajes

$dominio_actual = (getenv('APP_BASE_URL') ?: 'https://localhost') ;
$url_web = "" ;
$web_path = dirname(__DIR__);
$upload_path = (lapices_app_path() . "/fotos_usuarios");
$upload_path_dni = (lapices_app_path() . "/dni_usuarios");
$upload_path_firma = (lapices_app_path() . "/firma_usuarios");
$upload_path_platos = (lapices_app_path() . "/fotos_platos");
$url_root = "" ;
$root_path = (lapices_app_path()) ;
$config_path = $dominio_actual.$url_root."/configuracion/" ;
$access_database="gestionguarderias";
$ano_inicio_actividad=2005;

$carpeta_documentacion=(lapices_storage_path('temp_informes'));

$galeria_fotos_path=(lapices_app_path() . "/galeria") ;
$descarga_docs_path=(lapices_app_path() . "/descargas") ;

$limite_de_edad=10;

$titulo_web = "Gestión de Escuelas Infantiles" ;
$nombre_centro = "LÁPICES" ;

//Configuración CHAT
$servidor_irc = "irc.empresa.com" ;
$canal_irc = "LAPICES" ;

//Configuracion correo
$servidor_de_correo = "mail.empresa.com" ;
$servidor_de_correo_saliente = $servidor_de_correo ;

$puerto_correo = 25 ;

$mail_principal = "webmaster@escuelas-infantiles.com" ;

//Aspecto de menus, noticias y dimensiones de tablas
$numero_columnas_menu = 5 ;

$ancho_pagina_total = 775 ;

$nombre_cookie = "Gestion_guarderias" ;

//Numero máximo de cursos mostrados en la página inicial
$cursos_inicio = 5 ;

//Longitud de las noticias de la barra lateral
$lonfitud_not = 100 ;

//Numero de noticias en buscador
$num_not = 10 ;

// Opcion seleccionada en el menú
$submenu_activo=app_request_value($_GET, "Submenu", "");

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

$fondo_at = "#afe5a0" ;
$color_fuente_opciones = "#00529C" ;
$fondo_web = "#FFFFFF";
$color_fuente = "#000000" ;
$color_fuente_link = "#343994";
$fuente_general = "Arial, Helvetica";
$fondo_ventana = "#777777";
$color_lat = "#cccccc";
$color_lat2 = "#dddddd";
$color_menu = "#ededed" ;
$empresa_curso = "ZAEBSAL, S.L.";
$domicilio_empresa = "Avenida General Avilés, 54";
$domicilio_empresa_corto = "Avenida General Avilés, 54";
$poblacion_empresa = "Valencia";
$provincia_empresa = "Valencia";
$cp_empresa = "46015";
$fax_empresa = "96 XXX XX XX";
$telefono_empresa = "96 347 97 56";
$telefono1_empresa = "";
$mail_contacto_empresa="info@escuelas-infantiles.com";
$cif_empresa="B97324123";
$texto_inicio = "";
$texto_pie_pagina = "Gestión de Escuelas Infantiles - Lápices";


//Nombre de la sesion
$usuarios_sesion="accesoprivado";

//Informacion para conectar a la base de datos

// $sql_host = (getenv('APP_DB_HOST') ?: 'localhost');
$sql_host=app_env_value('APP_DB_HOST', "localhost");
$sql_db=app_env_value('APP_DB_NAME', "gestionguarderias");
$sql_usuario=app_env_value('APP_DB_USER', '');
$sql_pass=app_env_value('APP_DB_PASSWORD', '');
$sql_blog_ss=app_env_value('APP_BLOG_DB_NAME', "blog");
$sql_blog_pass=app_env_value('APP_BLOG_DB_PASSWORD', '');
//Tablas de la base de datos
//Tabla de usuarios
$sql_tabla = "usuarios";

//Variable para saber en todo momento donde estamos
$path_index=app_request_value($_SERVER, 'PHP_SELF', '/');

//META TAGS para buscadores
$meta_description="";
$meta_keywords="";

//Inicia la sesion
app_start_session($usuarios_sesion);
$app_session_expired_scope=app_enforce_idle_timeout();
if ($app_session_expired_scope==='') {
        if (app_session_scope()!=='' && app_request_is_navigation()) {
                app_touch_user_activity();
        }
} else {
        // Evita avisos en las pantallas antiguas durante la peticion que caduca.
        $_SESSION['permitido_padre']=false;
        if (app_request_is_navigation() && !headers_sent()) {
                $status_redireccion=app_request_value($_SERVER, 'REQUEST_METHOD', 'GET')==='GET'
                        ? 302
                        : 303;
                header(
                        'Location: '.app_session_login_url($app_session_expired_scope, true),
                        true,
                        $status_redireccion
                );
                exit;
        }
}
app_security_headers(app_session_scope()!=='');

$_SESSION['$url_web']=$url_web;

// Alergias y Alimentos no introducidos
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_frutossecos","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos","alergias_cacahuetes","alergias_altramuces","alergias_apio","alergias_mostaza","alergias_sesamo","alergias_crustaceos","alergias_moluscos","alergias_proternera","alergias_pielmelocoton","alergias_cerdo","alergias_berenjena");
$array_nombres_alergias=array("Leche y sus derivados","Huevo","Frutos de cáscara","Gluten","Pescado","Soja","Sulfitos","Frutos secos sin cáscara","Altramuces","Apio","Mostaza","Sésamo","Crustaceos","Moluscos","Intolerancia a la Proteina Ternera","Intolerancia a la Piel Melocotón","Intolerancia al  Cerdo","Intolerancia al la berenjena");
$nombres_alergias=array("leche y sus derivados","huevo","frutos de cáscara","gluten","pescado","soja","sulfitos","frutos secos sin cáscara","altramuces","apio","mostaza","sésamo","crustaceos","moluscos","intolerancia a la proteina ternera","intolerancia a la piel melocotón","intolerancia al Cerdo","Intolerancia al la Berenjena");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Lentejas","Lacteos");
$nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Lentejas","Lacteos");


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

if (!isset($_SESSION['nombre_usu']) || !isset($_SESSION['usuario_password'])){
                $permitido=false;
        } else {
                $permitido=true;
                $identificador=$_SESSION['nombre_usu'];
                $nivel_acceso=$_SESSION['nivel_acceso'];
                $nombre_completo=$_SESSION['nombre_completo'];
                $email=$_SESSION['email_usu'];
                $nombre=$_SESSION['nombre'];
                $idUsuario=$_SESSION['idusuario'];
                $usuario_multicentro=$_SESSION['usuario_multicentro'];

                $zona_alumnos=$_SESSION['zona_alumnos'];
                $zona_aulas=$_SESSION['zona_aulas'];
                $zona_facturacion=$_SESSION['zona_facturacion'];
                $zona_comedor=$_SESSION['zona_comedor'];
                $zona_almacen=$_SESSION['zona_almacen'];
                $zona_incidencias=$_SESSION['zona_incidencias'];
                $zona_personal=$_SESSION['zona_personal'];
                $zona_pedidos=$_SESSION['zona_pedidos'];
                $zona_inferior=$_SESSION['zona_inferior'];
                $zona_mensajes=$_SESSION['zona_mensajes'];
                $zona_compras=$_SESSION['zona_compras'];
                $zona_gabinete=$_SESSION['zona_gabinete'];
                $zona_corregir_pruebas=$_SESSION['zona_corregir_pruebas'];
                $zona_actividades=$_SESSION['zona_actividades'];
                
                // if (($nivel_acceso<4) and ($usuario_multicentro==0)) {
                if (($nivel_acceso<=2) or ($nivel_acceso==10)) {
                                      if ($usuario_multicentro!=1) {
                                              $_SESSION['centro_actual']=$_SESSION['id_centro_usuario'];
                                              }
                                        if (strlen($_SESSION['centro_actual'])==0) {
                                                $_SESSION['centro_actual']=$_SESSION['id_centro_usuario'];
                                                }
                                        $db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
                                        db_select_db("$sql_db",$db_validacion);
                                        $centro_sesion=(int) $_SESSION['centro_actual'];
                                        $busca_datos_centro=db_query_params(
                                                "SELECT id_centro, nombre_centro, poblacion_centro, tipo_centro, hora_entrada1, hora_salida1, hora_entrada2, hora_salida2, hora_entrada1_mj, hora_salida1_mj, hora_entrada2_mj, hora_salida2_mj FROM centros WHERE id_centro=?",
                                                "i",
                                                array($centro_sesion),
                                                $db_validacion
                                        );
                                        if (db_num_rows($busca_datos_centro)!=0) {
                                                        $datos_centro=db_fetch_array($busca_datos_centro);
                                                        $_SESSION['nombre_centro_actual']=$datos_centro["nombre_centro"];
                                                        $_SESSION['poblacion_centro_actual']=$datos_centro["poblacion_centro"];

                                                        $hora_entrada_centro_man=$datos_centro["hora_entrada1"];
                                                        $hora_salida_centro_man=$datos_centro["hora_salida1"];
                                                        $hora_entrada_centro_tar=$datos_centro["hora_entrada2"];
                                                        $hora_salida_centro_tar=$datos_centro["hora_salida2"];

                                                        $hora_entrada_centro_man_mj=$datos_centro["hora_entrada1_mj"];
                                                        $hora_salida_centro_man_mj=$datos_centro["hora_salida1_mj"];
                                                        $hora_entrada_centro_tar_mj=$datos_centro["hora_entrada2_mj"];
                                                        $hora_salida_centro_tar_mj=$datos_centro["hora_salida2_mj"];

                                                } else {
                                                        $_SESSION['centro_actual']="";
                                                        $_SESSION['nombre_centro_actual']="";
                                                        $_SESSION['poblacion_centro_actual']="";
                                                }
                                        // La conexion se reutiliza durante esta peticion.
                                      }
									  
                // Cambio de centro limitado a la portada, por POST y con CSRF.
                $es_solicitud_cambio_centro = $path_index === "/index.php"
                        && app_request_value($_SERVER, "REQUEST_METHOD", "") === "POST"
                        && app_request_value($_POST, "panel_action", "") === "change_centre";
                if ($es_solicitud_cambio_centro) {
                        $centro_enviado=app_request_int($_POST, "id_centro_seleccionado", 0);
                        $puede_cambiar_centro=((int) $nivel_acceso>=3 || (int) $usuario_multicentro===1);
                        $csrf_centro_valido=app_verify_csrf(app_request_value($_POST, "_csrf", ""));

                        if ($centro_enviado>0 && $puede_cambiar_centro && $csrf_centro_valido) {
                                $db_cambio_centro=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
                                db_select_db("$sql_db",$db_cambio_centro);
                                $filtro_centro=(int) $idUsuario===1 ? "" : " AND mostrar_en_lapices=1";
                                $sql_cambio_centro="SELECT id_centro, nombre_centro, poblacion_centro, tipo_centro, hora_entrada1, hora_salida1, hora_entrada2, hora_salida2, hora_entrada1_mj, hora_salida1_mj, hora_entrada2_mj, hora_salida2_mj FROM centros WHERE id_centro=?";
                                if ($filtro_centro!=="") {
                                        $sql_cambio_centro.=" AND mostrar_en_lapices=1";
                                }
                                $busca_datos_centro=db_query_params(
                                        $sql_cambio_centro,
                                        "i",
                                        array($centro_enviado),
                                        $db_cambio_centro
                                );

                                if ($busca_datos_centro && db_num_rows($busca_datos_centro)!==0) {
                                        $datos_centro=db_fetch_array($busca_datos_centro);
                                        $_SESSION['centro_actual']=(int) $datos_centro["id_centro"];
                                        $_SESSION['nombre_centro_actual']=$datos_centro["nombre_centro"];
                                        $_SESSION['poblacion_centro_actual']=$datos_centro["poblacion_centro"];
                                        $_SESSION['tipo_centro_actual']=(int) $datos_centro["tipo_centro"];
                                } else {
                                        $_SESSION['panel_flash']=array(
                                                "message" => "No se ha podido seleccionar ese centro.",
                                                "type" => "error"
                                        );
                                }

                                db_close($db_cambio_centro);
                                header("Location: ".$url_root."/index.php");
                                exit;
                        } else {
                                $_SESSION['panel_flash']=array(
                                        "message" => "La solicitud de cambio de centro no es valida.",
                                        "type" => "error"
                                );
                        }
                }

                $centro_actual=app_request_int($_SESSION, "centro_actual", 0);
                $nombre_centro_actual=app_request_value($_SESSION, "nombre_centro_actual", "");
                $poblacion_centro_actual=app_request_value($_SESSION, "poblacion_centro_actual", "");
                $tipo_centro_actual=app_request_int($_SESSION, "tipo_centro_actual", 0);
                }

// Proteccion central de las rutas privadas heredadas.
app_enforce_management_request_security($permitido);

$db_validacion=db_link();
if (!($db_validacion instanceof mysqli)) {
        $db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
}
db_select_db("$sql_db",$db_validacion);

//PROFILING
require_once __DIR__ . '/personal_onboarding.php';
personal_onboarding_guard($permitido);

/*
if(($_SERVER['REMOTE_ADDR'] == '84.127.234.248') or ($_SERVER['REMOTE_ADDR'] == '213.129.165.204')){
    mysql_query("set profiling_history_size=200");
    mysql_query("SET profiling=1");
}
*/

// Nombres cursos
$array_nombres_cursos=array();
if ($permitido===true || !empty($_SESSION['permitido_padre'])) {
        $cache_cursos=app_request_value($_SESSION, '_app_course_names', array());
        $cache_cursos_fecha=app_request_int($_SESSION, '_app_course_names_at', 0);
        if (is_array($cache_cursos) && $cache_cursos_fecha>(time()-300)) {
                $array_nombres_cursos=$cache_cursos;
        } else {
                $busca_lista_cursos=db_query("SELECT id_curso, nombre FROM cursos_academicos");
                while($lista_cursos=db_fetch_array($busca_lista_cursos)) {
                        $array_nombres_cursos[$lista_cursos["id_curso"]]=$lista_cursos["nombre"];
                        }
                $_SESSION['_app_course_names']=$array_nombres_cursos;
                $_SESSION['_app_course_names_at']=time();
        }
}

//Introduce en tabla de estadisticas
$info_adicional=app_request_value($_SERVER, "QUERY_STRING", "");
$REMOTE_ADDR=app_request_value($_SERVER, "REMOTE_ADDR", "");
$HTTP_ACCEPT_LANGUAGE=app_request_value($_SERVER, "HTTP_ACCEPT_LANGUAGE", "");
$HTTP_USER_AGENT=app_request_value($_SERVER, "HTTP_USER_AGENT", "");
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

if (app_request_int($_GET,"id_aula",0)!=0) {
        $id_aula=app_request_int($_GET,"id_aula",0);
        }

// Comprueba si hay que hacer copia del registro
/*
$comprueba_tam=mysql_query("SELECT * FROM estadisticas1");
if (mysql_num_rows($comprueba_tam)>50000) {
        // include("inc/guardar_registro_accesos.php");
        }
 */
 
// estadisticas padres
if ((!empty($_SESSION['permitido_padre']))
        and ($path_index!='/gestion/agenda/control_alertas.php')
        and app_request_is_navigation()) {
	
	$string_log=date("Y/m/d H:i:s").",".$path_index.",".$info_adicional.";";
	
	db_query_params(
                "UPDATE control_acceso_padres "
                . "SET logacceso=RIGHT(CONCAT(COALESCE(logacceso, ''), ?), 16000) "
                . "WHERE sesion=?",
                "ss",
                array($string_log, session_id())
        );
	
} 
 
?>
