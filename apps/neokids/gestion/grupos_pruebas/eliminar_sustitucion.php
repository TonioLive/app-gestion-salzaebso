<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$nombre_mostrar=utf8_decode($_GET["nombre_mostrar"]);

$id_sustitucion=$_GET["id_sustitucion"];

if ($id_sustitucion!=0) {

	// Guarda cambios
	$sustituye=db_query_params('DELETE FROM personal_asistencia_sustituciones WHERE id_sustitucion=?;', 's', array($id_sustitucion));
	
}

?>
<script>
window.parent.closeModal();
</script>