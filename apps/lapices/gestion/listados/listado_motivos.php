<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar este motivo?")) {
                window.location.href="eliminar_motivo.php?id_motivo="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de listados<br></b><small>Listado de Motivos de cancelación de matriculas.</small></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM contactos_motivos_cancelacion ORDER BY nombre_motivo") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=2 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay motivo, haz clic en el botón <i>"Nuevo motivo"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Motivo</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        // comprueba si hay articulos
                        $consulta2="SELECT * FROM contactos WHERE motivo_cancelacion=".$datos_familia["id_motivo"];
                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_motivo"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar motivo que no hayan sido asignadas a ningún contacto.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        $nombre_motivo=$datos_familia["nombre_motivo"];
                                if (strlen($nombre_motivo)!=0) {
                                        $nombre_motivo=urldecode($nombre_motivo);
                                        }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_motivo;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="modificar_motivo.php?id_motivo=<?=$datos_familia["id_motivo"];?>">Modificar</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_listados.php'" style="font-size: 10px"> <input type="button" value="Nuevo motivo" style="font-size: 10px" onclick="window.location.href='modificar_motivo.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../../configuracion/parte_inferior.php"); ?>
