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
<?php 

$busca_menus=db_query_params('SELECT id_menu FROM menu_calendario WHERE fecha>=? AND fecha<=? GROUP BY id_menu', 'ss', array((string)(date("Y-m-01")), (string)(date("Y-m-t"))));

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

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];

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

// Empezamos
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$nombres_alimentos=array("Verdura","Pollo","Ternera","Pescado","Huevo","Lentejas","Lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten");
$nombres_alergias=array("Lacteos","Huevo","Proteina de la ternera","Frutos secos","Marisco","Piel de melocoton","Celiaco");

?>
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><img src="/webcam/imagenes/encabezado_menu.gif" border=0></tr></td>
<tr><td><table width=690 border=2 bordercolor="cc424f">
<tr>
<td bgcolor="#ffffff" align="center">
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$mes_s=1;
$mesnombre=$nombre_mes[$mes];
$dias_trabajados_mes=0;
?>
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b><?=$nombre_menu_seleccionado?><br><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
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
<?php    echo $dia_sem_largo[$dia_s]; ?>
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

    if ($mes==date("n",$inicio_time_stamp)) {
    
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        $estado_boton=" checked";
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                } else {
                // Si es domingo lo marca como festivo
                if (($dias==7) or ($dias==6)) {
                        $estado_boton=" checked";
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                }
                
        // Comprueba si tiene ya los platos asignados

        // $busca_platos_dia=mysql_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");

        ?>
        <td align="center" valign="top" bgcolor="<?=$fondo_celda_dia;?>" onclick="info_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$id_menu;?>);">
        <font face="<?=$fuente_general?>" color="#000000" size=2>
        <?php 
        print "<a href='javascript:info_dia($inicio_time_stamp,$id_curso_actual,$id_menu)' title='Ver deatlles del menú: ".$nombre_menu_seleccionado."'>";
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

$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $inicio_time_stamp))));
if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                // Edita el nuevo día
                $actualizar=false;
                $id_dia_menu=$datos_platos_dia["id_dia"];
                $comida1=$datos_platos_dia["comida1"];
                $comida2=$datos_platos_dia["comida2"];
                $comida3=$datos_platos_dia["comida3"];
                $comida4=$datos_platos_dia["comida4"];
                $comida5=$datos_platos_dia["comida5"];
                $postre1=$datos_platos_dia["postre1"];
                $merienda1=$datos_platos_dia["merienda1"];
                $merienda2=$datos_platos_dia["merienda2"];

        $color_alerta="#FF0000";

        echo "<table width=100% border=0 cellpadding=0 cellspacing=1>";
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 3:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 4:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 5:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Postre:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        echo "</table>";
        }
        ?>
        </font>
        </td>
        <?php 
        } else {
        ?>
        <td align="center" bgcolor="#AAAAAA">
        <font face="<?=$fuente_general?>" color="#000000" size=1>&nbsp;</font>
        </td>
        <?php 
        }
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php 
}
$mes=$mes+1;
if ($mes==13) {
        $mes=1;
        }
$anio_actual=date("Y",$inicio_time_stamp);
$inicio_time_stamp=$inicio_time_stamp-(86400*7);

$anio_actual_1=date("Y",$inicio_time_stamp);
$mes_actual_1=date("n",$inicio_time_stamp);
?>
</table>
<table width=100%>
<tr>
<td align="left" width=25%><?php 
if (($mes_ini_curso==$mes_actual_1) and ($anio_ini_curso==$anio_actual_1)) {
?>
&nbsp;
<?php 
} else {
?><a href="ver_menu.php?dia=<?=$ano_anterior?>-<?=$mes_anterior?>-1" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','/webcam/imagenes/mes_anterior_act.gif',1)"><img src="/webcam/imagenes/mes_anterior_des.gif" alt="Ver menú" name="Image1" border=0 id="Image1"></a><?php 
}
?></td>
<td width=25% align="center"><a href="/index.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
<td width=25% align="center"><a target="_new" href="imprimir_menu.php?dia=<?=date("Y-m-d",$inicio_mes_time_stamp)?>&id_menu=<?=$id_menu;?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','/webcam/imagenes/imprimir_act.gif',1)"><img src="/webcam/imagenes/imprimir_des.gif" alt="Ver menú" name="Image4" border=0 id="Image4"></a></td>
<td align="right" width=25%><?php 
if (($mes_fin_curso==$mes_actual_1) and ($anio_fin_curso==$anio_actual_1)) {
?>
&nbsp;
<?php 
} else {
?><a href="ver_menu.php?dia=<?=$ano_siguiente?>-<?=$mes_siguiente?>-1" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','/webcam/imagenes/mes_siguiente_act.gif',1)"><img src="/webcam/imagenes/mes_siguiente_des.gif" alt="Ver menú" name="Image2" border=0 id="Image2"></a><?php 
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
<?php 
}
?>
<script language="Javascript">
function info_dia(dia,curso,id_menu) {
        window.open("info_menu.php?dia="+dia+"&curso="+curso+"&id_menu="+id_menu,"info_menu","toolbar=no,width=600,height=500");
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
