<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_cumple=$_GET["id_cumple"];

if (strlen($id_cumple)!=0) {

        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM alumnos_cumples_confirmados WHERE id_cumple=?', 's', array($id_cumple));

		if (db_num_rows($busca_nota)!=0) {

        	$datos_nota=db_fetch_array($busca_nota);
        
        	$estado_actual=$datos_nota["confirmado"];

        	if ($estado_actual==0) {
                $estado_actual=1;
                echo("ok: Cumpleaños marcado celebrado");
                } else {
                $estado_actual=0;
                echo("ok: Celebración del cumpleaños anulada");
                }

        	// Actualiza
        
        	$update_nota=db_query("UPDATE alumnos_cumples_confirmados SET confirmado=$estado_actual WHERE id_cumple=$id_cumple");
			
			} else {
			
			echo("Error: No se encuentra la información solicitada.");
			
			}

        } else {
        
        echo("Error: No se encuentra la información solicitada.");
        
        }

?>