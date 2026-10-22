<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Centros</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Domicilio</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Fax</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                         <?php 
                         $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                         while($centros=db_fetch_array($consulta_centros)){
                         ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["nombre_centro"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["domicilio_centro"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["poblacion_centro"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["telefono_centro"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["fax_centro"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="editar_centro.php?id_centro=<?=$centros["id_centro"];?>"><img src="editar.gif" border=0></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table><br>
                         <input type="button" onclick="window.location.href='editar_centro.php'" value="Introducir Nuevo Centro" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px">
                </td>
        </tr>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
