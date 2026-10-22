<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_pedido=$_GET["id_pedido"];

// Busca articulos genéricos que se han pedido

$busca_datos_pedido=db_query("SELECT * FROM pedidos_material WHERE id_pedido=$id_pedido");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);


?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido.php">
                        <input type="hidden" name="_csrf" value="<?=app_escape(app_csrf_token())?>">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido de material para el <?=$datos_aula["nombre"];?></b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?> - Fecha: <?=$fecha_pedido;?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                $busca_detalle=db_query("SELECT * FROM detalles_pedido_material WHERE id_pedido=$id_pedido ORDER BY generico_solicitado") or die(db_error());
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width=20% rowspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Descripción</b></font></td>
                                        <td align="center" rowspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Existencias<br>Aula</b></font></td>
                                        <td align="center" rowspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad<br>inicial pedida</b></font></td>
                                        <td align="center" rowspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Cantidades</b></font></td>
                                        <td align="center" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>Referencias disponibles para este genérico</b></font></td>
                                </tr>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="center" colspan=5 width=50%>
                                                <table cellpadding=1 cellspacing=0 border=0 width=100%>
                                                <tr valign="bottom">
                                                <td align="left" width=30%><font size=1 face="<?= $fuente_general; ?>"><b>Referencia</b></font></td>
                                                <td align="center" width=10%><font size=1 face="<?= $fuente_general; ?>"><b>Stock almacén</b></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad<br>a servir</b></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                                </tr>
                                                </table>
                                        </td>
                                </tr>
                                <?php 
                                $total_pedido=0;
                                $contador_filas=0;
                                $contador_total=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $contador_filas++;
                                $busca_existencias_articulo=db_query("SELECT * FROM existencias_aulas WHERE id_aula=".$id_aula." AND nombre_generico='".$datos_detalle["generico_solicitado"]."'");
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                                        }

                                $nombre_articulo=$datos_detalle["generico_solicitado"];

                                // Busca pedidos abiertos de este articulo
                                $busca_cantidad=db_query("SELECT * FROM detalles_pedido_material WHERE id_pedido=$id_pedido AND generico_solicitado='".$datos_detalle["generico_solicitado"]."'");
                                if (db_num_rows($busca_cantidad)==0) {
                                        $cantidad=number_format(0,2,",",".");
                                        } else {
                                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                        $cantidad=number_format($datos_cantidad_articulo["cantidad"],2,",",".");
                                        $cantidad_dato1=$datos_cantidad_articulo["cantidad"];
                                        }
                                
                                // Busca pedidos pendientes de servir de este articulo
                                $busca_cantidad_pendiente=db_query("SELECT * FROM detalles_pedido_material,pedidos_material WHERE detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado='".$datos_detalle["generico_solicitado"]."'");
                                if (db_num_rows($busca_cantidad_pendiente)==0) {
                                        $pendiente_de_servir="";
                                        $pendiente_de_servir_mia="";
                                        } else {
                                        $cantidad_pendiente_de_servir=0;
                                        $cantidad_servida_total=0;
                                        $cantidad_pendiente_de_servir_mia=0;
                                        while($datos_cantidad_pendiente=db_fetch_array($busca_cantidad_pendiente)) {
                                                // Busca si existen albaranes parcialmente servidos
                                                $cantidad_servida=0;
                                                $busca_albaranes_servidos=db_query("SELECT * FROM albaranes_entrega, detalles_albaran_entrega WHERE albaranes_entrega.id_albaran=detalles_albaran_entrega.id_albaran AND detalles_albaran_entrega.id_detalle_pedido=".$datos_cantidad_pendiente["id_detalle"]);
                                                while($datos_cantidad_albaranes_servidos=db_fetch_array($busca_albaranes_servidos)) {
                                                        $cantidad_servida=$cantidad_servida+$datos_cantidad_albaranes_servidos["cantidad"];
                                                        }
                                                $cantidad_servida_total=$cantidad_servida_total+$cantidad_servida;
                                                $cantidad_pendiente_fila=$datos_cantidad_pendiente["cantidad"]-$cantidad_servida;
                                                if ($datos_cantidad_pendiente["id_aula"]==$id_aula) {
                                                        $cantidad_pendiente_de_servir_mia=$cantidad_pendiente_de_servir_mia+$cantidad_pendiente_fila;
                                                        }
                                                $cantidad_pendiente_de_servir=$cantidad_pendiente_de_servir+$cantidad_pendiente_fila;
                                                }
                                        $pendiente_de_servir_mia_dato=$cantidad_pendiente_de_servir_mia;
                                        $pendiente_de_servir=number_format($cantidad_pendiente_de_servir,2,",",".");
                                        $pendiente_de_servir_mia=number_format($cantidad_pendiente_de_servir_mia,2,",",".");
                                        }
                                
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["generico_solicitado"]?>">
                                        <input type="hidden" name="id_fila_detalles_<?=$contador_filas;?>" value="<?=$datos_detalle["id_detalle"]?>">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$existencias_articulo;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="cantidad_<?=$contador_filas;?>" value="<?=$cantidad;?>" style="font-size: 10px;text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>">Servida: <?=number_format($cantidad_servida_total,2,",",".");?><br>Pendiente: <?=$pendiente_de_servir_mia;?><br>Total aulas: <?=$pendiente_de_servir?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" colspan=5>
                                                <table cellpadding=1 cellspacing=1 border=0 width=100%>
                                                <?php 
                                                // Busca referencias para el genérico
                                                $contador_filas1=0;
                                                $busca_referencias_gen=db_query("SELECT * FROM tarifa WHERE ARTICULO_GENERICO='".$datos_detalle["generico_solicitado"]."'");
                                                while($referencias_gen=db_fetch_array($busca_referencias_gen)) {
                                                $contador_filas1++;
                                                
                                                $id_articulo_tarifa=$referencias_gen["ID"];
                                                $precio_articulo=$referencias_gen["PRECIO_VENTA"];
                                                $nombre_articulo_tarifa=$referencias_gen["ARTICULO"];
                                                
                                                // Busca si hay un pedido sin albaranar
                                                $busca_cantidades_guardadas=db_query("SELECT * FROM detalles_pedido_extendido WHERE id_detalle_pedido=".$datos_detalle["id_detalle"]." AND id_tarifa=$id_articulo_tarifa");
                                                if (db_num_rows($busca_cantidades_guardadas)!=0) {
                                                        $cantidades_guardadas=db_fetch_array($busca_cantidades_guardadas);
                                                        $cantidad_servida=$cantidades_guardadas["cantidad"];
                                                        } else {
                                                        $cantidad_servida=0;
                                                        }
                                                // Comprueba stock actual del articulo
                                                $busca_entradas=db_query("SELECT id_articulo, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_proveedor WHERE id_articulo=$id_articulo_tarifa GROUP BY id_articulo;");
                                                $entradas=db_fetch_array($busca_entradas);
                                                $total_entradas=$entradas["SumaDeCANTIDAD"];
                                                
                                                $busca_salidas=db_query("SELECT id_material, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_material=$id_articulo_tarifa GROUP BY id_material;");
                                                $salidas=db_fetch_array($busca_salidas);
                                                $total_salidas=$salidas["SumaDeCANTIDAD"];
                                                
                                                $stock_articulo=$total_entradas-$total_salidas;
                                                
                                                if ($stock_articulo<=0) {
                                                        $stock_articulo="<font color=red>$stock_articulo</font>";
                                                        }
                                                
                                                $detalle_articulo=$nombre_articulo_tarifa."\nUnidades: ".$referencias_gen["UNIDAD"]."\nPrecio coste: ".number_format($referencias_gen["PRECIO_COSTE"],2,",",".")." €";
                                                $total_pedido=$total_pedido+round(($cantidad_servida*$precio_articulo),2);
                                                ?>
                                                <tr>
                                                <input type="hidden" name="id_detalle_pedido_<?=$contador_total;?>" value="<?=$datos_detalle["id_detalle"]?>">
                                                <input type="hidden" name="id_articulo_tarifa_<?=$contador_total;?>" value="<?=$id_articulo_tarifa?>">
                                                <input type="hidden" name="precio_articulo_tarifa_<?=$contador_total;?>" value="<?=$precio_articulo?>">
                                                <input type="hidden" name="totales" value="<?=round(($cantidad_servida*$precio_articulo),2)?>">
                                                <td align="left" width=30%><font size=1 face="<?= $fuente_general; ?>"><a title="<?=$detalle_articulo;?>"><?=$nombre_articulo_tarifa;?></a></font></td>
                                                <td align="right" width=10%><font size=1 face="<?= $fuente_general; ?>"><?=$stock_articulo;?></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="cantidad_servida_<?=$contador_total;?>" value="<?=number_format($cantidad_servida,2,",",".");?>" style="font-size: 10px;text-align= right" onblur="comprueba_cantidades1(<?=$contador_filas;?>,<?=$contador_filas1?>,<?=$contador_total;?>,'<?=$nombre_articulo_tarifa?>')"<?php if ($pendiente_de_servir_mia_dato<=0) { print " disabled"; } ?>></font></td>
                                                <td align="right" width=20%><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio_articulo,2,",",".");?> €</font></td>
                                                <td align="right" width=20%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="importe_fila_<?=$contador_total;?>" value="<?=number_format($cantidad_servida*$precio_articulo,2,",",".");?>" style="font-size: 10px;text-align= right" readonly> €</font></td>
                                                </tr>
                                                <?php 
                                                $contador_total++;
                                                }
                                                ?>
                                                </table>
                                        </td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="center" colspan=4>&nbsp;</td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Total pedido:</b> <input type="text" size=5 name="total_pedido" value="<?=number_format($total_pedido,2,",",".");?>" style="font-size: 10px;text-align= right" readonly> €</font></td>
                                </tr>
                                </table>
                                <br><input type="button" onclick="salir()" style="font-size: 10px" value="Salir"> <input type="button" onclick="guardar()" style="font-size: 10px" value="Guardar cambios y salir"> <input type="button" onclick="ver_albaranes()" style="font-size: 10px" value="Ver albaranes de este pedido"> <input type="button" onclick="albaran()" style="font-size: 10px" value="Generar nuevo albarán"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        <input type="hidden" name="contador_filas" value="<?=$contador_filas;?>">
                        <input type="hidden" name="contador_total" value="<?=$contador_total;?>">
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
                                }
                        }

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                }
        }
