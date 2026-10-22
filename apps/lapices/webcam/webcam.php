<?php 
require("../configuracion/config.php"); 
require("parte_superior_padres_corta_publica.php"); ?>
    <div align="center">
        <br><br>
    <table width=70% border=0 cellpadding=0 cellspacing=0>
    <tr valign="top">
            <td align="center">
                    <img src="imag/webcam.gif" border="0" align="right">
            </td>
    <td width=80%>
                    <p align="center"><font size=3><b>Acceso al área privada para padres de las escuelas infantiles</b><small><br><br>
                    En la escuela de tu hijo, te habrán proporcionado una carta que contiene un <b>NOMBRE DE USUARIO</b> y una <b>CONTRASEÑA</b>.<br>
                    <br><br>
                    Introduce a continuación tus datos de acceso<br>para empezar a utilizar el servicio.</p><br><br></small></font>
            </td>
    </tr>
    <tr valign="top">
<form method="POST" action="acceso.php">
    <td colspan=2 align="center">
        <table border=0 cellpadding=2 cellspacing=0>
        <tr valign="top">
        <td align="right"><font size=2><b>Usuario:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="user" size="8" align="right" value="<?=$nombre_usuario;?>"></font></td>
        </tr><tr valign="top">
        <td align="right"><font size=2><b>Contraseña:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="password" name="pass" size="8" align="right"></font></td>
        </tr>
        </table><input type="image" img src="imag/enviarpeq.gif" border="0" name="enviar"></div>
     </td>
</form>
     </tr>
</table></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
