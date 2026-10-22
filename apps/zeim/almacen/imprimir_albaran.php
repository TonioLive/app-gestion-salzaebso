<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$accion=$_GET["accion"];
$id_albaran=$_GET["id_albaran"];

// Busca articulos genéricos que se han pedido

$busca_datos_pedido=db_query_params('SELECT * FROM albaranes_entrega WHERE id_albaran=?', 's', array($id_albaran));

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_albaran"]));
$observaciones=$datos_pedido["observaciones"];
$id_pedido=$datos_pedido["id_pedido"];
// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas, centros WHERE centro_aulas.id_centro=centros.id_centro AND id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);

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

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="no">
<div align="center"><center>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="700">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Albarán de entrega de material</b></font><br><br>
                                <font size=3 face="<?= $fuente_general; ?>"><b>Albarán Nº: <?=$id_pedido?>/<?=date("Y",strtotime($datos_pedido["fecha_albaran"]));?> - Fecha de albarán: <?=$fecha_pedido;?></b><br><br><b>Centro:</b> <?=$datos_aula["nombre_centro"]?> &nbsp;&nbsp;<b>Aula:</b> <?=$datos_aula["nombre"];?></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                $busca_detalle=db_query_params('SELECT * FROM detalles_albaran_entrega, gestionguarderias.tarifa, gestionguarderias.tipos_iva_servicios WHERE detalles_albaran_entrega.id_material=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND detalles_albaran_entrega.id_albaran=? ORDER BY detalles_albaran_entrega.generico_solicitado', 's', array($id_albaran)) or die(db_error());
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Genérico solicitado</b></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Material entregado</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                        <?php 
                                        if ($accion=="1") {
                                        ?>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>IVA %</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total Base</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Importe IVA</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                        <?php 
                                        }
                                        ?>
                                </tr>
                                <?php 
                                $total_base=0;
                                $total_iva=0;
                                $total_albaran=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $contador_filas++;

                                $nombre_generico=$datos_detalle["generico_solicitado"];
                                $nombre_material=$datos_detalle["ARTICULO"];
                                $cantidad=$datos_detalle["cantidad"];
                                $precio=$datos_detalle["PRECIO_VENTA"];
                                $iva=$datos_detalle["porcentaje_iva"];

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_generico;?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_material;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad,2,",",".")." ".$datos_detalle["UNIDAD"];?></font></td>
                                        <?php 
                                        if ($accion=="1") {
                                        ?>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($iva*100,2,",",".");?> %</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio*$iva,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio*(1+$iva),2,",",".");?> €</font></td>
                                        <?php 
                                        }
                                        ?>
                                        </td>
                                </tr>
                                <?php 
                                $total_base=$total_base+round($cantidad*$precio,2);
                                $total_iva=$total_iva+round($cantidad*$precio*$iva,2);
                                }
                                $total_albaran=$total_base+$total_iva;
                                if ($accion=="1") {
                                ?>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="right" colspan=7><font size=1 face="<?= $fuente_general; ?>"><b>Total base imponible:</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($total_base,2,",",".");?> €</font></td>
                                </tr>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="right" colspan=7><font size=1 face="<?= $fuente_general; ?>"><b>Total IVA:</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($total_iva,2,",",".");?> €</font></td>
                                </tr>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="right" colspan=7><font size=1 face="<?= $fuente_general; ?>"><b>Total Albarán:</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($total_albaran,2,",",".");?> €</font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                </td>
                        </tr>
                </table>
                <br>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="700">
                    <tr valign="top">
                                <td align="right" width=10%><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><font size=2 face="<?= $fuente_general; ?>"><?=$observaciones;?><br><br></font></font></td>
                        </tr>
                </table>
</center></div>
<script language="Javascript">
window.print();
window.close();
</script>
</body>
</html>
