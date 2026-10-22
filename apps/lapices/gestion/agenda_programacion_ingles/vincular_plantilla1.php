<?php 
require("../../configuracion/config.php");

$id_plantilla=$_GET["id_plantilla"];
$id_aula=$_GET["id_aula"];
$fecha_inicio=$_GET["fecha_inicio"];

db_query("INSERT INTO agenda_programacion_aulas_vinculadas (id_plantilla,id_aula,fecha_inicio) VALUES ('$id_plantilla','$id_aula','".date("Y-m-d",$fecha_inicio)."');");


header("Location: vincular_plantilla.php?fecha=".$fecha_inicio."&id_plantilla=".$id_plantilla."#".$id_grupo."_".$id_subgrupo);        
?>
