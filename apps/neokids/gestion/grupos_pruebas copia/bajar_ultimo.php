<?php 
require("../../configuracion/config.php");

$id_grupo=$_GET["id_grupo"];
$id_usuario=$_GET["id_usuario"];
$id_subgrupo=$_GET["id_subgrupo"];
$id_lista=$_GET["id_lista"];
$nombre_mostrar=$_GET["nombre_mostrar"];

// Busca orden actual
$busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id=?', 's', array($id_lista));
$datos_en_lista=db_fetch_array($busca_en_lista);
$orden_actual=$datos_en_lista["orden"];

// Busca ultimo en la lista
$busca_ultimo_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=? ORDER BY orden DESC', 's', array($id_grupo));
$datos_ultimo_lista=db_fetch_array($busca_ultimo_lista);
$nuevo_orden=$datos_ultimo_lista["orden"];

// Cambia orden de los otros
$cambia_ordenes=db_query("UPDATE lista_espera_orden SET orden=orden-1 WHERE id_grupo=$id_grupo AND orden>$orden_actual ORDER BY orden");

// Intercambia usuarios
db_query("UPDATE lista_espera_orden SET orden=$nuevo_orden WHERE id=$id_lista");

header("Location: lista_espera.php?id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");        
?>
