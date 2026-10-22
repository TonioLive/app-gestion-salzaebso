<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_pedido=$_GET["id_pedido"];

$fecha=$_GET["fecha"];

// Busca articulos genéricos que se han pedido

$busca_datos_pedido=db_query("SELECT * FROM pedidos_proveedor, proveedores WHERE pedidos_proveedor.id_proveedor=proveedores.ID_PROVEEDOR AND pedidos_proveedor.id_pedido=$id_pedido");

$datos_pedido=db_fetch_array($busca_datos_pedido);

$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

if (strlen($observaciones)!=0) {
        $observaciones=urldecode($observaciones);
        }
        
$id_pedido=$datos_pedido["id_pedido"];

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Hoja de pedido de material para el proveedor <b>"<?=$datos_pedido["RAZON_SOCIAL"];?></b>"</font><br><br><font size=3 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?>/<?=date("Y",strtotime($datos_pedido["fecha_pedido"]));?> - Fecha de pedido: <?=$fecha_pedido;?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                $busca_detalle=db_query("SELECT * FROM detalles_pedido_proveedor, tarifa WHERE detalles_pedido_proveedor.id_articulo=tarifa.ID AND detalles_pedido_proveedor.id_pedido=$id_pedido ORDER BY tarifa.ARTICULO") or die(db_error());
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Artículo Genérico</b></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Denominación</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>IVA %</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total Base</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Importe IVA</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                </tr>
                                <?php 
                                $total_base=0;
                                $total_iva=0;
                                $total_pedido=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $contador_filas++;

                                $nombre_generico=$datos_detalle["ARTICULO_GENERICO"];
                                $nombre_material=$datos_detalle["ARTICULO"];
                                $cantidad=$datos_detalle["cantidad"];
                                $precio=$datos_detalle["precio"];
                                $iva=$datos_detalle["tipo_iva"];

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_generico;?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_material;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad,2,",",".")." ".$datos_detalle["UNIDAD"];?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($iva*100,2,",",".");?> %</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio*$iva,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad*$precio*(1+$iva),2,",",".");?> €</font></td>
                                        </td>
                                </tr>
                                <?php 
                                $total_base=$total_base+round($cantidad*$precio,2);
                                $total_iva=$total_iva+round($cantidad*$precio*$iva,2);
                                }
                                $total_pedido=$total_base+$total_iva;
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
                                        <td align="right" colspan=7><font size=1 face="<?= $fuente_general; ?>"><b>Total pedido:</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($total_pedido,2,",",".");?> €</font></td>
                                </tr>
                                </table>
                                <br><input type="button" onclick="salir()" style="font-size: 10px" value="Salir"> <input type="button" onclick="imprimir_pedidos(1)" style="font-size: 10px" value="Imprimir pedido valorado"> <input type="button" onclick="imprimir_pedidos(2)" style="font-size: 10px" value="Imprimir pedido no valorado"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        </tr>
                </table>
<script language="Javascript">

function imprimir_pedidos(accion) {
                window.open('imprimir_pedido.php?id_pedido=<?=$id_pedido;?>&accion='+accion,"ver_alabran","width=750,height=450");
                }

function salir() {
<?php 
if (strlen($fecha)!=0) {
?>
                window.location.href='ver_pedidos_fecha.php?fecha=<?=$fecha;?>';
<?php 
} else {
?>
                window.location.href='ver_pedidos.php?proveedor=<?=$datos_pedido["id_proveedor"];?>';
<?php 
}
?>
                }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
