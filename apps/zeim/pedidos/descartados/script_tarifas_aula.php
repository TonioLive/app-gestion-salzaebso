var tarifa=Array();
var familias=Array();

var familia_actual="";

<?php 

// Generar script con array de tarifas
$orden_array=0;

$consulta_tarifas = db_query("SELECT * FROM tarifa,tipos_iva_servicios WHERE tarifa.TIPO_SERVICIO=3 AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva ORDER BY ARTICULO") or die (db_error());

while($datos_tarifas = db_fetch_array($consulta_tarifas)){
?>
tarifa[<?=$orden_array?>]=Array(<?=$datos_tarifas["ID"]?>,"<?=str_replace(chr(34),"'",$datos_tarifas["CATEGORIA"]);?>","<?=$datos_tarifas["ID"]?>","<?=$datos_tarifas["PRECIO_VENTA"]?>","<?=str_replace(chr(34),"'",$datos_tarifas["ARTICULO"]);?>","<?=$datos_tarifas["UNIDAD"];?>","<?=$datos_tarifas["porcentaje_iva"];?>");
<?php 
$orden_array=$orden_array+1;
}

$orden_array=0;

$consulta_familias = db_query("SELECT * FROM tipos_iva_servicios ORDER BY categoria") or die (db_error());
while($datos_familias = db_fetch_array($consulta_familias)){
?>
familias[<?=$orden_array?>]=Array(<?=$datos_familias["id_tipo_iva"]?>,"<?=str_replace(chr(34),"'",$datos_familias["categoria"]);?>","<?=$datos_familias["porcentaje_iva"];?>");
<?php 
$orden_array=$orden_array+1;
}
?>
