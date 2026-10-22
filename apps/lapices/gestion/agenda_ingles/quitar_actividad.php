<?php 
require("../../configuracion/config.php");

$id_aula=$_GET["id_aula"];
$dia=$_GET["dia"];
$tiempo_trabajo=$_GET["tiempo_trabajo"];


        // busca ultima actividad nota
        $busca_programacion=db_query("SELECT * FROM agenda_programacion_ingles WHERE fecha='".$dia."' AND tiempo_trabajo=$tiempo_trabajo AND id_aula=$id_aula ORDER BY orden_actividad DESC LIMIT 0,1");
        if (db_num_rows($busca_programacion)!=0) {
                $datos_programacion=db_fetch_array($busca_programacion);
                $orden_actual=$datos_programacion["orden_actividad"];
                $id_actual=$datos_programacion["id_prog"];
                
                if ($orden_actual>1) {
                        // Borra ultima
                        $borra_prog=db_query("DELETE FROM agenda_programacion_ingles WHERE id_prog=$id_actual;");
                        }
                }

// Inicio semana
$dia_seleccionado=date("w",strtotime($dia));
if ($dia_seleccionado==0) {
	$dia_inicio=date("Y-m-d",strtotime($dia)-(86400*7));
	} else {
	$dia_inicio=date("Y-m-d",strtotime($dia)-(86400*($dia_seleccionado-1)));		
	}

header("Location: semana_programacion.php?id_aula=$id_aula&fecha_inicio=$dia_inicio&pagina_activa=$tiempo_trabajo");

?>
