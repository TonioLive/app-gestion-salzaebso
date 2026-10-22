<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];

if (strlen($id_aula)!=0) {

        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
        if (db_num_rows($busca_datos_aula)!=0) {
                $datos_aula=db_fetch_array($busca_datos_aula);
                $mensaje="Edición";
                $nombre_aula=$datos_aula["nombre"];
                $capacidad=$datos_aula["capacidad"];
                $ocupacion_minima=$datos_aula["ocupacion_minima"];
                $descripcion=$datos_aula["descripcion"];
                $equipamiento=$datos_aula["equipamiento"];
                $edad_minima=$datos_aula["edad_minima"];
                $edad_maxima=$datos_aula["edad_maxima"];
                $curso_academico=$datos_aula["id_curso"];
                $docente_titular=$datos_aula["docente_titular"];
                $id_centro=$datos_aula["id_centro"];
                } else {
                $id_aula="";
                $id_centro=$centro_actual;
                $mensaje="Alta";
                }
                
        }

// A continuación detecta en que dia cae el primer lunes de esta mes
// Inicio=fecha del lunes
// mesini=Nombre del mes en el que empieza la semana
// mesfin=Nombre del mes en el que acaba la semana
// Si hoy es domingo pasa automaticamente a la semana siguiente

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
} 

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

?>
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?=$nombre_aula;?><br>
<?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?>
</b></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%" height="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</font>
</td>
<?php  
}
?>
</tr>
<?php  
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {
  
  if ($mes==date("n",$inicio_time_stamp))
    {
    $fondo_celda_dia="#EFEFEF";
    } else {
    $fondo_celda_dia="#AAAAAA";
    }
  if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#DD9999";
    }
?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>">
<font face="<?=$fuente_general?>" color="#000000" size=3>
<?php 
    if ($mes==date("n",$inicio_time_stamp))
    {
      print "<b>";    } 
    if ($dias==7)
    {
      print "<font color=#800000>";    } 
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    print "<font color='blue'>";    }
    print (date("j",$inicio_time_stamp));
    if ($mes==date("n",$inicio_time_stamp))
    {
    print "</b>";
    }
    print "</a>";

    // Tarde desactivada
    $estado_boton_tarde="";

    /*if (date("H")<13) {
        $estado_boton_tarde=" disabled";
        }
        */

?>
</font><div align="center"><input type="button" onclick="window.location.href='/gestion/agenda/dia_aula.php?turno=0&id_aula=<?=$id_aula?>&dia=<?= $inicio_time_stamp; ?>'" value=" Mañana "><br><input type="button" onclick="window.location.href='/gestion/agenda/dia_aula.php?turno=1&id_aula=<?=$id_aula?>&dia=<?= $inicio_time_stamp; ?>'" value="   Tarde   "<?=$estado_boton_tarde;?>></div>
</td>
<?php 
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php  
}
?>
<tr>
<td>
</td>
</tr>
</table>
<br><font size=2>
<a href="calendario_mes.php?id_aula=<?=$id_aula?>&dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior | <a href="calendario_mes.php?id_aula=<?=$id_aula?>&dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<font color="#000000" size=2 face="<?=$fuente_general?>">
(*) Haz click! en el día para acceder al control de asistencia<br>
</font>
<?php  require("../configuracion/parte_inferior.php"); ?>
