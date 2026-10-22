<?php  require("../../configuracion/config.php"); ?>
<?php  require("../../configuracion/autentifica.php"); ?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
</style>
<script lenguaje="Javascript">
self.focus();
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="no">
<div align="left">
<?php 
$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

$id_empleado=$_GET["id_empleado"];

$busca_empleado=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_empleado)) or die(db_error());

$datos_empleado=db_fetch_array($busca_empleado);

$nombre_empleado=$datos_empleado["Nombre"]." ".$datos_empleado["Apellido1"]." ".$datos_empleado["Apellido2"];

?>
<div align="center" style="display:block;padding:0px;border:0px solid">
<br><br>
<font size=3 face="<?= $fuente_general; ?>">
<b><big>Preparación de platos</big></b><br><br>
Fecha: <?=date("d/m/Y");?><br><br>
<?=$nombre_empleado;?>
</font>
<br><br>
<table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="700">
                        <tr valign="middle">
                                <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="Ver platos" onclick="window.location.href='ver_platos_hoy_provisional.php?id_empleado=<?=$id_empleado;?>'" style="font-size:18px; width: 200; height: 84"></font></td>
                                <td><font size=3 face="<?= $fuente_general; ?>">Listado de platos que se deben preparar hoy.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="Salir" onclick="window.location.href='index.php'" style="font-size:18px; width: 200; height: 84"></font></td>
                                <td><font size=3 face="<?= $fuente_general; ?>">Salir a la pantalla anterior.</font></td>
                        </tr>
</table>
</div>
<?php 
db_close();
?>
</div>
</body>
</html>
