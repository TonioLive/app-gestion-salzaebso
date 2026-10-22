<?php  require("../configuracion/parte_superior_privado.php"); ?>
<div style="margin:20px;text-align:left"><a href="/gestion/personal/alta.php" style="display:inline-block;padding:14px 20px;border-radius:12px;background:#21675a;color:#fff;font-weight:bold;text-decoration:none">Alta de personal: manual o desde contrato PDF</a><p>La nueva alta guarda la ficha, el contrato, el horario y el aula en un solo proceso.</p></div>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de personal</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Gestión del personal" onclick="window.location.href='/gestion/docentes/listado_docentes.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de los horarios del personal de la escuela, contratos laborales y control de la documentación de PRL</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Calendario laboral" onclick="window.location.href='/gestion/calendario/calendario_laboral.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Calendario laboral del centro.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Incidendias personal" onclick="window.location.href='/gestion/calendario/calendario_ausencias.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Calendario de incidencias de personal.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Control personal" onclick="window.location.href='/gestion/docentes/seleccion_fecha_horas.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de las horas de entrada y salida del personal.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../configuracion/parte_inferior.php"); ?>
