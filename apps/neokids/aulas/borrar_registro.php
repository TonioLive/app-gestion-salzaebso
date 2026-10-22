<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

$id_registro=$_POST["id_registro"];
if (strlen($id_registro)==0) {
        $id_registro=$_GET["id_registro"];
        }

// Comprueba si ya ha sido introucido
$actualiza=db_query_params('DELETE FROM servicios_consumidos WHERE id_servicio_consumido=?', 's', array($id_registro));

?>
<script>
self.opener.history.go(0);
window.close();
</script>
