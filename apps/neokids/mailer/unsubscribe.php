<?php 
require("../configuracion/config.php");
require("../configuracion/parte_superior.php");
?>
<div align="center"><br>
<form name="cancelar" action="unsubscribe1.php" method="GET">
<font size=3 face="<?=$fuente_general?>">
Si desea no seguir recibiendo informácion por correo electrónico,<br>
introduzca aquí su cuenta de correo y pulse el botón <b>"Cancelar suscripción"</b><br><br>
Dirección de E-Mail: <input type="text" name="cuenta" size=45><br><br>
<input type="button" onclick="window.location.href='/'" value="Salir"> <input type="submit" value="Cancelar suscripción">
</font>
</form>
</div>
<?php 
require("../configuracion/parte_inferior.php"); ?>
?>