<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prueba=$_POST["id_prueba"];
$id_alumno=$_POST["id_alumno"];
$accion_despues=$_POST["accion_despues"];

$observaciones_prueba=$_POST["observaciones_prueba"];
if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=app_url_text_utf8($observaciones_prueba);
                }
                
$fecha_dia=$_POST["fecha_dia"];
$fecha_mes=$_POST["fecha_mes"];
$fecha_ano=$_POST["fecha_ano"];

$fecha=$fecha_ano."-".$fecha_mes."-".$fecha_dia;

$id_prueba1=$_POST["prueba1"];
$id_prueba2=$_POST["prueba2"];

// Calcula edad real el día de la prueba

        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_alumno) or die(db_error());

        $datos_usuario=db_fetch_array($consulta_datos_usuario);

                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(intval($fecha_ano)-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=intval($fecha_mes);
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_real=$edad_d_l;
                
                $dia_l=date("j",strtotime($fecha_l));
                if (intval($fecha_dia)<$dia_l) {
                        $edad_real=$edad_real-1;
                        }
                if ($edad_real<0) {
                        $edad_real=0;
                        }

$actualiza_brunet=db_query("UPDATE pruebas_comparadas SET er='$edad_real', fecha='$fecha', id_prueba1='$id_prueba1', id_prueba2='$id_prueba2', observaciones_prueba='$observaciones_prueba' WHERE id_prueba=$id_prueba") or die(db_error()."-1");


if ($accion_despues==0) {
        $url_retorno="resultado_comparivaat.php?id_prueba=".$id_prueba."&id_alumno=".$id_alumno;
        } else {
        $url_retorno="seguimiento.php?id_alumno=".$id_alumno;
        }
        
                        
        
header("Location: ".$url_retorno);
?>
