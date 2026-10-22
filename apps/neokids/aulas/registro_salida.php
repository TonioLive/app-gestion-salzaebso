<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_registro=$_GET["id_registro"];

$busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_servicio_consumido=?', 's', array($id_registro));

$datos_registro=db_fetch_array($busca_registro_entrada);

$id_aula=$datos_registro["id_servicio"];
$id_alumno=$datos_registro["id_alumno"];
$fecha=strtotime($datos_registro["fecha"]);
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$observaciones=$datos_registro["observaciones"];
$incidencia=$datos_registro["incidencia"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=?', 's', array($id_alumno));
$datos_alumno=db_fetch_array($busca_alumno)
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de salida</b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br><i><?=$datos_aula["nombre"];?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br><br>
  Hora de salida: <b><?=date("H:i:s");?><br>
  <form method=POST action="registro_salida1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">
  <input type="hidden" name="id_aula" value="<?=$id_aula?>">
  <input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
  <input type="hidden" name="fecha" value="<?=$fecha?>">
  <input type="hidden" name="hora" value="<?=time()?>">
  <font size=1>¿Incidencia en el horario? <input type="radio" name="incidencia" value="0"<?php  if ($incidencia==0) { print " checked"; } ?>> No <input type="radio" name="incidencia" value="1"<?php  if ($incidencia==1) { print " checked"; } ?>>Si<br> En caso afirmativo, detallar la incidencia en cuadro de observaciones.</font><br>
  Observaciones:<br><textarea style="font-family: Arial; font-size: 11px" name="observaciones" cols=80 rows=6><?=$observaciones;?></textarea><br><br>
  <input type="submit" value="Aceptar"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

