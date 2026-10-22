<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_subgrupo=$_GET["id_subgrupo"];

if (strlen($id_subgrupo)!=0) {

        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo_subgrupo=?', 's', array($id_subgrupo));

		if (db_num_rows($busca_nota)!=0) {

        	$datos_nota=db_fetch_array($busca_nota);
        
        	$estado_actual=$datos_nota["bloqueado"];

        	if ($estado_actual==0) {
                $estado_actual=1;
                echo("ok: Grupo bloquedado, no se pueden inscribir más alumnos hasta que sea desbloqueado");
                } else {
                $estado_actual=0;
                echo("ok: Grupo desbloquedado, a partir de ahora ya se pueden inscribir más alumnos");
                }

        	// Actualiza
        
        	$update_nota=db_query("UPDATE aulas_grupos_subgrupos SET bloqueado=$estado_actual, usuario_bloqueo='$idUsuario' WHERE id_grupo_subgrupo=$id_subgrupo");
			
			} else {
			
			echo("Error: No se encuentra la información solicitada.");
			
			}

        } else {
        
        echo("Error: No se encuentra la información solicitada.");
        
        }

?>