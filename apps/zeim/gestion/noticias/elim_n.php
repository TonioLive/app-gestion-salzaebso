<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior.php");

$codigo=$_GET["noticia"];
$ancho_tabla=$ancho_pagina_total-185
?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd_at.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" colspan=3 align="center">
                            <font size="3" color="#000000"><b>Gestión del tablón de anuncios</font></b>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id_at.gif" border=0 width=8 height=8></td>
                </tr>
    </table><br>
<p align="center"><br>
<br>
<br>
<strong><big>Realmente desea eliminar la noticia.</big></strong></p>
<br>
<p align="center">
<form>
<input type="button" name="Si" value="Aceptar" onClick="window.location.href='borrar2.php?Submenu=<?php  echo $submenu_activo; ?>&Cod=<?php  echo $codigo; ?>'"> <input type="button" name="No" value="Cancelar" onClick="history.go(-1);"></p>
</form>
<!-- Hasta aquí -->  
<?php  require("../../configuracion/parte_inferior.php"); ?>

