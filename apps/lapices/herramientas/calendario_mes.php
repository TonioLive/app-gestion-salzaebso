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
$nombre_formulario=$_GET["formulario_destino"];
$campo_destino=$_GET["campo_destino"];
$inicio=$_GET["dia"];


if (strlen($inicio)!=0) {
        $anio_fecha=substr($inicio,0,4);
        } else {
        $anio_fecha=date("Y");
        }

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

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>">
<a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_anterior_c1?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>"><<<</a> | <a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_anterior_c?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>"><<</a> | <b><?php  echo $mesnombre; ?>, <?php  echo date("Y",$fecha_time_stamp); ?></b> | <a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_posterior_c?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>">>></a> | <a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_posterior_c1?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>">>>></a>
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
<td align="center" bgcolor="<?=$fondo_celda_dia;?>">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<?php 
    print "<a href='javascript:seleccionar(\"".date("d",$inicio_time_stamp)."\",\"".date("m",$inicio_time_stamp)."\",\"".date("Y",$inicio_time_stamp)."\")' title='Seleccionar día'>";
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
</tr>
<?php  
}
?>
<tr>
<td>
</td>
</tr>
</table>
<font size=1><a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior</a> | <a href="calendario_mes.php?formulario_destino=<?=$nombre_formulario?>&campo_destino=<?=$campo_destino?>&dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
</font>
</div>
<script language="Javascript">
function seleccionar(dia,mes,ano) {
        window.opener.document.<?=$nombre_formulario;?>.<?=$campo_destino?>_dia.value=dia;
        window.opener.document.<?=$nombre_formulario;?>.<?=$campo_destino?>_mes.value=mes;
        window.opener.document.<?=$nombre_formulario;?>.<?=$campo_destino?>_ano.value=ano;
        window.close();
        }
</script>
</body>
