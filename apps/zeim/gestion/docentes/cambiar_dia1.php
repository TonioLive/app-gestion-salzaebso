<?php 
require("../../configuracion/config.php");

$id_docente=$_POST["id_docente"];
$dia=$_POST["dia"];
$no_asistencia=$_POST["no_asistencia"];

$url_retorno="calendario_mes.php?id_docente=$id_docente&dia=".date("Y/m/d",$dia);

                        $hora_e=$_POST["hora_e"];
                        $min_e=$_POST["min_e"];
                        $hora_s=$_POST["hora_s"];
                        $min_s=$_POST["min_s"];

                        $hora_e1=$_POST["hora_e1"];
                        $min_e1=$_POST["min_e1"];
                        $hora_s1=$_POST["hora_s1"];
                        $min_s1=$_POST["min_s1"];
                        
                        $hora_i_f=$_POST["hora_i_f"];
                        $min_i_f=$_POST["min_i_f"];
                        $hora_s_f=$_POST["hora_s_f"];
                        $min_s_f=$_POST["min_s_f"];
                        
                        $tipo_ausencia=$_POST["tipo_ausencia"];
                        $sustituido_por=$_POST["sustituido_por"];
                        
                        // Borra el horario de ese dia por si acaso
                        db_query_params('DELETE FROM horario_docentes_dias WHERE id_usuario=? AND fecha=?', 'ss', array($id_docente, (string)(date("Y-m-d", $dia))));
                        
                        // Guarda cambios
                        db_query_params('INSERT INTO horario_docentes_dias (id_usuario, fecha, hora_entrada1, hora_salida1, hora_entrada2, hora_salida2, no_asistencia, tipo_ausencia, sustituido_por, hora_ini_falta, hora_fin_falta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)', 'sssssssssss', array($id_docente, (string)(date("Y-m-d", $dia)), (string)($hora_e) . ':' . (string)($min_e) . ':00', (string)($hora_s) . ':' . (string)($min_s) . ':00', (string)($hora_e1) . ':' . (string)($min_e1) . ':00', (string)($hora_s1) . ':' . (string)($min_s1) . ':00', (string)($no_asistencia), (string)($tipo_ausencia), (string)($sustituido_por), (string)($hora_i_f) . ':' . (string)($min_i_f) . ':00', (string)($hora_s_f) . ':' . (string)($min_s_f) . ':00')) or die(db_error());

header("Location: $url_retorno");

?>
