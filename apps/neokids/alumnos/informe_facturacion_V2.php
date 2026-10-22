<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

function trasforma_cols($numero_col) {

        $alfabeto=($numero_col/26);
        if ((round($alfabeto,0)>$alfabeto) or (round($alfabeto,0)==$alfabeto)) {
                $alfabeto=round($alfabeto,0)-1;
                } else {
                $alfabeto=round($alfabeto,0);
                }
        if ($alfabeto>0) {
                $letra_col=chr($alfabeto+64).chr(($numero_col-($alfabeto*26))+64);
                } else {
                $letra_col=chr($numero_col+64);
                }

        return $letra_col;
        }

// Datos de inicio

// Curso 1

$curso_seleccionado=$_GET["id_curso_act"];

$busca_datos_del_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));

$datos_del_curso=db_fetch_array($busca_datos_del_curso);
$nombre_curso_ini=$datos_del_curso["nombre"];

$anio_inicial=date("Y",strtotime($datos_del_curso["fecha_inicio"]));
$anio_final=date("Y",strtotime($datos_del_curso["fecha_fin"]));
$mes_inicial=date("n",strtotime($datos_del_curso["fecha_inicio"]));
$mes_final=date("n",strtotime($datos_del_curso["fecha_fin"]));

$anio_fin_edades=$anio_inicial;
$anio_inicio_edades=$anio_fin_edades-5;


$busca_centros_resultado=db_query("SELECT * FROM centros ORDER BY nombre_centro");

echo "Curso: $nombre_curso_ini<br>";

while($centros_resultado=db_fetch_array($busca_centros_resultado)){


$contador_centros=$centros_resultado["id_centro"];
$nombre_centros=$centros_resultado["nombre_centro"];

        echo "Centro: $nombre_centros ($contador_centros)<br>";
        
        for($contador_anios=$anio_inicio_edades;$contador_anios<=$anio_fin_edades;$contador_anios++) {

        echo "Nacidos el año $contador_anios<br>";
        
        $anio_seleccionado=$anio_inicial;
        
        for ($contador_meses=$mes_inicial;$contador_meses<=($mes_final+(12*($anio_final-$anio_inicial)));$contador_meses++) {
        
                if ($contador_meses>12) {
                        $mes_seleccionado=$contador_meses-12;
                        if ($contador_meses==13) {
                                $anio_seleccionado++;
                                }
                        } else {
                        $mes_seleccionado=$contador_meses;
                        }

                $mes_mostrar="0".$mes_seleccionado;
                $mes_mostrar=substr($mes_mostrar,strlen($mes_mostrar)-2,2);

                $primer_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-01";
                $ultimo_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-".date("t",strtotime($primer_dia_mes_seleccionado));

                // Paso 1: Calcula ocupación de los grupos de edades durante cada mes del curso

                        // Busca numero de alumnos

                        $busca_alumnos_en_aula=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND year(usuarios.fecha_nacimiento)=? AND alumnos.id_curso=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=?', 'sssss', array($contador_centros, $contador_anios, $curso_seleccionado, (string)($ultimo_dia_mes_seleccionado), (string)($primer_dia_mes_seleccionado)));
                        $total_alumnos=db_num_rows($busca_alumnos_en_aula);

                        echo $nombre_mes[$mes_seleccionado].": ".$total_alumnos."<br>";

                        }

                }
        }
        
// Curso 2

$curso_seleccionado=$_GET["id_curso_sig"];

$busca_datos_del_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));

$datos_del_curso=db_fetch_array($busca_datos_del_curso);
$nombre_curso_ini=$datos_del_curso["nombre"];

$anio_inicial=date("Y",strtotime($datos_del_curso["fecha_inicio"]));
$anio_final=date("Y",strtotime($datos_del_curso["fecha_fin"]));
$mes_inicial=date("n",strtotime($datos_del_curso["fecha_inicio"]));
$mes_final=date("n",strtotime($datos_del_curso["fecha_fin"]));

$anio_fin_edades=$anio_inicial;
$anio_inicio_edades=$anio_fin_edades-5;


$busca_centros_resultado=db_query("SELECT * FROM centros ORDER BY nombre_centro");

echo "Curso: $nombre_curso_ini<br>";

while($centros_resultado=db_fetch_array($busca_centros_resultado)){


$contador_centros=$centros_resultado["id_centro"];
$nombre_centros=$centros_resultado["nombre_centro"];

        echo "Centro: $nombre_centros ($contador_centros)<br>";

        for($contador_anios=$anio_inicio_edades;$contador_anios<=$anio_fin_edades;$contador_anios++) {

        echo "Nacidos el año $contador_anios<br>";

        $anio_seleccionado=$anio_inicial;

        for ($contador_meses=$mes_inicial;$contador_meses<=($mes_final+(12*($anio_final-$anio_inicial)));$contador_meses++) {

                if ($contador_meses>12) {
                        $mes_seleccionado=$contador_meses-12;
                        if ($contador_meses==13) {
                                $anio_seleccionado++;
                                }
                        } else {
                        $mes_seleccionado=$contador_meses;
                        }

                $mes_mostrar="0".$mes_seleccionado;
                $mes_mostrar=substr($mes_mostrar,strlen($mes_mostrar)-2,2);

                $primer_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-01";
                $ultimo_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-".date("t",strtotime($primer_dia_mes_seleccionado));

                // Paso 1: Calcula ocupación de los grupos de edades durante cada mes del curso

                        // Busca numero de alumnos

                        $busca_alumnos_en_aula=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND year(usuarios.fecha_nacimiento)=? AND alumnos.id_curso=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=?', 'sssss', array($contador_centros, $contador_anios, $curso_seleccionado, (string)($ultimo_dia_mes_seleccionado), (string)($primer_dia_mes_seleccionado)));
                        $total_alumnos=db_num_rows($busca_alumnos_en_aula);

                        echo $nombre_mes[$mes_seleccionado].": ".$total_alumnos."<br>";

                        }

                }
        }
        
?>
