<?php
require_once dirname(__DIR__).'/configuracion/family.php';
$verified_family_id=family_identity();
$contador_documentos=0;
$lista_alumnos_gestionados='';
$arrayFiltrosCentro=array();
$arrayFiltrosAula=array();


// Comprobar movil

if (function_exists('isMobile')==false) {
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}
$comprobarMovil=false;
$mostrar_pagado=false;

// Fin comprobar movil

// Imágenes no vistas

if (function_exists('contarImagenesNuevas')==false) {

  /**
  * Función simplificada para contar imágenes nuevas en una carpeta y sus subcarpetas.
  *
  * @param string $directorio El directorio a procesar.
  * @param array $imagenes_vistas Las imágenes vistas previamente.
  * @param string $fechaCorte Fecha de corte para marcar imágenes como nuevas.
  * @return int Número de imágenes nuevas encontradas.
  */
  function contarImagenesNuevas($directorio, $imagenes_vistas, $fechaCorte) {
  $elementos = array_diff(scandir($directorio), array('.', '..', 'thumbnails')); // Excluir '.' y '..'
  $nuevas = 0;

  foreach ($elementos as $elemento) {
    $rutaCompleta = $directorio . '/' . $elemento;
    if (is_dir($rutaCompleta)) {
    // Si es una subcarpeta, llamar a la función recursivamente
    $nuevas += contarImagenesNuevas($rutaCompleta, $imagenes_vistas, $fechaCorte);
    } else {
    // Verificar si es una imagen
    $extensionesImagen = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
    $extension = pathinfo($elemento, PATHINFO_EXTENSION);
    if (in_array(strtolower($extension), $extensionesImagen)) {
            // Comprobar si la imagen es nueva (modificada después de la fecha de corte)
            if (!in_array($rutaCompleta, $imagenes_vistas) && filemtime($rutaCompleta) > strtotime($fechaCorte)) {
            $nuevas++;
            }
    }
    }
  }

  return $nuevas;
  }

}

// Fin imágenes no vistas

require("../configuracion/autentifica_padre.php");

// Desactivamos mensajes para administracion
$mostrar_mensajes_administracion=true;

if (strlen(app_value($_GET,"id_curso"))!=0) {
$id_curso_actual=app_value($_GET,"id_curso");
} else {

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                /*
                $busca_curso_actual=mysql_query("SELECT * FROM cursos_academicos WHERE fecha_fin<='".date("Y-m-d")."' LIMIT 1;");
                if (mysql_num_rows($busca_curso_actual)!=0) {
                  $datos_curso_actual=mysql_fetch_array($busca_curso_actual);
                  $id_curso_actual=$datos_curso_actual["id_curso"]; 
                }
                */

                }

}

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=app_value($_GET,"version_js");

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=app_value($_SESSION,'version_js',0);
        }
if (strlen($version_js)==0) {
        $version_js=0;
        }

