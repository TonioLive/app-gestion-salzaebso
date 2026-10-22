<?php 
require("../../configuracion/config.php");

$id_pago=$_GET["id_pago"];
$id_docente=$_GET["id_docente"];
$id_contrato=$_GET["id_contrato"];


$url_retorno="resumen_contrato.php?id_docente=$id_docente&id_contrato=$id_contrato";

db_query_params('DELETE FROM personal_pagos WHERE id_pago=? AND id_empleado=? AND id_contrato=?', 'sss', array($id_pago, $id_docente, $id_contrato)) or die(db_error());

// Borra la información relacionada

header("Location: $url_retorno");

?>
