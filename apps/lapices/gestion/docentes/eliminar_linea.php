<?php 
require("../../configuracion/config.php");

$id_docente=$_GET["id"];
$fecha_linea=$_GET["dia"];

$dia_pago=date("d",strtotime($fecha_linea));
$mes_pago=date("m",strtotime($fecha_linea));
$ano_pago=date("Y",strtotime($fecha_linea));

$url_retorno="ver_pagas.php?fecha_pago_dia=$dia_pago&fecha_pago_mes=$mes_pago&fecha_pago_ano=$ano_pago";

db_query("DELETE FROM personal_pagos WHERE id_empleado=$id_docente AND fecha_pago='$fecha_linea'") or die(db_error());

// Borra la información relacionada

header("Location: $url_retorno");

?>
