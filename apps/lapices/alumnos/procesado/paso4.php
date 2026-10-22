<?php 
// Recibimos los datos
$usuario_curso=$_POST["curso"];
$codigo_inscripcion=$_POST["codigo_inscripcion"];

$proceso_decision=$_POST["proceso_decision"];
$usuario_fechamat_dia=$_POST["usuario_fechamat_dia"];
$usuario_fechamat_mes=$_POST["usuario_fechamat_mes"];
$usuario_fechamat_ano=$_POST["usuario_fechamat_ano"];

$usuario_fechabaja_dia=$_POST["usuario_fechabaja_dia"];
$usuario_fechabaja_mes=$_POST["usuario_fechabaja_mes"];
$usuario_fechabaja_ano=$_POST["usuario_fechabaja_ano"];

$tipo_mensualidad=$_POST["tipo_mensualidad"];
$comedor=$_POST["comedor"];
if (strlen($comedor)==0) {
        $comedor=0;
        }
$tipo_menu_comedor=$_POST["tipo_menu_comedor"];
if (strlen($tipo_menu_comedor)==0) {
        $tipo_menu_comedor=0;
        }
$numero_tomas=$_POST["numero_tomas"];
if (strlen($numero_tomas)==0) {
        $numero_tomas=0;
        }
$banco_entidad=$_POST["banco_entidad"];
$banco_poblacion=$_POST["banco_poblacion"];
$banco_titular=$_POST["banco_titular"];
$banco_dni_titular=$_POST["banco_dni_titular"];
$banco_domicilio=$_POST["banco_domicilio"];
$banco_iban=$_POST["banco_iban"];
$banco_cuenta1=$_POST["banco_cuenta1"];
$banco_cuenta2=$_POST["banco_cuenta2"];
$banco_cuenta3=$_POST["banco_cuenta3"];
$banco_cuenta4=$_POST["banco_cuenta4"];
$forma_de_pago=$_POST["forma_de_pago"];

$banco_cuenta=$banco_cuenta1." ".$banco_cuenta2." ".$banco_cuenta3." ".$banco_cuenta4;

// comprobamos los datos opcionales que necesiten ser comprobados
if ($usuario_fechamat_dia!="--" and $usuario_fechamat_mes!="--" and $usuario_fechamat_ano!="----") {
        $fecha_matricula=$usuario_fechamat_ano."/".$usuario_fechamat_mes."/".$usuario_fechamat_dia;
        if (!checkdate(intval($usuario_fechamat_mes),intval($usuario_fechamat_dia),intval($usuario_fechamat_ano))) {
                        $fecha_matricula="";
                        }
                } else {
                        $fecha_matricula="";
                }

if ($usuario_fechabaja_dia!="--" and $usuario_fechabaja_mes!="--" and $usuario_fechabaja_ano!="----") {
        $fecha_baja=$usuario_fechabaja_ano."/".$usuario_fechabaja_mes."/".$usuario_fechabaja_dia;
        if (!checkdate(intval($usuario_fechabaja_mes),intval($usuario_fechabaja_dia),intval($usuario_fechabaja_ano))) {
                        $fecha_baja="";
                        }
                } else {
                        $fecha_baja="";
                }
