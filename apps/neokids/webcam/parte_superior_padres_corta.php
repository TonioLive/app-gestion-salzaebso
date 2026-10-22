<?php  require("../configuracion/autentifica_padre.php");


$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
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
<script language="Javascript">

        setInterval("comprueba_horario()",1000);

</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-2"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" border=0></div>
  	<div class="col-md-10"></div>
  </div>
  <div class="row"> <!-- Principal -->
<?php 

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=$_GET["version_js"];

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
        
$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));

        if (db_num_rows($busca_usuario)==0) {
                $errores=$errores."El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                }

        }

    if ($errores!="") {
    ?>
<div class="panel panel-default">
  <div class="panel-body text-center">

<p class="text-center"><h3>Acceso al área privada</h3><br />
<span class="text-danger">Se han producido los siguientes errores.</span></font></p>
<p><big><?=$errores?></big>
</p>
Vuelve a introducir tus datos de acceso.</p>
<br />
<form class="form-inline" method="POST" action="acceso.php">
  <div class="form-group">
    <label for="user">Usuario</label>
    <input type="text" class="form-control" id="user" name="user" placeholder="Nombre de usuario">
  </div>
  <div class="form-group">
    <label for="pass">Contraseña</label>
    <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
  </div>
  <button type="submit" class="btn btn-default"><i class="fa fa-sign-in"></i> Entrar</button>
</form>
 
</div>
</div>
    <?php 
    }
    ?>