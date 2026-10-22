<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$observaciones=$_POST["observaciones"];
$medidas_adoptadas=$_POST["medidas_adoptadas"];
$resuelta=$_POST["resuelta"];
$comedor=$_POST["comedor"];
$hora_e=$_POST["hora_e"];
$min_e=$_POST["min_e"];
$seg_e=$_POST["seg_e"];
$hora_s=$_POST["hora_s"];
$min_s=$_POST["min_s"];
$seg_s=$_POST["seg_s"];


$hora_e_c=$_POST["hora_e_c"];
$min_e_c=$_POST["min_e_c"];
$seg_e_c=$_POST["seg_e_c"];
$hora_s_c=$_POST["hora_s_c"];
$min_s_c=$_POST["min_s_c"];
$seg_s_c=$_POST["seg_s_c"];

if (($hora_e_c=="00") and ($min_e_c=="00") and ($seg_e_c=="00") and ($hora_s_c=="00") and ($min_s_c=="00") and ($seg_s_c=="00")) {
        db_query("UPDATE servicios_consumidos SET tipo_menu=$comedor, hora_entrada='".$hora_e.":".$min_e.":".$seg_e."',hora_salida='".$hora_s.":".$min_s.":".$seg_s."',observaciones='$observaciones', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta WHERE id_servicio_consumido=$id_registro") or die(db_error());
        } else {
        // echo("UPDATE servicios_consumidos SET tipo_menu=$comedor, hora_entrada_c='".$hora_e_c.":".$min_e_c.":".$seg_e_c."',hora_salida_c='".$hora_s_c.":".$min_s_c.":".$seg_s_c."',hora_entrada='".$hora_e.":".$min_e.":".$seg_e."',hora_salida='".$hora_s.":".$min_s.":".$seg_s."',observaciones='$observaciones', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta WHERE id_servicio_consumido=$id_registro");
        db_query("UPDATE servicios_consumidos SET tipo_menu=$comedor, hora_entrada_c='".$hora_e_c.":".$min_e_c.":".$seg_e_c."',hora_salida_c='".$hora_s_c.":".$min_s_c.":".$seg_s_c."',hora_entrada='".$hora_e.":".$min_e.":".$seg_e."',hora_salida='".$hora_s.":".$min_s.":".$seg_s."',observaciones='$observaciones', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta WHERE id_servicio_consumido=$id_registro") or die(db_error());
        }
        
// header("Location: gestion_incidencias.php?c=".$resuelta);
?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
