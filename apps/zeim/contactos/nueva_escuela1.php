<?php 
require("../configuracion/config.php");

$id_competencia=$_POST["id_competencia"];

$nombre_escuela=$_POST["nombre_escuela"];
$coordenadas=$_POST["coordenadas"];
$calle=$_POST["calle"];
$poblacion=$_POST["poblacion"];
$municipal=$_POST["municipal"];
$cp=$_POST["cp"];

// Comprobamos si es nuevo
if ($id_competencia==0) {
                        $consulta_insert=array('INSERT INTO escuelas_competencia (nombre_escuela) VALUES (?)', 's', array((string)($nombre_escuela)));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_competencia=db_insert_id();
                        }

// Separamos latitud y longitud

$coordenadas_actuales=str_replace("(","",$coordenadas);
$coordenadas_actuales=str_replace(")","",$coordenadas_actuales);
$array_coordenadas=explode(",",$coordenadas_actuales);
$latitud_actual=$array_coordenadas[0];
$longitud_actual=$array_coordenadas[1];

                        
if ($num_errores==0) {
        // Introduce información
        db_query_params('UPDATE escuelas_competencia SET municipal=?, nombre_escuela=?, latitud=?, longitud=?,calle=?,poblacion=?,cp=? WHERE id_competencia=?', 'ssssssss', array((string)($municipal), (string)($nombre_escuela), (string)($latitud_actual), (string)($longitud_actual), (string)($calle), (string)($poblacion), (string)($cp), $id_competencia)) or die(db_error()."-2");
        
        }
        
header("location: listado_escuelas.php");

?>
