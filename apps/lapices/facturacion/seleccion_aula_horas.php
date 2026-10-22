<?php  require("../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Selecciona el aula y el mes para consultar las horas extra consumidas por los alumnos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <form name="horas_extra" method="GET" action="resumen_horas_extra.php">
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>">Aula:</font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><select name="id_aula" size="1" style="font-size: 10px">
								<option value="0">Todas</option>
                                <?php 
                                $id_centro=$centro_actual;
                                $consulta_aulas=db_query("SELECT centro_aulas.*,cursos_academicos.nombre AS nombre_curso FROM centro_aulas,cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_centro=$id_centro ORDER BY centro_aulas.id_curso DESC,centro_aulas.nombre;") or die(db_error());
                                while($aulas=db_fetch_array($consulta_aulas)){
                                ?>
                                <option value="<?=$aulas["id_aula"]?>"><?=$aulas["nombre"]?> (<?=$aulas["nombre_curso"]?>)</option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>">Mes y año:</font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><select name="mes" size="1" style="font-size: 10px">
                                <?php 
                                for ($c_mes=1;$c_mes<=12;$c_mes++){
                                ?>
                                <option value="<?=$c_mes?>"<?php  if ($c_mes==date("n")) { print " selected"; } ?>><?=$nombre_mes[$c_mes];?></option>
                                <?php 
                                }
                                ?>
                                </select> / <select name="ano" size="1" style="font-size: 10px">
                                <?php 
                                for ($c_ano=$ano_inicio_actividad;$c_ano<=date("Y")+2;$c_ano++){
                                ?>
                                <option value="<?=$c_ano?>"<?php  if ($c_ano==date("Y")) { print " selected"; } ?>><?=$c_ano?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        </table>
                        <input type="submit" value="Ver resumen de horas extras" style="font-size: 10px"> <input type="button" value="Cancelar" style="font-size: 10px" onclick="window.location.href='gestion_facturacion.php'">
                </td>
                </form>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../configuracion/parte_inferior.php"); ?>
