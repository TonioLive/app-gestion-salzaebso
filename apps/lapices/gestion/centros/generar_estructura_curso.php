<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_curso=$_GET["id_curso"];

include("crea_estructura_carpetas.php");

include("crea_estructura_carpetas_2023.php");

include("crea_estructura_carpetas_padres.php");

?>
<p align="center"><br><br><font size="3">Estructura de carpetas de documentación para el Curso <?=$datos_curso["nombre"]?> generada</font><br><br>
<input type="button" value="Salir" onclick="window.location.href='<?=$_SERVER["HTTP_REFERER"];?>'">
<?php  require("../../configuracion/parte_inferior.php"); ?>
