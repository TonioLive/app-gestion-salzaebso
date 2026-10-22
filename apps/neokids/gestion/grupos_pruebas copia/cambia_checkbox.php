<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_falta=$_GET["id_falta"];

if (strlen($id_falta)!=0) {

        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM servicios_consumidos_faltas WHERE id_falta=?', 's', array($id_falta));

		if (db_num_rows($busca_nota)!=0) {

        	$datos_nota=db_fetch_array($busca_nota);
        
        	$estado_actual=$datos_nota["confirmada"];

        	if ($estado_actual==0) {
                $estado_actual=1;
                echo("ok: Asistencia confirmada");
                } else {
                $estado_actual=0;
                echo("ok: Confirmación anulada");
                }

        	// Actualiza
        
        	$update_nota=db_query("UPDATE servicios_consumidos_faltas SET confirmada=$estado_actual WHERE id_falta=$id_falta");
			
			} else {
			
			echo("Error: No se encuentra la información solicitada.");
			
			}

        } else {
        
        echo("Error: No se encuentra la información solicitada.");
        
        }

?>