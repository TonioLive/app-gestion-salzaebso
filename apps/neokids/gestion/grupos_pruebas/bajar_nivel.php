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
$nuevo_orden=$orden_actual+1;

// Busca sustituto
$busca_sustituto=db_query("SELECT * FROM lista_espera_orden WHERE id_grupo=$id_grupo AND orden=$nuevo_orden");
$datos_sustituto=db_fetch_array($busca_sustituto);

$id_lista_sustituto=$datos_sustituto["id"];

// Intercambia usuarios
db_query("UPDATE lista_espera_orden SET orden=$nuevo_orden WHERE id=$id_lista");
db_query("UPDATE lista_espera_orden SET orden=$orden_actual WHERE id=$id_lista_sustituto");

header("Location: lista_espera.php?id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");        
?>
