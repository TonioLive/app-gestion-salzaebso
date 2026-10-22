<?php
header("Location: /app/index.php");

require("../configuracion/parte_superior_sin_login.php"); ?>
    <div align="center">
        <br><br><br><br><br>
    <table width=70% border=0 cellpadding=0 cellspacing=0>
    <tr valign="top">
            <td align="center">
                    <img src="imag/webcam.gif" border="0" align="right">
            </td>
    <td width=80%>
                    <p align="center"><font size=3><b>Acceso al área privada para padres de alumnos de la Escuela infantil Sant Pau</b><small><br><br>
                    En la escuela de tu hijo, te habrán proporcionado una carta que contiene un <b>NOMBRE DE USUARIO</b> y una <b>CONTRASEÑA</b>.<br>
                    Estos datos te permiten acceder a dsitintos servicios: Calendario de comedor, cámaras situadas en el aula a la que asiste tu hijo y las zonas comunes (patio, comedor, etc.) durante el horario docente y mientras tu hijo se encuentre en el centro.<br><br>
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
<?php  require("../configuracion/parte_inferior.php"); ?>
