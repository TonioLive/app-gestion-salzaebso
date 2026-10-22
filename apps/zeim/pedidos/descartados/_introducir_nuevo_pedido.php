<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_pedido=$_GET["id_pedido"];

if (strlen($id_pedido)==0) {
        $id_aula=$_GET["id_cliente"];
        
        // Comprueba is hay un pedido para este aula hoy
        
        $busca_pedido_hoy=db_query_params('SELECT * FROM pedidos_material WHERE id_aula=? AND fecha_pedido=?', 'ss', array($id_aula, (string)(date("Y-m-d"))));
        if (db_num_rows($busca_pedido_hoy)!=0) {
                        $datos_pedido_hoy=db_fetch_array($busca_pedido_hoy);
                        $id_pedido=$datos_pedido_hoy["id_pedido"];
                } else {
                        db_query_params('INSERT INTO pedidos_material (id_aula, id_usuario, fecha_pedido) VALUES (?, ?, ?);', 'sss', array((string)($id_aula), (string)($idUsuario), (string)(date("Y-m-d"))));
                        $id_pedido=db_insert_id();
                }
        }

// Busca pedido

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
                                // Busca documentos de este expediente
                                $busca_detalle=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? ORDER BY id_detalle', 's', array($id_pedido)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                <tr bgcolor="#efefef" valign="top">
                                        <td align="left" width="30%"><font size=1 face="<?= $fuente_general; ?>"><b>Categoria</b></font></td>
                                        <td align="left" width="40%"><font size=1 face="<?= $fuente_general; ?>"><b>Decripción</b></font></td>
                                        <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                        <td align="center" width="5%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        <td align="center" width="5%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                </tr>
                                <?php 
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $busca_datos_articulo=db_query_params('SELECT * FROM tarifa,tipos_iva_servicios WHERE ID=? AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva', 's', array($datos_detalle["id_material"]));
                                $datos_articulo=db_fetch_array($busca_datos_articulo);
                                $categoria=$datos_articulo["categoria"];
                                $nombre_articulo=$datos_articulo["ARTICULO"];
                                $cantidad=number_format($datos_detalle["cantidad"],2,",",".");
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$categoria;?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$cantidad;?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar_doc(<?=$datos_detalle["id_detalle"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar_fila(<?=$datos_detalle["id_detalle"];?>,'<?=$nombre_articulo;?>')"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="nuevo_articulo()" style="font-size: 10px" value="Agregar nuevo material"> <input type="button" onclick="salir()" style="font-size: 10px" value="Cancelar pedido"> <input type="button" onclick="guardar()" style="font-size: 10px" value="Tramitar pedido"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido.php">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <input type="hidden" name="id_quitar_fila" value="">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                    </form>
                        </tr>
                </table>
<script language="Javascript">
function guardar() {
        document.nuevo_pedido.action="guarda_pedido.php?r=1";
        document.nuevo_pedido.submit();
        }
function salir() {
        if (confirm("¿Quieres salir y eliminar el pedido actual?")) {
                window.location.href='eliminar_pedido.php?id_pedido=<?=$id_pedido;?>';
                }
        }
function nuevo_articulo() {
        window.open('nuevo_articulo.php?id_pedido=<?=$id_pedido?>&id_aula=<?=$id_aula;?>','editar','toolbar=no,resizable=no,width=500,height=400');
        }
        
function borrar_fila(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación la fila seleccionada ("+nombre_mostrar+")?")) {
                document.nuevo_pedido.id_quitar_fila.value=id_borrar;
                document.nuevo_pedido.submit();
                }
        }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
