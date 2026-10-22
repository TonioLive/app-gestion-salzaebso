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

// Busca articulos genéricos que se pueden pedirse

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
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido.php">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido de material para el <?=$datos_aula["nombre"];?></b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?> - Fecha: <?=$fecha_pedido;?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_detalle=db_query("SELECT ARTICULO_GENERICO FROM tarifa WHERE ARTICULO_GENERICO<>'' GROUP BY ARTICULO_GENERICO ORDER BY ARTICULO_GENERICO");
                                ?><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                <tr bgcolor="#efefef" valign="top">
                                        <td align="left" width="40%"><font size=1 face="<?= $fuente_general; ?>"><b>Decripción</b></font></td>
                                        <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Existencias</b><br>Corregir para ajustar a las existencias disponibles.</font></td>
                                        <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b><br>Entre parentesis los pedidos pendientes de servir</b></font></td>
                                </tr>
                                <?php 
                                $contador_filas=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                $contador_filas++;
                                $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($id_aula, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["cantidad"],2,",",".");
                                        }

                                $nombre_articulo=$datos_detalle["ARTICULO_GENERICO"];

                                // Busca pedidos abiertos de este articulo
                                $busca_cantidad=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? AND generico_solicitado=?', 'ss', array($id_pedido, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_cantidad)==0) {
                                        $cantidad=number_format(0,2,",",".");
                                        } else {
                                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                        $cantidad=number_format($datos_cantidad_articulo["cantidad"],2,",",".");
                                        }
                                
                                // Busca pedidos pendientes de servir de este articulo
                                $busca_cantidad_pendiente=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido<>? AND id_albaran=0 AND generico_solicitado=?', 'ss', array($id_pedido, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_cantidad_pendiente)==0) {
                                        $pendiente_de_servir="";
                                        } else {
                                        $cantidad_pendiente_de_servir=0;
                                        while($datos_cantidad_pendiente=db_fetch_array($busca_cantidad_pendiente)) {
                                                $cantidad_pendiente_de_servir=$cantidad_pendiente_de_servir+$datos_cantidad_pendiente["cantidad"];
                                                }
                                        $pendiente_de_servir=" <font color=red>(".number_format($cantidad_pendiente_de_servir,2,",",".").")</font>";
                                        }
                                
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["ARTICULO_GENERICO"]?>">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="existencias_<?=$contador_filas;?>" value="<?=$existencias_articulo;?>" style="font-size: 10px;text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=5 name="cantidad_<?=$contador_filas;?>" value="<?=$cantidad;?>" style="font-size: 10px;text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"><?=$pendiente_de_servir?></font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="salir()" style="font-size: 10px" value="Cancelar pedido"> <input type="button" onclick="guardar()" style="font-size: 10px" value="Tramitar pedido"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                    </form>
                        </tr>
                </table>
<script language="Javascript">
function comprueba_cantidades(fila) {
        var errores=0;
        var mensajes_error="";

        existencias=eval("self.document.nuevo_pedido.existencias_"+fila+".value");
        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");
        
        if (existencias!="") {
                        existencias=existencias.replace(",",".");
                        if (isNaN(existencias)) {
                                mensajes_error=mensajes_error+"Las existencias de '"+nombre_fila+"' deben ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                }
        }
function guardar() {
        var errores=0;
        var mensajes_error="";
        for (fila=1;fila<=<?=$contador_filas;?>;fila++) {
        existencias=eval("self.document.nuevo_pedido.existencias_"+fila+".value");
        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");
        if (existencias!="") {
                        existencias=existencias.replace(",",".");
                        if (isNaN(existencias)) {
                                mensajes_error=mensajes_error+"Las existencias de '"+nombre_fila+"' deben ser un valor numérico.\u000A";

                                errores=1;
                                }
                        }
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        }
        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                } else {
                document.nuevo_pedido.action="guarda_pedido.php?r=1";
                document.nuevo_pedido.submit();
                }
                
        }
function salir() {
        if (confirm("¿Quieres salir y eliminar el pedido actual?")) {
                window.location.href='eliminar_pedido.php?id_pedido=<?=$id_pedido;?>';
                }
        }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
