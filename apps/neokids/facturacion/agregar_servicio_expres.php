<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_servicio_nuevo=$_GET["id_servicio"];
$id_nuevo_alumno=$_GET["id_alumno"];
$cantidad=$_GET["unidades"];
$dto=$_GET["dto"];
$periodo=$_GET["periodo"];

// Inserta servicio
$inserta_servicio=db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento,periodo) VALUES (?,?,?,\'2\',?,?,?)', 'ssssss', array((string)($id_nuevo_alumno), (string)($id_servicio_nuevo), (string)(date("Y-m-d")), (string)($cantidad), (string)($dto), (string)($periodo)))or die(db_error()."-c");

header("Location: editar_servicios.php?i=$id_nuevo_alumno");
?>