<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_lista=$_POST["id_lista"];
$observaciones=$_POST["observaciones"];

if (strlen($observaciones)!=0) {
        $observaciones=urlencode($observaciones);
        }

$actualizar_lista=db_query("UPDATE menu_lista_compra SET observaciones='$observaciones' WHERE id_lista=$id_lista") or die(db_error()." - 1");

$array_id_alimento=$_POST["id_alimento"];
$array_cantidad_alimento=$_POST["cantidad_redondeada"];
$array_id_lista_alimento=$_POST["id_lista_alimento"];

$array_stock_articulo=$_POST["stock_articulo"];
$array_cantidad_servida_uds=$_POST["cantidad_servida_uds"];
$array_stock_resultante_articulo=$_POST["stock_resultante_articulo"];

for($c_array=0;$c_array<count($array_id_alimento);$c_array++) {

        if (strlen($array_cantidad_alimento[$c_array])!=0) {
                $cantidad_alimento=number_format($array_cantidad_alimento[$c_array],2,".","");
                } else {
                $cantidad_alimento=0;
                }
        $actualiza_fila_lista=db_query("UPDATE menu_productos_lista SET cantidad_redondeada='$cantidad_alimento' WHERE id_producto=".$array_id_lista_alimento[$c_array].";") or die(db_error()." - 2");

        }

$array_id_articulo_tarifa=$_POST["id_articulo_tarifa"];
$array_cantidad_servida=$_POST["cantidad_servida"];

for($c_array=0;$c_array<count($array_id_articulo_tarifa);$c_array++) {

        if (strlen($array_cantidad_servida[$c_array])!=0) {
                $cantidad_alimento=number_format($array_cantidad_servida[$c_array],2,".","");
                } else {
                $cantidad_alimento=0;
                }
        if (strlen($array_stock_articulo[$c_array])!=0) {
                $stock_articulo=number_format($array_stock_articulo[$c_array],2,".","");
                } else {
                $stock_articulo=0;
                }
        if (strlen($array_cantidad_servida_uds[$c_array])!=0) {
                $cantidad_servida_uds=number_format($array_cantidad_servida_uds[$c_array],2,".","");
                } else {
                $cantidad_servida_uds=0;
                }
        if (strlen($array_stock_resultante_articulo[$c_array])!=0) {
                $stock_resultante_articulo=number_format($array_stock_resultante_articulo[$c_array],2,".","");
                } else {
                $stock_resultante_articulo=0;
                }
        // Comprueba si ya está
        $busca_cantidades_servidas=db_query("SELECT * FROM menu_productos_proveedor WHERE id_lista=$id_lista AND id_producto_tarifa=".$array_id_articulo_tarifa[$c_array]) or die(db_error()." - 3");
        if (db_num_rows($busca_cantidades_servidas)!=0) {
                        // Actualiza vieja
                        $cantidades_servidas=db_fetch_array($busca_cantidades_servidas);
                        $id_prod_servido=$cantidades_servidas["id_producto_lista"];
                        $actualiza_fila_lista=db_query("UPDATE menu_productos_proveedor SET cantidad='$cantidad_alimento', stock='$stock_articulo', cantidad_resultante='$stock_resultante_articulo', unidades='$cantidad_servida_uds' WHERE id_producto_lista=".$id_prod_servido.";") or die(db_error()." - 4");
                        } else {
                        // Inserta nueva
                        $introduce_fila_lista=db_query("INSERT INTO menu_productos_proveedor (id_producto_tarifa,id_lista,cantidad,stock,cantidad_resultante,unidades) VALUES ('".$array_id_articulo_tarifa[$c_array]."','$id_lista','$cantidad_alimento','$stock_articulo','$stock_resultante_articulo','$cantidad_servida_uds');") or die(db_error()." - 5");
                        }

        }

header("Location: ver_lista_compra.php?id_lista=$id_lista");

?>
