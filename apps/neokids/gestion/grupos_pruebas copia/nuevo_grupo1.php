<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
 
$id_dia=$_POST["id_dia"];
if (strlen($id_dia)==0) {
        $id_dia=$_GET["id_grupo"];
        } 
 
$id_curso=$_POST["id_curso"];
if (strlen($id_curso)==0) {
        $id_curso=$_GET["id_curso"];
        }
        
$id_grupo=$_POST["id_grupo"];
if (strlen($id_grupo)==0) {
        $id_grupo=$_GET["id_grupo"];
        }

$hora_inicio=$_POST["hora_i"].":".$_POST["min_i"].":00";
$hora_fin=$_POST["hora_f"].":".$_POST["min_f"].":00";

if (strlen($id_grupo)!=0) {

	$actualiza=db_query_params('UPDATE aulas_grupos SET hora_inicio=?, hora_fin=? WHERE id_grupo=?', 'sss', array((string)($hora_inicio), (string)($hora_fin), $id_grupo)) or die(db_error());	
		
} else {	
	
	$nuevo=db_query_params('INSERT INTO aulas_grupos (dia,hora_inicio,hora_fin,id_curso) VALUES (?,?,?,?);', 'ssss', array((string)($id_dia), (string)($hora_inicio), (string)($hora_fin), (string)($id_curso)));
	
	// reordena
	$orden_actual=1;
	$busca_por_orden=db_query_params('SELECT * FROM aulas_grupos WHERE dia=? AND id_curso=? ORDER BY hora_inicio, hora_fin', 'ss', array($id_dia, $id_curso));
	while($datos_orden=db_fetch_array($busca_por_orden)) {
		$actualiza_orden=db_query("UPDATE aulas_grupos SET orden=$orden_actual WHERE id_grupo=".$datos_orden["id_grupo"]);
		$orden_actual++;			
	}
		
}

?>
<script>
	self.opener.history.go(0);
	window.close();
</script>
