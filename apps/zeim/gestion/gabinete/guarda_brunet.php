<?php  require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prueba=$_POST["id_prueba"];
$id_alumno=$_POST["id_alumno"];
$accion_despues=$_POST["accion_despues"];

$observaciones_prueba=$_POST["observaciones_prueba"];
if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=urldecode($observaciones_prueba);
                }
                
$fecha_dia=$_POST["fecha_dia"];
$fecha_mes=$_POST["fecha_mes"];
$fecha_ano=$_POST["fecha_ano"];
$fecha=$fecha_ano."-".$fecha_mes."-".$fecha_dia;

// Calcula edad real el día de la prueba

        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_alumno)) or die(db_error());

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

$hora=$_POST["hora_hora"].":".$_POST["hora_min"].":00";

$actualiza_brunet=db_query_params('UPDATE pruebas_brunet SET er=?, fecha=?, hora=?, observaciones_prueba=? WHERE id_prueba=?', 'sssss', array((string)($edad_real), (string)($fecha), (string)($hora), (string)($observaciones_prueba), $id_prueba)) or die(db_error()."-1");

// Inserta resultados
$busca_items_brunet=db_query("SELECT * FROM pruebas_brunet_items WHERE num_orden<161");
while($items_brunet=db_fetch_array($busca_items_brunet)) {
        $item_actual=$_POST["item".$items_brunet["id_item"]];
        if (strlen($item_actual)!=0) {
        // Comprueba si esta
        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet["id_item"])) or die(db_error()."-1");
        if (db_num_rows($busca_respuesta_item)!=0) {
                $respuestas_items_brunet=db_fetch_array($busca_respuesta_item);
                $actualiza_item=db_query("UPDATE pruebas_brunet_respuestas SET respuesta=$item_actual WHERE id_respuesta=".$respuestas_items_brunet["id_respuesta"]) or die(db_error()."-1");
                } else {
                $inserta_item=db_query_params('INSERT INTO pruebas_brunet_respuestas (id_prueba,id_item,respuesta) VALUES (?,?,?);', 'sss', array((string)($id_prueba), (string)($items_brunet["id_item"]), (string)($item_actual))) or die(db_error()."-1");
                }
                }
        }
                        

if ($accion_despues==0) {
        $url_retorno="resultado_brunet.php?id_prueba=".$id_prueba."&id_alumno=".$id_alumno;
        } else {
        $url_retorno="seguimiento.php?id_alumno=".$id_alumno;
        }
        
                        
        
header("Location: ".$url_retorno);
?>
