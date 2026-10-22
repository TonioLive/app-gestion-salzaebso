<?php 
require("../../configuracion/config.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$dia=$_GET["dia"];
$nivel_actual=$_GET["nivel"];

$externo=$_GET["externo"];

$nuevo_nivel=$nivel_actual+1;

if ($nuevo_nivel>4) {
        $nuevo_nivel=4;
        }

$bbdd_usuarios="";
if ($externo==1) {
        $bbdd_usuarios="gestionguarderias.";
        }
        
$actualiza_nivel=db_query("UPDATE ".$bbdd_usuarios."alumnos SET nivel_actual=$nuevo_nivel WHERE id_alumno=$id_alumno");

header("Location: dia_aula.php?dia=$dia&id_grupo=$id_grupo&dia=$dia");

        
?>
