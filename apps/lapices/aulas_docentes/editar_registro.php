<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_registro=$_GET["id_registro"];

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
$comedor=$datos_registro["tipo_menu"];
$tipo_menu_comedor=$datos_registro["tipo_menu_comedor"];
$menu_seleccionado_hoy=$datos_registro["menu_seleccionado_hoy"];
$incidencia=$datos_registro["incidencia"];
$turno=$datos_registro["turno"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
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
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
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
        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
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
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de entrada y salida</b><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br><i><?=$datos_aula["nombre"];?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br><br>
  Hora entrada: <b><?=$hora_entrada;?></b>, Hora de salida: <b><?=$hora_salida;?></b><br>
  <form method=POST action="editar_registro1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">
  <?php 
  if (($turno==0) or ($datos_alumno["tipo_mensualidad"]==1)) {
  ?>
  <font size=2 face="<?= $fuente_general; ?>"><b>Tipo de menú:<font size=1>&nbsp;</b><select name="menu_seleccionado_hoy">
                                        <option value="0"<?php  if ($menu_actual==0) { print " selected"; } ?>>No asiste al comedor</option>
                                        <?php 
                                        // $consulta_datos_menus=mysql_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(mysql_error());
                                        $consulta_datos_menus=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha)."' ORDER BY menu_menus.edad_ini_menu;") or die(db_error());
                                        
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_seleccionado_hoy) { print " selected"; }  if ($datos_menus["id_menu"]==$id_menu_seleccionado) { print " style='color: #1eae32'"; } ?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select></font></font><br><br>
  <?php 
  } else {
  ?>
  <input type="hidden" name="menu_seleccionado_hoy" value="<?=$menu_seleccionado_hoy;?>">
  <?php 
  }
  ?>
  <font size=1>¿Incidencia en el horario? <input type="radio" name="incidencia" value="0"<?php  if ($incidencia==0) { print " checked"; } ?>> No <input type="radio" name="incidencia" value="1"<?php  if ($incidencia==1) { print " checked"; } ?>>Si<br> En caso afirmativo, detallar la incidencia en cuadro de observaciones.</font><br>
  <b>Observaciones:</b><br><textarea style="font-family: Arial; font-size: 11px" name="observaciones" cols=80 rows=6><?=$observaciones;?></textarea><br><br>
  <input type="submit" value="Guardar cambios"> <input type="button" onclick="window.close()" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

