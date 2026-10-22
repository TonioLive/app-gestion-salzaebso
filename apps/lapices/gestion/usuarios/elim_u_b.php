<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");
 require("../../configuracion/parte_superior.php");

$codigo=$_GET["Cod"];
?>
<p align="center"><br>
<br>
<br>
<strong><big><font color="<?php  echo $color_fuente; ?>">
Opción desactivada temporalmente<br><br>
<!--¿Realmente deseas eliminar los datos?<br><br>
Al hacerlo se eliminará totalmente toda la información sobre el usuario contenida en nuestra base de datos, tambien se eliminará la información relativa a los cursos en los que ha participado.
</big></strong></p>
<br>
<p align="center">
<form id=form1 name=form1>
<input type="button" name="Si" value="Aceptar" onClick="window.location.href='borrar_u.php?Submenu=<?php  echo $submenu_activo; ?>&Cod=<?php  echo $codigo; ?>'">--> <input type="button" name="No" value="Cancelar" onClick="history.go(-1);"></p>
</form>
<!-- Hasta aquí -->  
<?php  require("../../configuracion/parte_inferior.php"); ?>
