<?php  require("../../configuracion/autentifica_padre.php");


// Extensiones de imagen permitidas
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

if (!function_exists('containsImages')) {
    function containsImages($folderPath, $imageExtensions) {
        // Verificamos si la carpeta existe
        if (is_dir($folderPath)) {
            // Obtenemos el contenido de la carpeta
            $files = scandir($folderPath);

            foreach ($files as $file) {
                // Omitimos los directorios especiales '.' y '..'
                if ($file !== '.' && $file !== '..') {
                    $filePath = $folderPath . '/' . $file;

                    if (is_dir($filePath)) {
                        // Llamamos recursivamente si es una subcarpeta
                        if (containsImages($filePath, $imageExtensions)) {
                            return true; // Retornamos true si encontramos una imagen en una subcarpeta
                        }
                    } elseif (is_file($filePath)) {
                        // Verificamos si es un archivo de imagen
                        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                        if (in_array($fileExtension, $imageExtensions)) {
                            return true; // Retornamos true si encontramos una imagen
                        }
                    }
                }
            }
        }
        return false; // No se encontró ninguna imagen
    }
}

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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<script src="/webcam/js/lightbox/ekko-lightbox.min.js"></script>

<?php  include("../estilos.php"); ?>

<link href="/webcam/js/lightbox/ekko-lightbox.min.css" rel="stylesheet" type="text/css">

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
        $busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");

        if (db_num_rows($busca_usuario)==0) {
                $errores=$errores."El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                }

        }
?>
    <div align="center">
    <?php 
    if ($errores!="") {
    ?>
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
    <?php 
    }
    ?>
