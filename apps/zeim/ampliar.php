<?php  require("configuracion/config.php"); ?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("configuracion/estilos/estilo1.php"); ?>
</style>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
scroll="auto">
<br>
<div align="center">
<table width="95%" cellpadding=0 cellspacing=0 border="0" >
<tr>
<td>
<font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>">
<small>
<table width="100%" cellpadding=0 cellspacing=0>
<tr>
        <td width="8"><img src="<?php  echo $url_root; ?>/privado/imagen/esquina_si.gif" border=0 width=8 height=8></td>
        <td bgcolor="#ffffff"><img src="<?php  echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=1></td>
        <td width="8"><img src="<?php  echo $url_root; ?>/privado/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
</tr>
<tr bgcolor="#ffffff">
<td></td>
<td><font size=2 face="<?php  echo $fuente_general; ?>">
<?php 
$cod=$_GET["N"];

$query="SELECT * FROM NoticiasN WHERE Cod=".$cod;

$db_n2=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_n2);
$rsSlc_query=db_query(($query),$db_n2);

if (db_num_rows($rsSlc_query)!=0)
{
  $rs1=db_fetch_array($rsSlc_query);
  
  $titular=$rs1["Texto_Titular"];
  $texto=$rs1["Texto"];
  $fecha=date("j/n/Y",strtotime($rs1["Fecha_ins"]));
  $cod=$rs1["Cod"];
  if ($cod/2==round($cod/2,0))
  {
    $alineacion="left";
  }
    else
  {
    $alineacion="right";
  }

}
?>
<p align="center">
<b><?php  echo $titular; ?></b>
</p><p align="left">
<?php 
    if (strlen($rs1["Nombre_img"])>0)
    {

?>
  <table border="0 width="100%"><tr valign="top"><td><img src="<?=$url_root?>/privado/descarga/<?= $rs1["Nombre_img"]; ?>" align=center></td><td bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?= $texto; ?></font></td></tr></table>
<?php 
    }
      else
    {
?>
<?php  echo $texto; ?>
<?php 
    }
?>
</p>
<small><b>Fecha de publicación:</b> <i><?php  echo $fecha; ?></i></small>
<br><br></font>
</td>
<td>
</tr>
<tr>
        <td width="8"><img src="<?php      echo $url_root; ?>/privado/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
        <td bgcolor="#ffffff"><img src="<?php      echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=1></td>
        <td width="8"><img src="<?php      echo $url_root; ?>/privado/imagen/esquina_id.gif" border=0 width=8 height=8></td>
</tr>
</table>
<?php 
db_close($db_n2);
?>
</small>
</font>
</td>
</tr>
</table>
</div>
<p align="center">
<a href="javascript:window.close()">Cerrar la ventana</a>
</p>
</body>
</html>
