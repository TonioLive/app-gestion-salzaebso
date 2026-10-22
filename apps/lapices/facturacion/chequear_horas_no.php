<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_usuario=$_POST["id_usuario"];
$id_alumno=$_POST["id_alumno"];
$mes_sel=$_POST["mes"];
$ano_sel=$_POST["ano"];
$id_aula=$_POST["id_aula"];

$consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$id_usuario." AND id_servicio=$id_aula AND month(fecha)=$mes_sel AND year(fecha)=$ano_sel;");
while($datos_asistencia=db_fetch_array($consulta_asistencia)) {

if ($_POST["no_facturar_".$datos_asistencia["id_servicio_consumido"]]=="1") {
        db_query("UPDATE servicios_consumidos SET no_facturar=1 WHERE id_servicio_consumido=".$datos_asistencia["id_servicio_consumido"]);
        } else {
        db_query("UPDATE servicios_consumidos SET no_facturar=0 WHERE id_servicio_consumido=".$datos_asistencia["id_servicio_consumido"]);
        }

}

header("Location: detalles_horas_extra.php?id_aula=$id_aula&id_alumno=$id_alumno&mes=$mes_sel&ano=$ano_sel");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

