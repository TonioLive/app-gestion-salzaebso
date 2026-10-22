<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_curso=$_GET["id_curso"];

if (strlen($id_curso)!=0) {

        $busca_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);

                // Genera la carpeta del curso

                $nombre_del_curso="Curso ".$datos_curso["nombre"]."-ID".$id_curso;

                @mkdir("/home/gabintete/$nombre_del_curso") ;

                // Genera estructura Escuelas, Aulas y Alumnos

                $busca_escuelas=db_query("SELECT * FROM centros");
                
                while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
                
                        @mkdir("/home/gabintete/$nombre_del_curso/$nombre_escuela") ;

                        // Aulas
                        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=".$datos_escuelas["id_centro"]." AND id_curso=$id_curso;");
                        while($datos_aulas=db_fetch_array($busca_aulas)) {

                                $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];

                                @mkdir("/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula") ;

                                // Educadora
                                if ((!is_null($datos_aulas["docente_titular"])) and ($datos_aulas["docente_titular"]!=0)) {
                                        $busca_educadora=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_aulas["docente_titular"]);
                                        $datos_educadora=db_fetch_array($busca_educadora);
                                        $nombre_educadora="00 Educadora-".$datos_educadora["Nombre"]." ".$datos_educadora["Apellido1"]." ".$datos_educadora["Apellido2"]."-ID".$datos_aulas["docente_titular"];
                                        @mkdir("/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_educadora") ;
                                        }

                        
                                // Alumnos
                                $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=".$datos_aulas["id_aula"]." ORDER BY Apellido1, Apellido2, Nombre");

                                while($datos_alumnos=db_fetch_array($busca_alumnos)){
                                        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
                                        @mkdir("/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno") ;
                                        }

                                }
                        }


                }
                
        }

?>
<p align="center"><br><br><font size="3">Estructura de carpetas del Gabinte Psicopedagógico para el Curso <?=$datos_curso["nombre"]?> generada</font><br><br>
<input type="button" value="Salir" onclick="window.location.href='cursos.php'">
<?php  require("../../configuracion/parte_inferior.php"); ?>
