<?php 
require("../../configuracion/config.php");

$id_docente=$_POST["id_docente"];
$id_cambio=$_POST["id_cambio"];


$url_retorno="horario_docente.php?id_docente=$id_docente";

if ($id_cambio==0) {
        $intro_nuevo_cambio=db_query("INSERT INTO personal_historial (id_empleado) VALUES ($id_docente);");
        $id_cambio=db_insert_id();
        }


                $id_aula_cambio=$_POST["id_aula"];

                // Busca escuela
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula_cambio");
                $datos_aula=db_fetch_array($busca_datos_aula);
                $id_centro_cambio=$datos_aula["id_centro"];


                $observaciones=$_POST["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=addslashes($observaciones);
                        }

                        $fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                }
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                }

                if ($fecha_inicio_dia!="--" and $fecha_inicio_mes!="--" and $fecha_inicio_ano!="----") {
                        $fecha_inicio=$fecha_inicio_ano."/".$fecha_inicio_mes."/".$fecha_inicio_dia;
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio="";
                                }
                        } else {
                        $fecha_inicio="";
                        }
                if ($fecha_fin_dia!="--" and $fecha_fin_mes!="--" and $fecha_fin_ano!="----") {
                        $fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin="";
                                }
                        } else {
                        $fecha_fin="";
                        }
                        
                        // Guarda cambios
                        db_query("UPDATE personal_historial SET fecha_inicio='$fecha_inicio', fecha_fin='$fecha_fin', id_escuela='$id_centro_cambio', id_aula='$id_aula_cambio', observaciones='$observaciones' WHERE id=$id_cambio") or die(db_error());

header("Location: $url_retorno");

?>
