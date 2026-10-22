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
 <META HTTP-EQUIV='Reply-to' CONTENT=''><link rel="SHORTCUT ICON" href="">
 
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
$inicio=$_GET["fecha_inicio"];
$id_menu=$_GET["id_menu"];
$plantilla=$_GET["plantilla"];

$numero_semana=$_GET["semana"];

if (strlen($inicio)!=0) {
    $fecha_time_stamp=$inicio;
    $anio_fecha=date("Y",$inicio);
} else {
    $anio_fecha=date("Y");
    $fecha_time_stamp=time();
}
$id_dia_copiado=$_GET["id_dia_copiado"];

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
<font color="#000000" size=3 face="<?=$fuente_general?>">
<b><?php  echo $mesnombre; ?>, <?php  echo date("Y",$fecha_time_stamp); ?></b>
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
<font face="<?=$fuente_general?>" color="#ffffff" size=2>
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

    // Comprueba si tiene ya los platos asignados
    $textoMostrado=""; 
    $busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");
    if (db_num_rows($busca_platos_dia)!=0) {
            if ($fondo_celda_dia=="#F17327") {
                    $fondo_celda_dia="#E7FF30";
                    } else {
                    $fondo_celda_dia="#5DDD6C";
                    }
                    $textoMostrado = "<div align='center'>Día ya programado.</div>";
            } else {
            // Otra cosa
            }    
?>
<td align="center" bgcolor="<?=$fondo_celda_dia;?>">
<font face="<?=$fuente_general?>" color="#000000" size=2>
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

    echo($textoMostrado);

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
<font size=2><a href="calendario_copiar_plantilla.php?id_dia_copiado=<?=$id_dia_copiado?>&semana=<?=$numero_semana?>&id_menu=<?=$id_menu?>&fecha_inicio=<?=strtotime($ano_anterior."-".$mes_anterior."-1");?>"><< Mes anterior</a> | <a href="calendario_copiar_plantilla.php?id_dia_copiado=<?=$id_dia_copiado?>&semana=<?=$numero_semana?>&id_menu=<?=$id_menu?>&fecha_inicio=<?=strtotime($ano_siguiente."-".$mes_siguiente."-1");?>">Mes siguiente >></a><br>
</font>
</font>
</div>
<script language="Javascript">
function seleccionar(dia,mes,ano) {
        if (confirm("Se sobreescribirá el contenido de los días seleccionados.\n\n¿Estás seguro?")) {
                self.location.href='copiar_dia_plantilla.php?plantilla=<?=$plantilla?>&id_dia_menu=<?=$id_dia_copiado?>&dia='+dia+'&mes='+mes+'&ano='+ano+"&id_menu_destino=<?=$id_menu?>";
                }
        }
</script>
</body>
