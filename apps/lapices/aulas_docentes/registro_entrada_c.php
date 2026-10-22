<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_GET["id_registro"];
$hora=time();

$hora_entrada_c=date("H:i:s",$hora);

db_query("UPDATE servicios_consumidos SET hora_entrada_c='$hora_entrada_c' WHERE id_servicio_consumido=$id_registro") or dei(db_error());

// Comprueba si se ha registrado la salida de la mañana, en caso contrario se registra la hora de salida como la hora de entrada en el comedor
$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_servicio_consumido=$id_registro");
$datos_registro=db_fetch_array($busca_registro_entrada);
if ($datos_registro["hora_salida"]=="") {
        // Busca tipo de jornada alumno
        $busca_jornada_alumno=db_query("SELECT * FROM alumnos WHERE id_usuario=".$datos_registro["id_alumno"]);
        $datos_jornada_alumno=db_fetch_array($busca_jornada_alumno);
        $tipo_jornada=$datos_jornada_alumno["$datos_jornada_alumno"];
        if ($tipo_jornada==2) {
                db_query("UPDATE servicios_consumidos SET hora_salida='$hora_entrada_c' WHERE id_servicio_consumido=$id_registro") or die(db_error());
                }
        }

?>
<script language="Javascript">
window.close();
// self.opener.history.go(0);
</script>
