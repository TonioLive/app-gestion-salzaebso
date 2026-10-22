<?php 

require("../configuracion/autentifica_padre.php");

// Desactivamos mensajes para administracion
$mostrar_mensajes_administracion=true;

if (strlen($_GET["id_curso"])!=0) {
$id_curso_actual=$_GET["id_curso"];
} else {

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

}

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=$_GET["version_js"];

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
if (strlen($version_js)==0) {
        $version_js==0;
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
	            <b>Grupo</b>
	          </div>
	        </div>
	        ';
		 
	        $contador_hijos=0;
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
	
	                // Datos del grupo del niño
	                $busca_grupos_alumno=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($datos_hijos["id_curso"], $datos_hijos["padre_de"])) or die(db_error());                
	                
	                $lista_grupos_alumno="";
	        		while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
	                	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
	                	}
	                	
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
		                    '.$lista_grupos_alumno.'
		                  </div>
		                </div>
		                ';    
	                
					// Notificación de documentos y carpeta no creada
					
					$nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
					$nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;					
					$descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/padres";
					
			        $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');
        
			        $descarga_centro_path=str_replace($sustituir,"_",$descarga_centro_path);					
					
                    $contador_documentos=0; 											
					
					if (file_exists($descarga_centro_path)) {         
						$d = dir($descarga_centro_path);

                        while($s=$d->read()) {

                        	if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="XLSX")) and (substr($s,0,1)!='.')) {

	                            // Busca si leido
	                            $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
	
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
  <meta charset="windows-1252">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="<?php  echo $meta_description ; ?>">
  <meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">  
  
  <link rel="shortcut icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
  <link rel="icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
    
  <title><?php  echo $titulo_web ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/clientes/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/clientes/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="/clientes/dist/css/extra.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/clientes/dist/css/skins/_all-skins.min.css">

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/clientes/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  
    <!-- fullCalendar 2.2.5-->
  <link rel="stylesheet" href="/clientes/plugins/fullcalendar/fullcalendar.css">
  <link rel="stylesheet" href="/clientes/plugins/fullcalendar/fullcalendar.print.css" media="print">
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-boxed TO GET A BOXED LAYOUT -->
<body class="hold-transition skin-blue layout-boxed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="/clientes/index.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="/clientes/imagenes/logo_mini_dashboard_neokids.png" height=50 border=0></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="/clientes/imagenes/logo_dashboard_neokids.png" height=50 border=0></span>
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
              <li class="footer"><a href="/clientes/mensajeria.php">Ver todos los mensajes</a></li>
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
              <li class="footer"><a href="/clientes/documentos.php">Ver todos los documentos</a></li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="/clientes/dist/img/default-user-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="/clientes/dist/img/default-user-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  <?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?>
                  <small>Alumnos Gestionados</small>                  
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
              	<?=$lista_alumnos_gestionados?>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <a <a href="/configuracion/cerrar_sesion_padres.php" class="btn btn-responsive btn-default btn-flat">Cerrar sesión <i class="fa fa-sign-out"></i></a>
                </div>
              </li>
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
          <img src="/clientes/dist/img/default-user-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?=mb_convert_case($datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"], MB_CASE_TITLE)?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">Menú Principal</li>
		<li><a href="/clientes/index.php"><i class="fa fa-home"></i> <span>Inicio</span></a>
        <li>
          <a href="/clientes/mensajeria.php">
            <i class="fa fa-envelope"></i> <span>Mensajes</span>
            <?php if ($total_no_leidos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-green"><?=$total_no_leidos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>					
		<li><a href="http://www.neo-kids.es/news/" target="_blank"><i class="fa fa-newspaper-o"></i> <span>Noticias</span></a></li>
        <li>
          <a href="/clientes/documentos.php">
            <i class="fa fa-file-image-o"></i> <span>Documentos e Imágenes</span>
            <?php if ($contador_documentos>0) { ?> 
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow"><?=$contador_documentos?></small>
            </span>
            <?php } ?>	
          </a>
        </li>		
		<li><a href="/clientes/agenda.php"><i class="fa fa-calendar"></i> <span>Calendario</span></a></li>
		<li><a href="/clientes/recibos.php"><i class="fa fa-money"></i> <span>Recibos</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- =============================================== -->