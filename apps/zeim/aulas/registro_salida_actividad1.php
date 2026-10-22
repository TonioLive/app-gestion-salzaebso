<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$hora=$_POST["hora"];

$hora_salida=date("H:i:s",$hora);

db_query_params('UPDATE servicios_consumidos_asistencia SET hora_salida=? WHERE id_servicio_consumido=?', 'ss', array((string)($hora_salida), $id_registro)) or dei(db_error());

?>
<script language="Javascript">
window.close();
self.opener.history.go(0);
</script>
