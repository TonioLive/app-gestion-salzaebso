<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$actualiza_similares=db_query("UPDATE detalles_factura SET concepto_desglosar=3 WHERE id_factura>20221 AND concepto like 'Horas extra%' OR concepto like 'Hora extra%' OR cod_facturacion like 'he_%' ") or die(db_error());
$actualiza_similares=db_query("UPDATE detalles_factura SET concepto_desglosar=3 WHERE id_factura>20221 AND concepto like 'Educacion%' OR concepto like 'Educación%'") or die(db_error());
$actualiza_similares=db_query("UPDATE detalles_factura SET concepto_desglosar=2 WHERE id_factura>20221 AND concepto like 'Comedor suelto%' OR cod_facturacion like 'co_%'") or die(db_error());
$actualiza_similares=db_query("UPDATE detalles_factura SET concepto_desglosar=1 WHERE id_factura>20221 AND concepto like 'Asistencia%' OR cod_facturacion like 'cod_e_%'") or die(db_error());


$busca_tarifa=db_query("SELECT * FROM tarifa") or die(db_error());

while($tarifa=db_fetch_array($busca_tarifa)) {

        $actualiza_similares=db_query("UPDATE detalles_factura SET concepto_desglosar=".$tarifa["desglosar_como"]." WHERE id_factura>20221 AND concepto like '".$tarifa["ARTICULO"]."%'") or die(db_error());

        }


?>
