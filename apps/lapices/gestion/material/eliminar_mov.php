<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$id_producto=$_GET["id_producto"];
$id_mov=$_GET["id_mov"];

$borra1=db_query("DELETE FROM movimientos_material WHERE id_mov=$id_mov");


header("Location: resumen_entradas_producto.php?id_producto=$id_producto");

?>