$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));
        
        if (db_num_rows($busca_usuario)==0) {
                $errores="El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                // Mostramos la información del niño

                $id_usuario=$datos_usuario["id_usuario"];
                $id_hijo=$datos_usuario["padre_de"];
                
                // Obtener imágenes vistas previamente por el usuario
                if ($id_usuario==10200) {
                  $fechaCorte = '2024-09-11'; // Fecha de corte, solo se marcarán nuevas las imágenes modificadas después de esta fecha
                } else {
                  $fechaCorte = '2024-11-21'; // Fecha de corte, solo se marcarán nuevas las imágenes modificadas después de esta fecha
                }

                $query = db_query_params('SELECT nombre_imagen FROM registro_vista_imagenes WHERE id_usuario = ?', 's', array($id_usuario));
                $imagenes_vistas = [];
                while ($row = db_fetch_array($query)) {
                $imagenes_vistas[] = $row['nombre_imagen'];
                }

                // Busca datos del padre.
                $busca_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario)) or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                // Comprueba si tiene email
                $no_email=false;
                if (strlen((string)app_value($datos_padre,"email"))==0) {
                        $no_email=true;
                        $errores="-No disponemos de tu dirección de Email.<br>";
                        } else {
                        // y si el mail es correcto
                        $email_padre=$datos_padre["email"];
                        if ((strpos($email_padre,"@") ? strpos($email_padre,"@")+1 : 0)==0 || (strpos($email_padre," ") ? strpos($email_padre," ")+1 : 0)!=0 || (strpos($email_padre,".") ? strpos($email_padre,".")+1 : 0)==0)
                                {
                                $no_email=true;
                                $errores="- El E-Mail que tenemos parece ser incorrecto, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).<br>";
                                }
                        }
                // Comprueba DNI
                $dni_padre=$datos_padre["DNI"];
                if (strlen($dni_padre)==0)
                                {
                                $errores="- No disponemos de tu nº de DNI o NIE.";
                                }
                // Comprueba telefono
                $telefono_padre=$datos_padre["telefono1"].$datos_padre["telefono2"].$datos_padre["movil"];
                if (strlen($telefono_padre)==0)
                                {
                                $errores="- No disponemos de ningún teléfono de contacto.";
                                }
                // Comprueba domicilio
                $domicilio_padre=$datos_padre["Direccion"];
                if (strlen($domicilio_padre)==0)
                                {
                                $errores="- No disponemos de tu domicilio.";
                                }
                // Comprueba cp
                $codigo_postal_padre=$datos_padre["cp"];
                if (strlen($codigo_postal_padre)==0)
                                {
                                $errores="- No disponemos de tu código postal.";
                                }
                }

        }
        
// Anulación de errores TEMPORAL        
$errores="";        
        
