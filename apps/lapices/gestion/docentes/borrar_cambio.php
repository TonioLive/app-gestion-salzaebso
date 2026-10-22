<?php 
require("../../configuracion/config.php");

$id_docente=$_GET["id_docente"];
$id_cambio=$_GET["id_cambio"];


$url_retorno="horario_docente.php?id_docente=$id_docente";

db_query("DELETE FROM personal_historial WHERE id_empleado=$id_docente AND id=$id_cambio") or die(db_error());

// Borra la información relacionada

header("Location: $url_retorno");

?>
