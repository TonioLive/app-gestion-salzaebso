<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos


$id_aula=$_GET["id_aula"];
$turno=$_GET["turno"];
$id_alumno=$_GET["id_alumno"];

$inicio=$_GET["fecha"];
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
$mesnombre=$nombre_mes[$mes];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=?', 's', array($id_alumno));
$datos_alumno=db_fetch_array($busca_alumno);

// Busca inscripcion


$consulta_inscripcion=db_query_params('SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($datos_aula["id_curso"], $id_alumno)) or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);
$comedor=$datos_inscripcion["comedor"];
$tipo_menu_comedor=$datos_inscripcion["tipo_menu_comedor"];
$menu_actual=$datos_inscripcion["menu_actual"];

if (($menu_actual==0) and ($comedor!=2)) {
                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y")-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                        if (date("m")<date("m",strtotime($datos_alumno["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n")-date("n",strtotime($datos_alumno["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),2);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),2);
                                }
        if ($edad_alumno_anos>1) {
                $plural_anios="s";
                } else {
                $plural_anios="";
                }
        if ($edad_alumno_meses>1) {
                $plural_meses="es";
                } else {
                $plural_meses="";
                }

        // Comprueba si es de regimen
        if ($comedor==3) {
                        $filtro_regimen=" AND regimen=1";
                } else {
                        $filtro_regimen=" AND regimen=0";
                }

        // Calcula el menú que le toca por edades
        $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
        // $busca_menu_apropiado=mysql_query("SELECT * FROM menu_menus WHERE edad_ini_menu<=$edad_alumno $filtro_regimen") or die(mysql_error());
        $diferencia_actual=1000;
        $id_menu_seleccionado=0;
        $nombre_menu_seleccionado="";
        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                if ($diferencia<=$diferencia_actual) {
                        $diferencia_actual=$diferencia;
                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                        $nombre_menu_seleccionado=urldecode($menu_apropiado["nombre_menu"]);
                        }
                }
        $menu_actual=$id_menu_seleccionado;
        }

$hora=time();

$fecha=date("Y/m/d",$fecha_time_stamp);
$hora_entrada=date("H:i:s",$hora);

// Comprueba si ya ha sido introucido
$busca_si_esta=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_alumno AND id_servicio=$id_aula AND turno=$turno AND fecha='$fecha'");
if (db_num_rows($busca_si_esta)==0) {
        db_query_params('INSERT INTO servicios_consumidos (id_alumno,id_servicio,turno,fecha,hora_entrada,tipo_menu,tipo_menu_comedor,menu_seleccionado_hoy) VALUES (?,?,?,?,?,?,?,?)', 'ssssssss', array((string)($id_alumno), (string)($id_aula), (string)($turno), (string)($fecha), (string)($hora_entrada), (string)($comedor), (string)($tipo_menu_comedor), (string)($menu_actual))) or die(db_error());
        }

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
self.close();
// self.opener.history.go(0);
</script>
