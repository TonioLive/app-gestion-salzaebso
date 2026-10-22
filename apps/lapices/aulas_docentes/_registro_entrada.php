<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$turno=$_GET["turno"];
$id_alumno=$_GET["id_alumno"];

$inicio=$_GET["fecha"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
$datos_alumno=db_fetch_array($busca_alumno);

// Busca inscripcion


$consulta_inscripcion=db_query("SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_curso=".$datos_aula["id_curso"]." AND alumnos.id_usuario=$id_alumno") or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);
$comedor=$datos_inscripcion["comedor"];
$tipo_menu_comedor=$datos_inscripcion["tipo_menu_comedor"];
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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de entrada</b><br>
    <b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br><i><?=$datos_aula["nombre"];?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br><br>
  Hora de entrada: <b><?=date("H:i:s");?></b><br>
  <form method=POST action="registro_entrada1.php" name="registro">
  <input type="hidden" name="id_aula" value="<?=$id_aula?>">
  <input type="hidden" name="turno" value="<?=$turno?>">
  <input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
  <input type="hidden" name="fecha" value="<?=$fecha_time_stamp?>">
  <input type="hidden" name="hora" value="<?=time()?>">
  <?php 
  if ($turno==0) {
  ?>
  <font size=2 face="<?= $fuente_general; ?>"><b>Comedor:&nbsp;</b><input type="radio" name="comedor" value="2"<?php  if ($comedor==2) { print " checked"; } ?>>No &nbsp; &nbsp;<input type="radio" name="comedor" value="1"<?php  if ($comedor==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="comedor" value="3"<?php  if ($comedor==3) { print " checked"; } ?>> Regimen</font><br>
  <font size=2 face="<?= $fuente_general; ?>"><b>Tipo de menú:<font size=1>&nbsp;</b><input type="radio" name="tipo_menu_comedor" value="1"<?php  if ($tipo_menu_comedor==1) { print " checked"; } ?>>Pequeños &nbsp;<input type="radio" name="tipo_menu_comedor" value="2"<?php  if ($tipo_menu_comedor==2) { print " checked"; } ?>> Mayores &nbsp;<input type="radio" name="tipo_menu_comedor" value="3"<?php  if ($tipo_menu_comedor==3) { print " checked"; } ?>> Colegial &nbsp;<input type="radio" name="tipo_menu_comedor" value="4"<?php  if ($tipo_menu_comedor==4) { print " checked"; } ?>> Toma &nbsp;<input type="radio" name="tipo_menu_comedor" value="5"<?php  if ($tipo_menu_comedor==5) { print " checked"; } ?>> Bebe</font></font><br><br>
  <?php 
  } else {
  ?>
  <input type="hidden" name="comedor" value="<?=$comedor;?>">
  <input type="hidden" name="tipo_menu_comedor" value="<?=$tipo_menu_comedor;?>">
  <?php 
  }
  ?>
  <font size=1>¿Incidencia en el horario? <input type="radio" name="incidencia" value="0" checked> No <input type="radio" name="incidencia" value="1">Si<br> En caso afirmativo, detallar la incidencia en cuadro de observaciones.</font><br>
  Observaciones:<br><textarea style="font-family: Arial; font-size: 11px" name="observaciones" cols=80 rows=6></textarea><br><br>
  <input type="submit" value="Aceptar"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

