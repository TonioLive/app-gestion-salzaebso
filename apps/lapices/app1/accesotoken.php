<?php  require("../configuracion/config.php"); ?>
<?php 
require("../api/JWT.php");

ini_set("display_errors", 1);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

use \Firebase\JWT\JWT;

$key = (getenv('LAPICES_LEGACY_JWT_SECRET') ?: '');

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
                    $errores=$errores."2: Token expired.<br>";               
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

} 

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}	

if ($errores!="") {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  include("estilos.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>

</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" class="tabla_principal" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1">
  <tr>
    <td align="center">
        <img border="0" src="/webcam/imagenes/cabecera_<?=$nombre_pagina_actual?>.png">
    </td>
  </tr>
  <tr>
    <td width="<?= $ancho_pagina_total; ?>" valign="top" align="center">
        <div align="center">
        <div align="center">
    <br><br><br><br><br>
    <table width=70% border=0 cellpadding=0 cellspacing=0>
    <tr valign="top">
            <td align="center">
                    <img src="imag/webcam.gif" border="0" align="right">
            </td>
    <td width=80%>
                    <p align="center"><font size=3><b>Se han producido los siguientes errores.</b></font></p>
                    <blockquote>
                    <?=$errores?>
                    </blockquote>
            </td>
    </tr>
    <tr valign="top">
<form method="POST" action="acceso.php">
    <td colspan=2 align="center">
        <table border=0 cellpadding=2 cellspacing=0>
        <tr valign="top">
        <td align="right"><font size=2><b>Usuario:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="user" size="8" align="right" value="<?=$nombre_usuario;?>"></font></td>
        </tr><tr valign="top">
        <td align="right"><font size=2><b>Contraseña:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="password" name="pass" size="8" align="right"></font></td>
        </tr>
        </table><input type="image" img src="imag/enviarpeq.gif" border="0" name="enviar"></div>
     </td>
</form>
     </tr>
</table>
</div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
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
    
    header("Location: menu.php");
    
    }
    ?>
