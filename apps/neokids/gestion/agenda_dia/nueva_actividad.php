<?php 
require("../../configuracion/config.php");

$id_aula=$_GET["id_aula"];
$dia=$_GET["dia"];
$tiempo_trabajo=$_GET["tiempo_trabajo"];


        // busca ultima actividad nota
        $orden_actual=0;
        $busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE fecha='".$dia."' AND tiempo_trabajo=$tiempo_trabajo AND id_aula=$id_aula ORDER BY orden_actividad DESC LIMIT 0,1");
        if (db_num_rows($busca_programacion)!=0) {
                $datos_programacion=db_fetch_array($busca_programacion);
                $orden_actual=$datos_programacion["orden_actividad"];
                }
        $orden_actual++;
                
        // Inserta nueva
        $inserta_prog=db_query_params('INSERT INTO agenda_programacion (id_aula,fecha,tiempo_trabajo,lugar,orden_actividad) VALUES (?,?,?,\'Clase\',?);', 'ssss', array((string)($id_aula), (string)($dia), (string)($tiempo_trabajo), (string)($orden_actual)));


header("Location: semana_programacion.php?id_aula=$id_aula&fecha_inicio=$dia&pagina_activa=$tiempo_trabajo");

?>
