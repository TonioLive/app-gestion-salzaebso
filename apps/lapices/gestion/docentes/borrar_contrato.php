<?php 
require("../../configuracion/config.php");

$id_docente=$_GET["id_docente"];
$id_contrato=$_GET["id_contrato"];


$url_retorno="horario_docente.php?id_docente=$id_docente";

db_query("DELETE FROM personal_contratos WHERE id_empleado=$id_docente AND id_contrato=$id_contrato") or die(db_error());

// Borra la información relacionada

header("Location: $url_retorno");

?>
