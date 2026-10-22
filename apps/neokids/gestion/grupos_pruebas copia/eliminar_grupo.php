<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
        
$id_grupo=$_POST["id_grupo"];
if (strlen($id_grupo)==0) {
        $id_grupo=$_GET["id_grupo"];
        }

$busca_datos_grupo=db_query_params('SELECT * FROM aulas_grupos WHERE id_grupo=?', 's', array($id_grupo));
	
	$datos_grupo=db_fetch_array($busca_datos_grupo);
	
	$id_dia=$datos_grupo["dia"];
	$id_curso=$datos_grupo["id_curso"];

$borra=db_query_params('DELETE FROM aulas_grupos WHERE id_grupo=?', 's', array($id_grupo)) or die(db_error());
$borra1=db_query_params('DELETE FROM aulas_grupos_subgrupos WHERE id_grupo=?', 's', array($id_grupo)) or die(db_error());
$borra2=db_query_params('DELETE FROM aulas_grupos_monitores WHERE id_grupo=?', 's', array($id_grupo)) or die(db_error());

	// reordena
	$orden_actual=1;
	$busca_por_orden=db_query_params('SELECT * FROM aulas_grupos WHERE dia=? AND id_curso=? ORDER BY hora_inicio, hora_fin', 'ss', array($id_dia, $id_curso));
	while($datos_orden=db_fetch_array($busca_por_orden)) {
		$actualiza_orden=db_query("UPDATE aulas_grupos SET orden=$orden_actual WHERE id_grupo=".$datos_orden["id_grupo"]);
		$orden_actual++;			
	}

?>
<script>
	self.opener.history.go(0);
	window.close();
</script>