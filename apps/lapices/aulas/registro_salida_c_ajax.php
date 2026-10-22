<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$hora_salida_c=date("H:i:s");

db_query("UPDATE servicios_consumidos SET hora_salida_c='$hora_salida_c' WHERE id_servicio_consumido=$id_registro") or die(db_error());

?>