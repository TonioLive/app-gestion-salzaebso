<?php  require("../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar esta familia?")) {
                window.location.href="eliminar_familia.php?familia="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de servicios<br></b><small>Listado de Familias.</small></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM tipos_iva_servicios ORDER BY tipo_servicios, categoria") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=3 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay familias, haz clic en el botón <i>"Nueva familia"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        if ($datos_familia["tipo_servicios"]!=$tipo_actual) {
                                $tipo_actual=$datos_familia["tipo_servicios"];
                                $busca_nombres_tipo=db_query("SELECT * FROM tipos_categorias WHERE id_tipo=".$datos_familia["tipo_servicios"]);
                                $nombres_tipo=db_fetch_array($busca_nombres_tipo);
                                $tipo_actual_nombre=$nombres_tipo["nombre_tipo_cat"];
                                ?>
                                <tr>
                                <td colspan=3 align=left bgcolor="#efefef"><font size=2><b><?=$tipo_actual_nombre?></b></font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=1 face="<?=$fuente_general;?>"><b>Familia</b></font></td>
                                        <td><font size=1 face="<?=$fuente_general;?>"><b>Tipo de IVA</b></font></td>
                                        <td><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                                </tr>
                                <?php 
                                }
                        
                        // comprueba si hay articulos
                        $consulta2=array('SELECT * FROM tarifa WHERE CATEGORIA=?', 's', array((string)($datos_familia["id_tipo_iva"])));
                        $count=db_query_params($consulta2[0], $consulta2[1], $consulta2[2]);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_tipo_iva"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar familias que no contengan artículos.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_familia["categoria"];?></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["porcentaje_iva"]*100,2,",",".");?> %</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="modificar_familia.php?familia=<?=urlencode($datos_familia["id_tipo_iva"]);?>">Modificar</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?> | <a href="nuevo_articulo.php?familia=<?=urlencode($datos_familia["id_tipo_iva"]);?>">Nuevo servicio</a> | <a href="ver_familia.php?familia=<?=urlencode($datos_familia["id_tipo_iva"]);?>">Ver servicios</a></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px"> <input type="button" value="Nueva familia" style="font-size: 10px" onclick="window.location.href='nueva_familia.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
