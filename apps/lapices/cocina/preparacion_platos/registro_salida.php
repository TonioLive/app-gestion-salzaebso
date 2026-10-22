<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_GET["id_registro"];

$hora_salida=date("H:i:s");

db_query("UPDATE asistencia_empleados SET hora_salida='$hora_salida' WHERE id_asistencia=$id_registro") or die(db_error());

header("Location: mapa_mesas.php");
?>
