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

db_query("UPDATE servicios_consumidos SET hora_salida_c='$hora_salida_c',almuerzo='$almuerzo',comida1='$comida1',comida2='$comida2',postre='$postre',merienda='$merienda',deposicion1='$deposicion1',deposicion2='$deposicion2',deposicion3='$deposicion3',siesta='$siesta' WHERE id_servicio_consumido=$id_registro") or die(db_error());

?>
<script language="Javascript">
window.close();
// self.opener.history.go(0);
</script>
