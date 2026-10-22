<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Incidencias</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                        if ($nivel_acceso>=3) {
                        if ($nivel_acceso>=4) {
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Incidencias Asistencia" onclick="window.location.href='gestion_incidencias.php'" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de las incidencias de asistencia de los alumnos.</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Seguimiento incidencias" onclick="window.location.href='incidencias_general.php'" style="font-size:17px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión incidencias de: Accidentes, Educativas, Material, Enfermedad, Comedor y Cocina.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Estadísticas Incidencias" onclick="window.location.href='estadisticas_incidencias.php'" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Estádisticas de las incidencias de: Accidentes, Educativas, Material, Enfermedad, Comedor y Cocina.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Est. Inci. por Escuela" onclick="window.location.href='estadisticas_incidencias_escuela.php'" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Estádisticas de las incidencias por escuelas de: Accidentes, Educativas, Material, Enfermedad, Comedor y Cocina.</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Notificar Incidencia" onclick="window.location.href='notificar_previo.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Notificar incidencias de: Accidentes, Educativas, Material, Enfermedad, Comedor o Cocina.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Mis incidencias" onclick="window.location.href='mis_incidencias.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de incidencias notificadas por mi.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../../configuracion/parte_inferior.php"); ?>
