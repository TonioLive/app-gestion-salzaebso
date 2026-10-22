<?php  
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
  $fecha_time_stamp=$inicio;
} 

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];


//Inicio es el día en el que empieza la primera semana del mes
if ($diasemana_inicio_mes==0) {
	$diasemana_inicio_mes=7;	
}
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

?>
<small><small><small>
<font color="#000000"><b>
Calendario de actividades<br>
<?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?>
</b></font>
</small></small></small>
<table border=0 cellspacing="1" cellpadding="1">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="Verdana" color="#ffffff">
<small><small>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</small></small>
</font>
<td valign="top">
<font face="Verdana" color="#ffffff">
<small><small>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</small></small>
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
?>
<td align="right" bgcolor="#EFEFEF">
<font face="Verdana" color="#000000">
<small><small>
<a href="javascript:openOferta('<?= $url_web; ?>/actividades.php?id_curso=<?= $id_curso; ?>&dia=<?= $inicio_time_stamp; ?>',450,450,704)">
<?php 
    if ($mes==date("n",$inicio_time_stamp))
    {
      print "<b>";    } 
    if ($dias==7)
    {
      print "<font color=#800000>";    } 
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y",$fecha_time_stamp))
    {
    print "<font color='blue'>";    }
    print (date("j",$inicio_time_stamp));
    if ($mes==date("n",$inicio_time_stamp))
    {
    print "</b>";
    }
    print "</a>";
    $inicio_time_stamp=$inicio_time_stamp+86400;
?>
</small></small>
</font>
</td>
<?php  
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
<small><small><small>
<font color="#000000">
(*) Haz click! en el día<br>
para ver las actividades.<br>
<a href="javascript:openOferta('<?php  echo $url_web; ?>/calendario.asp?id_curso=<?php  echo $id_curso; ?>',540,350,1850)"><b>Ampliar el calendario</b></a>
</font>
</small></small></small>
