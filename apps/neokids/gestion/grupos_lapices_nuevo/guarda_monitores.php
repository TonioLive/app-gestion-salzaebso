<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];

$monitor1=$_POST["monitor1"];
$monitor2=$_POST["monitor2"];
$monitor3=$_POST["monitor3"];
$monitor4=$_POST["monitor4"];

$actualiza1=db_query("UPDATE aulas_grupos_monitores SET id_usuario=$monitor1 WHERE num_monitor=1 AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo") or die(db_error());
$actualiza2=db_query("UPDATE aulas_grupos_monitores SET id_usuario=$monitor2 WHERE num_monitor=2 AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo") or die(db_error());
$actualiza3=db_query("UPDATE aulas_grupos_monitores SET id_usuario=$monitor3 WHERE num_monitor=3 AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo") or die(db_error());
$actualiza4=db_query("UPDATE aulas_grupos_monitores SET id_usuario=$monitor4 WHERE num_monitor=4 AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo") or die(db_error());
?>
<script>
window.opener.history.go(0);
window.close();
</script>