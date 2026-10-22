<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prog=$_POST["id_prog"];
$id_objetivos=$_POST["id_objetivos"];
$capa_activa=$_POST["capa_activa"];

$cadena_obj="";

for($c_obj=0;$c_obj<count($id_objetivos);$c_obj++) {
	$cadena_obj.=$id_objetivos[$c_obj]."],[";
	}

if (strlen($cadena_obj)!=0) {
	$cadena_obj="[".$cadena_obj;
	$cadena_obj=substr($cadena_obj, 0, strlen($cadena_obj)-2);
	}

$cambia_linea=db_query_params('UPDATE agenda_programacion_ingles SET objetivos=? WHERE id_prog=?', 'ss', array((string)($cadena_obj), $id_prog)) or die(db_error());

$focuson="objetivo_".$id_prog;

$busca_datos_programacion=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE id_prog=?', 's', array($id_prog));

$datos_programacion=db_fetch_array($busca_datos_programacion);

$id_aula=$datos_programacion["id_aula"];
$fecha_inicio=$datos_programacion["fecha"];

?>
<script languaje="Javascript">
self.opener.location.href='semana_programacion.php?id_aula=<?=$id_aula?>&fecha_inicio=<?=$fecha_inicio;?>&pagina_activa=<?=$capa_activa?>&focuson=<?=$focuson?>';
window.close();
</script>
