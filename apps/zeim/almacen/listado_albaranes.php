<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_pedido=$_GET["id_pedido"];

// Busca albaranes

$busca_pedidos_pendientes=db_query_params('SELECT * FROM albaranes_entrega,centro_aulas WHERE albaranes_entrega.id_aula=centro_aulas.id_aula AND albaranes_entrega.id_pedido=? ORDER BY albaranes_entrega.fecha', 's', array($id_pedido)) or die(db_error()." -1");
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Relación de albaranes emitidos para el pedido nº <?=$id_pedido?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                   <tr bgcolor="#efefef" valign="top">
                                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Número Albarán</b></font></td>
                                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Albarán</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha entrega</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                   </tr>
                                <?php 
                                $contador_filas=0;
                                while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {

                                $id_pedido_actual=$datos_detalle["id_albaran"];
                                $id_aula=$datos_detalle["id_aula"];
                                $nombre_aula=$datos_detalle["nombre"];
                                $fecha_pedido=date("d/m/Y",strtotime($datos_detalle["fecha"]));
                                $fecha_entrega="";
                                $observaciones=$datos_detalle["observaciones"];

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$id_pedido_actual;?>/<?=date("Y",strtotime($datos_detalle["fecha"]))?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_pedido;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_entrega;?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:ver_albaran(<?=$datos_detalle["id_albaran"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar_fila(<?=$datos_detalle["id_albaran"];?>)"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="window.location.href='gestion_almacen.php'" style="font-size: 10px" value="Salir"><br><br>
                                </td>
                        </tr>
                        </tr>
                </table>
<script language="Javascript">
function ver_albaran(id_albaran) {
        window.location.href='ver_albaran.php?id_albaran='+id_albaran;
        }

function borrar_fila(id_borrar) {
        if (confirm("¿Deseas confirmar la eliminación del albarán seleccionado ("+id_borrar+")?")) {
                // Saltamos a la pagina de borrar
                window.location.href='eliminar_albaran.php?id_pedido=<?=$id_pedido?>&id_albaran='+id_borrar;
                }
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
