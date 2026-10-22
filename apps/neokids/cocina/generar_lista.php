<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");


$fecha_inicio=$_POST["fecha_inicio"];
$fecha_fin=$_POST["fecha_fin"];

$crear_lista=db_query_params('INSERT INTO menu_lista_compra (fecha_ini,fecha_fin) VALUES (?,?)', 'ss', array((string)(date("Y-m-d", $fecha_inicio)), (string)(date("Y-m-d", $fecha_fin)))) or die(db_error());

$id_lista_nueva=db_insert_id();

$array_id_alimento=$_POST["id_alimento"];
$array_cantidad_alimento=$_POST["cantidad_alimento"];

for($c_array=0;$c_array<count($array_id_alimento);$c_array++) {

        $inserta_fila_lista=db_query_params('INSERT INTO menu_productos_lista (id_lista,id_alimento,cantidad_recomendada,cantidad_redondeada) VALUES (?,?,?,?);', 'ssss', array((string)($id_lista_nueva), (string)($array_id_alimento[$c_array]), (string)($array_cantidad_alimento[$c_array]), (string)($array_cantidad_alimento[$c_array]))) or die(db_error());

        }

header("Location: ver_lista_compra.php?id_lista=$id_lista_nueva");

?>
