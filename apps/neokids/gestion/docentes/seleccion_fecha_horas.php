<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=6 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=6 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Selecciona el mes para consultar las horas.</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=6 width="98%">
                        <form name="horas_extra" method="GET" action="listado_resumen_horas.php">
	                    <!--
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>">Empleado/a:</font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><select name="id_aula" size="1">
                                <?php 
                                $id_centro=$centro_actual;
                                $consulta_aulas=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1', 's', array($centro_actual)) or die(db_error());
                                if (db_num_rows($consulta_aulas)==0) {
                                        ?>
                                        <option value="0">No hay empleados/as en el centro.</option>
                                        <?php 
                                        }
                                while($aulas=db_fetch_array($consulta_aulas)){
                                
                                	// Comprueba si tienen contrato en vigor
	
  									$fecha_control=date("Y-m-d");
									$busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($aulas["id_usuario"], (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());

  									if (db_num_rows($busca_contratos)!=0) {
  										$estilo_linea=" color: green";
  									} else {
  										$estilo_linea=" color: red";
  									}
                                
                                ?>
                                <option value="<?=$aulas["id_usuario"]?>" style="<?=$estilo_linea?>"><?=$aulas["Apellido1"]?> <?=$aulas["Apellido2"]?>, <?=$aulas["Nombre"]?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        -->
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>">Mes y año:</font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><select name="mes" size="1">
                                <?php 
                                for ($c_mes=1;$c_mes<=12;$c_mes++){
                                ?>
                                <option value="<?=$c_mes?>"<?php  if ($c_mes==date("n")) { print " selected"; } ?>><?=$nombre_mes[$c_mes];?></option>
                                <?php 
                                }
                                ?>
                                </select> / <select name="ano" size="1">
                                <?php 
                                for ($c_ano=$ano_inicio_actividad;$c_ano<=date("Y");$c_ano++){
                                ?>
                                <option value="<?=$c_ano?>"<?php  if ($c_ano==date("Y")) { print " selected"; } ?>><?=$c_ano?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        </table>
                        <input type="submit" value="Ver resumen de horas"> <input type="button" value="Cancelar" onclick="window.location.href='/facturacion/gestion_educadoras.php'">
                </td>
                </form>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../../configuracion/parte_inferior.php"); ?>
