<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Selecciona la categoría de la incidencia</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                        
                        $busca_grupos=db_query("SELECT grupo FROM incidencias_tipos GROUP BY grupo ORDER BY GRUPO");

                        while($grupos=db_fetch_array($busca_grupos)) {
                        	?>
							<tr valign="top">
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="<?=$grupos["grupo"]?>" onclick="window.location.href='notificar.php?grupo=<?=$grupos["grupo"]?>'" style="font-size:18px; width: 200; height: 32"></font></td>
                        </tr>
                        	<?php 
                        	}                                                
?>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../../configuracion/parte_inferior.php"); ?>
