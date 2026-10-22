<?php
require_once __DIR__ . '/../configuracion/deployment-config.php';
if (function_exists('isMobile')==false) {
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", app_request_value($_SERVER, 'HTTP_USER_AGENT', ''));
	}
}

$comprobarMovil=false;

require("../configuracion/autentifica_padre.php");

// Desactivamos mensajes para administracion
$mostrar_mensajes_administracion=true;

$id_curso_solicitado=app_request_int($_GET, "id_curso", 0);
if ($id_curso_solicitado>0) {
$id_curso_actual=$id_curso_solicitado;
} else {

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

}

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=app_request_value($_GET, "version_js", "");

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
if (strlen($version_js)==0) {
        $version_js=0;
        }

$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");
        
        if (db_num_rows($busca_usuario)==0) {
                $errores="El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                // Mostramos la información del niño

                $id_usuario=$datos_usuario["id_usuario"];
                $id_hijo=$datos_usuario["padre_de"];

                // Busca datos del padre.
                $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                // Comprueba si tiene email
                $no_email=false;
                if (strlen($datos_padre["email"])==0) {
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
		
		$fechalimite=strtotime(date("Y-m-d"))+(86400*15);	
		
		$mostrar_mensajes=true;
		$mostrar_documentacion=true;
		$mostrar_agenda=true;
	    
		// Notificaciones de mensajes  	       
        
		// Busca otros padres
	    $filtro_padres="";
	    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
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
	            $busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=".$mensajes["id_nota_padre"]." AND respondido_por<>$id_usuario");
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
	    
	    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");
	
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
	            <b>Grupo</b>
	          </div>
	        </div>
	        ';
		 
	        $contador_hijos=0;
	        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
	                
	                $id_curso_mostrado=$datos_hijos["id_curso"];
	                
	                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
	                if (db_num_rows($busca_datos_cursos)!=0) {
	                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
	                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
	                        } else {
	                        $nombre_curso_mostrado="";
	                        }
	                        
	                // Datos de la escuela del niño
	                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]) or die(db_error());
	
	                $datos_escuela=db_fetch_array($busca_datos_escuela);	           
	                	
					// Listado de alumnos en menú superior
	                	
					$lista_alumnos_gestionados.='        
		                <div class="row">
		                  <div class="col-xs-4 text-center">
		                    '.$nombre_curso_mostrado.'
		                  </div>
		                  <div class="col-xs-4 text-center">
		                    '.$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"].'
		                  </div>
		                </div>
		                ';    
	                
					// Notificación de documentos y carpeta no creada
					
					$nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
					$nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;					
					$descarga_centro_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/padres");
					
			        $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');
        
			        $descarga_centro_path=str_replace($sustituir,"_",$descarga_centro_path);					
					
                    $contador_documentos=0; 											
					
					if (file_exists($descarga_centro_path)) {         
						$d = dir($descarga_centro_path);

                        while($s=$d->read()) {

                        	if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="XLSX")) and (substr($s,0,1)!='.')) {

	                            // Busca si leido
	                            $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_centro_path$subcarpeta_actual/$s'");
	
	                            if (db_num_rows($busca_leido)==0) {
		                            $contador_documentos++;
	                            }
	                    	}
                        }
                        $d->close(); 
                    } else {
						$error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del centro. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                	}  										                
	                	
	       	}   
		    
		    
		}		
						
		
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
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="<?php  echo $meta_description ; ?>">
  <meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">  
  
  <link rel="shortcut icon" href="https://www.escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
  <link rel="icon" href="https://www.escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
    
  <title>Familias &middot; L&aacute;pices</title>
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
  <link rel="stylesheet" href="/app/assets/familias-app.css?v=20260727.6">
  <link rel="stylesheet" href="/gestion/panel/assets/session-timeout.css?v=20260909.1" data-session-timeout-style>
  <script src="/app/assets/familias-app.js?v=20260727.6" defer></script>
  <script src="/gestion/panel/assets/session-timeout.js?v=20260909.1" defer></script>
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<link rel="stylesheet" href="/gestion/comunicacion/assets/comunicacion.css?v=20261005.1">
<script src="/gestion/comunicacion/assets/avisos-familias.js?v=20261005.1" charset="utf-8" defer></script>
</head>
<body class="hold-transition skin-blue sidebar-mini familias-app-shell">
<!-- Site wrapper -->
<div class="wrapper">

  <header class="main-header">
    <a href="/app/index.php" class="logo" aria-label="L&aacute;pices, &aacute;rea de familias">
      <span class="logo-mini">
        <span class="familias-brand"><img src="/app/imagenes/logo_redondo.jpg" alt=""></span>
      </span>
      <span class="logo-lg">
        <span class="familias-brand">
          <img src="/app/imagenes/logo_redondo.jpg" alt="Escuelas Infantiles L&aacute;pices">
          <span class="familias-brand__copy"><strong>L&aacute;pices</strong><small>&Aacute;rea de familias</small></span>
        </span>
      </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Contraer o expandir menú lateral</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
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
          <li class="familias-logout-item">
            <a class="familias-logout" href="/app/cerrar_sesion.php" title="Cerrar sesi&oacute;n" onclick="return window.confirm('\u00bfQuieres cerrar la sesi\u00f3n?');">
              <i class="fa fa-sign-out" aria-hidden="true"></i>
              <span class="familias-logout__label">Cerrar sesi&oacute;n</span>
            </a>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="/app/dist/img/default-user-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="/app/dist/img/default-user-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  <?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?>
                  <small>Alumnos Gestionados</small>                  
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
              	<?=$lista_alumnos_gestionados?>
              </li>
              <?php /*
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <a href="/app/cerrar_sesion.php" class="btn btn-responsive btn-default btn-flat">Cerrar sesión <i class="fa fa-sign-out"></i></a>
                </div>
              </li>
              */ ?>
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
          <p><?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
