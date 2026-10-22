<?php  require("../configuracion/parte_superior_privado_cocina_bootstrap.php"); ?>
<?php  require("inc/config_platos.php"); ?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=3 face="<?= $fuente_general; ?>"><b>Gestión de menús<br></b><small>Listado de Grupos</small><br>
				<input type="button" onclick="window.location.href='gestion_menu.php'" value="Salir">

				
                    <table class="table table-bordered table-striped">
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><b>ID</b></td>
                                <td><b>Grupo</b></td>
                                <td><b>&nbsp;</b></td>
                        </tr>
                        <?php
                        
                        foreach ($grupos as $claveGrupo=>$nombreGrupo) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><?=$claveGrupo;?></td>
                                <td><?=$nombreGrupo;?></td>
                                <td align="center"><a class="btn btn-primary btn-md" href="gestion_platos.php?grupo=<?=$claveGrupo;?>">Ver platos</a></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_menu.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
