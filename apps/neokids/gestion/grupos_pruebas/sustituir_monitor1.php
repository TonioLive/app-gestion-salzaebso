<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$nombre_mostrar=$_POST["nombre_mostrar"];
$fecha=$_POST["fecha"];

$id_sustitucion=$_POST["id_sustitucion"];

$tipo_ausencia=$_POST["id_subgrupo"];
$id_monitorSel=$_POST["id_monitorSel"];
$sustituido_por=$_POST["sustituido_por"];
$desde=$_POST["desde"];
if ($desde) {

	$fecha_completa=explode("/",$desde);
	$fechadesde=$fecha_completa[2]."-".$fecha_completa[1]."-".$fecha_completa[0];

}
$hasta=$_POST["hasta"];
if ($hasta) {

	$fecha_completa=explode("/",$hasta);
	$fechahasta=$fecha_completa[2]."-".$fecha_completa[1]."-".$fecha_completa[0];

}
$observaciones=$_POST["observaciones"];


if ($id_sustitucion!=0) {

	// Guarda cambios
	$sustituye=db_query_params('UPDATE personal_asistencia_sustituciones SET id_grupo=? ,id_subgrupo=? ,tipo_ausencia=? ,sustituido_por=? ,desde=? ,hasta=? ,observaciones=? WHERE id_sustitucion=?;', 'ssssssss', array((string)($id_grupo), (string)($id_subgrupo), (string)($tipo_ausencia), (string)($sustituido_por), (string)($fechadesde), (string)($fechahasta), (string)($observaciones), $id_sustitucion));
	
} else {

	// Crea registro
	$sustituye=db_query_params('INSERT INTO personal_asistencia_sustituciones (id_grupo,id_subgrupo,tipo_ausencia,id_usuario,sustituido_por,desde,hasta,observaciones) VALUES (?,?,?,?,?,?,?,?);', 'ssssssss', array((string)($id_grupo), (string)($id_subgrupo), (string)($tipo_ausencia), (string)($id_monitorSel), (string)($sustituido_por), (string)($fechadesde), (string)($fechahasta), (string)($observaciones)));

}

?>
<script>
window.parent.closeModal();
window.parent.history.go(0);
</script>