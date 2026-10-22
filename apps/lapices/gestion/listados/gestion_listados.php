<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Listados</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Profesiones" onclick="window.location.href='listado_profesiones.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de profesiones de los padres</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Informe profesiones" onclick="window.location.href='informe_profesiones.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Informe de profesiones de los padres de la escuela.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listado profesiones" onclick="window.location.href='informe_profesiones_completo.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de profesiones de los padres de la escuela.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Motivos de cancelación" onclick="window.location.href='listado_motivos.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de motivos de cancelación de la matricula de un posible alumno.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Tipos de contacto" onclick="window.location.href='listado_tipos.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de tipos de contacto. ¿Cómo nos han conocido?</font></td>
                        </tr>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../../configuracion/parte_inferior.php"); ?>
