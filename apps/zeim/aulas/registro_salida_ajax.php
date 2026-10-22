<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];

$hora_salida=date("H:i:s");

db_query_params('UPDATE servicios_consumidos SET hora_salida=? WHERE id_servicio_consumido=?', 'ss', array((string)($hora_salida), $id_registro)) or dei(db_error());

?>