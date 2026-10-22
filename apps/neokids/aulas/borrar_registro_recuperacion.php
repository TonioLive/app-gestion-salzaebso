<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

$id_falta=$_POST["id_falta"];
if (strlen($id_falta)==0) {
        $id_falta=$_GET["id_falta"];
        }

// Comprueba si ya ha sido introucido
$actualiza=db_query_params('UPDATE servicios_consumidos_faltas SET hora_entrada=null, hora_salida=null WHERE id_falta=?', 's', array($id_falta));

?>
<script>
self.opener.history.go(0);
window.close();
</script>
