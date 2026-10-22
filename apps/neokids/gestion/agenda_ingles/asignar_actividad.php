<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prog=$_GET["id_prog"];
$id_actividad=$_GET["id_actividad"];

if ($id_actividad!=0) {
        $busca_actividad=db_query_params('SELECT * FROM agenda_programacion_ingles_actividades WHERE id_actividad=?', 's', array($id_actividad)) or die(db_error());
        $datos_actividad=db_fetch_array($busca_actividad);
        $descripcion_actividad=$datos_actividad["descripcion_actividad"];
        } else {
        $descripcion_actividad="";
        }

$cambia_linea=db_query_params('UPDATE agenda_programacion_ingles SET id_actividad=?,actividad=? WHERE id_prog=?', 'sss', array((string)($id_actividad), (string)($descripcion_actividad), $id_prog)) or die(db_error());

?>
<script languaje="Javascript">
self.opener.history.go(0);
window.close();
</script>
