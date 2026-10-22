<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");
 require("../../configuracion/parte_superior.php");

mt_srand((double)microtime()*1000000);
$id=(mt_rand(0,10000000)/10000000);
$titular=$_POST["titular"];
$noticia=$_POST["Texto_not"];
$nombre_img=$_POST["Nombre_img"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];
$tipo=$_POST["tipo"];

$num_errores=0;


if (strlen($noticia)==0)
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="No has introducido el texto de la noticia.";
} 

if ($dia=="--" || $mes=="--" || $anyo=="----")
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="No has completado la fecha de caducidad.";
} else {
        if (!checkdate(intval($mes),intval($dia),intval($ano))) {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="La fecha de caducidad escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                 }
        }

if (strlen($titular)==0)
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="No has introducido el titular de la noticia.";
} 


if ($num_errores==0)
{

$fecha_caducidad=$ano."/".$mes."/".$dia;
$noticia=str_replace(chr(13),"<br>",$noticia);

//Introduce en la tabla NoticiasN

$db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta_insert="INSERT INTO NoticiasN values(".
"'', ".
"'".$tipo."', ".
"'".date("%Y/%m/%d")."', ".
"'".$noticia."', ".
"'".$fecha_caducidad."', ".
"'1', ".
"'', ".
"0, ".
"'".$nombre_img."', ".
"1, ".
"1, ".
"'".$titular."', ".
"1, ".
"0, ".
"'', ".
"'')";

db_query($consulta_insert);

db_close();

} 

$ancho_tabla=$ancho_pagina_total-185;
?>
<div align="right">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd_at.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" colspan=3 align="center">
                            <font size="3" color="#000000"><b>Gestión del tablón de anuncios</font></b>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id_at.gif" border=0 width=8 height=8></td>
                </tr>
    </table><br>
<table border="0" width="100%">
  <tr>
    <td>
        <p align="center">
<?php  
if ($num_errores=="0")
{

?>
<p align="center">&nbsp;</p>

<p align="center"><b>La noticia ha sido introducida con exito.<br><br>
A partir de ahora estará disponible en la página de inicio y en el tablón de anuncios.</b></p>

<p align="center"><br>
<input type="button" value="Volver a la página principal" name="B1"
onClick="window.location='<?=$url_web?>/opciones_menu.php?Submenu=<?=$submenu_activo;?>'"></p><small>
<?php  
}
  else
{
?>
<p align="center"><b><i>
Se han detectado errores al cumplimentar el formulario.<br>Por favor, vuelva atras para revisar
los siguientes puntos:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table>
<?php 
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";
    }
?>
</table>
<?php 
  print "</small></small><br><br><b>Vuelve al paso anterior y corrige los errores.</b><br><br>";
  print "<input type=\"button\" value=\"<< Volver al paso anterior\" onclick=\"history.go(-1)\" id=1 name=1></p>";
} 
?>
        </p>
    </td>
  </tr>
</table>
</div>
<!-- Hasta aquí -->  
<?php  require("../../configuracion/parte_inferior.php"); ?>

