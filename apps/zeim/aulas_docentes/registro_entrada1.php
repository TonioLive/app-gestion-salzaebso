<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_aula=$_POST["id_aula"];
$turno=$_POST["turno"];
$id_alumno=$_POST["id_alumno"];

$inicio=$_POST["fecha"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];;

$hora=$_POST["hora"];
$comedor=$_POST["comedor"];
$tipo_menu_comedor=$_POST["tipo_menu_comedor"];
$incidencia=$_POST["incidencia"];
$observaciones=$_POST["observaciones"];

$fecha=date("Y/m/d",$fecha_time_stamp);
$hora_entrada=date("H:i:s",$hora);

db_query_params('INSERT INTO servicios_consumidos (id_alumno,id_servicio,turno,fecha,hora_entrada,observaciones,tipo_menu,tipo_menu_comedor,incidencia) VALUES (?,?,?,?,?,?,?,?,?)', 'sssssssss', array((string)($id_alumno), (string)($id_aula), (string)($turno), (string)($fecha), (string)($hora_entrada), (string)($observaciones), (string)($comedor), (string)($tipo_menu_comedor), (string)($incidencia))) or dei(db_error());

if ($turno!=0) {
// Comprueba si se ha registrado la salida del comedor
$fecha=date("Y-m-d",$fecha_time_stamp);
$busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND turno=0 AND fecha=?', 'sss', array($id_alumno, $id_aula, (string)($fecha))) or die(db_error());

if (db_num_rows($busca_registro_entrada)!=0) {
$datos_registro=db_fetch_array($busca_registro_entrada);

$id_registro_man=$datos_registro["id_servicio_consumido"];
if (($datos_registro["hora_salida_c"]=="") and ($datos_registro["tipo_menu"]!=0) and ($datos_registro["tipo_menu"]!=2)) {
        if ($datos_registro["hora_entrada_c"]!="") {
                db_query_params('UPDATE servicios_consumidos SET hora_salida_c=? WHERE id_servicio_consumido=?', 'ss', array((string)($hora_entrada), $id_registro_man)) or die(db_error());
                } else {
                db_query_params('UPDATE servicios_consumidos SET hora_salida=?, incidencia=1, observaciones=\'No se ha marcado la hora de salida del aula por la mañana. Se habia marcado asistencia al comedor pero no se ha registrado la entrada.\' WHERE id_servicio_consumido=?', 'ss', array((string)($hora_salida_centro_man), $id_registro_man)) or die(db_error());
                }
        }
if (($datos_registro["hora_salida"]=="") and (($datos_registro["tipo_menu"]==0) or ($datos_registro["tipo_menu"]==2))) {
        db_query_params('UPDATE servicios_consumidos SET hora_salida=?, incidencia=1, observaciones=\'No se ha marcado la hora de salida del aula por la mañana.\' WHERE id_servicio_consumido=?', 'ss', array((string)($hora_salida_centro_man), $id_registro_man)) or die(db_error());
        }
}
}

?>
<script language="Javascript">
window.close();
self.opener.history.go(0);
</script>
