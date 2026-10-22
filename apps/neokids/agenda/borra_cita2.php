<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$origen_cita=$_GET["oc"];
$idcita=$_GET["idCita"];
$id_curso=$_GET["id_curso"];

$db_cc=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_cc);

$consulta_cita=db_query("SELECT * FROM Agenda WHERE id=".$idcita) or die (db_error());

if (db_num_rows($consulta_cita)!=0) {

 $rscita=db_fetch_array($consulta_cita);
 $fecha_time_stamp=strtotime($rscita["Fecha"]);
 db_free_result($consulta_cita);
 }
 
$consulta_cita2=db_query("DELETE FROM Agenda WHERE id=".$idcita) or die (db_error());
$consulta_cita2=db_query("DELETE FROM agenda_asistentes WHERE id_cita=".$idcita) or die (db_error());

db_close($db_cc);

  if ($origen_cita!=1) {
        header("Location: ".$url_web."/actividades.php?dia=".$fecha_time_stamp."&id=".(mt_rand(0,10000000)/10000000)."&id_curso=".$id_curso);
        } else {
        ?>
<script language="Javascript">
self.opener.actualizarCitas();
window.close();
</script>
        <?php 
        }
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
</style>
<script language="javascript">
window.opener.actualizarCitas();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
scroll="auto">
</body>
</html>

