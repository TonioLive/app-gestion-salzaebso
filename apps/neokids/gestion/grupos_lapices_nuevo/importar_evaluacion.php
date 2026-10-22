<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];

$busca_datos_alumno=db_query_params('SELECT * FROM gestionguarderias.alumnos LEFT JOIN gestionguarderias.usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=?;', 's', array($id_alumno)) or die(db_error());

$alumnos=db_fetch_array($busca_datos_alumno);
$id_usuario_alumno=$alumnos["id_usuario"];

$id_aula=$alumnos["aula"];
$id_curso_aula=$alumnos["id_curso"];

// Busca evaluaciones anteriores
$busca_alumnos_usuario=db_query_params('SELECT *, agenda_programacion_evaluaciones_estado_alumnos.id_curso AS curso_evaluacion, gestionguarderias.alumnos.aula AS id_aula_alumno FROM gestionguarderias.alumnos, agenda_programacion_evaluaciones_estado_alumnos WHERE gestionguarderias.alumnos.id_alumno=agenda_programacion_evaluaciones_estado_alumnos.id_alumno AND gestionguarderias.alumnos.id_usuario=? AND gestionguarderias.alumnos.id_alumno<>? ORDER BY gestionguarderias.alumnos.fecha_matricula', 'ss', array($id_usuario_alumno, $id_alumno)) or die(db_error());

while($otras_evaluaciones=db_fetch_array($busca_alumnos_usuario)) {

		$id_curso_evaluacion=$otras_evaluaciones["id_curso"];
		$id_aula_evaluacion=$otras_evaluaciones["id_aula_alumno"];
		$id_alumno_evaluacion=$otras_evaluaciones["id_alumno"];
		
		$busca_evaluacion_guardada=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=? AND id_aula=? AND id_alumno=? AND nota<>0;', 'sss', array($id_curso_evaluacion, $id_aula_evaluacion, $id_alumno_evaluacion)) or die(db_error());
		
		while($objetivos_superados=db_fetch_array($busca_evaluacion_guardada)){
		
			// Comprueba valor actual
			$busca_nueva_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$id_alumno." AND id_objetivo=".$objetivos_superados["id_objetivo"].";") or die(db_error());
			if (db_num_rows($busca_nueva_evaluacion_guardada)!=0) {
				$valor_evaluacion=db_fetch_array($busca_nueva_evaluacion_guardada);
				if ($valor_evaluacion["nota"]==0) {
					// No está trabajado, copia el resultado
					$actualiza_valor=db_query("UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=".$objetivos_superados["nota"]." WHERE id_eval=".$valor_evaluacion["id_eval"]) or die(db_error());
					} else {
						if (($objetivos_superados["nota"]==1) and ($valor_evaluacion["nota"]==2)) {
							// La copiada está como superado, la anota
							$actualiza_valor=db_query("UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=".$objetivos_superados["nota"]." WHERE id_eval=".$valor_evaluacion["id_eval"]) or die(db_error());
							}										
					}
			
			}
		}
	}

header("Location: evaluar_alumno.php?id_alumno=".$id_alumno);
?>