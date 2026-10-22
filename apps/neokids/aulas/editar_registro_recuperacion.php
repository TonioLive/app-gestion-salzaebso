<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_falta=$_GET["id_falta"];

$busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos_faltas WHERE id_falta=?', 's', array($id_falta));

$datos_registro=db_fetch_array($busca_registro_entrada);

$fecha=strtotime($datos_registro["fecha_recuperacion"]);
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$hora_entrada=date("H:i:s",strtotime($datos_registro["hora_entrada"]));
$hora_salida=$datos_registro["hora_salida"];
if ($hora_salida=="") {
        $hora_salida="No registrada";
        } else {
        $hora_salida=date("H:i:s",strtotime($datos_registro["hora_salida"]));
        }
        
$busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($datos_registro["id_alumno"]));

$datos_alumno=db_fetch_array($busca_alumno);
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


function borrar_control() {

	if (confirm("¿Quieres borrar el control de asistencia de <?=$datos_alumno["Nombre"]?> <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?> el día <?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?>?")) {
	
		window.location.href='borrar_registro_recuperacion.php?id_falta=<?=$id_falta?>';
	
	}

}
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de entrada y salida</b><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Nombre"]?> <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?></b><br><br>
  <form method=POST action="editar_registro_recuperacion1.php" name="registro">
  <input type="hidden" name="id_falta" value="<?=$id_falta?>">  
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
  ?></select></b>
  <br /> Hora de salida: <b><select name="hora_s" size=1 style="font-size: 10px"><?php 
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
  ?></select></b><br><br><br>
  <input type="button"  onclick="borrar_control();" value="Borrar control de asistencia"> <input type="submit" value="Guardar cambios"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

