<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_proveedor=$_GET["proveedor"];

// Busca albaranes

$busca_pedidos_pendientes=db_query("SELECT * FROM pedidos_proveedor, proveedores WHERE pedidos_proveedor.id_proveedor=proveedores.ID_PROVEEDOR AND pedidos_proveedor.id_proveedor=$id_proveedor ORDER BY pedidos_proveedor.fecha_pedido") or die(db_error()." -1");
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Relación de pedidos realizados</b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                   <tr bgcolor="#efefef" valign="top">
                                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Número Pedido</b></font></td>
                                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha pedido</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha entrega</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                   </tr>
                                <?php 
                                $contador_filas=0;
                                while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {

                                $id_pedido_actual=$datos_detalle["id_pedido"];
                                $fecha_pedido=date("d/m/Y",strtotime($datos_detalle["fecha_pedido"]));
                                if ($datos_detalle["fecha_entrega"]!="0000-00-00") {
                                        $fecha_entrega=date("d/m/Y",strtotime($datos_detalle["fecha_entrega"]));
                                        } else {
                                        $fecha_entrega="";
                                        }

                                $observaciones=$datos_detalle["observaciones"];

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$id_pedido_actual;?>/<?=date("Y",strtotime($datos_detalle["fecha_pedido"]))?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_pedido;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_entrega;?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:ver_albaran(<?=$datos_detalle["id_pedido"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar_fila(<?=$datos_detalle["id_pedido"];?>)"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="window.location.href='listado_proveedores.php'" style="font-size: 10px" value="Salir"><br><br>
                                </td>
                        </tr>
                        </tr>
                </table>
<script language="Javascript">
function ver_albaran(id_albaran) {
        // window.location.href='editar_pedido_proveedor.php?id_pedido='+id_albaran;
        window.location.href='ver_pedido_proveedor.php?id_pedido='+id_albaran;
        }

function borrar_fila(id_borrar) {
        if (confirm("¿Deseas confirmar la eliminación del pedido seleccionado ("+id_borrar+")?")) {
                // Saltamos a la pagina de borrar
                window.location.href='eliminar_pedido_proveedor.php?_csrf=<?=rawurlencode(app_csrf_token())?>&id_pedido='+id_borrar;
                }
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
