<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_n=$_POST["id_n"];
$id_curso_inscripcion=$_POST["id_curso_inscripcion"];

$edad_espera=$_POST["edad_espera"];

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

        $actualiza_lista=db_query("UPDATE lista_espera SET edad_espera='$edad_espera',fecha_alta='$fecha_inscripcion',fecha_deseada='$fecha_deseada',fecha_baja='$fecha_baja',motivo_baja='$motivo_baja',observaciones_lista_espera='$observaciones' WHERE id_usu_lista=$id_n");

        // Cuestionario
        $busca_preguntas=db_query("SELECT * FROM lista_espera_cuestionarios WHERE desactivada=0 AND (id_curso=0 OR id_curso=$id_curso_inscripcion) ORDER BY id_tipo_punto");
        while($lista_preguntas=db_fetch_array($busca_preguntas)) {
                $respuesta_pregunta=$_POST["P".$lista_preguntas["id_tipo_punto"]];
                if (strlen($respuesta_pregunta)==0) {
                        $respuesta_pregunta=0;
                        }
                $inserta_respuesta=db_query("UPDATE lista_espera_respuestas SET valor='$respuesta_pregunta' WHERE id_usu_lista=$id_n AND id_tipo_puntos=".$lista_preguntas["id_tipo_punto"]) or die(db_error());
                }
                
header("Location: /lista_espera/lista_espera.php?edad_lista=$edad_espera&id_curso_lista=$id_curso_inscripcion");

?>
