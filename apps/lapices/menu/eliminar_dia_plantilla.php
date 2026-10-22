<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
$accion=$_GET["accion"];
$id_dia=$_GET["id_dia"];
$dia=$_GET["dia"];
$semana=$_GET["semana"];
$id_menu=$_GET["id_menu"];
$plantilla=$_GET["plantilla"];

$borrar_articulo = db_query("DELETE FROM menu_calendario_plantilla WHERE id_dia=$id_dia") or die (db_error());

if ($accion==1) {
        header("Location: calendario_menu_plantilla.php?id_menu=$id_menu&mes_seleccionado=$mes_seleccionado&fecha=$fecha");
        }

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>


