<?php  require("../configuracion/parte_superior_docente.php"); ?>
<?php 
$id_actividad=$_GET["id_actividad"];

if (strlen($id_actividad)!=0) {

        $busca_datos_actividad=db_query("SELECT * FROM tarifa WHERE ID=$id_actividad");
        if (db_num_rows($busca_datos_actividad)!=0) {
                $datos_actividad=db_fetch_array($busca_datos_actividad);
                $mensaje="Edición";
                $nombre_actividad=$datos_actividad["ARTICULO"];
                $fecha_inicio_actividad=$datos_actividad["FECHA_INICIO_ASISTENCIA"];
                $fecha_fin_actividad=$datos_actividad["FECHA_FIN_ASISTENCIA"];
                } else {
                $id_actividad="";
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
  $fecha_time_stamp=strtotime($fecha_inicio_actividad);
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

// Sumamos 2 horas por lo del cambio horario
$inicio_time_stamp=$inicio_time_stamp+7200;

?>
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?=$nombre_actividad;?><br>
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
    
    $estado_boton="";
    if (($inicio_time_stamp>=strtotime($fecha_inicio_actividad)) and ($inicio_time_stamp<=strtotime($fecha_fin_actividad)+79200)) {
        $estado_boton="";
        } else {
        $estado_boton=" disabled";
        }
?>
</font><div align="center"><input type="button" onclick="window.location.href='control_asistencia_actividad.php?id_actividad=<?=$id_actividad?>&dia=<?= $inicio_time_stamp; ?>'" value=" Asistencia "<?=$estado_boton;?>></div>
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
<a href="calendario_mes_actividad.php?id_actividad=<?=$id_actividad?>&dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior | <a href='gestion_aulas.php'>Salir</a> | <a href="calendario_mes_actividad.php?id_actividad=<?=$id_actividad?>&dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<font color="#000000" size=2 face="<?=$fuente_general?>">
(*) Haz click! en el día para acceder al control de asistencia<br>
</font>
<?php  require("../configuracion/parte_inferior.php"); ?>
