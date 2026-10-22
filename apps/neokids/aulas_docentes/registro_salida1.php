<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$hora=$_POST["hora"];
$incidencia=$_POST["incidencia"];
$observaciones=$_POST["observaciones"];

$hora_salida=date("H:i:s",$hora);

db_query_params('UPDATE servicios_consumidos SET hora_salida=?, observaciones=?,incidencia=? WHERE id_servicio_consumido=?', 'ssss', array((string)($hora_salida), (string)($observaciones), (string)($incidencia), $id_registro)) or dei(db_error());

?>
<script language="Javascript">
self.close();
// self.opener.history.go(0);
</script>
