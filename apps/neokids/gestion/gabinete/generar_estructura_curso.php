<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_curso=$_GET["id_curso"];

if (strlen($id_curso)!=0) {

        $busca_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);

                // Genera la carpeta del curso

                $nombre_del_curso="Curso ".$datos_curso["nombre"]."-ID".$id_curso;
               
	        	$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');
        
        		$carpeta_curso = "/var/www/vhosts/escuelainfantilsantpau.com/gabintete/$nombre_del_curso";
        
    		    $carpeta_curso=str_replace($sustituir,"_",$carpeta_curso);

                @mkdir($carpeta_curso) ;

                // Genera estructura Escuelas, Aulas y Alumnos

                $busca_escuelas=db_query("SELECT * FROM centros");
                
                while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
                
						$carpeta_escuela = "/var/www/vhosts/escuelainfantilsantpau.com/gabintete/$nombre_del_curso/$nombre_escuela";
        
    		    		$carpeta_escuela=str_replace($sustituir,"_",$carpeta_escuela);
                
                        @mkdir($carpeta_escuela) ;

                        // Aulas
                        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? AND id_curso=?;', 'ss', array($datos_escuelas["id_centro"], $id_curso));
                        while($datos_aulas=db_fetch_array($busca_aulas)) {

                                $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];

								$carpeta_aula = "/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula";
        
    		    				$carpeta_aula=str_replace($sustituir,"_",$carpeta_aula);
                
                        		@mkdir($carpeta_aula) ;

                                // Educadora
                                if ((!is_null($datos_aulas["docente_titular"])) and ($datos_aulas["docente_titular"]!=0)) {
                                        $busca_educadora=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_aulas["docente_titular"]);
                                        $datos_educadora=db_fetch_array($busca_educadora);
                                        $nombre_educadora="00 Educadora-".$datos_educadora["Nombre"]." ".$datos_educadora["Apellido1"]." ".$datos_educadora["Apellido2"]."-ID".$datos_aulas["docente_titular"];
                                        
                                        $carpeta_educadora = "/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_educadora";
        
		    		    				$carpeta_educadora=str_replace($sustituir,"_",$carpeta_educadora);                                        
                                        
                                        @mkdir($carpeta_educadora) ;
                                        }

                        
                                // Alumnos
                                $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=? ORDER BY Apellido1, Apellido2, Nombre', 's', array($datos_aulas["id_aula"]));

                                while($datos_alumnos=db_fetch_array($busca_alumnos)){
                                        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];

                                        $carpeta_alumno = "/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno";
        
		    		    				$carpeta_alumno=str_replace($sustituir,"_",$carpeta_alumno);                                        
                                        
                                        @mkdir($carpeta_alumno) ;                                        
                                        
                                        }

                                }
                        }


                }
                
        }

?>
<p align="center"><br><br><font size="3">Estructura de carpetas del Gabinte Psicopedagógico para el Curso <?=$datos_curso["nombre"]?> generada</font><br><br>
<input type="button" value="Salir" onclick="window.location.href='cursos.php'">
<?php  require("../../configuracion/parte_inferior.php"); ?>
