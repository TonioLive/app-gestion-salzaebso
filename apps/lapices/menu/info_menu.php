<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$dia=$_GET["dia"];
$curso=$_GET["curso"];
$id_menu=$_GET["id_menu"];

$filtrar_edades=$_GET["filtrar_edades"];

$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$dia)."'");
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
                } else {
                // Agrega el nuevo día
                $nuevo_dia=db_query("INSERT INTO menu_calendario (id_menu,fecha) VALUES ($id_menu,'".date("Y-m-d",$dia)."') ");
                $id_dia_menu=db_insert_id();
                $actualizar=true;
                $comida1=0;
                $comida2=0;
                $comida3=0;
                $comida4=0;
                $comida5=0;
                $postre1=0;
                $merienda1=0;
                $merienda2=0;
                }

$consulta_articulo = db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu") or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_menu=$datos_articulo["nombre_menu"];
if (strlen($nombre_menu)!=0) {
        $nombre_menu=app_url_text_utf8($nombre_menu);
        }
$id_centro_menu=$datos_articulo["id_centro"];

if ($datos_articulo["edad_fin_menu"]==100) {
                                $edad_fin_actual=">18";
                                } else {
                                $edad_fin_actual=$datos_articulo["edad_fin_menu"];
                                }
$edad_ini_actual=$datos_articulo["edad_ini_menu"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_articulo["edad_fin_menu"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;

$edad_ini_numero=$datos_articulo["edad_ini_menu"];
$edad_fin_numero=$datos_articulo["edad_fin_menu"];

if ($filtrar_edades!=1) {
$filtro_edades=" AND ((edad_ini_plato<=$edad_ini_numero) OR (edad_fin_plato<=$edad_fin_numero) OR (edad_ini_plato=$edad_ini_numero))";
} else {
$filtro_edades="";
}

// echo $filtro_edades;

$total_kcal_menu=0;
$total_hc_menu=0;
$total_prot_menu=0;
$total_grasas_menu=0;

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

function salir() {
        window.close();
        }

//-->
</SCRIPT>
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
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><br>
        <table width=580 border=0 cellpadding=0 cellspacing=0 align="center">
        <tr><td><img src="/webcam/imagenes/encabezado_info.gif" border=0></tr></td>
        <tr><td><table width=580 border=2 bordercolor="#cc424f">
        <tr bgcolor="#ffffff" valign="top">
                <td width="100%" valign="top" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Comidas del día <?=date("d/m/Y",$dia);?><br><br><big><?=$nombre_menu;?></big><br><?=$edades;?><br></b></font>
                <br>
                <table width=100% border=1 cellpadding=1 cellspacing=0 bordercolor="#cc424f" style="border-collapse: collapse">
                                <tr bgcolor="#efefef" valign="top">
                                        <td width="8%"><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                                        <td width="38%"><font size=1 face="<?=$fuente_general;?>"><b>Plato</b></font></td>
                                        <td align="left" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>KCal</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>H.C.</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>Proteinas</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>Grasas</b></font></td>
                                </tr>
                        <?php 
                                $id_plato=$comida1;
                                $nombre_plato_lista="1º";
                                
                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$comida2;
                                $nombre_plato_lista="2º";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$comida3;
                                $nombre_plato_lista="3º";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$comida4;
                                $nombre_plato_lista="4º";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$comida5;
                                $nombre_plato_lista="5º";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$postre1;
                                $nombre_plato_lista="Postre";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$merienda1;
                                $nombre_plato_lista="Merienda";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>

                        <tr>
                        <?php  } ?>
                        <?php 
                                $id_plato=$merienda2;
                                $nombre_plato_lista="Merienda";

                                $busca_articulos=db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato");

                                if (db_num_rows($busca_articulos)!=0) {

                                        $listado_articulos=db_fetch_array($busca_articulos);

                                        ?>
                                <tr bgcolor="#ffffff">
                                        <td align="left"><font size=1><b><?=$nombre_plato_lista?></b></font></td>
                                        <td align="left"><font size=1><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)
                                        <?php 

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;

                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                        $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                        }
                                $total_kcal_menu=$total_kcal_menu+$total_kcal;
                                $total_hc_menu=$total_hc_menu+$total_hc;
                                $total_prot_menu=$total_prot_menu+$total_prot;
                                $total_grasas_menu=$total_grasas_menu+$total_grasas;

                                ?>
                                </font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                        <tr>
                        <?php  } ?>
                         </table>
                         <font size=2><b>Totales día</b></font>
                         <table width=100% border=1 cellpadding=1 cellspacing=0 bordercolor="#cc424f" style="border-collapse: collapse">
                         <tr bgcolor="#efefef">
                                <td align="left" width="25%"><font size=1 face="<?= $fuente_general; ?>"><b>KCal</b></font></td>
                                <td align="center" width="25%"><font size=1 face="<?= $fuente_general; ?>"><b>H.C.</b></font></td>
                                <td align="center" width="25%"><font size=1 face="<?= $fuente_general; ?>"><b>Proteinas</b></font></td>
                                <td align="center" width="25%"><font size=1 face="<?= $fuente_general; ?>"><b>Grasas</b></font></td>
                        </tr><tr bgcolor="#ffffff">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal_menu,2,",",".");?> Kcal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc_menu,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot_menu,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas_menu,2,",",".");?> g</font></td>
                        </tr>
                         </table>
                         <br>
                        <a href="javascript:salir();" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
        </td></tr>
        </table>
        </td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_info.gif" border=0></td></tr>
        </table>
</div>
</body>
</html>

