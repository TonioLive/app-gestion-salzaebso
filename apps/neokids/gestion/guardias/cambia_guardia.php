<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$valor=$_GET["valor"];
$id_guardia=$_GET["id_guardia"];

if (strlen($id_guardia)!=0) {

        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM personal_guardias WHERE id_guardia=?', 's', array($id_guardia));

		if (db_num_rows($busca_nota)!=0) {

        	// Actualiza
        
        	$update_nota=db_query("UPDATE personal_guardias SET id_usuario=$valor WHERE id_guardia=$id_guardia");
			
			echo("ok");
			
			} else {
			
			echo("Error: No se encuentra la información solicitada.");
			
			}

        } else {
        
        echo("Error: No se encuentra la información solicitada.");
        
        }

?>