<li><a href="/app/avisos.php"><i class="fa fa-bullhorn" aria-hidden="true"></i> <span>Avisos del centro</span> <span data-family-notice-count hidden></span></a></li>
        <li class="header">Menú Principal</li>
		<li><a href="/app/index.php"><i class="fa fa-home"></i> <span>Inicio</span></a></li>
        <li>
          <a href="/app/mensajeria.php">
            <i class="fa fa-envelope"></i> <span>Mensajes</span>
            <?php if ($total_no_leidos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-green"><?=$total_no_leidos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>
        <li>
          <a href="/app/documentos.php">
            <i class="fa fa-file-image-o"></i> <span>Documentos e Imágenes</span>
            <?php if ($contador_documentos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow"><?=$contador_documentos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>		
		<li><a href="/app/agenda.php"><i class="fa fa-calendar"></i> <span>Calendario</span></a></li>
		<li><a href="/app/recibos.php"><i class="fa fa-money"></i> <span>Recibos</span></a></li>
		<li class="familias-sidebar-logout"><a href="/app/cerrar_sesion.php" onclick="return window.confirm('\u00bfQuieres cerrar la sesi\u00f3n?');"><i class="fa fa-sign-out"></i> <span>Cerrar sesi&oacute;n</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <nav class="familias-bottom-nav" aria-label="Navegaci&oacute;n principal">
    <a href="/app/index.php"><i class="fa fa-home" aria-hidden="true"></i><span>Inicio</span></a>
    <a href="/app/mensajeria.php"><i class="fa fa-envelope" aria-hidden="true"></i><span>Mensajes</span><?php if ($total_no_leidos>0) { ?><span class="familias-bottom-nav__badge"><?=$total_no_leidos?></span><?php } ?></a>
    <a href="/app/agenda.php"><i class="fa fa-calendar" aria-hidden="true"></i><span>Agenda</span></a>
    <a href="/app/documentos.php"><i class="fa fa-folder-open" aria-hidden="true"></i><span>Documentos</span><?php if ($contador_documentos>0) { ?><span class="familias-bottom-nav__badge"><?=$contador_documentos?></span><?php } ?></a>
    <button type="button" data-familias-menu aria-label="Abrir men&uacute;"><i class="fa fa-bars" aria-hidden="true"></i><span>Men&uacute;</span></button>
  </nav>

  <!-- =============================================== -->