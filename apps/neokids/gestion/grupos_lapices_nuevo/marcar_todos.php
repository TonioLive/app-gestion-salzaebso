<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];

$nivel=$_GET["nivel"];

$resultado=$_GET["resultado"];

$busca_datos_alumno=db_query_params('SELECT * FROM gestionguarderias.alumnos LEFT JOIN gestionguarderias.usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=?;', 's', array($id_alumno)) or die(db_error());

$alumnos=db_fetch_array($busca_datos_alumno);
$id_usuario_alumno=$alumnos["id_usuario"];

$id_aula=$alumnos["aula"];
$id_curso_aula=$alumnos["id_curso"];

	// Fecha de incorporación
	$fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
	if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
	$fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
	if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
	$edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));
	if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
	if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }
$lista_grupos_alumno="";
        
$busca_alumnos_grupo=db_query_params('SELECT * FROM gestionguarderias.usuarios,aulas_alumnos_lapices_grupos,aulas_grupos,gestionguarderias.alumnos WHERE gestionguarderias.usuarios.id_usuario=gestionguarderias.alumnos.id_usuario AND aulas_alumnos_lapices_grupos.id_alumno=gestionguarderias.alumnos.id_alumno AND aulas_alumnos_lapices_grupos.id_grupo=aulas_grupos.id_grupo AND gestionguarderias.alumnos.id_alumno=?', 's', array($id_alumno)) or die(db_error());

while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
	}
                                
if (strlen($lista_grupos_alumno)!=0) {
	$lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
	}         


// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=$id_alumno AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
	$datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $estado_evaluacion_guardada1=$datos_estado_evaluacion_guardada["estado_t1"];
    $estado_evaluacion_guardada2=$datos_estado_evaluacion_guardada["estado_t2"];
    $estado_evaluacion_guardada3=$datos_estado_evaluacion_guardada["estado_t3"];
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
    
    $busca_objetivos=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g<>0 AND nivel=".$nivel);
    while($lista_objetivos=db_fetch_array($busca_objetivos)) {
	    $busca_evaluacion_guardada=db_query("UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=$resultado WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$id_alumno." AND id_objetivo=".$lista_objetivos["id_objetivo"].";") or die(db_error());
	    }
    
    } 


header("Location: evaluar_alumno.php?id_alumno=".$id_alumno);


?>
