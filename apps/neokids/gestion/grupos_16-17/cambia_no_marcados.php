<?php 
require("../../configuracion/config.php");

$id_grupo=$_GET["id_grupo"];
$id_usuario=$_GET["id_usuario"];
$id_subgrupo=$_GET["id_subgrupo"];
$id_lista=$_GET["id_lista"];
$nombre_mostrar=$_GET["nombre_mostrar"];

// Busca estado actual
$busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id=?', 's', array($id_lista));
$datos_en_lista=db_fetch_array($busca_en_lista);

$estado_actual=$datos_en_lista["no_mostrado"];
if ($estado_actual==1) {
        $nuevo_estado=0;
        } else {
        $nuevo_estado=1;
        }
        
// Cambia estado
db_query("UPDATE lista_espera_orden SET no_mostrado=$nuevo_estado WHERE id=$id_lista");

header("Location: lista_espera.php?id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");
?>
