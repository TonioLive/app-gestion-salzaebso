<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

// Busca pedidos penientes de servir

$busca_pedidos_pendientes=db_query("SELECT * FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_albaran=0 GROUP BY detalles_pedido_material.id_pedido ORDER BY centro_aulas.id_centro,centro_aulas.nombre") or die(db_error()." -1");
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Relación de pedidos de material nuevos o pendientes de servir</b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                <?php 
                                $contador_filas=0;
                                $nombre_centro_selec="";
                                while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {
                                $contador_filas++;

                                $id_pedido_actual=$datos_detalle["id_pedido"];
                                $id_aula=$datos_detalle["id_aula"];
                                $nombre_aula=$datos_detalle["nombre"];
                                $fecha_pedido=date("d/m/Y",strtotime($datos_detalle["fecha_pedido"]));
                                $observaciones=$datos_detalle["observaciones"];
                                
                                // Comprueba si hay algún albarán servido
                                $busca_albaranes=db_query("SELECT * FROM albaranes_entrega WHERE id_pedido=$id_pedido_actual");
                                if (db_num_rows($busca_albaranes)!=0) {
                                        $estado_pedido="Servido";
                                        } else {
                                        $estado_pedido="Pendiente";
                                        }
                                // Busca datos del centro
                                $consulta_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_detalle["id_centro"]);
                                $datos_centro=db_fetch_array($consulta_datos_centro);
                                
                                $nombre_centro_fila=$datos_centro["nombre_centro"];
                                $nombre_pedido=$nombre_centro_fila.", ".$nombre_aula.", ".$fecha_pedido;
                                if ($nombre_centro_selec!=$nombre_centro_fila) {
                                        $nombre_centro_selec=$nombre_centro_fila
                                        ?>
                                        <tr>
                                        <td bgcolor="#ececec" valign="top" colspan=5>Centro: <?=$nombre_centro_selec?></td>
                                        </tr>
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="left" width="40%"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                                <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha pedido</b></font></td>
                                                <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Estado</b></font></td>
                                                <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>
                                        <?php 
                                        }
                                
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left" width="50%"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula;?></font></td>
                                        <td align="center" width="21%"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_pedido;?></font></td>
                                        <td align="center" width="21%"><font size=1 face="<?= $fuente_general; ?>"><?=$estado_pedido;?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar_pedido(<?=$datos_detalle["id_pedido"];?>)"><img src="editar.gif" border=0></a></td>
                                        <?php 
                                        if ($estado_pedido=="Pendiente") {
                                        ?>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar_fila(<?=$datos_detalle["id_pedido"];?>,'<?=$nombre_pedido;?>')"><img src="borrar.gif" border=0></a></td>
                                        <?php 
                                        } else {
                                        ?>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:alert('No puedes elimiar pedidos servidos parcialmente.')"><img src="borrar_des.gif" border=0></a></td>
                                        <?php 
                                        }
                                        ?>

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
function editar_pedido(id_pedido) {
        window.location.href='editar_pedido.php?id_pedido='+id_pedido;
        }

function borrar_fila(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación del pedido seleccionado ("+nombre_mostrar+")?")) {
                // Saltamos a la pagina de borrar
                window.location.href='eliminar_pedido.php?_csrf=<?=rawurlencode(app_csrf_token())?>&id_pedido='+id_borrar;
                }
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
