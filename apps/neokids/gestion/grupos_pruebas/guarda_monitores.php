<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];

$monitor1=$_POST["monitor1"];
$monitor2=$_POST["monitor2"];
$monitor3=$_POST["monitor3"];
$monitor4=$_POST["monitor4"];

$actualiza1=db_query_params('UPDATE aulas_grupos_monitores SET id_usuario=? WHERE num_monitor=? AND id_grupo=? AND id_subgrupo=?','iiii',array((int)$monitor1,1,(int)$id_grupo,(int)$id_subgrupo)) or die(db_error());
$actualiza2=db_query_params('UPDATE aulas_grupos_monitores SET id_usuario=? WHERE num_monitor=? AND id_grupo=? AND id_subgrupo=?','iiii',array((int)$monitor2,2,(int)$id_grupo,(int)$id_subgrupo)) or die(db_error());
$actualiza3=db_query_params('UPDATE aulas_grupos_monitores SET id_usuario=? WHERE num_monitor=? AND id_grupo=? AND id_subgrupo=?','iiii',array((int)$monitor3,3,(int)$id_grupo,(int)$id_subgrupo)) or die(db_error());
$actualiza4=db_query_params('UPDATE aulas_grupos_monitores SET id_usuario=? WHERE num_monitor=? AND id_grupo=? AND id_subgrupo=?','iiii',array((int)$monitor4,4,(int)$id_grupo,(int)$id_subgrupo)) or die(db_error());

$descripcion_evento=$_POST["descripcion_evento"];

$actualiza_capacidad=db_query_params('UPDATE aulas_grupos_subgrupos SET capacidad=?,tipo_subgrupo=?,descripcion_evento=? WHERE id_grupo=? AND id_subgrupo=?','iisii',array((int)app_value($_POST,'capacidad'),(int)app_value($_POST,'tipo_subgrupo'),$descripcion_evento,(int)$id_grupo,(int)$id_subgrupo)) or die(db_error());

?>
<script>
window.opener.history.go(0);
window.close();
</script>