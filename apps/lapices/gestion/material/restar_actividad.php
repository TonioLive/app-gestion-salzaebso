<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_actividad=$_GET["id_actividad"];

$busca_datos=db_query("SELECT * FROM servicios_contratados WHERE id_servicio_contratado=$id_actividad");

if (db_num_rows($busca_datos)!=0) {
        $datos=db_fetch_array($busca_datos);
        $cantidad_total=$datos["cantidad"]-1;
        if ($cantidad_total>0) {
                $actualiza=db_query("UPDATE servicios_contratados SET cantidad=$cantidad_total WHERE id_servicio_contratado=$id_actividad");
                } else {
                $borra=db_query("DELETE FROM servicios_contratados WHERE id_servicio_contratado=$id_actividad");
                }
        }

?>
<script language="Javascript">
// window.close();
self.opener.history.go(0);
</script>
