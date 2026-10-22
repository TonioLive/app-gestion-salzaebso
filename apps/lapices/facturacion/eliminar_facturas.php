<?php  require("../configuracion/config.php"); ?>
<?php
// Datos
$array_facturas=$_GET["facturas"];

$facturas=explode(",",$array_facturas);

error_reporting(7);

for ($contador_facturas=0;$contador_facturas<count($facturas);$contador_facturas++) {

$id_factura=$facturas[$contador_facturas];

// Busca datos de la factura
db_query("DELETE FROM facturas_emitidas WHERE id_factura=$id_factura");
db_query("DELETE FROM detalles_factura WHERE id_factura=$id_factura");
db_query("DELETE FROM pagos_a_cuenta WHERE id_factura=$id_factura");


}

?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
