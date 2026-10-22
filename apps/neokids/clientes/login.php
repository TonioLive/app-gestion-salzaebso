<?php 
require("../configuracion/config.php"); 
?>
<?php  
require("../configuracion/config.php"); 

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}

$redirect=$_GET["redirect"];


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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/clientes/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-boxed TO GET A BOXED LAYOUT -->
<body class="hold-transition skin-blue layout-boxed sidebar-mini">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-12 col-xs-12 text-center"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" border=0 width="100"></div>
  </div>
  <div class="row"> <!-- Principal -->
  
	<div class="panel panel-default">
	  <div class="panel-body text-center">
	<?php
	
	$permiso_entrar=false;
	
	// if ( (substr($_SERVER['REMOTE_ADDR'],0,8)=='10.10.1.') AND ((strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')) or (strpos($_SERVER['HTTP_USER_AGENT'], 'Android'))) ) {
	if ( ( (substr($_SERVER['REMOTE_ADDR'],0,8)=='10.10.1.') and ((strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh'))) ) or ($_SERVER['REMOTE_ADDR']=='79.146.250.234') ) {	
		$permiso_entrar=true;	
	
	}
	
	// $permiso_entrar=false;
	
	if ((time()>=strtotime('2017-10-01 00:00:00')) or ($permiso_entrar==true)) {	
	?> 
	<p class="text-center"><h3>Acceso al área privada</h3>
	En <?=$nombre_centro?>, te habrán proporcionado una carta que contiene un <strong>NOMBRE DE USUARIO</strong> y una <strong>CONTRASEÑA</strong>.<br />
	Estos datos te permiten acceder al área privada donde ponemos a tu disposición múltiples servicios para mantenerte al día de las actividades de <?=$nombre_centro?>.<br />
	Introduce a continuación tus datos de acceso<br />para empezar a utilizar el servicio.</p>
	<form class="form-inline" method="POST" action="acceso.php">
		<input type="hidden" name="redirect" id="redirect" value="<?=$redirect;?>">
	<div class="row">
	  <div class="form-group">
	    <label for="user">Usuario</label><br />
	    <input type="text" class="form-control" id="user" name="user" placeholder="Nombre de usuario">
	  </div>
	</div>
	<div class="row">
	  <div class="form-group">
	    <label for="pass">Contraseña</label> <br />
	    <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
	  </div>
	</div>
	<div class="row">	
	  <br />   
	  <a type="button" class="btn btn-responsive btn-default" href="http://www.neo-kids.es"><i class="fa fa-backward"></i> Volver a la web</a> <button type="submit" class="btn btn-responsive btn-default"><i class="fa fa-sign-in"></i> Entrar</button>
	</div>  
	</form>
	<?php
		} else {
	?>		
		<div class="text-center">
			<h1>Servicio desactivado</h1>
			<h1>Este servicio estará disponible para todos los usuarios<br />
				a partir del día 1 de octubre de 2017</h1>
			<h1>Perdone las molestias</h1>		
		</div>
	<?php		
		}
	?>
	</div>
	</div>
</div>
<!-- Pie de página -->
<div class="row">
</div>
</div>
</body>
</html>