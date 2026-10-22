<?php  require("../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar este proveedor?")) {
                window.location.href="eliminar_proveedor.php?id_proveedor="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de servicios<br></b><small>Listado de Familias.</small></font>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM proveedores ORDER BY tipo_proveedor,RAZON_SOCIAL") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=3 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay proveedores, haz clic en el botón <i>"Nuevo proveedor"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        }
                        $tipo_proveedor_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        if ($tipo_proveedor_actual!=$datos_familia["tipo_proveedor"]) {
                                ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td colspan=4><font size=2 face="<?= $fuente_general; ?>"><b>Proveedores <?=$tipos_proveedores[$datos_familia["tipo_proveedor"]][0];?></b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Proveedor</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Domicilio</b></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Última<br>evaluación</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                                <?php 
                                $tipo_proveedor_actual=$datos_familia["tipo_proveedor"];
                                }
                        
                        // comprueba si hay articulos
                        $consulta2=array('SELECT * FROM tarifa WHERE PROVEEDOR=?', 's', array((string)($datos_familia["ID_PROVEEDOR"])));
                        $count=db_query_params($consulta2[0], $consulta2[1], $consulta2[2]);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["ID_PROVEEDOR"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar proveedore que hayan suministrado artículos.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }

                        // Busca ultima evaluación
                        $consulta_datos_evaluacion=db_query_params('SELECT * FROM proveedores_evaluaciones WHERE id_proveedor=? ORDER BY fecha_valoracion DESC LIMIT 0,1', 's', array($datos_familia["ID_PROVEEDOR"]));

                        if (db_num_rows($consulta_datos_evaluacion)!=0) {
                                $datos_evaluacion=db_fetch_array($consulta_datos_evaluacion);
                                $ultima_actualizacion=date("d/m/Y",strtotime($datos_evaluacion["fecha_valoracion"]));
                                } else {
                                $ultima_actualizacion="-";
                                }
                                
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_familia["RAZON_SOCIAL"];?></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_familia["DOMICILIO"];?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$ultima_actualizacion;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:modif_proveedor(<?=$datos_familia["ID_PROVEEDOR"];?>);">Modificar</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?> | <a href="ver_pedidos.php?proveedor=<?=$datos_familia["ID_PROVEEDOR"];?>">Historial pedidos</a></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/almacen/gestion_almacen.php'" style="font-size: 10px"> <input type="button" value="Nuevo proveedor" style="font-size: 10px" onclick="window.open('nuevo_proveedor_b.php','proveedores','toolbar=no,resizable=no,width=600,height=585')">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">
function modif_proveedor(id_pro){
        window.open('nuevo_proveedor_b.php?u='+id_pro,'proveedores','toolbar=no,scrollbars=yes,resizable=no,width=600,height=585');
        }
</script>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
