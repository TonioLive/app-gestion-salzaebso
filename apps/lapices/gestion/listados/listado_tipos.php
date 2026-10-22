<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar este tipo?")) {
                window.location.href="eliminar_tipo.php?id_tipo="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <b>Gestión de listados<br></b><small>Listado de tipos de contacto.</small>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM contactos_tipo ORDER BY nombre_tipo") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=2 align=center bgcolor="#FFFFFF"><br><br><b>No hay tipos, haz clic en el botón <i>"Nuevo tipo"</i> para empezar a trabajar</b><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><b>Tipo de contacto</b></td>
                                <td><b>&nbsp;</b></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        // comprueba si hay articulos
                        $consulta2="SELECT * FROM contactos WHERE como_conocio=".$datos_familia["id_tipo"];
                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_tipo"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar tipos que no hayan sido asignados a ningún contacto.\")'><font color=#ff0000>";
                                $link_elim2="</a>";
                                }
                        $nombre_tipo=$datos_familia["nombre_tipo"];
                                if (strlen($nombre_tipo)!=0) {
                                        $nombre_tipo=urldecode($nombre_tipo);
                                        }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><?=$nombre_tipo;?></td>
                                <td align="center"><a href="modificar_tipo.php?id_tipo=<?=$datos_familia["id_tipo"];?>">Modificar</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_listados.php'"> <input type="button" value="Nuevo tipo" onclick="window.location.href='modificar_tipo.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../../configuracion/parte_inferior.php"); ?>
