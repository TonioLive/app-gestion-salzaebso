<?php 
require("../../configuracion/config.php");

$id_horario=$_POST["id_horario"];
$id_docente=$_POST["id_docente"];
$nss=$_POST["nss"];
$codigo_nominaplus=$_POST["codigo_nominaplus"];
$servicio_comun=$_POST["servicio_comun"];

$titulacion=$_POST["titulacion"];
if (strlen($titulacion)!=0) {
                        $titulacion=addslashes($titulacion);
                        }
$especialidad=$_POST["especialidad"];
if (strlen($especialidad)!=0) {
                        $especialidad=addslashes($especialidad);
                        }

$url_retorno="listado_docentes.php";

                        $fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                }
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                }
                        $hora_e=$_POST["hora_e"];
                        $min_e=$_POST["min_e"];
                        $hora_s=$_POST["hora_s"];
                        $min_s=$_POST["min_s"];

                        $hora_e1=$_POST["hora_e1"];
                        $min_e1=$_POST["min_e1"];
                        $hora_s1=$_POST["hora_s1"];
                        $min_s1=$_POST["min_s1"];

                if ($fecha_inicio_dia!="--" and $fecha_inicio_mes!="--" and $fecha_inicio_ano!="----") {
                        $fecha_inicio=$fecha_inicio_ano."/".$fecha_inicio_mes."/".$fecha_inicio_dia;
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio="";
                                }
                        } else {
                        $fecha_inicio="";
                        }
                if ($fecha_fin_dia!="--" and $fecha_fin_mes!="--" and $fecha_fin_ano!="----") {
                        $fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin="";
                                }
                        } else {
                        $fecha_fin="";
                        }
                        
                        $l=$_POST["l"];
                        if (strlen($l)==0) {
                                $l=0;
                                }
                        $m=$_POST["m"];
                        if (strlen($m)==0) {
                                $m=0;
                                }
                        $x=$_POST["x"];
                        if (strlen($x)==0) {
                                $x=0;
                                }
                        $j=$_POST["j"];
                        if (strlen($j)==0) {
                                $j=0;
                                }
                        $v=$_POST["v"];
                        if (strlen($v)==0) {
                                $v=0;
                                }
                        $s=$_POST["s"];
                        if (strlen($s)==0) {
                                $s=0;
                                }
                        $d=$_POST["d"];
                        if (strlen($d)==0) {
                                $d=0;
                                }
                        $horas_semana=$_POST["horas_semana"];
                        
                        $horas_l=$_POST["horas_l"];
                        $horas_l=str_replace(",", ".", $horas_l);
                        $horas_m=$_POST["horas_m"];
                        $horas_m=str_replace(",", ".", $horas_m);
                        $horas_x=$_POST["horas_x"];
                        $horas_x=str_replace(",", ".", $horas_x);
                        $horas_j=$_POST["horas_j"];
                        $horas_j=str_replace(",", ".", $horas_j);
                        $horas_v=$_POST["horas_v"];
                        $horas_v=str_replace(",", ".", $horas_v);
                        $horas_s=$_POST["horas_s"];
                        $horas_s=str_replace(",", ".", $horas_s);
                        $horas_d=$_POST["horas_d"];
                        $horas_d=str_replace(",", ".", $horas_d);
                        
                        // Guarda cambios
                        db_query_params('UPDATE horario_docentes SET horas_semana=?, fecha_inicio=?, fecha_fin=?, hora_entrada1=?, hora_salida1=?, hora_entrada2=?, hora_salida2=?, l=?, m=?, x=?, j=?, v=?, s=?, d=?, horas_l=?, horas_l=?, horas_m=?, horas_x=?, horas_j=?, horas_v=?, horas_s=?, horas_d=? WHERE id_horario=?', 'sssssssssssssssssssssss', array((string)($horas_semana), (string)($fecha_inicio), (string)($fecha_fin), (string)($hora_e) . ':' . (string)($min_e) . ':00', (string)($hora_s) . ':' . (string)($min_s) . ':00', (string)($hora_e1) . ':' . (string)($min_e1) . ':00', (string)($hora_s1) . ':' . (string)($min_s1) . ':00', (string)($l), (string)($m), (string)($x), (string)($j), (string)($v), (string)($s), (string)($d), (string)($horas_l), (string)($horas_l), (string)($horas_m), (string)($horas_x), (string)($horas_j), (string)($horas_v), (string)($horas_s), (string)($horas_d), $id_horario)) or die(db_error());
                        
                        
                        db_query("UPDATE usuarios SET servicio_comun='$servicio_comun', titulacion='$titulacion', especialidad='$especialidad', codigo_nominaplus='$codigo_nominaplus', NSS='$nss' WHERE id_usuario=$id_docente") or die(db_error());

header("Location: $url_retorno");

?>
