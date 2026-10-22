<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_escuela=$_GET["id_escuela"];
$id_producto=$_GET["id_producto"];
$id_mov=$_GET["id_mov"];

$borra1=db_query_params('DELETE FROM movimientos_material_escuelas WHERE id_mov=?', 's', array($id_mov));


header("Location: resumen_entradas_producto_escuela.php?id_escuela=$id_escuela&id_producto=$id_producto");

?>
