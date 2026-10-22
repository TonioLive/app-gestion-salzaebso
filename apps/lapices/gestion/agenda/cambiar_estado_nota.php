<?php 
require("../../configuracion/config.php");

$id_nota=$_GET["id_nota"];

if (strlen($id_nota)!=0) {

        // busca datos nota
        $busca_nota=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE id=$id_nota");

        $datos_nota=db_fetch_array($busca_nota);
        
        $estado_actual=$datos_nota["realizada"];

        $id_aula=$datos_nota["id_aula"];
        $dia=strtotime($datos_nota["dia"]);
        $id_alumno=$datos_nota["id_alumno"];
        $id_area=$datos_nota["area"];

        if ($estado_actual==0) {
                $estado_actual=1;
                } else {
                $estado_actual=0;
                }

        // Actualiza
        
        $update_nota=db_query("UPDATE agenda_alumnos_seguimiento SET realizada=$estado_actual WHERE id=$id_nota");

        }

if ($id_alumno!=0) {
        header("Location: editar_notas.php?id_alumno=$id_alumno&dia=".$dia."&id_aula=$id_aula&area=$id_area");
        } else {
        header("Location: dia_aula.php?dia=$dia&id_aula=$id_aula&pagina_activa=".($id_area+1));
        }

?>
