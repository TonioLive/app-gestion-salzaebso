<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php'; 
if (strlen($id_curso)!=0) {
		
    $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');	
		$sustituirpor=array('A', 'R', 'A', 'A', 'a', 'r', 'a', 'a', 'S','E', 'C', 'E', 'E', 'e', 'c', 'e', 'e','I', 'E', 'D', 'I', 'i', 'e', 'd', 'i','O', 'N', 'O', 'O', 'o', 'n', 'o', 'o','U', 'U', 'U', 'U', 'u', 'u', 'u', 'u','N', 'n', 'C', 'c');

    $busca_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
    if (db_num_rows($busca_datos_curso)!=0) {
        $datos_curso=db_fetch_array($busca_datos_curso);

        // Genera la carpeta del curso

        $nombre_del_curso="Alumnos Curso ".$datos_curso["nombre"];

        $nombreCarpetaCurso = str_replace($sustituir,$sustituirpor,(lapices_storage_path('documentacion') . "/$nombre_del_curso"));

        @mkdir($nombreCarpetaCurso) ;

        // Genera estructura Escuelas, Aulas y Alumnos

        $busca_escuelas=db_query("SELECT * FROM centros");

        while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                    
            // Genera Carpeta Escuela

            $busca_alumnos_centro=db_query("SELECT id_alumno FROM alumnos WHERE id_curso=$id_curso and id_centro=".$datos_escuelas["id_centro"].";");

            if (db_num_rows($busca_alumnos_centro)!=0) {

                $nombre_escuela=$datos_escuelas["nombre_centro"];

                $nombreCarpetaEscuela = str_replace($sustituir,"_",(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela"));

                @mkdir($nombreCarpetaEscuela);

                // Aulas
                $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=".$datos_escuelas["id_centro"]." AND id_curso=$id_curso;");

                while($datos_aulas=db_fetch_array($busca_aulas)) {

                    // Alumnos
                    $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=".$datos_aulas["id_aula"]." ORDER BY Apellido1, Apellido2, Nombre");

                    while($datos_alumnos=db_fetch_array($busca_alumnos)){
                        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_usuario"];

                        // Comprueba carpeta a?o nacimiento
                        if (is_null($datos_alumnos["fecha_nacimiento"])==false) {
                            $anioNacimiento = date("Y",strtotime($datos_alumnos["fecha_nacimiento"]));

                            if ($anioNacimiento=='-0001') {
                                $anioNacimiento="Sin fecha de nacimiento";
                            }

                            $nombreCarpetaNacimiento = (lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$anioNacimiento");

                            if (file_exists($nombreCarpetaNacimiento)==false) {
                                @mkdir($nombreCarpetaNacimiento) ;
                            }

                            $nombreCarpetaAlumno = str_replace($sustituir,$sustituirpor,(lapices_storage_path('documentacion') . "/$nombre_del_curso/$nombre_escuela/$anioNacimiento/$nombre_alumno"));

                            @mkdir($nombreCarpetaAlumno) ;

                        }
                    }
                }
            }
        }
    }
}
?>
