<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$hora=$_POST["hora"];

$hora_salida=date("H:i:s",$hora);

db_query("UPDATE servicios_consumidos_asistencia SET hora_salida='$hora_salida' WHERE id_servicio_consumido=$id_registro") or dei(db_error());

?>
<script language="Javascript">
window.close();
self.opener.history.go(0);
</script>
