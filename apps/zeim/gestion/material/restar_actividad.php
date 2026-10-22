<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_actividad=$_GET["id_actividad"];

$busca_datos=db_query_params('SELECT * FROM servicios_contratados WHERE id_servicio_contratado=?', 's', array($id_actividad));

if (db_num_rows($busca_datos)!=0) {
        $datos=db_fetch_array($busca_datos);
        $cantidad_total=$datos["cantidad"]-1;
        if ($cantidad_total>0) {
                $actualiza=db_query_params('UPDATE servicios_contratados SET cantidad=? WHERE id_servicio_contratado=?', 'ss', array($cantidad_total, $id_actividad));
                } else {
                $borra=db_query_params('DELETE FROM servicios_contratados WHERE id_servicio_contratado=?', 's', array($id_actividad));
                }
        }

?>
<script language="Javascript">
// window.close();
self.opener.history.go(0);
</script>
