<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_docente.php");

// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)==0) {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));
                }

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$id_mensaje=$_GET["id_mensaje"];
$id_alumno=$_GET["id_alumno"];

$busca_mensajes=db_query_params('SELECT * FROM agenda_notas_padres WHERE id_alumno=? AND id_nota_padre=?', 'ss', array($id_alumno, $id_mensaje));              
    
if (db_num_rows($busca_mensajes)!=0) { 
		
		$mensajes=db_fetch_array($busca_mensajes);    
    	
    	if (($mensajes["medicacion_agenda"]==0) and ($mensajes["tipo_nota"]==1)) {
    	
    		$fecha_inicio_medicacion=strtotime($mensajes["fecha_medicacion"]);
    		$fecha_inicio_medicacion=$fecha_inicio_medicacion+3660;
    		$fecha_fin_medicacion=strtotime($mensajes["fecha_fin_medicacion"]);
    		$fecha_fin_medicacion=$fecha_fin_medicacion+3660;
    		
    		// Busca inscripcion del alumno en el curso actual
    		$busca_inscripcion=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=? AND id_centro=?', 'sss', array($id_alumno, $id_curso_actual, $centro_actual));
    		if (db_num_rows($busca_inscripcion)!=0) {
    			$datos_inscripcion=db_fetch_array($busca_inscripcion);
    			$id_alumno_aula=$datos_inscripcion["id_alumno"];
    			$id_aula=$datos_inscripcion["aula"];
    			} else {
    			$id_alumno_aula=0;
    			$id_aula=0;
    			}
    		
    		while($fecha_inicio_medicacion<=$fecha_fin_medicacion) {
    		$fecha_medicacion=date("Y-m-d",$fecha_inicio_medicacion);
    		// Comprueba si es lectivo
			$busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND estado=1 AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $fecha_inicio_medicacion))));
        if (db_num_rows($busca_dia_en_calendario)==0) {
    		// Introduce en agenda
    		$consulta_insert="INSERT INTO agenda_alumnos_seguimiento (area, id_alumno, dia, id_aula, hora, nota, tipo_nota, avisar, introducida_por) VALUES (1, $id_alumno_aula, '$fecha_medicacion', $id_aula, '".$mensajes["hora_medicacion"]."', 'Administrar ".$mensajes["cantidad"]." de ".$mensajes["medicamento"]."<br><br>".$mensajes["texto_nota"]."', 1, 15, $idUsuario);";
    		
    		$inserta_nota=db_query($consulta_insert) or die(db_error());
					}
				// Incrementa un día
				$fecha_inicio_medicacion=$fecha_inicio_medicacion+86400;
    			}
    		// Marca como insertada
    		$actualiza_tabla=db_query_params('UPDATE agenda_notas_padres SET medicacion_agenda=1 WHERE id_alumno=? AND id_nota_padre=?', 'ss', array($id_alumno, $id_mensaje));
    		}
    
    
    	}

header("Location: ver_mensaje.php?id_alumno=$id_alumno&id_mensaje=$id_mensaje");

?>