function comprueba_cantidades1(fila,fila1,fila_total,nombre_fila) {
        var errores=0;
        var mensajes_error="";

        cantidad=eval("self.document.nuevo_pedido.cantidad_servida_"+fila_total+".value");

        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                eval("self.document.nuevo_pedido.cantidad_servida_"+fila_total+".focus()");
                                errores=1;
                                } else {
                                cantidad=Number(cantidad);
                                precio_articulo=eval("self.document.nuevo_pedido.precio_articulo_tarifa_"+fila_total+".value");
                                precio_articulo=Number(precio_articulo);
                                total_fila=Math.round(cantidad*precio_articulo*100)/100;
                                eval("self.document.nuevo_pedido.importe_fila_"+fila_total+".value="+total_fila);
                                
                                // Calcula el total
                                self.document.nuevo_pedido.totales[fila_total].value=total_fila;
                                totale_pedido=0;
                                for (ctot=0;ctot<=<?=$contador_total-1;?>;ctot++) {
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
                document.nuevo_pedido.action="guarda_pedido.php?r=1";
                document.nuevo_pedido.submit();
                
        }
function ver_albaranes() {
                document.nuevo_pedido.action="guarda_pedido.php?r=2";
                document.nuevo_pedido.submit();

        }
function albaran() {
        if (confirm("¿Quieres generar un albarán con los artículos pedidos?")) {
                document.nuevo_pedido.action="guarda_pedido.php?generar_albaran=1";
                document.nuevo_pedido.submit();
                }

        }
function salir() {
        if (confirm("¿Quieres salir sin guardar los cambios?")) {
                window.location.href='pedidos_pendientes.php';
                }
        }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
