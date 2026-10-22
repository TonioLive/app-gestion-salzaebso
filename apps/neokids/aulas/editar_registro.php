<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_registro=$_GET["id_registro"];

$busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_servicio_consumido=?', 's', array($id_registro));

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
$comedor=$datos_registro["tipo_menu"];
$tipo_menu_comedor=$datos_registro["tipo_menu_comedor"];
$menu_seleccionado_hoy=$datos_registro["menu_seleccionado_hoy"];
$incidencia=$datos_registro["incidencia"];
$turno=$datos_registro["turno"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=?', 's', array($id_alumno));
$datos_alumno=db_fetch_array($busca_alumno);

                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y")-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                        if (date("m")<date("m",strtotime($datos_alumno["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n")-date("n",strtotime($datos_alumno["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),2);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),2);
                                }
        if ($edad_alumno_anos>1) {
                $plural_anios="s";
                } else {
                $plural_anios="";
                }
        if ($edad_alumno_meses>1) {
                $plural_meses="es";
                } else {
                $plural_meses="";
                }

        // Comprueba si es de regimen
        if ($comedor==3) {
                        $filtro_regimen=" AND regimen=1";
                } else {
                        $filtro_regimen=" AND regimen=0";
                }

        // Calcula el menú que le toca por edades
        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha)."' AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
        // $busca_menu_apropiado=mysql_query("SELECT * FROM menu_menus WHERE edad_ini_menu<=$edad_alumno $filtro_regimen") or die(mysql_error());
        $diferencia_actual=1000;
        $id_menu_seleccionado=0;
        $nombre_menu_seleccionado="";
        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                if ($diferencia<=$diferencia_actual) {
                        $diferencia_actual=$diferencia;
                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                        $nombre_menu_seleccionado=urldecode($menu_apropiado["nombre_menu"]);
                        }
                }
        // Menu defecto
        if ($id_menu_seleccionado==0) {
                $texto_menu_seleccionado="<font color=red>No hay menús apropiados para la edad del niño.</font>";
                } else {
                $texto_menu_seleccionado="<font color=green>El menú que más se ajusta a la edad del niño es<br>\"$nombre_menu_seleccionado\".</font>";
                }


if (($menu_seleccionado_hoy==0) and ($comedor!=2)) {
        $menu_seleccionado_hoy=$id_menu_seleccionado;
        }

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
	
		window.location.href='borrar_registro.php?id_registro=<?=$id_registro?>';
	
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
  <form method=POST action="editar_registro1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">  
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

