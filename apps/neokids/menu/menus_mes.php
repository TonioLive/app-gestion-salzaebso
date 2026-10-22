<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>                                                                                                                                               <div align="center">
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="MM_preloadImages('/webcam/imagenes/ver_menu_act.gif')">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align=left>
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
<div align="center">
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><img src="/webcam/imagenes/encabezado_menu.gif" border=0></tr></td>
<tr><td><table width=690 border=2 bordercolor="cc424f">
<tr>
<td bgcolor="#ffffff" align="center">
<?php 
// Busca el curso actual

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$inicio=$datos_curso_actual["fecha_inicio"];

if (strtotime($inicio)<strtotime(date("Y-m-d"))) {
        $inicio=date("Y-m-d");
        }

if (strtotime($inicio)>strtotime($datos_curso_actual["fecha_fin"])) {
        $inicio=$datos_curso_actual["fecha_fin"];
        }

$dia_sel_inicio=$_GET["dia"];
if (strlen($dia_sel_inicio)!=0) {
        $inicio=$dia_sel_inicio;
        }


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",strtotime($inicio));
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

//Inicio es el día en el que empieza la primera semana del mes + 6 horas (por lo del cambio horario, mire usted)
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400+21600;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;

$mes_fin_curso=date("n",strtotime($datos_curso_actual["fecha_fin"]));
$anio_fin_curso=date("Y",strtotime($datos_curso_actual["fecha_fin"]));
$mes_ini_curso=date("n",strtotime($datos_curso_actual["fecha_inicio"]));
$anio_ini_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));


$anio_actual=date("Y",$fecha_time_stamp);
$mesnombre=$nombre_mes[$mes];
?>
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b>Listado de menús programados para <?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b><br><br></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<?php 
$busca_menus=db_query_params('SELECT id_menu FROM menu_calendario WHERE fecha>=? AND fecha<=? GROUP BY id_menu', 'ss', array((string)(date("Y-m-01", $fecha_time_stamp)), (string)(date("Y-m-t", $fecha_time_stamp))));

while($lista_menus=db_fetch_array($busca_menus)){

$id_menu=$lista_menus["id_menu"];


        //  Busca nombre menú
        if ($id_menu!=0) {
                $busca_nombre_menu=db_query_params('SELECT * FROM menu_menus WHERE id_menu=?', 's', array($id_menu));
                if (db_num_rows($busca_nombre_menu)!=0) {
                        $datos_nombre_menu=db_fetch_array($busca_nombre_menu);
                        $nombre_menu_seleccionado=$datos_nombre_menu["nombre_menu"];
                        if (strlen($nombre_menu_seleccionado)!=0) {
                                $nombre_menu_seleccionado=urldecode($nombre_menu_seleccionado);
                                }
                        } else {
                        $nombre_menu_seleccionado="";
                        }
                } else {
                $nombre_menu_seleccionado="";
                }

?>
</tr>
<tr bgcolor="#ffffff">
<td><font color="#cc424f" size=3 face="<?=$fuente_general?>"><b><?=$nombre_menu_seleccionado?></b></font></td>
<td width=25% align="center"><a target="_new" href="imprimir_menu.php?id_menu=<?=$id_menu;?>&dia=<?=$inicio?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4<?=$id_menu;?>','','/webcam/imagenes/imprimir_act.gif',1)"><img src="/webcam/imagenes/imprimir_des.gif" alt="Ver menú" name="Image4<?=$id_menu;?>" border=0 id="Image4<?=$id_menu;?>"></a></td>
</tr>
<?php 
}
?>
</table>
<table width=100%>
<tr>
<td align="left" width=33%><?php 
if (($mes_ini_curso==$mes_actual_1) and ($anio_ini_curso==$anio_actual_1)) {
?>
&nbsp;
<?php 
} else {
?><a href="menus_mes.php?dia=<?=$ano_anterior?>-<?=$mes_anterior?>-1" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','/webcam/imagenes/mes_anterior_act.gif',1)"><img src="/webcam/imagenes/mes_anterior_des.gif" alt="Ver menú" name="Image1" border=0 id="Image1"></a><?php 
}
?></td>
<td width=34% align="center"><a href="/index.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
<td align="right" width=33%><?php 
if (($mes_fin_curso==$mes_actual_1) and ($anio_fin_curso==$anio_actual_1)) {
?>
&nbsp;
<?php 
} else {
?><a href="menus_mes.php?dia=<?=$ano_siguiente?>-<?=$mes_siguiente?>-1" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','/webcam/imagenes/mes_siguiente_act.gif',1)"><img src="/webcam/imagenes/mes_siguiente_des.gif" alt="Ver menú" name="Image2" border=0 id="Image2"></a><?php 
}
?></td>
</tr>
</table>
</td>
</tr>
</table></td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_menu.gif" border=0></td></tr>
        </table>
<br>
<script language="Javascript">
function info_dia(dia,curso,id_menu) {
        window.open("info_menu.php?dia="+dia+"&curso="+curso+"&id_menu="+id_menu,"info_menu","toolbar=no,width=600,height=500");
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
