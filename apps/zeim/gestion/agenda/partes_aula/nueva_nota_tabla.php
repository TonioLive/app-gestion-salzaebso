<?php
header("Content-Type: text/xml; charset=windows-1252");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 01 Jan 2007 01:00:00 GMT");

require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$id_aula=$_GET["id_aula"];
$dia=$_GET["dia"];
$id_alumno=$_GET["id_alumno"];
$nota=$_GET["texto_nota"];
if ($nota) {
	$nota=utf8_decode($nota);
}
$centro_actual=$_GET["centro_aula"];

$id_area=2;
$avisar=0;
$tipo_nota=3;
$hora_nota=date("H:i");


$consulta_insert=array('INSERT INTO agenda_alumnos_seguimiento (id_aula,dia,id_alumno,area,introducida_por) VALUES (?,?,?,?,?)', 'sssss', array((string)($id_aula), (string)(date("Y-m-d", $dia)), (string)($id_alumno), (string)($id_area), (string)($idUsuario)));
db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
$id_nota=db_insert_id();

$num_errores=0;

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query_params('UPDATE agenda_alumnos_seguimiento SET hora=?, nota=?, avisar=?,tipo_nota=? WHERE id=?', 'sssss', array((string)($hora_nota), (string)($nota), (string)($avisar), (string)($tipo_nota), $id_nota)) or die(db_error()."-2");

	// Inserta notificaciones app movil
        /* De momento esto no
        if ($id_alumno!=0) {
                // Busca padres
                $padresAlumno = mysql_query("SELECT * FROM padres WHERE padre_de=".$id_alumno.";");
                while($datosPadres=mysql_fetch_array($padresAlumno)) {

                        // Busca TOKENS
                        $buscaTokens = mysql_query("SELECT * FROM tokens_fcm WHERE id_usuario=".$datosPadres['id_usuario']." GROUP BY FCMtoken;");
                        if (mysql_num_rows($buscaTokens)!=0) {
                                // Inserta Notificación
                                $nuevaNotificacion = mysql_query("INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_nota_seguimiento) VALUES ('".date('Y-m-d')."','".$datosPadres['id_usuario']."','token','Escuela Infantil','La educadora ha introducido una nueva nota.',0,'".$id_nota."');");
                        }

                }
        } else {
                // Inserta Notificación
                $nuevaNotificacion = mysql_query("INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_nota_seguimiento) VALUES ('".date('Y-m-d')."','".$id_aula."','aula','Escuela Infantil','La educadora ha introducido una nueva nota.',0,'".$id_nota."');");
        }
        */

}


$fecha_time_stamp=$dia;
$tipo_incidencias=$id_area;

include("tabla_cambios.php");

?>