<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_usuario=$_POST["id_usuario"];
$id_curso_inscripcion=$_POST["id_curso_inscripcion"];

$id_centro_pre=$_POST["id_centro_pre"];
if (strlen($id_centro_pre)==0) {
        $id_centro_pre=$centro_actual;
        }

$edad_espera=$_POST["edad_espera"];

$usuario_fechains_dia=$_POST["usuario_fechains_dia"];
$usuario_fechains_mes=$_POST["usuario_fechains_mes"];
$usuario_fechains_ano=$_POST["usuario_fechains_ano"];

$usuario_fechades_dia=$_POST["usuario_fechades_dia"];
$usuario_fechades_mes=$_POST["usuario_fechades_mes"];
$usuario_fechades_ano=$_POST["usuario_fechades_ano"];

$observaciones=$_POST["observaciones"];

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

// Comprueba si ya está

$busca_lista=db_query("SELECT * FROM lista_espera WHERE id_usuario=$id_usuario AND id_curso=$id_curso_inscripcion AND edad_espera=$edad_espera");

if (db_num_rows($busca_lista)==0) {
        // Inserta en lista de espera

        $inserta_lista=db_query_params('INSERT INTO lista_espera (id_usuario,id_curso,edad_espera,fecha_alta,fecha_deseada,observaciones_lista_espera,id_centro) VALUES (?,?,?,?,?,?,?);', 'sssssss', array((string)($id_usuario), (string)($id_curso_inscripcion), (string)($edad_espera), (string)($fecha_inscripcion), (string)($fecha_deseada), (string)($observaciones), (string)($id_centro_pre)));

        $id_nuevo_usuario_lista=db_insert_id();

        // Cuestionario
        $busca_preguntas=db_query_params('SELECT * FROM lista_espera_cuestionarios WHERE desactivada=0 AND (id_curso=0 OR id_curso=?) ORDER BY id_tipo_punto', 's', array($id_curso_inscripcion));
        while($lista_preguntas=db_fetch_array($busca_preguntas)) {
                $respuesta_pregunta=$_POST["P".$lista_preguntas["id_tipo_punto"]];
                if (strlen($respuesta_pregunta)==0) {
                        $respuesta_pregunta=0;
                        }
                $inserta_respuesta=db_query_params('INSERT INTO lista_espera_respuestas (id_usu_lista,id_tipo_puntos,valor) VALUES (?,?,?)', 'sss', array((string)($id_nuevo_usuario_lista), (string)($lista_preguntas["id_tipo_punto"]), (string)($respuesta_pregunta))) or die(db_error());
                }
                }
                
header("Location: /lista_espera/lista_espera.php?edad_lista=$edad_espera&id_curso_lista=$id_curso_inscripcion");

?>
