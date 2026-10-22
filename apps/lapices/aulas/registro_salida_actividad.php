<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_registro=$_GET["id_registro"];

$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos_asistencia WHERE id_servicio_consumido=$id_registro");

$datos_registro=db_fetch_array($busca_registro_entrada);

$id_actividad=$datos_registro["id_servicio"];
$id_alumno=$datos_registro["id_alumno"];
$fecha=strtotime($datos_registro["fecha"]);
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];

// Busca datos del actividad
$consulta_datos_actividad=db_query("SELECT * FROM tarifa WHERE ID=$id_actividad");
$datos_actividad=db_fetch_array($consulta_datos_actividad);

$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de salida</b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br><i><?=$datos_actividad["ARTICULO"];?></i></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br><br>
  Hora de salida: <b><?=date("H:i:s");?><br>
  <form method=POST action="registro_salida_actividad1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">
  <input type="hidden" name="id_actividad" value="<?=$id_actividad?>">
  <input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
  <input type="hidden" name="fecha" value="<?=$fecha?>">
  <input type="hidden" name="hora" value="<?=time()?>">
  <input type="submit" value="Aceptar"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

