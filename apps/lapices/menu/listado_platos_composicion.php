<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_ancho_cocina.php"); ?>
<?php  require("inc/config_platos.php"); ?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de menús<br></b><small>Listado de Platos con sus ingredientes.</small></font>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
						
                        $consulta_familia = db_query("select menu_alimentos.kcal_gramo, menu_alimentos.hc_gramo, menu_alimentos.prot_gramo, menu_alimentos.grasas_gramo, menu_alimentos.grasas_sat_gramo, menu_alimentos.azucar_gramo, menu_alimentos.sodio_mg, menu_platos.id_plato,menu_platos.nombre_plato,menu_platos.tipo_plato,menu_platos.edad_ini_plato,menu_platos.edad_fin_plato,menu_alimentos.nombre_alimento,menu_ingredientes.cantidad_racion, menu_alimentos.unidad_medida from menu_platos,menu_ingredientes,menu_alimentos where menu_platos.id_plato=menu_ingredientes.id_plato and menu_ingredientes.id_alimento=menu_alimentos.id_alimento order by menu_platos.edad_ini_plato,menu_platos.id_plato") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=6 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay platos</font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
								<td><font size=2 face="<?=$fuente_general;?>"><b>Id.</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Nombre plato</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Tipo</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Edades</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Nombre ingrediente</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Cantidad Ingrediente</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>KCal</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>H.C</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Proteinas</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Grasas</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Grasas Saturadas</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Azúcares</b></font></td>
								<td><font size=2 face="<?=$fuente_general;?>"><b>Sodio/mg</b></font></td>
                        </tr>
                        <?php 
                        }
                        $plato_actual=-1;
						
						$total_kcal=0;
						$total_hc=0;
						$total_prot=0;
						$total_grasas=0;
						$total_grasas_sat=0;
						$total_azucar=0;
						$total_sodio=0;
						$nombre_plato_totales="";
						
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
							if ($plato_actual!=$datos_familia['id_plato']) {
									if ($plato_actual!=-1) {
										?>
										<tr valign="top" bgcolor="#EFEFEF">
												<td colspan="6"><font size=2 face="<?=$fuente_general;?>"><b>Totales <?=app_url_text_utf8($nombre_plato_totales)?></b></font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_grasas_sat,2,",",".");?> g</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_azucar,2,",",".");?> g</font></td>
												<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sodio,2,",",".");?> mg</font></td>
										</tr>
										<?php
									$total_kcal=0;
									$total_hc=0;
									$total_prot=0;
									$total_grasas=0;
									$total_grasas_sat=0;
									$total_azucar=0;
									$total_sodio=0;
									}
								
									$plato_actual=$datos_familia['id_plato'];
									$id_mostrado=$plato_actual;
									$nombre_plato_mostrado=$datos_familia['nombre_plato'];
									$nombre_plato_totales=$nombre_plato_mostrado;
							} else {
									$id_mostrado='';
									$nombre_plato_mostrado='';
							}
						
							$edades='';
							
							$edad_ini=$datos_familia['edad_ini_plato']." años";
							if ($datos_familia['edad_ini_plato']<2) {
								$edad_ini=ceil($datos_familia['edad_ini_plato']*12)." meses";
							}							
							
							$edad_fin=$datos_familia['edad_fin_plato']." años";
							if ($datos_familia['edad_fin_plato']<2) {
								$edad_fin=ceil($datos_familia['edad_fin_plato']*12)." meses";
							}
							
							$edades = $edad_ini." a ".$edad_fin;
							
                                $total_kcal=$total_kcal+round((($datos_familia["kcal_gramo"]*$datos_familia["cantidad_racion"])/100),2);
                                $total_hc=$total_hc+round((($datos_familia["hc_gramo"]*$datos_familia["cantidad_racion"])/100),2);
                                $total_prot=$total_prot+round((($datos_familia["prot_gramo"]*$datos_familia["cantidad_racion"])/100),2);
                                $total_grasas=$total_grasas+round((($datos_familia["grasas_gramo"]*$datos_familia["cantidad_racion"])/100),2);
								$total_grasas_sat=$total_grasas_sat+round((($datos_familia["grasas_sat_gramo"]*$datos_familia["cantidad_racion"])/100),2);
								$total_azucar=$total_azucar+round((($datos_familia["azucar_gramo"]*$datos_familia["cantidad_racion"])/100),2);
								$total_sodio=$total_sodio+round((($datos_familia["sodio_mg"]*$datos_familia["cantidad_racion"])/100),2);
								
								
                                if ($datos_familia["unidad_medida"]==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
							
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
								<td><font size=2 face="<?= $fuente_general; ?>"><?=$id_mostrado;?></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=app_url_text_utf8($nombre_plato_mostrado);?></font></td>
								<td><font size=2 face="<?= $fuente_general; ?>"><?=$tipos_plato[$datos_familia["tipo_plato"]];?></font></td>
								<td><font size=2 face="<?= $fuente_general; ?>"><?=$edades;?></font></td>
								<td><font size=2 face="<?= $fuente_general; ?>"><?=app_url_text_utf8($datos_familia['nombre_alimento']);?></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($datos_familia["cantidad_racion"],2,",",".");?></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["kcal_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> KCal</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["hc_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["prot_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["grasas_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["grasas_sat_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>								
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["azucar_gramo"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>								
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format((($datos_familia["sodio_mg"]*$datos_familia["cantidad_racion"])/100),2,",",".");?> g</font></td>								
                        </tr>
                        <?php 
                        }
						if ($plato_actual!=-1) {
						?>
						<tr valign="top" bgcolor="#EFEFEF">
								<td colspan="6"><font size=2 face="<?=$fuente_general;?>"><b>Totales <?=app_url_text_utf8($nombre_plato_totales)?></b></font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_grasas_sat,2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_azucar,2,",",".");?> g</font></td>
								<td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sodio,2,",",".");?> mg</font></td>
						</tr>
						<?php
						}
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_alimentos.php'" style="font-size: 10px"> <input type="button" value="Nuevo alimento" style="font-size: 10px" onclick="window.location.href='modificar_alimento.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
