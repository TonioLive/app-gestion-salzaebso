<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];
$copia=$_GET["copia"];
$mostrar_domicilio=$_GET["dom"];
$es_albaran=$_GET["alb"];

header("Location: /pdf2/facturacion/factura.php?id_factura=$id_factura&copia=$copia&mostrar_domicilio=$mostrar_domicilio&es_albaran=$es_albaran");

?>