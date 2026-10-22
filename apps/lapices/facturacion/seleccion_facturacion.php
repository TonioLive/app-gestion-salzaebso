<?php  require("../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Consulta de facturación</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación por mes" onclick="window.location.href='resumen_facturacion_mes.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación por alumno" onclick="window.location.href='resumen_facturacion_alumno.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación por cliente" onclick="window.location.href='resumen_facturacion_cliente.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../configuracion/parte_inferior.php"); ?>