$resultado="";
if (strlen($usuario_curso)!=0) {

// Asignación de aula
$consulta_datos_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario_seleccionado");
$datos_alumno=db_fetch_array($consulta_datos_alumno);

// Consulta datos del curso en el que se inscribe
$datos_proximo_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$usuario_curso");
if (db_num_rows($datos_proximo_curso)!=0) {
        $info_proximo_curso=db_fetch_array($datos_proximo_curso);
        $fecha_inicio_proximo_curso=$info_proximo_curso["fecha_inicio"];
        if ($fecha_inicio_proximo_curso=="0000-00-00") {
                $fecha_inicio_proximo_curso=date("Y-m-d");
                }
        } else {
        $fecha_inicio_proximo_curso=date("Y-m-d");
        }

if ($datos_alumno["nonato"]==1) {
        $mes_nac=12;
        $anio_nac=date("Y",$info_proximo_curso["fecha_inicio"]);
        } else {
        // 1. Obtiene mes y año de nacimiento del niño
        $mes_nac=date("n",strtotime($datos_alumno["fecha_nacimiento"]));
        $anio_nac=date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
        }

// 3. Busca aula adecuada para su edad

$consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND mes_minimo>=$mes_nac AND anio_minimo>=$anio_nac AND mes_maximo<=$mes_nac AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,mes_maximo,anio_minimo,mes_minimo;");
$resultado="El alumno no ha sido inscrito. No hay aulas disponibles.";
$id_aula_donde_se_inscribe=0;

if (db_num_rows($consulta_aulas)==0) {
        // No hay aulas edecuadas
        // Busca mirando solo el año
        $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_minimo>=$anio_nac AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
        echo db_num_rows($consulta_aulas);
        if (db_num_rows($consulta_aulas)!=0) {
                $la_de_menor_edad=db_fetch_array($consulta_aulas);
                $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento.";
                $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                } else {
                // Busca mirando solo el año máximo
                $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
                if (db_num_rows($consulta_aulas)!=0) {
                        $la_de_menor_edad=db_fetch_array($consulta_aulas);
                        $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento y sin tener en cuenta la edad máxima.";
                        $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                        } else {
                        // Busca mirando solo el año mínimo
                        $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_minimo>=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
                        if (db_num_rows($consulta_aulas)!=0) {
                                $la_de_menor_edad=db_fetch_array($consulta_aulas);
                                $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento y sin tener en cuenta la edad mínima.";
                                $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                                }
                        }
                }
        } else {
        // Hay aulas adecuadas, calcula la ocupación, el promedio de edades y decide en cual inscribirlo
        $la_de_mayor_ocupacion=0;
        $nombre_de_la_de_mayor_ocupacion="";
        $mayor_ocupacion=0;
        while($listado_aulas=db_fetch_array($consulta_aulas)){
                // Calcula ocupación
                $consulta_ocupacion=db_query("SELECT * FROM alumnos WHERE aula=".$listado_aulas["id_aula"]." AND fecha_baja<='".$fecha_matricula."'");
                $ocupacion_actual=db_num_rows($consulta_ocupacion);

                if (($ocupacion_actual<$listado_aulas["capacidad"]) and ($ocupacion_actual>=$mayor_ocupacion)) {
                        $la_de_mayor_ocupacion=$listado_aulas["id_aula"];
                        $nombre_de_la_de_mayor_ocupacion=$listado_aulas["nombre"];
                        $mayor_ocupacion=$ocupacion_actual;
                        }
                }
        if ($la_de_mayor_ocupacion!=0) {
                $resultado="El alumno ha sido inscrito en el aula '".$nombre_de_la_de_mayor_ocupacion."'";
                $id_aula_donde_se_inscribe=$la_de_mayor_ocupacion;
                } else {
                $resultado="El alumno no ha sido inscrito. Todas las aulas están completas.";
                }
        }

if ((strlen($usuario_curso)!=0) and ($id_aula_donde_se_inscribe!=0)) {
        // Comprueba si esta en lista de espera
        $busca_en_lista=db_query("SELECT * FROM lista_espera WHERE id_usuario=$id_usuario_seleccionado AND id_curso=$usuario_curso");
        if (db_num_rows($busca_en_lista)!=0) {
                $datos_lista=db_fetch_array($busca_en_lista);
                $actualiza_estado=db_query("UPDATE lista_espera SET fecha_baja='$fecha_matricula', motivo_baja=1 WHERE id_usu_lista=".$datos_lista["id_usu_lista"]);
                }
        db_query("INSERT INTO alumnos (id_usuario, id_curso, fecha_inscripcion, fecha_matricula, fecha_baja, id_centro, tipo_mensualidad, comedor, tipo_menu_comedor, numero_tomas, forma_de_pago, banco_entidad, banco_poblacion, banco_titular, banco_dni_titular, banco_domicilio, banco_cuenta, banco_iban, aula, proceso_decision, aula_ayudas, codigo_inscripcion) VALUES ('".$id_usuario_seleccionado."', '".$usuario_curso."', '".date("Y-m-d")."', '".$fecha_matricula."', '".$fecha_baja."', '".$centro_actual."', '$tipo_mensualidad', '$comedor', '$tipo_menu_comedor', '$numero_tomas', '$forma_de_pago', '$banco_entidad', '$banco_poblacion', '$banco_titular', '$banco_dni_titular', '$banco_domicilio', '$banco_cuenta', '$banco_iban', '$id_aula_donde_se_inscribe', '$proceso_decision', '$id_aula_donde_se_inscribe', '$codigo_inscripcion')");
        }
}
?>
