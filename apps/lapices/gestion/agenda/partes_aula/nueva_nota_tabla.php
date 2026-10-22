<?php
require_once __DIR__ . '/../../../configuracion/encoding.php';
header("Content-Type: text/xml; charset=UTF-8");
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
	$nota=app_text_utf8($nota);
}
$centro_actual=$_GET["centro_aula"];

$id_area=2;
$avisar=0;
$tipo_nota=3;
$hora_nota=date("H:i");


$consulta_insert="INSERT INTO agenda_alumnos_seguimiento (id_aula,dia,id_alumno,area,introducida_por) VALUES ('$id_aula','".date("Y-m-d",$dia)."','$id_alumno','$id_area','$idUsuario')";
db_query($consulta_insert) or die(db_error());
$id_nota=db_insert_id();

$num_errores=0;

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query("UPDATE agenda_alumnos_seguimiento SET hora='$hora_nota', nota='".db_real_escape_string($nota)."', avisar='$avisar',tipo_nota='$tipo_nota' WHERE id=".$id_nota) or die(db_error()."-2");

        }


$fecha_time_stamp=$dia;
$tipo_incidencias=$id_area;

include("tabla_cambios.php");

?>