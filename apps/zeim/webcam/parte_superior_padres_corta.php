<?php  require("../configuracion/autentifica_padre.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script language="Javascript">

        setInterval("comprueba_horario()",1000);

</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10><?php 
if ($_SESSION['permitido_padre']==true)
{
?>
<br><br><a title="Cerrar sesión" href="<?= $url_root; ?>/configuracion/cerrar_sesion_padres.php"><img src="<?= $url_root; ?>/imagen/menu/fin.gif" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
}
?></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
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
        $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));

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