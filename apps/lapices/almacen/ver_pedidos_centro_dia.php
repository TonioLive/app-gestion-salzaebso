<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$fecha=$_GET["dia_sel"];

if (strlen($fecha)==0) {
        $fecha=time();
        }

$fecha_mostrar=date("Y-m-d",$fecha);

// Busca albaranes
$busca_pedidos_pendientes=db_query("SELECT * FROM pedidos_material_centros WHERE fecha_pedido='".date("Y-m-d",$fecha)."'") or die(db_error());
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Relación de pedidos realizados el día <?=date("d/m/Y",$fecha);?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table width=100%  cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                   <tr bgcolor="#efefef" valign="top">
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Número Pedido</b></font></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                   </tr>
                                <?php 
                                $contador_filas=0;
                                while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {

                                $id_pedido_actual=$datos_detalle["id_pedido_centro"];
                                $fecha_pedido=date("d/m/Y",strtotime($datos_detalle["fecha_pedido"]));

                                $observaciones=$datos_detalle["observaciones"];
                                if (strlen($observaciones)!=0) {
                                        $observaciones=str_replace(chr(92),"",$observaciones);
                                        }
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$id_pedido_actual;?>/<?=date("Y",strtotime($datos_detalle["fecha_pedido"]))?></font></td>
                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$fecha_pedido;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$observaciones;?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:ver_albaran(<?=$datos_detalle["id_pedido_centro"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar_fila(<?=$datos_detalle["id_pedido_centro"];?>)"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="window.location.href='/index.php';" style="font-size: 10px" value="Salir"><br><br>
                                </td>
                        </tr>
                        </tr>
                </table>
<script language="Javascript">
function ver_albaran(id_albaran) {
        // window.location.href='editar_pedido_proveedor.php?id_pedido='+id_albaran;
        window.location.href='ver_pedido_centro.php?fecha=<?=$fecha?>&id_pedido='+id_albaran;
        }

function borrar_fila(id_borrar) {
        alert("Opción desactivada");
        // if (confirm("¿Deseas confirmar la eliminación del pedido seleccionado ("+id_borrar+")?")) {
                // Saltamos a la pagina de borrar
                // window.location.href='eliminar_pedido_proveedor.php?fecha=<?=$fecha?>&id_pedido='+id_borrar;
        //        }
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
