<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_proveedor=$_POST["PROVEEDOR"];

$busca_datos_proveedor=db_query_params('SELECT * FROM proveedores WHERE ID_PROVEEDOR=?', 's', array($id_proveedor));

$datos_proveedor=db_fetch_array($busca_datos_proveedor);

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido_proveedor.php">
                        <input type="hidden" name="id_proveedor" value="<?=$id_proveedor?>">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido de material al proveedor "<?=$datos_proveedor["RAZON_SOCIAL"];?>"</b></font><br><font size=2 face="<?= $fuente_general; ?>"></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                $busca_detalle=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND PROVEEDOR=? ORDER BY ARTICULO_GENERICO, ARTICULO', 's', array($id_proveedor));
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                <tr bgcolor="#efefef" valign="top">
                                        <td align="left" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Genérico</b></font></td>
                                        <td align="left" width="30%"><font size=1 face="<?= $fuente_general; ?>"><b>Decripción</b></font></td>
                                        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>Stock actual</b></font></td>
                                        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>Pedidos pendientes</b></font></td>
                                        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                </tr>
                                <?php 
                                $contador_filas=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $id_articulo_tarifa=$datos_detalle["ID"];
                                $precio_articulo=$datos_detalle["PRECIO_VENTA"];
                                $nombre_articulo_tarifa=$datos_detalle["ARTICULO"];
                                                
                                // Comprueba stock actual del articulo
                                $busca_entradas=db_query_params('SELECT id_articulo, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_proveedor WHERE id_articulo=? GROUP BY id_articulo;', 's', array($id_articulo_tarifa));
                                $entradas=db_fetch_array($busca_entradas);
                                $total_entradas=$entradas["SumaDeCANTIDAD"];

                                $busca_salidas=db_query_params('SELECT id_material, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_material=? GROUP BY id_material;', 's', array($id_articulo_tarifa));
                                $salidas=db_fetch_array($busca_salidas);
                                $total_salidas=$salidas["SumaDeCANTIDAD"];

                                $stock_articulo=$total_entradas-$total_salidas;

                                if ($stock_articulo<=0) {
                                                        $stock_articulo="<font color=red>".number_format($stock_articulo,2,",",".")."</font>";
                                                        }

                                // Busca pedidos pendientes de servir de este articulo
                                $busca_cantidad_pendiente=db_query_params('SELECT * FROM detalles_pedido_material,pedidos_material WHERE detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado=?', 's', array((string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_cantidad_pendiente)==0) {
                                        $pendiente_de_servir="";
                                        } else {
                                        $cantidad_pendiente_de_servir=0;
                                        while($datos_cantidad_pendiente=db_fetch_array($busca_cantidad_pendiente)) {
                                                // Busca si existen albaranes parcialmente servidos
                                                $cantidad_servida=0;
                                                $busca_albaranes_servidos=db_query_params('SELECT * FROM albaranes_entrega, detalles_albaran_entrega WHERE albaranes_entrega.id_albaran=detalles_albaran_entrega.id_albaran AND detalles_albaran_entrega.id_detalle_pedido=?', 's', array($datos_cantidad_pendiente["id_detalle"]));
                                                while($datos_cantidad_albaranes_servidos=db_fetch_array($busca_albaranes_servidos)) {
                                                        $cantidad_servida=$cantidad_servida+$datos_cantidad_albaranes_servidos["cantidad"];
                                                        }
                                                $cantidad_pendiente_de_servir=$cantidad_pendiente_de_servir+$datos_cantidad_pendiente["cantidad"]-$cantidad_servida;
                                                }
                                        if ($cantidad_pendiente_de_servir>0) {
                                                $pendiente_de_servir=" <font color=red>".number_format($cantidad_pendiente_de_servir,2,",",".")."</font>";
                                                } else {
                                                $pendiente_de_servir=number_format($cantidad_pendiente_de_servir,2,",",".");
                                                }
                                        }
                                
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["ARTICULO"]?>">
                                        <input type="hidden" name="id_fila_detalles_<?=$contador_filas;?>" value="">
                                        <input type="hidden" name="id_fila_tipo_iva_<?=$contador_filas;?>" value="<?=$datos_detalle["porcentaje_iva"];?>">
                                        <input type="hidden" name="id_articulo_tarifa_<?=$contador_filas;?>" value="<?=$id_articulo_tarifa?>">
                                        <input type="hidden" name="precio_articulo_tarifa_<?=$contador_filas;?>" value="<?=$precio_articulo?>">
                                        <input type="hidden" name="totales" value="0">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_detalle["ARTICULO_GENERICO"]?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_articulo_tarifa;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$stock_articulo;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$pendiente_de_servir;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input tabindex=<?=1000+$contador_filas?> type="text" size=5 name="cantidad_<?=$contador_filas;?>" value="0" style="font-size: 10px;text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio_articulo,2,",",".");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input tabindex=<?=3000+$contador_filas?> type="text" size=5 name="importe_fila_<?=$contador_filas;?>" value="0.00" style="font-size: 10px;text-align= right" readonly> €</font></td>
                                </tr>
                                <?php 
                                $contador_filas++;
                                }
                                ?>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="right" colspan=6><font size=1 face="<?= $fuente_general; ?>"><b>Total pedido:</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="total_pedido" value="<?=number_format($total_pedido,2,",",".");?>" style="font-size: 10px;text-align= right" readonly> €</font></td>
                                </tr>
                                </table>
                                <br><input type="button" onclick="salir()" style="font-size: 10px" value="Cancelar pedido"> <input type="button" onclick="guardar()" style="font-size: 10px" value="Tramitar pedido"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        <input type="hidden" name="contador_filas" value="<?=$contador_filas;?>">
                    </form>
                        </tr>
                </table>
<script language="Javascript">
function comprueba_cantidades(fila) {
        var errores=0;
        var mensajes_error="";

        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");

        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                eval("self.document.nuevo_pedido.cantidad_"+fila+".focus()");
                                errores=1;
                                } else {
                                cantidad=Number(cantidad);
                                precio_articulo=eval("self.document.nuevo_pedido.precio_articulo_tarifa_"+fila+".value");
                                precio_articulo=Number(precio_articulo);
                                total_fila=Math.round(cantidad*precio_articulo*100)/100;
                                eval("self.document.nuevo_pedido.importe_fila_"+fila+".value="+total_fila);

                                // Calcula el total
                                self.document.nuevo_pedido.totales[fila].value=total_fila;
                                totale_pedido=0;
                                for (ctot=0;ctot<=<?=$contador_filas-1;?>;ctot++) {
                                        totale_pedido=totale_pedido+Number(self.document.nuevo_pedido.totales[ctot].value);
                                        }
                                self.document.nuevo_pedido.total_pedido.value=totale_pedido;
                                }
                        }

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                }
        }

function guardar() {
        var errores=0;
        var mensajes_error="";
        var total_cantidades=0;
        for (fila=0;fila<=<?=$contador_filas-1;?>;fila++) {
        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");

        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                } else {
                                total_cantidades=total_cantidades+cantidad;
                                }
                        }
        }
        if (total_cantidades==0) {
                                 mensajes_error=mensajes_error+"No has pedido ningún artículo.\u000A";
                                 errores=1;
                                 }
        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                } else {
                document.nuevo_pedido.action="guarda_pedido_proveedor.php?r=1";
                document.nuevo_pedido.submit();
                }
                
        }
function salir() {
        if (confirm("¿Quieres salir y eliminar el pedido actual?")) {
                window.location.href='gestion_almacen.php';
                }
        }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
