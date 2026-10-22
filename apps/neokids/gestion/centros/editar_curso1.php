<?php  require("../../configuracion/config.php"); ?>
<?php 

$id_aula=$_POST["id_aula"];
$id_curso=$_POST["id_curso"];
$o=$_POST["o"];
if (strlen($o)==0) {
        $url_retorno="editar_aula.php?id_aula=".$id_aula;
        } else {
        $url_retorno=$o;
        }

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                        $nombre_curso=$_POST["nombre_curso"];
                        
                        $fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        $fecha_inicio=$fecha_inicio_ano."/".$fecha_inicio_mes."/".$fecha_inicio_dia;
                        
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        $fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;

                        $fecha_limite_dia=$_POST["fecha_limite_dia"];
                        $fecha_limite_mes=$_POST["fecha_limite_mes"];
                        $fecha_limite_ano=$_POST["fecha_limite_ano"];
                        $fecha_limite=$fecha_limite_ano."/".$fecha_limite_mes."/".$fecha_limite_dia;

                        $estado=$_POST["estado"];

                        $comprobar_inscripcion=$_POST["comprobar_inscripcion"];
                        $comprobar_cuotas=$_POST["comprobar_cuotas"];
                        
                        $permitir_tiempo_gracia=$_POST["permitir_tiempo_gracia"];
                        $num_dias_asisten_externos=$_POST["num_dias_asisten_externos"];
                        
                        if (strlen($id_curso)!=0) {
                                        // Guarda cambios
                                        db_query_params('UPDATE cursos_academicos SET num_dias_asisten_externos=?, permitir_tiempo_gracia=?, comprobar_inscripcion=?, comprobar_cuotas=?,nombre=?, fecha_inicio=?, fecha_fin=?, estado=?, fecha_limite_presentacion=? WHERE id_curso=?;', 'ssssssssss', array((string)($num_dias_asisten_externos), (string)($permitir_tiempo_gracia), (string)($comprobar_inscripcion), (string)($comprobar_cuotas), (string)($nombre_curso), (string)($fecha_inicio), (string)($fecha_fin), (string)($estado), (string)($fecha_limite), $id_curso)) or die(db_error());
                                } else {
                                        // Introduce nuevo curso
                                        db_query_params('INSERT INTO cursos_academicos (nombre, fecha_inicio, fecha_fin, estado, comprobar_inscripcion, comprobar_cuotas,fecha_limite_presentacion,permitir_tiempo_gracia,num_dias_asisten_externos) VALUES (?, ?, ?, ?, ?, ?,?,?,?);', 'sssssssss', array((string)($nombre_curso), (string)($fecha_inicio), (string)($fecha_fin), (string)($estado), (string)($comprobar_inscripcion), (string)($comprobar_cuotas), (string)($fecha_limite), (string)($permitir_tiempo_gracia), (string)($num_dias_asisten_externos))) or die(db_error());
                                        $id_nuevo_curso=db_insert_id();
                                        
                                        // Introduce nueva aula
                                        db_query_params('INSERT INTO centro_aulas (id_centro, nombre, capacidad, ocupacion_minima, descripcion, equipamiento, edad_minima, edad_maxima, id_curso, tipo, docente_titular, id_menu_predefinido, mes_minimo, anio_minimo, mes_maximo, anio_maximo, ciclo, edad_minima_prog, edad_maxima_prog) VALUES (?, \'Neokids\', 0, 0, \'\', \'\', 0, 0, ?, 0, 0, 0, 1, 1995, 1, 1995, 0, 0, 0);', 'ss', array($centro_actual, $id_nuevo_curso));
                                }
                        }
        
header("Location: ".$url_retorno);
?>