$mostrar_registro_padres=false;        
if ($errores!="") {
	
	$mostrar_registro_padres=true;
	
	} else {
		
		// recopila toda la información necesaria para el menú lateral y las alertas


    $fechalimite=strtotime(date("Y-m-d"))-(86400*30);	
    // $fechalimite=strtotime(date("Y-m-d"));	
    /*
    // Solo para depuracion usuario DEMO
    if ($id_usuario!=10200) {
      $fechalimite=strtotime(date("Y-m-d"))+(86400*15);	
    } else {
      $fechalimite=strtotime(date("Y-m-d"))-(86400*820); // Usuario DEMO
    }
    */


		$mostrar_mensajes=true;
		$mostrar_documentacion=true;
		$mostrar_agenda=true;
    $imagenesNuevas=0;

		// Notificaciones de mensajes  	       
        
		// Busca otros padres
	    $filtro_padres="";
	    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
	    while($padres=db_fetch_array($busca_padres)){
	            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
	            }
	    if ($filtro_padres!="") {
	            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
	            }
	
	
	    // Busca mensajes
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("No leido","Leido","Cerrado");
        
	    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) ORDER BY fecha DESC, hora DESC");
	
	    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) AND tipo_nota=2 AND estado=0 ORDER BY fecha DESC");

	    $total_no_leidos=db_num_rows($busca_no_leidos);
	    
	    // Busca respuestas
	    while($mensajes=db_fetch_array($busca_mensajes)) {
	            $busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=? AND respondido_por<>?', 'ss', array($mensajes["id_nota_padre"], $id_usuario));
	            if (db_num_rows($busca_respuestas)!=0) {
	                    $total_no_leidos++;
	                }
	            }
	            
	    $texto_alerta1="";
	    
	    if ($total_no_leidos!=0) {
		    
	            $coletilla_leido="";
	            if ($total_no_leidos>1) {
	                    $coletilla_leido="s";
	                    }
	            $texto_alerta1="Tienes $total_no_leidos mensaje$coletilla_leido no leido$coletilla_leido";
	            
	   		} else {
		            
				$texto_alerta1="No tienes mensajes nuevos";
		            
	        }		    
	    
	    // Bucle de hijos
	    
	    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");
	
	    if (db_num_rows($busca_datos_alumno)!=0) {
		 		 			 
			$lista_alumnos_gestionados='        
	        <div class="row">
	          <div class="col-xs-4 text-center">
	            <b>Curso</b>
	          </div>
	          <div class="col-xs-4 text-center">
	            <b>Alumno</b>
	          </div>
	          <div class="col-xs-4 text-center">
	            <b>Aula</b>
	          </div>
	        </div>
	        ';
		 
	        $contador_hijos=0;
          $contador_documentos=0; 

	        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
	                
	                $id_curso_mostrado=$datos_hijos["id_curso"];
	                
	                $busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
	                if (db_num_rows($busca_datos_cursos)!=0) {
	                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
	                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
	                        } else {
	                        $nombre_curso_mostrado="";
	                        }
	                        
	                // Datos de la escuela del niño
	                $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"])) or die(db_error());
	
	                $datos_escuela=db_fetch_array($busca_datos_escuela);	   

                  // Datos del aula del niño
                  $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"])) or die(db_error());
	
                  $datos_aula=db_fetch_array($busca_datos_aula);	                          
	                	
					// Listado de alumnos en menú superior
	                	
					$lista_alumnos_gestionados.='        
		                <div class="row">
		                  <div class="col-xs-4 text-center">
		                    '.$nombre_curso_mostrado.'
		                  </div>
		                  <div class="col-xs-4 text-center">
		                    '.$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"].'
		                  </div>
                      <div class="col-xs-4 text-center">
		                    '.$datos_aula["nombre"].'
		                  </div>
		                </div>
		                ';    
	                


					// Notificación de documentos y carpeta no creada
					
					$nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
					$nombre_del_curso="Curso ".$datos_curso_mostrado["nombre"]."-ID".$datos_aula["id_curso"];		
          $nombre_aula=$datos_aula["nombre"]."-ID".$datos_aula["id_aula"];			

          // Carpeta fotos
          $baseDirectory = "/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/$nombre_aula";

          // Carpeta documentación                
					$descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/documentacion/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";

			    $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');
        
			    $descarga_centro_path=str_replace($sustituir,"_",$descarga_centro_path);					
					          																
					if (file_exists($descarga_centro_path)) {         
						$d = dir($descarga_centro_path);

                        while($s=$d->read()) {

                        	if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="XLSX")) and (substr($s,0,1)!='.')) {

	                            // Busca si leido
	                            $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . '/' . (string)($s)));
	
	                            if (db_num_rows($busca_leido)==0) {
		                            $contador_documentos++;
	                            }
	                    	}
                        }
                        $d->close(); 
                    } else {
						          $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del centro. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                	}
          
          // Busca imágenes sin ver
          
          $imagenesNuevas+= contarImagenesNuevas($baseDirectory, $imagenes_vistas, $fechaCorte);           

          // Fin imágenes
	       	}   		    
		}										
	}
	
    $texto_alerta0="";

    if ($imagenesNuevas!=0) {
	    
            $coletilla_leido="";
            if ($imagenesNuevas>1) {
                    $coletilla_leido="s";
                    }
            $texto_alerta0="Hay $imagenesNuevas foto$coletilla_leido nueva$coletilla_leido";
            
   		} else {
	            
			$texto_alerta0="No hay fotos nuevas";
	            
        }	    

    $texto_alerta2="";
    
    if ($contador_documentos!=0) {
	    
            $coletilla_leido="";
            if ($contador_documentos>1) {
                    $coletilla_leido="s";
                    }
            $texto_alerta2="Tienes $contador_documentos documento$coletilla_leido nuevo$coletilla_leido  sin leer";
            
   		} else {
	            
			$texto_alerta2="No tienes documentos nuevos";
	            
        }		
        
  // Busca circulares enviadas        
  $hay_circulares=false;
  
  if (basename($_SERVER['PHP_SELF'])=='index.php') {
  
          $busca_datos_alumno=db_query_params('SELECT *, alumnos.id_centro AS id_centro_alumno FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.");
          
          $fecha_inicio_mensajes='2024-10-01';
          $arrayFiltrosAula=array();
          while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
                  $arrayFiltrosCentro[$datos_alumno["id_centro_alumno"]]="(aul='' AND cen=".$datos_alumno["id_centro_alumno"].")";
                  $arrayFiltrosAula[$datos_alumno["aula"]]="(aul=".$datos_alumno["aula"].")";
                  
                  // Muestra solo mensajes desde que el alumno se inscribió
                  if (strtotime($datos_alumno["fecha_matricula"])>=strtotime($fecha_inicio_mensajes)) {
                          $fecha_inicio_mensajes=$datos_alumno["fecha_matricula"];
                  }
                          
          }
          
          $listaFiltros1=implode(" OR ",$arrayFiltrosCentro);
          $listaFiltros2=implode(" OR ",$arrayFiltrosAula);
          
          $allFilters=array_merge(array_values($arrayFiltrosCentro),array_values($arrayFiltrosAula)); $lista_filtros_aula=$allFilters ? " AND (".implode(" OR ",$allFilters).")" : " AND 1=0";        
          
          
          $select_mensajes="SELECT * FROM mensajes_temporal WHERE mensajes_temporal.contactos='0' AND mensajes_temporal.fecha>='$fecha_inicio_mensajes'".$lista_filtros_aula." ORDER BY mensajes_temporal.fecha";
          
          $busca_mensajes=db_query($select_mensajes);
                  
          while($datos_mensajes=db_fetch_array($busca_mensajes)) {   
                  
                  $busca_lectura=db_query_params('SELECT * FROM mensajes_registro_lectura WHERE id_mensaje_email=? AND id_usuario=?', 'ss', array($datos_mensajes["id_mensaje"], $id_usuario)) or die(db_error());
          
                  if (db_num_rows($busca_lectura)==0) {
                          $hay_circulares=true;
                  }
          
          }              
  }
  // Fin circulares              

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="windows-1252">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="<?php  echo $meta_description ; ?>">
  <meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">  
  
  <link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.icon">
  <link rel="icon" href="/favicon.ico" type="image/vnd.microsoft.icon">
    
  <title><?php  echo $titulo_web ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, viewport-fit=cover" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/app/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/app/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="/app/dist/css/extra.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/app/dist/css/skins/_all-skins.min.css">

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/app/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  
    <!-- fullCalendar 2.2.5-->
  <link rel="stylesheet" href="/app/plugins/fullcalendar/fullcalendar.css">
  <link rel="stylesheet" href="/app/plugins/fullcalendar/fullcalendar.print.css" media="print">
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<link rel="stylesheet" href="/app/assets/familias-app.css?v=20260923"><link rel="stylesheet" href="/app/assets/brand.css?v=20260923"></head>
<!-- ADD THE CLASS layout-boxed TO GET A BOXED LAYOUT -->
<body class="hold-transition skin-blue layout-boxed sidebar-mini familias-app-shell family-zeim">
<!-- Site wrapper -->
<div class="wrapper">

  <header class="main-header"><a class="logo" href="/app/"><span class="logo-mini">Z</span><span class="logo-lg">ZEIM</span></a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Contraer o expandir menú lateral</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Fotos: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-file-image-o"></i>
              <?php if ($imagenesNuevas>0) { ?>
              <span class="label label-success"><?=$imagenesNuevas?></span>
              <?php } ?>
            </a>
            <ul class="dropdown-menu">
              <li class="header"><?=$texto_alerta0?></li>
              <li class="footer"><a href="/app/fotos.php">Ir a las galerías de fotos</a></li>
            </ul>
          </li>

          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <?php if ($total_no_leidos>0) { ?>
              <span class="label label-success"><?=$total_no_leidos?></span>
              <?php } ?>
            </a>
            <ul class="dropdown-menu">
              <li class="header"><?=$texto_alerta1?></li>
              <li class="footer"><a href="/app/mensajeria.php">Ver todos los mensajes</a></li>
            </ul>
          </li>

          <!-- Documentos nuevos: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-file-text-o"></i>
              <?php if ($contador_documentos>0) { ?>
              <span class="label label-warning"><?=$contador_documentos?></span>
              <?php } ?>
            </a>
            <ul class="dropdown-menu">
              <li class="header"><?=$texto_alerta2?></li>
              <li class="footer"><a href="/app/documentos.php">Ver todos los documentos</a></li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="/app/dist/img/default-user-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?=mb_convert_case(security_escape($datos_padre["Nombre"])." ".security_escape($datos_padre["Apellido1"])." ".security_escape($datos_padre["Apellido2"]), MB_CASE_TITLE)?></span>              
            </a>                
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="/app/dist/img/default-user-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  <?=mb_convert_case(security_escape($datos_padre["Nombre"])." ".security_escape($datos_padre["Apellido1"])." ".security_escape($datos_padre["Apellido2"]), MB_CASE_TITLE)?>
                  <?php
                  // Mostramos bienvenida
                  print "¡Hola! ";
                  $hora=date("H");
                  if (($hora>=22 || $hora<5))
                    {
                      print "Buenas noches, ";  }

                    if ($hora>=15 && $hora<22)
                    {
                      print "Buenas tardes, ";  }

                    if ($hora>=5 && $hora<15)
                    {
                      print "Buenos dias, ";  }
                      
                  print "<i>".strtoupper($datos_padre["Nombre"])."</i>";                
                ?>                  
                  <small>Alumnos Gestionados</small>                  
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
              	<?=$lista_alumnos_gestionados?>
              </li>
              <?php 
              if ($_SERVER['HTTP_HOST']!="santpau.escuelas-infantiles.com") {
                ?>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <form method="post" action="/app/salir.php"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><button class="btn btn-responsive btn-default btn-flat" type="submit">Cerrar sesión <i class="fa fa-sign-out"></i></button></form>
                </div>
              </li>
              <?php
              }
              ?>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- =============================================== -->

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="/app/dist/img/default-user-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?=mb_convert_case(security_escape($datos_padre["Nombre"])." ".security_escape($datos_padre["Apellido1"])." ".security_escape($datos_padre["Apellido2"]), MB_CASE_TITLE)?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">Menú Principal</li>
		<li><a href="/app/index.php"><i class="fa fa-home"></i> <span>Inicio</span></a>
        <li>
          <a href="/app/mensajeria.php">
            <i class="fa fa-envelope"></i> <span>Mensajes privados</span>
            <?php if ($total_no_leidos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-green"><?=$total_no_leidos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>
        <li><a href="#" id="cambiarSrc" data-url="/app/ver_ultimo_mensaje.php?todos=1"><i class="fa fa-envelope-o"></i> Circulares Email</a></li>
        <li><a href="/app/notas_educadora.php"><i class="fa fa-sticky-note-o"></i> <span>Notas educadoras</span></a></li>			
        <li><a href="/app/notificaciones.php"><i class="fa fa-bell-o"></i> <span>Notificaciones recibidas</span></a></li>			
        <li><a href="/app/comedor.php"><i class="fa fa-cutlery"></i> <span>Comedor</span></a></li>	
        <li><a href="/app/agenda_asistencia.php"><i class="fa fa-calendar-check-o"></i> Agenda Escolar</a></li>			
        <li>
          <a href="/app/fotos.php">
            <i class="fa fa-file-image-o"></i> <span>Fotos</span>
            <?php if ($imagenesNuevas>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow"><?=$imagenesNuevas?></small>
            </span>
            <?php } ?>	            
          </a>
        </li>	 
        <li>
          <a href="/app/documentos.php">
            <i class="fa fa-file-o"></i> <span>Documentos y Evaluaciones</span>
            <?php if ($contador_documentos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow"><?=$contador_documentos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>	                
		<li><a href="/app/agenda.php"><i class="fa fa-calendar"></i> <span>Calendario</span></a></li>
		<li><a href="/app/recibos.php"><i class="fa fa-money"></i> <span>Recibos</span></a></li>
    <li><a href="/app/instrucciones_app.php"><i class="fa fa-question-circle"></i> <span>Instrucciones APP</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside><nav class="familias-bottom-nav" aria-label="Navegación principal"><a href="/app/index.php"><i class="fa fa-home" aria-hidden="true"></i><span>Inicio</span></a><a href="/app/mensajeria.php"><i class="fa fa-envelope" aria-hidden="true"></i><span>Mensajes</span></a><a href="/app/agenda.php"><i class="fa fa-calendar" aria-hidden="true"></i><span>Agenda</span></a><a href="/app/documentos.php"><i class="fa fa-folder-open" aria-hidden="true"></i><span>Documentos</span></a><button type="button" data-familias-menu aria-label="Abrir menú"><i class="fa fa-bars" aria-hidden="true"></i><span>Menú</span></button></nav>

  <!-- =============================================== -->