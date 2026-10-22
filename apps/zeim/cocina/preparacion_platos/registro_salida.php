<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_GET["id_registro"];

$hora_salida=date("H:i:s");

db_query_params('UPDATE asistencia_empleados SET hora_salida=? WHERE id_asistencia=?', 'ss', array((string)($hora_salida), $id_registro)) or die(db_error());

header("Location: mapa_mesas.php");
?>
