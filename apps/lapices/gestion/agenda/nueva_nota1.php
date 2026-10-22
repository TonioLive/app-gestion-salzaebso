<?php 
require("../../configuracion/config.php");

$lista_ventanas=array(1,3,4,5);

$id_aula=$_POST["id_aula"];
$dia=$_POST["dia"];
$id_alumno=$_POST["id_alumno"];
$id_area=$_POST["area"];

$id_nota=$_POST["id_nota"];

$hora=$_POST["hora"];
$min=$_POST["min"];

$hora_nota=$hora.":".$min.":00";
$nota=$_POST["nota"];
$avisar=$_POST["avisar"];
$tipo_nota=$_POST["tipo_nota"];


                // Comprobamos si es nuevo
                if ($id_nota==0) {
                        $consulta_insert="INSERT INTO agenda_alumnos_seguimiento (id_aula,dia,id_alumno,area,introducida_por) VALUES ('$id_aula','".date("Y-m-d",$dia)."','$id_alumno','$id_area','$idUsuario')";
                        db_query($consulta_insert) or die(db_error());
                        $id_nota=db_insert_id();
                        }

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query("UPDATE agenda_alumnos_seguimiento SET hora='$hora_nota', nota='".db_real_escape_string($nota)."', avisar='$avisar',tipo_nota='$tipo_nota' WHERE id=".$id_nota) or die(db_error()."-2");

        }

if ($id_alumno!=0) {
        header("Location: editar_notas.php?id_alumno=$id_alumno&dia=".$dia."&id_aula=$id_aula&area=$id_area");
        } else {

        header("Location: dia_aula.php?dia=$dia&id_aula=$id_aula&pagina_activa=".$lista_ventanas[$id_area]);
        }
?>
