<?php  require("../../configuracion/config.php"); ?>
<?php 
$id_centro=$_POST["id_centro"];
$id_aula=$_POST["id_aula"];
$o=$_POST["o"];

if ($o==1) {
        $url_retorno="gestion_aulas.php?curso_seleccionado=".$_POST["curso_academico"];
        } else {
        $url_retorno="editar_centro1.php?id_centro=".$id_centro;
        }
        
$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                        $nombre_aula=$_POST["nombre_aula"];
                        $tipo=$_POST["tipo"];
                        $capacidad=$_POST["capacidad"];
                        $ocupacion_minima=$_POST["ocupacion_minima"];
                        $descripcion=$_POST["descripcion"];
                        $equipamiento=$_POST["equipamiento"];
                        $edad_minima=$_POST["edad_minima"];
                        $edad_maxima=$_POST["edad_maxima"];
                        $edad_minima_prog=$_POST["edad_minima_prog"];
                        $edad_maxima_prog=$_POST["edad_maxima_prog"];

                        $mes_minimo=$_POST["mes_minimo"];
                        $anio_minimo=$_POST["anio_minimo"];
                        $mes_maximo=$_POST["mes_maximo"];
                        $anio_maximo=$_POST["anio_maximo"];
                        
                        $curso_academico=$_POST["curso_academico"];
                        $docente_titular=$_POST["docente_titular"];
                        $id_menu_predefinido=$_POST["id_menu_predefinido"];
                        $ciclo=$_POST["ciclo"];
                        
                        
                        if (strlen($id_aula)!=0) {
                                        // Guarda cambios
                                        db_query_params('UPDATE centro_aulas SET ciclo=?,mes_minimo=?,anio_minimo=?,mes_maximo=?,anio_maximo=?,id_menu_predefinido=?, nombre=?, capacidad=?, ocupacion_minima=?, descripcion=?, equipamiento=?, edad_minima=?, edad_maxima=?, edad_minima_prog=?, edad_maxima_prog=?, id_curso=?, tipo=?, docente_titular=? WHERE id_aula=?;', 'sssssssssssssssssss', array((string)($ciclo), (string)($mes_minimo), (string)($anio_minimo), (string)($mes_maximo), (string)($anio_maximo), (string)($id_menu_predefinido), (string)($nombre_aula), (string)($capacidad), (string)($ocupacion_minima), (string)($descripcion), (string)($equipamiento), (string)($edad_minima), (string)($edad_maxima), (string)($edad_minima_prog), (string)($edad_maxima_prog), (string)($curso_academico), (string)($tipo), (string)($docente_titular), $id_aula)) or die(db_error());
                                } else {
                                        // Introduce nuevo centro
                                        db_query_params('INSERT INTO centro_aulas (nombre, capacidad, ocupacion_minima, descripcion, equipamiento, edad_minima, edad_maxima, id_curso, id_centro, tipo, docente_titular, id_menu_predefinido,mes_minimo,anio_minimo,mes_maximo,anio_maximo,ciclo, edad_minima_prog,edad_maxima_prog) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?, ?, ?);', 'sssssssssssssssssss', array((string)($nombre_aula), (string)($capacidad), (string)($ocupacion_minima), (string)($descripcion), (string)($equipamiento), (string)($edad_minima), (string)($edad_maxima), (string)($curso_academico), (string)($id_centro), (string)($tipo), (string)($docente_titular), (string)($id_menu_predefinido), (string)($mes_minimo), (string)($anio_minimo), (string)($mes_maximo), (string)($anio_maximo), (string)($ciclo), (string)($edad_minima_prog), (string)($edad_maxima_prog))) or die(db_error());
                                        $id_aula=db_insert_id();
                                }
                        }
        
header("Location: ".$url_retorno);
?>
