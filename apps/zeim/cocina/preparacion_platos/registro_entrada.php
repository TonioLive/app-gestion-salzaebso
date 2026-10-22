<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$turno=$_GET["turno"];
$id_usuario=$_GET["id_usuario"];

$fecha=date("Y-m-d");
$hora_entrada=date("H:i:s");


db_query_params('INSERT INTO asistencia_empleados (id_usuario,turno,fecha,hora_entrada,id_centro_asistencia) VALUES (?,?,?,?,?)', 'sssss', array((string)($id_usuario), (string)($turno), (string)($fecha), (string)($hora_entrada), (string)($id_tienda))) or dei(db_error());

db_close();

header("Location: mapa_mesas.php");

?>
