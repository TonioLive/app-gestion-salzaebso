<?php  require("../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar esta lista?")) {
                window.location.href="eliminar_lista.php?id_lista="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de compras</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Nueva lista" onclick="window.location.href='nueva_lista_compra.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Genera lista de la compra entre dos fechas.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Listas de compras.</b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_lista = db_query("SELECT * FROM menu_lista_compra ORDER BY fecha_ini DESC") or die (db_error());
                        
                        if (db_num_rows($consulta_lista)==0) {
                        ?>
                        <tr>
                        <td colspan=5 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay listas elaboradas</font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=1 face="<?=$fuente_general;?>"><b>ID</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Del día</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Al día</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        
                        while($datos_lista = db_fetch_array($consulta_lista)){
                        $texto_observaciones=$datos_lista["observaciones"];

                        if (strlen($texto_observaciones)!=0) {
                                $texto_observaciones=urldecode($texto_observaciones);
                                }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_lista["id_lista"];?></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_lista["fecha_ini"]));?></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_lista["fecha_fin"]));?></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$texto_observaciones;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="#" onclick="window.open('ver_lista_compra.php?id_lista=<?=$datos_lista["id_lista"];?>','lista_compra','toolbar=no,resizable=yes,scrollbars=yes')">Ver/Editar</a> | <a href="#" onclick="eliminar(<?=$datos_lista["id_lista"];?>)">Eliminar</a></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
