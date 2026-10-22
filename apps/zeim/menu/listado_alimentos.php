<?php  require("../configuracion/parte_superior_privado_ancho_cocina.php"); ?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de menús<br></b><small>Listado de Alimentos.</small></font>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM gestionguarderias.menu_alimentos ORDER BY nombre_alimento") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=6 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay alimentos, haz clic en el botón <i>"Nuevo alimento"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
								<td><font size=2 face="<?=$fuente_general;?>"><b>Id.</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Alimento</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>KCal (KCal/100 g o ml)</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>H.C.(g/100 g o ml)</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Proteinas (g/100 g o ml)</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Grasas (g/100 g o ml)</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Grasas Saturadas (g/100 g o ml)</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Azúcares (g/100 g o ml)</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Sodio/mg</b></font></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        // comprueba si hay articulos
                        $consulta2=array('SELECT * FROM menu_ingredientes WHERE id_alimento=?', 's', array((string)($datos_familia["id_alimento"])));
                        $count=db_query_params($consulta2[0], $consulta2[1], $consulta2[2]);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_alimento"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar alimentos que no se encuentren en la composición de los platos.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        $nombre_alimento=$datos_familia["nombre_alimento"];
                                if (strlen($nombre_alimento)!=0) {
                                        $nombre_alimento=urldecode($nombre_alimento);
                                        }
                        if ($datos_familia["unidad_medida"]==0) {
                                $coletilla_unidad="g";
                                } else {
                                $coletilla_unidad="ml";
                                }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
								<td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_familia["id_alimento"];?></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_alimento;?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["kcal_gramo"],2,",",".");?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["hc_gramo"],2,",",".");?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["prot_gramo"],2,",",".");?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["grasas_gramo"],2,",",".");?></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["grasas_sat_gramo"],2,",",".");?></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["azucar_gramo"],2,",",".");?></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["sodio_mg"],2,",",".");?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_alimentos.php'" style="font-size: 10px"> <!--<input type="button" value="Nuevo alimento" style="font-size: 10px" onclick="window.location.href='modificar_alimento.php';">-->
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
