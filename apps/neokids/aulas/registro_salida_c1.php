<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$hora=$_POST["hora"];

$almuerzo=$_POST["almuerzo"];
$comida1=$_POST["comida1"];
$comida2=$_POST["comida2"];
$postre=$_POST["postre"];
$merienda=$_POST["merienda"];
$deposicion1=$_POST["deposicion1"];
$deposicion2=$_POST["deposicion2"];
$deposicion3=$_POST["deposicion3"];
$siesta=$_POST["siesta"];

$hora_salida_c=date("H:i:s",$hora);

db_query_params('UPDATE servicios_consumidos SET hora_salida_c=?,almuerzo=?,comida1=?,comida2=?,postre=?,merienda=?,deposicion1=?,deposicion2=?,deposicion3=?,siesta=? WHERE id_servicio_consumido=?', 'sssssssssss', array((string)($hora_salida_c), (string)($almuerzo), (string)($comida1), (string)($comida2), (string)($postre), (string)($merienda), (string)($deposicion1), (string)($deposicion2), (string)($deposicion3), (string)($siesta), $id_registro)) or die(db_error());

?>
<script language="Javascript">
window.close();
// self.opener.history.go(0);
</script>
