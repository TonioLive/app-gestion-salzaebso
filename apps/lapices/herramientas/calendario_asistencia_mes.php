<?php 
include("../configuracion/config.php");
?>
<html>
<head>

 <title>SELECCIÓN FECHAS</title>

 <META http-equiv='Content-Type' content='text/html; charset=UTF-8'>
 <META NAME='Keywords' CONTENT=''>
 <META NAME='Description' CONTENT=''>
 <META NAME='Generator' CONTENT='flash 5'>
 <META HTTP-EQUIV='Content-language' CONTENT='es'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''>
 <META NAME='Author' CONTENT=''>
 <META NAME='Distribution' CONTENT='Global'>
 <META NAME='Rating' CONTENT='General'>
 <META NAME='Robots' CONTENT='All'>
 <META NAME='Revisit-After' CONTENT='1 days'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''>
<style type="text/css">
        body {
                scrollbar-arrow-color:#840084;
                scrollbar-face-color:#DEE3D6;
                scrollbar-bace-color:green;
                scrollbar-highlight-color:#B5B6A5;
                scrollbar-shadow-color:black;
                scrollbar-track-color:#B5B6A5;
                font-family: Trebuchet MS, Arial;
                font-size: x-small;
                font-style: normal;
                line-height: normal;
                font-weight: normal;
                font-variant: normal;
                color: #000000;
                text-decoration: none;
}

a {  font-style: normal; font-weight: normal; color: #000000; text-decoration: none}
a:hover {  font-style: normal; font-weight: normal; text-decoration: underline}
</style>

<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="#FEFEFE" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center">
<?php 
$id_alumno=$_GET["id_alumno"];
$inicio=$_GET["dia"];


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",$inicio);
        } else {
        $anio_fecha=date("Y");
        }

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

$dia_anterior=strtotime($ano_anterior."-".$mes_anterior."-1");
$dia_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-1");

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;


// Datos alumno
$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$id_alumno");

$datos_alumno=db_fetch_array($busca_alumno);

$nombre_completo_alumno=$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"].", ".$datos_alumno["Nombre"];

?>
<font color="#000000" size=1 face="<?=$fuente_general?>">Calendario de aistencia de <?=$nombre_completo_alumno?><br>
</font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</font>
</td>
<?php  
}
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>ASIS.</b>
</font>
</td>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>COME.</b>
</font>
</td>
</tr>
<?php 
$total_asistidos_mes=0;
$total_comedor_mes=0;

$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
$total_asistidos_semana=0;
$total_comedor_semana=0;
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
  if ($mes==date("n",$inicio_time_stamp))
    {
        // Dias asistidos
        $filtros_asistencia="day(servicios_consumidos.fecha)=".date("j",$inicio_time_stamp)." AND year(servicios_consumidos.fecha)=".date("Y",$inicio_time_stamp);
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".date("n",$inicio_time_stamp);
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$id_alumno.";") or die(db_error());
        if (db_num_rows($busca_dias_asistidos)!=0) {
                $total_asistidos_semana++;
                $total_asistidos_mes++;
                $fondo_celda_dia="#79c671";
                }

        $busca_dias_comedor=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND (tipo_menu=1 OR tipo_menu=3) AND id_alumno=".$id_alumno.";") or die(db_error());
        if (db_num_rows($busca_dias_comedor)!=0) {
                $total_comedor_semana++;
                $total_comedor_mes++;
                $fondo_celda_dia="#89a7d9";
                }
        }
    
?>
<td align="center" bgcolor="<?=$fondo_celda_dia;?>">
<font face="<?=$fuente_general?>" color="#000000" size=1>
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
?>
</font>
</td>
<?php 
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
<td align="center" bgcolor="#EFEFEF">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<b><?=$total_asistidos_semana?></b>
</font>
</td>
<td align="center" bgcolor="#EFEFEF">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<b><?=$total_comedor_semana?></b>
</font>
</td>
</tr>
<?php  
}
?>
<td align="center" bgcolor="#EFEFEF">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<b><?=$total_asistidos_semana?></b>
</font>
</td>
<td align="center" bgcolor="#EFEFEF">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<b><?=$total_comedor_semana?></b>
</font>
</td>
<tr>
<td>
</td>
</tr>
</table>
<font size=1><br>
<b>Total días asistidos:</b> <?=$total_asistidos_mes;?><br>
<b>Total comedores asistidos:</b> <?=$total_comedor_mes?><br>
<br>
<a href="calendario_asistencia_mes.php?id_alumno=<?=$id_alumno?>&dia=<?=$dia_anterior;?>"><< Mes anterior</a> | <a href="calendario_asistencia_mes.php?id_alumno=<?=$id_alumno?>&dia=<?=$dia_siguiente?>">Mes siguiente >></a>
<br>
Leyenda de colores
<table align=center>
<tr>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#79c671"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Día asistido</font></td>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#89a7d9"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Día asistido con comedor</font></td>
</tr>
</table>
</font>
</font>
</div>
</body>
