<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("./funciones_alergias.php");

$dia=$_GET["dia"];
$curso=$_GET["curso"];
$id_menu=$_GET["id_menu"];

$filtrar_edades=$_GET["filtrar_edades"];

$busca_platos_dia=db_query("SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$dia)."'");
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
                $cena=$datos_platos_dia["cena"];             
                } else {
                // Agrega el nuevo día
                $id_dia_menu=0;
                $actualizar=true;
                $comida1=0;
                $comida2=0;
                $comida3=0;
                $comida4=0;
                $comida5=0;
                $postre1=0;
                $merienda1=0;
                $merienda2=0;
                $cena='';
                }

$consulta_articulo = db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=$id_menu") or die (db_error());
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

$filtro_edades=" AND grupo<>99";

if ($filtrar_edades!=1) {
$filtro_edades=" AND ((edad_ini_plato<=$edad_ini_numero) OR (edad_fin_plato<=$edad_fin_numero) OR (edad_ini_plato=$edad_ini_numero))";
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
self.opener.history.go(0);


function abrir_composicion(id_plato) {
        window.open("ver_composicion_plato.php?id_plato="+id_plato,"ver_plato","width=350,height=400");
        }
        
function repetir() {
        if (confirm("¿Quieres repetir este día "+self.document.repeticiones.numero_rep.value+" veces con una periodicidad de "+self.document.repeticiones.intervalo.value+" días  a partir del día <?=date("d/m/Y",$dia);?>?/n/nSe reemplazará el contenido de los días que ya tengan platos asignados.")) {
                self.document.repeticiones.submit();
                }
        }
//-->
</SCRIPT>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Comidas del día <?=date("d/m/Y",$dia);?><br><br><big><?=$nombre_menu;?></big><br><?=$edades;?><br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <form name="datos_familia" method="POST" action="editar_menu_dia1.php">
                        <input type="hidden" name="id_dia_menu" value="<?=$id_dia_menu?>">
                        <input type="hidden" name="curso" value="<?=$curso?>">
                        <input type="hidden" name="dia" value="<?=$dia?>">
                        <input type="hidden" name="id_menu" value="<?=$id_menu?>">
                                <tr bgcolor="#efefef" valign="top">
                                        <td width="8%"><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                                        <td width="30%"><font size=1 face="<?=$fuente_general;?>"><b>Plato</b></font></td>
                                        <td align="left" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>KCal</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>H.C.</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>Proteinas</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>Grasas</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>Alergenos</b></font></td>
                                        <td align="center" width="8%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                </tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>1º</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="comida1" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE (tipo_plato=0$filtro_edades) OR id_plato=$comida1 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {

$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;

                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($comida1==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }

                               


                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';
 
                                if ($comida1!=0) {
                                $id_plato=$comida1;

                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                
                                
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>2º</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="comida2" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE (tipo_plato=0$filtro_edades) OR id_plato=$comida2 ORDER BY nombre_plato");
                                if ((db_num_rows($busca_articulos)==0) and ($comida2!=0)) {
                                        $busca_datos_comida=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=$comida2");
                                        if (db_num_rows($busca_datos_comida)!=0) {
                                                $datos_comida=db_fetch_array($busca_datos_comida);
                                                ?>
                                                <option value="<?=$datos_comida["id_plato"]?>" selected><?=app_url_text_utf8($datos_comida["nombre_plato"]);?></option>
                                                <?php 
                                                }
                                        }
                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($comida2==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($comida2!=0) {
                                $id_plato=$comida2;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>
<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>3º</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="comida3" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE (tipo_plato=0$filtro_edades) OR id_plato=$comida3 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($comida3==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }
                                  
                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($comida3!=0) {
                                $id_plato=$comida3;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>
<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>4º</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="comida4" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE (tipo_plato=0$filtro_edades) OR id_plato=$comida4 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($comida4==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($comida4!=0) {
                                $id_plato=$comida4;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>

<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?=$fuente_general;?>"><b>5º</b></font></td>
                                <td align="left"><font size=1 face="<?=$fuente_general;?>"><select name="comida5" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE (tipo_plato=0$filtro_edades) OR id_plato=$comida5 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($comida5==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }

                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($comida5!=0) {
                                $id_plato=$comida5;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>

<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Postre</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="postre1" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE ((tipo_plato=1 OR tipo_plato=3)$filtro_edades) OR id_plato=$postre1 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($postre1==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }
 
                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($postre1!=0) {
                                $id_plato=$postre1;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>

<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Merienda</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="merienda1" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE ((tipo_plato=2 OR tipo_plato=3)$filtro_edades) OR id_plato=$merienda1 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($merienda1==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }


                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($merienda1!=0) {
                                $id_plato=$merienda1;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>

<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Merienda</b></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="merienda2" size=1 style="font-size: 10px" onchange="document.datos_familia.submit();">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM gestionguarderias.menu_platos WHERE ((tipo_plato=2 OR tipo_plato=3)$filtro_edades) OR id_plato=$merienda2 ORDER BY nombre_plato");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
$edad_ini_actual=$listado_articulos["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$listado_articulos["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                        ?>
                                        <option value="<?=$listado_articulos["id_plato"]?>"<?php  if ($merienda2==$listado_articulos["id_plato"]) { print " selected"; } ?>><?=app_url_text_utf8($listado_articulos["nombre_plato"]);?> (<?=$edades;?>)</option>
                                        <?php 
                                        }

                                $listaAlergenosPlato = '';
                                if ($id_plato!=0) {
                                        $contenidoCalorico = calculaCalorias($id_plato);
                                        if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                        }                
                                }
                                // Calcula contenido calorico plato
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                $id_plato=0;
                                $listaAlergenosPlato = '';

                                if ($merienda2!=0) {
                                $id_plato=$merienda2;
                                
                                $contenidoCalorico = calculaCalorias($id_plato);
                                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                                        $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                                }                                   
                                $busca_ingredientes=db_query("SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
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
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listaAlergenosPlato?></font></td>

<td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php  if ($id_plato!=0)  { ?><a href="javascript:abrir_composicion(<?=$id_plato;?>)"><img src="menu.gif" border=0></a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                        <tr>
                        <tr bgcolor="#ffffff">
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Cena</b></font></td>
                                <td align="left" colspan=6><input type="text" name="cena" id="cena" value="<?=$cena?>" style="font-size: 10px; width:100%" onblur="document.datos_familia.submit();"></td>

                        </tr>                                
                        </form>
                         </table>
                         <font size=2><b>Información nutricional</b></font>
                         <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
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
                         <input type="button" value="Salir" onclick="window.close();" style="font-size: 10px"> <input type="button" value="Borrar día" onclick="if (confirm('¿Eliminar la información de este día?')) { window.location.href='eliminar_dia.php?id_dia=<?=$id_dia_menu?>'; }" style="font-size: 10px"> <?php 
                         if ($filtrar_edades!=1) {
                         ?><input style="font-size: 10px" type="button" value="Mostrar solo platos de la edad" onclick="window.location.href='editar_menu_dia.php?filtrar_edades=1&dia=<?=$dia?>&curso=<?=$curso?>&id_menu=<?=$id_menu?>'"><?php 
                         } else {?><input style="font-size: 10px" type="button" value="Mostrar todos los platos" onclick="window.location.href='editar_menu_dia.php?dia=<?=$dia?>&curso=<?=$curso?>&id_menu=<?=$id_menu?>'"><?php 
                         }?> <input type="button" onclick="window.open('calendario_copiar.php?id_dia_copiado=<?=$id_dia_menu?>&dia=<?=date("Y-m-d",$dia);?>','calendario','width=320,height=250');" style="font-size: 10px" value="Copiar día..."> <input type="button" onclick="window.open('calendario_copiar_multiple.php?id_dia_copiado=<?=$id_dia_menu?>&dia=<?=date("Y-m-d",$dia);?>','calendario','width=320,height=250');" style="font-size: 10px" value="Copia Múltiple..."><br><br>
                         <table width=100% border=1 cellpadding=2 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse">
                         <form name="repeticiones" method="post" action="repetir_dia.php">
                        <input type="hidden" name="id_dia_menu" value="<?=$id_dia_menu?>">
                        <input type="hidden" name="curso" value="<?=$curso?>">
                        <input type="hidden" name="dia" value="<?=$dia?>">
                        <input type="hidden" name="id_menu" value="<?=$id_menu?>">
                         <tr>
                         <td align="center">
                         <font size=1 face="<?= $fuente_general; ?>"><b>Repetir este día:</b> <select name="numero_rep" style="font-size: 10px">
                         <?php 
                         for ($rep=1;$rep<=52;$rep++) {
                         ?>
                         <option value="<?=$rep?>"><?=$rep?></option>
                         <?php 
                                }
                         ?>
                         </select> veces cada <select name="intervalo" style="font-size: 10px">
                         <?php 
                         for ($rep=1;$rep<=5;$rep++) {
                         ?>
                         <option value="<?=$rep?>"><?=$rep?></option>
                         <?php 
                                }
                        $contador_semanas=1;
                         for ($rep=7;$rep<=56;$rep=$rep+7) {
                         $coletilla="";
                         if ($contador_semanas>1) {
                                $coletilla="s";
                                }
                         ?>
                         <option value="<?=$rep?>"><?=$contador_semanas?> semana<?=$coletilla;?></option>
                         <?php 
                         $contador_semanas++;
                                }
                         ?>
                         </select> días. <input type="button" style="font-size: 10px" value="Repetir" onclick="repetir()"></font>
                         </td>
                         </tr>
                         </form>
                         </table><br>
                </td>
        </tr>
</table>
<div>
<h3>Alergenos:</h3> 
<?php
foreach ($arrayKeysAlergias as $keyAlg => $numAlg) {
echo("<img src='https://catering.escuelas-infantiles.com/img/iconos/".$keyAlg.".png' border='0' title='".$nombres_alergias[$numAlg-1]."'> ".$numAlg.". ".ucfirst($nombres_alergias[$numAlg-1])." ");
if ($numAlg==6) {
        echo("<br />");
}
}

?>
</div>
</div>
<script language="Javascript">
self.opener.history.go(0);
</script>
</body>
</html>

