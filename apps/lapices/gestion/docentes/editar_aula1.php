<?php  require("../../configuracion/config.php"); ?>
<?php 
$id_centro=$_POST["id_centro"];
$id_aula=$_POST["id_aula"];
$o=$_POST["o"];

if ($o==1) {
        $url_retorno="gestion_aulas.php";
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
                        $curso_academico=$_POST["curso_academico"];
                        $docente_titular=$_POST["docente_titular"];
                        
                        if (strlen($id_aula)!=0) {
                                        // Guarda cambios
                                        db_query("UPDATE centro_aulas SET nombre='$nombre_aula', capacidad='$capacidad', ocupacion_minima='$ocupacion_minima', descripcion='$descripcion', equipamiento='$equipamiento', edad_minima='$edad_minima', edad_maxima='$edad_maxima', id_curso='$curso_academico', tipo='$tipo', docente_titular='$docente_titular' WHERE id_aula=$id_aula;") or die(db_error());
                                } else {
                                        // Introduce nuevo centro
                                        db_query("INSERT INTO centro_aulas (nombre, capacidad, ocupacion_minima, descripcion, equipamiento, edad_minima, edad_maxima, id_curso, id_centro, tipo, docente_titular) VALUES ('$nombre_aula', '$capacidad', '$ocupacion_minima', '$descripcion', '$equipamiento', '$edad_minima', '$edad_maxima', '$curso_academico', '$centro_actual', '$tipo', '$docente_titular');") or die(db_error());
                                        $id_aula=db_insert_id();
                                }
                        }
        
header("Location: ".$url_retorno);
?>
