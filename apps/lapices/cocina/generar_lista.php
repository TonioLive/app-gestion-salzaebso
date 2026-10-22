<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$tipo_lista=$_POST["tipo_lista"];
$fecha_inicio=$_POST["fecha_inicio"];
$fecha_fin=$_POST["fecha_fin"];

$crear_lista=db_query("INSERT INTO menu_lista_compra (fecha_ini,fecha_fin,tipo_lista) VALUES ('".date("Y-m-d",$fecha_inicio)."','".date("Y-m-d",$fecha_fin)."','$tipo_lista')") or die(db_error());

$id_lista_nueva=db_insert_id();

$array_id_alimento=$_POST["id_alimento"];
$array_cantidad_alimento=$_POST["cantidad_alimento"];

for($c_array=0;$c_array<count($array_id_alimento);$c_array++) {

        $inserta_fila_lista=db_query("INSERT INTO menu_productos_lista (id_lista,id_alimento,cantidad_recomendada,cantidad_redondeada) VALUES ('$id_lista_nueva','".$array_id_alimento[$c_array]."','".$array_cantidad_alimento[$c_array]."','".$array_cantidad_alimento[$c_array]."');") or die(db_error());

        }

header("Location: ver_lista_compra.php?id_lista=$id_lista_nueva");

?>
