<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
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
//-->
</SCRIPT>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 

// Valores por defecto
// Valores de la tabla

$id_plato=$_GET["id_plato"];

if (strlen($id_plato)!=0) {

$consulta_articulo = db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($id_plato)) or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_plato=$datos_articulo["nombre_plato"];
if (strlen($nombre_plato)!=0) {
        $nombre_plato=urldecode($nombre_plato);
        }
$tipo_plato=$datos_articulo["tipo_plato"];
$edad_ini_plato=$datos_articulo["edad_ini_plato"];
$edad_fin_plato=$datos_articulo["edad_fin_plato"];

$texto_encabezado="Editar";
} else {
$id_plato=0;
$texto_encabezado="Nuevo";
}


?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Composición del plato<br><br><?=$nombre_plato;?><br><br></b></font>
                         <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="85%">
                                <tr bgcolor="#efefef" valign="top">
                                        <td width="20%"><font size=1 face="<?=$fuente_general;?>"><b>Ingrediente</b></font></td>
                                        <td align="left" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad/niño</b></font></td>
                                        <td align="left" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>KCal/Ración</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>H.C.</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Proteinas</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Grasas</b></font></td>
                                </tr>
                                <?php 
                                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                                if (db_num_rows($busca_ingredientes)==0) {
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td align="center" colspan=7><font size=1 face="<?= $fuente_general; ?>">No hay ingredientes en el plato</font></td>
                                </tr>
                                <<?php 
                                }
                                $array_articulos="";
                                $contador_ing=0;
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $array_articulos=$array_articulos.$datos_ingredientes["id_ingrediente"].",";
                                $contador_ing++;
                                $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                if ($datos_ingredientes["unidad_medida"]==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td width=50%><font size=1 face="<?= $fuente_general; ?>"><?=urldecode($datos_ingredientes["nombre_alimento"]);?></font></td>
                                        <td align="right" width=15%><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_ingredientes["cantidad_racion"],2,",",".");?> <?=$coletilla_unidad;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> KCal</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>">Información nutricional plato:</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                </tr>
                                </table>
                         <br>
                         <input type="button" value="Cerrar" onclick="window.close();" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
                </td>
        </tr>
</table>
</div>
</body>
</html>
