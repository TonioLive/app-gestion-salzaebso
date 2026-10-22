<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_pedido=$_GET["id_pedido"];

// Busca articulos genéricos que se han pedido

$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido));

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);


?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido de material para el <?=$datos_aula["nombre"];?></b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?> - Fecha: <?=$fecha_pedido;?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                $busca_detalle=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? ORDER BY generico_solicitado', 's', array($id_pedido)) or die(db_error());
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width=50%><font size=1 face="<?= $fuente_general; ?>"><b>Descripción</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Existencias Aula</b></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad pedida</b></font></td>
                                </tr>
                                <?php 
                                $total_pedido=0;
                                $contador_filas=0;
                                $contador_total=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $contador_filas++;
                                $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($id_aula, (string)($datos_detalle["generico_solicitado"])));
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                                        }

                                $nombre_articulo=$datos_detalle["generico_solicitado"];

                                // Busca pedidos abiertos de este articulo
                                $busca_cantidad=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? AND generico_solicitado=?', 'ss', array($id_pedido, (string)($datos_detalle["generico_solicitado"])));
                                if (db_num_rows($busca_cantidad)==0) {
                                        $cantidad=number_format(0,2,",",".");
                                        } else {
                                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                        $cantidad=number_format($datos_cantidad_articulo["cantidad"],2,",",".");
                                        $cantidad_dato1=$datos_cantidad_articulo["cantidad"];
                                        }

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$existencias_articulo;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$cantidad;?></font></td>
                                        </td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="salir()" style="font-size: 10px" value="Salir"> <input type="button" onclick="ver_albaranes()" style="font-size: 10px" value="Ver albaranes de este pedido"> <br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        </tr>
                </table>
<script language="Javascript">

function salir() {
                window.location.href='pedidos_servidos.php';
        }
function ver_albaranes() {
                window.location.href='listado_albaranes.php?id_pedido=<?=$id_pedido;?>';
                }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
