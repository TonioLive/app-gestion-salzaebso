<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 

$id_registro=$_GET["id_incidencia"];

$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_servicio_consumido=$id_registro");

$datos_registro=db_fetch_array($busca_registro_entrada);

$id_aula=$datos_registro["id_servicio"];
$id_alumno=$datos_registro["id_alumno"];
$fecha=strtotime($datos_registro["fecha"]);
$hora_entrada=date("H:i:s",strtotime($datos_registro["hora_entrada"]));
$hora_salida=$datos_registro["hora_salida"];
if ($hora_salida=="") {
        $hora_salida="No registrada";
        } else {
        $hora_salida=date("H:i:s",strtotime($datos_registro["hora_salida"]));
        }
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$observaciones=$datos_registro["observaciones"];
$medidas_adoptadas=$datos_registro["medidas_adoptadas"];
$resuelta=$datos_registro["resuelta"];
$comedor=$datos_registro["tipo_menu"];
$incidencia=$datos_registro["incidencia"];
$turno=$datos_registro["turno"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

if (strlen($datos_aula["docente_titular"])!=0) {
        $consulta_datos_docente=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_aula["docente_titular"]);
        $datos_docente=db_fetch_array($consulta_datos_docente);
        $nombre_docente=$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"].", ".$datos_docente["Nombre"];
        } else {
        $nombre_docente="No registrado";
        }

$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
$datos_alumno=db_fetch_array($busca_alumno)
?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Incidencia en el registro de entrada y salida</b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br><i><?=$datos_aula["nombre"];?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></b><br><br>
    <b>Docente:</b> <?=$nombre_docente;?></font></td>
  </tr>
  <form method=POST action="detalles_asistecia1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br><br>
  Hora entrada: <b><select name="hora_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($datos_registro["hora_entrada"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($datos_registro["hora_entrada"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="seg_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("s",strtotime($datos_registro["hora_entrada"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></b>, Hora de salida: <b><select name="hora_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($datos_registro["hora_salida"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($datos_registro["hora_salida"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="seg_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("s",strtotime($datos_registro["hora_salida"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></b><br>
  <?php 
  if ($turno==0) {
  ?>
  <b>Comedor:</b> <font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="comedor" value="2"<?php  if ($comedor==2) { print " checked"; } ?>>No &nbsp; &nbsp;<input type="radio" name="comedor" value="1"<?php  if ($comedor==1) { print " checked"; } ?>> Normal&nbsp; &nbsp;<input type="radio" name="comedor" value="3"<?php  if ($comedor==3) { print " checked"; } ?>> Regimen</font><br><br>
  Hora entrada comedor: <b><select name="hora_e_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($datos_registro["hora_entrada_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($datos_registro["hora_entrada_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="seg_e_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("s",strtotime($datos_registro["hora_entrada_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></b>, Hora de salida: <b><select name="hora_s_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($datos_registro["hora_salida_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($datos_registro["hora_salida_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="seg_s_c" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("s",strtotime($datos_registro["hora_salida_c"]))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></b><br>
  <?php 
  } else {
  ?>
  <input type="hidden" name="comedor" value="2">
  <?php 
  }
  ?>
  <font size=1>¿Incidencia en el horario? <input type="radio" name="incidencia" disabled value="0"<?php  if ($incidencia==0) { print " checked"; } ?>> No <input type="radio" disabled name="incidencia" value="1"<?php  if ($incidencia==1) { print " checked"; } ?>>Si<br><br>
  <b>Observaciones:</b><br><textarea style="font-family: Arial; font-size: 11px" name="observaciones" cols=80 rows=6><?=$observaciones;?></textarea><br><br>
  <font size=1>Estado de la incidencia: <input type="radio" name="resuelta"  value="0"<?php  if ($resuelta==0) { print " checked"; } ?>> Pendiente de resolución <input type="radio" name="resuelta" value="1"<?php  if ($resuelta==1) { print " checked"; } ?>>Resuelta<br><br>
  <b>Medidas adoptadas:</b><br><textarea style="font-family: Arial; font-size: 11px" name="medidas_adoptadas" cols=80 rows=6><?=$medidas_adoptadas;?></textarea><br><br>
  <input type="submit" value="Guardar cambios"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

