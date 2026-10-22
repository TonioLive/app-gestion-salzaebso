<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php'; 
if (strlen($id_curso)!=0) {
		
		$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');	
		
        $busca_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);

                // Genera la carpeta del curso

                $nombre_del_curso="Curso ".$datos_curso["nombre"]."-ID".$id_curso;

				$nombreCarpetaCurso = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso"));

                @mkdir($nombreCarpetaCurso) ;

                // Genera estructura Escuelas, Aulas y Alumnos

                $busca_escuelas=db_query("SELECT * FROM centros");

                while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];

						$nombreCarpetaEscuela = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela"));
						$nombreCarpetaPadres = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/padres"));


 						@mkdir($nombreCarpetaEscuela);
                        @mkdir($nombreCarpetaPadres);

						
                        // Aulas
                        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=".$datos_escuelas["id_centro"]." AND id_curso=$id_curso;");
                        while($datos_aulas=db_fetch_array($busca_aulas)) {

                                $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];

								$nombreCarpetaAula = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula"));
								$nombreCarpetaAulaPadres = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres"));

                                @mkdir($nombreCarpetaAula) ;
								@mkdir($nombreCarpetaAulaPadres) ;
                                // Educadora
                                if ((!is_null($datos_aulas["docente_titular"])) and ($datos_aulas["docente_titular"]!=0)) {
                                        $busca_educadora=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_aulas["docente_titular"]);
                                        $datos_educadora=db_fetch_array($busca_educadora);
                                        $nombre_educadora="00 Educadora-".$datos_educadora["Nombre"]." ".$datos_educadora["Apellido1"]." ".$datos_educadora["Apellido2"]."-ID".$datos_aulas["docente_titular"];
                                        
										$nombreCarpetaEducadora = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_educadora"));                                       
                                        
                                        @mkdir($nombreCarpetaEducadora) ;
                                        }


                                // Alumnos
                                $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=".$datos_aulas["id_aula"]." ORDER BY Apellido1, Apellido2, Nombre");

                                while($datos_alumnos=db_fetch_array($busca_alumnos)){
                                        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];

                                        $nombreCarpetaAlumno = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno"));

                                        @mkdir($nombreCarpetaAlumno) ;
                                        }

                                }
                        }


                }

        // Gabinete

        $busca_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);

                // Genera la carpeta del curso

                $nombre_del_curso="Curso ".$datos_curso["nombre"]."-ID".$id_curso;

				$nombreCarpetaCurso = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso"));

                @mkdir($nombreCarpetaCurso) ;

                // Genera estructura Escuelas, Aulas y Alumnos

                $busca_escuelas=db_query("SELECT * FROM centros");

                while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];

						$nombreCarpetaEscuela = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela"));
						$nombreCarpetaPadres = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/padres"));
						
                        @mkdir($nombreCarpetaEscuela) ;
						@mkdir($nombreCarpetaPadres) ;

                        // Aulas
                        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=".$datos_escuelas["id_centro"]." AND id_curso=$id_curso;");
                        while($datos_aulas=db_fetch_array($busca_aulas)) {

                                $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];

								$nombreCarpetaAula = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula"));
								$nombreCarpetaAulaPadres = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres"));

                                @mkdir($nombreCarpetaAula) ;
								@mkdir($nombreCarpetaAulaPadres) ;
								
                                // Educadora
                                if ((!is_null($datos_aulas["docente_titular"])) and ($datos_aulas["docente_titular"]!=0)) {
                                        $busca_educadora=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_aulas["docente_titular"]);
                                        $datos_educadora=db_fetch_array($busca_educadora);
                                        $nombre_educadora="00 Educadora-".$datos_educadora["Nombre"]." ".$datos_educadora["Apellido1"]." ".$datos_educadora["Apellido2"]."-ID".$datos_aulas["docente_titular"];
                                        
										$nombreCarpetaEducadora = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_educadora"));                                       
                                        
                                        @mkdir($nombreCarpetaEducadora) ;                                        
                                        }


                                // Alumnos
                                $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=".$datos_aulas["id_aula"]." ORDER BY Apellido1, Apellido2, Nombre");

                                while($datos_alumnos=db_fetch_array($busca_alumnos)){
                                        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
                                        
                                        $nombreCarpetaAlumno = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno"));
                                        $nombreCarpetaAlumnoPadres = str_replace($sustituir,"_",(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres"));

                                        @mkdir($nombreCarpetaAlumno) ; 
                                        @mkdir($nombreCarpetaAlumnoPadres) ;                                        
                                                                               
                                        
                                        @mkdir() ;
                                        }

                                }
                        }


                }

        }
?>
