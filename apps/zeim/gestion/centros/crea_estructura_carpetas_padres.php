<?php 
if (strlen($id_curso)!=0) {

		$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');	

        $busca_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);

                // Genera la carpeta del curso

                $nombre_del_curso="Curso ".$datos_curso["nombre"]."-ID".$id_curso;
                
				$nombreCarpetaCurso = str_replace($sustituir,"_","/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso");

                @mkdir($nombreCarpetaCurso) ;

                // Genera estructura Escuelas, Aulas y Alumnos

                $busca_escuelas=db_query("SELECT * FROM centros");

                while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];

						$nombreCarpetaEscuela = str_replace($sustituir,"_","/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela");
						$nombreCarpetaComun = str_replace($sustituir,"_","/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/comun");
    
                        @mkdir($nombreCarpetaEscuela);
                        @mkdir($nombreCarpetaComun);
                        
                        // Aulas
                        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=? AND id_curso=?;', 'ss', array($datos_escuelas["id_centro"], $id_curso));
                        while($datos_aulas=db_fetch_array($busca_aulas)) {

                                $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
                                
								$nombreCarpetaAula = str_replace($sustituir,"_","/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/$nombre_aula");

                                @mkdir($nombreCarpetaAula);

                                }
                        }


                }

        }
?>
