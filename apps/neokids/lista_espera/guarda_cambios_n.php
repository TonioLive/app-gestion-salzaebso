<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_n=$_POST["id_n"];
$id_curso_inscripcion=$_POST["id_curso_inscripcion"];

$edad_espera=$_POST["edad_espera"];
$categoria=$_POST["categoria"];
$usuario_fechains_dia=$_POST["usuario_fechains_dia"];
$usuario_fechains_mes=$_POST["usuario_fechains_mes"];
$usuario_fechains_ano=$_POST["usuario_fechains_ano"];

$usuario_fechades_dia=$_POST["usuario_fechades_dia"];
$usuario_fechades_mes=$_POST["usuario_fechades_mes"];
$usuario_fechades_ano=$_POST["usuario_fechades_ano"];

$usuario_fechabaja_dia=$_POST["usuario_fechabaja_dia"];
$usuario_fechabaja_mes=$_POST["usuario_fechabaja_mes"];
$usuario_fechabaja_ano=$_POST["usuario_fechabaja_ano"];

$motivo_baja=$_POST["motivo_baja"];

$observaciones=$_POST["observaciones"];

$nivel_natacion=$_POST["nivel_natacion"];

// comprobamos los datos opcionales que necesiten ser comprobados
if ($usuario_fechains_dia!="--" and $usuario_fechains_mes!="--" and $usuario_fechains_ano!="----") {
        $fecha_inscripcion=$usuario_fechains_ano."/".$usuario_fechains_mes."/".$usuario_fechains_dia;
        if (!checkdate(intval($usuario_fechains_mes),intval($usuario_fechains_dia),intval($usuario_fechains_ano))) {
                        $fecha_inscripcion="";
                        }
                } else {
                        $fecha_inscripcion="";
                }
if ($usuario_fechades_dia!="--" and $usuario_fechades_mes!="--" and $usuario_fechades_ano!="----") {
        $fecha_deseada=$usuario_fechades_ano."/".$usuario_fechades_mes."/".$usuario_fechades_dia;
        if (!checkdate(intval($usuario_fechades_mes),intval($usuario_fechades_dia),intval($usuario_fechades_ano))) {
                        $fecha_deseada="";
                        }
                } else {
                        $fecha_deseada="";
                }
                
if ($usuario_fechabaja_dia!="--" and $usuario_fechabaja_mes!="--" and $usuario_fechabaja_ano!="----") {
        $fecha_baja=$usuario_fechabaja_ano."/".$usuario_fechabaja_mes."/".$usuario_fechabaja_dia;
        if (!checkdate(intval($usuario_fechabaja_mes),intval($usuario_fechabaja_dia),intval($usuario_fechabaja_ano))) {
                        $fecha_baja="";
                        }
                } else {
                        $fecha_baja="";
                }


        // actualiza datos en lista de espera

        $actualiza_lista=db_query_params('UPDATE lista_espera SET edad_espera=?, categoria=?, fecha_alta=?,fecha_deseada=?,fecha_baja=?,motivo_baja=?,observaciones_lista_espera=?, nivel_natacion=? WHERE id_usu_lista=?', 'sssssssss', array((string)($edad_espera), (string)($categoria), (string)($fecha_inscripcion), (string)($fecha_deseada), (string)($fecha_baja), (string)($motivo_baja), (string)($observaciones), (string)($nivel_natacion), $id_n));

		// Dias deseados
		
		
		$cadena_update="";
		
		for ($c_dia=1; $c_dia<=7; $c_dia=$c_dia+1) {
		
			$dia_actual=$_POST["dia_deseado_".$c_dia];
			
			if ($dia_actual==1) {
				$cadena_update.="dia_deseado_".$c_dia."=1, ";
				
				$hora_inicio_actual=$_POST["hora_i_".$c_dia].":".$_POST["min_i_".$c_dia];
				$cadena_update.="ini_horario_deseado_".$c_dia."='".$hora_inicio_actual."', ";
				
				$hora_fin_actual=$_POST["hora_f_".$c_dia].":".$_POST["min_f_".$c_dia];
				$cadena_update.="fin_horario_deseado_".$c_dia."='".$hora_fin_actual."', ";
				
				
				} else {
				$cadena_update.="dia_deseado_".$c_dia."=0, ";
				$cadena_update.="ini_horario_deseado_".$c_dia."='00:00:00', ";
				$cadena_update.="fin_horario_deseado_".$c_dia."='00:00:00', ";
				}
					
			}
		if ($cadena_update!="") {
			$cadena_update=substr($cadena_update,0,strlen($cadena_update)-2);

			$inserta_preferencias_dias=db_query("UPDATE lista_espera SET $cadena_update WHERE id_usu_lista=$id_n") or die(db_error());
			
			}


        // Cuestionario
        $busca_preguntas=db_query_params('SELECT * FROM lista_espera_cuestionarios WHERE desactivada=0 AND (id_curso=0 OR id_curso=?) ORDER BY id_tipo_punto', 's', array($id_curso_inscripcion));
        while($lista_preguntas=db_fetch_array($busca_preguntas)) {
                $respuesta_pregunta=$_POST["P".$lista_preguntas["id_tipo_punto"]];
                if (strlen($respuesta_pregunta)==0) {
                        $respuesta_pregunta=0;
                        }
                $inserta_respuesta=db_query_params('UPDATE lista_espera_respuestas SET valor=? WHERE id_usu_lista=? AND id_tipo_puntos=?', 'sss', array((string)($respuesta_pregunta), $id_n, $lista_preguntas["id_tipo_punto"])) or die(db_error());
                }
                
header("Location: /lista_espera/lista_espera.php?edad_lista=$edad_espera&id_curso_lista=$id_curso_inscripcion");

?>
