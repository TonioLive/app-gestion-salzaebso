<?php 
require("../../configuracion/config.php");

$id_pago=$_GET["id_pago"];
$id_docente=$_GET["id_docente"];
$id_contrato=$_GET["id_contrato"];


$url_retorno="resumen_contrato.php?id_docente=$id_docente&id_contrato=$id_contrato";

db_query("DELETE FROM personal_pagos WHERE id_pago=$id_pago AND id_empleado=$id_docente AND id_contrato=$id_contrato") or die(db_error());

// Borra la información relacionada

header("Location: $url_retorno");

?>
