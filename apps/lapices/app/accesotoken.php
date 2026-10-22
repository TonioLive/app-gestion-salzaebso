<?php  require("../configuracion/config.php"); ?>
<?php 
require("../api/JWT.php");

ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

use \Firebase\JWT\JWT;

$key = app_jwt_secret();

$token=$_GET["token"];

$errores="";
$id_usuario=0;

if ((strlen($token)!=0) and ($token!='null')) {
    // Comprueba si el token es válido
    $error = 0;
    
    $decoded = JWT::decode($token, $key, array('HS256'));
    
    $caducidad = $decoded->caducidad;

    if ($caducidad) {
            // Comprueba caducidad
            if (strtotime($caducidad)>=time()) {
                    // Busca si el token está activo
                    $busca_datos_usuario=db_query("SELECT * FROM tokens_activos WHERE token='$token'");
                    if (db_num_rows($busca_datos_usuario)==0) {
                            // El Token ha caducado
                            $errores=$errores."3: Token deleted.<br>";
                    }                
            } else {
                    // El token era correcto pero ha caducado 
                    $errores=$errores."1: Token expired.<br>";               
            }
    
    } else {                
            // El token no es correcto
            $error = 1;
            $errores=$errores."2: Token invalid.<br>";               
    }        
    
    if ($errores=="") {
        // Todo correcto     
        $id_usuario = $decoded->idUsuario;
        $id_hijo = $decoded->idHijo;
        $id_padre = $decoded->idPadre;
    }

} else {
  
  // Falta el token 
  $errores=$errores."4: Token is missing.<br>";  

}

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}	

if ($errores!="") {
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
    
  <title><?php  echo $titulo_web ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/app/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/app/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/app/dist/css/skins/_all-skins.min.css">

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
  	<div class="col-md-12 col-xs-12 text-center"><img src="/app/imagenes/logo_redondo.jpg" border=0 width="100"></div>
  </div>
  <div class="row"> <!-- Principal -->
  
	<div class="panel panel-default">
	  <div class="panel-body text-center">

	<p class="text-center"><h3>Acceso al área privada</h3>


        <b>Se han producido los siguientes errores.</b>
        <br /><br />                    
        <?=$errores?>
        <br />
	      Vuelve a intentarlo o prueba a introducir aquí tu usuario y contraseña.
        </p>
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
	  <button type="submit" class="btn btn-responsive btn-default"><i class="fa fa-sign-in"></i> Entrar</button>
	</div>  
	</form>
	</div>
	</div>
</div>
<!-- Pie de página -->
<div class="row">
</div>
</div>
</body>
</html>
    <?php 
    } else {
    // Iniciamos la sesión del padre

    // Busca datos del padre.
    $busca_datos_usuario_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del usuario padre.");

    $datos_usuario_padre=db_fetch_array($busca_datos_usuario_padre);
    
    $busca_datos_padre=db_query("SELECT * FROM padres WHERE id_padre=$id_padre") or die(db_error()."- No se encuentran los datos del padre.");

    $datos_padre=db_fetch_array($busca_datos_padre);

    session_cache_limiter('nocache,private');
    $_SESSION['nombre_padre']=$datos_padre['usuario_webcam'];
    $_SESSION['permitido_padre']=true;
    $_SESSION['id_padre']=$id_usuario;
    $_SESSION['id_hijo']=$id_hijo;
	  $_SESSION["mostrar_felicitacion"]=0;
	
    //Encriptamos la contraseña
    $password1 = md5($pass_usuario);

    $_SESSION['password_padre']=$password1;

	  $REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
	  $HTTP_ACCEPT_LANGUAGE=$_SERVER["HTTP_ACCEPT_LANGUAGE"];
	  $HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];

    // estadísticas de sesion
    db_query("INSERT INTO control_acceso_padres (sesion, id_padre, fecha, hora, remote_host, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, logacceso) VALUES ('".session_id()."', '$id_usuario', '".date("Y/m/d")."', '".date("H:i")."', '".$REMOTE_ADDR."','".$HTTP_ACCEPT_LANGUAGE."','".$HTTP_USER_AGENT."', '')") or die(db_error());
    
    header("Location: index.php");
    
    }
    ?>
