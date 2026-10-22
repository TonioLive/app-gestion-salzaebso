<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];

$hora_e=$_POST["hora_e"];
$min_e=$_POST["min_e"];
$seg_e=$_POST["seg_e"];
$hora_s=$_POST["hora_s"];
$min_s=$_POST["min_s"];
$seg_s=$_POST["seg_s"];


db_query_params('UPDATE servicios_consumidos SET hora_entrada=?,hora_salida=? WHERE id_servicio_consumido=?', 'sss', array((string)($hora_e) . ':' . (string)($min_e) . ':' . (string)($seg_e), (string)($hora_s) . ':' . (string)($min_s) . ':' . (string)($seg_s), $id_registro)) or die(db_error());
        
?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>