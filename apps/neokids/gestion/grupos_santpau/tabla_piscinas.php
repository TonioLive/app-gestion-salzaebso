<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$fecha_time_stamp=time();

// busca curso actual
// Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }

$busca_centros=db_query("SELECT * FROM escuelamunicipal.centros WHERE piscina=1 ORDER BY id_centro");

while($lista_centros=db_fetch_array($busca_centros)) {

$busca_aulas=db_query_params('SELECT * FROM escuelamunicipal.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual, $lista_centros["id_centro"]));

while($lista_aulas=db_fetch_array($busca_aulas)) {


	// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...

	$busca_alumnos=db_query_params('SELECT * FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND escuelamunicipal.alumnos.aula=? AND escuelamunicipal.alumnos.fecha_matricula<=? AND escuelamunicipal.alumnos.fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($lista_aulas["id_aula"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

	if (db_num_rows($busca_alumnos)!=0) {


		while($alumnos=db_fetch_array($busca_alumnos)) {
if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
			echo($lista_aulas["id_aula"].";".$lista_aulas["nombre"].";".$lista_centros["nombre_centro"].";".$alumnos["id_alumno"].";$edad_alumno<br>");
	
			}

		}

	}

}

?>