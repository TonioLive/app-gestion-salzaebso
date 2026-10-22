<?php 
require("configuracion/config.php");

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_validacion);

db_query("UPDATE sesion SET Fecha_cierre='".date("Y/m/d")."', Cerrada=1, Hora_cierre='".date("H:i")."' WHERE id_session='".session_id()."'") or die(db_error());

session_destroy();

header("Location: ".$url_root."/index.php");
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="GENERATOR" content="Microsoft FrontPage 3.0">
<meta name="Microsoft Border" content="none">
<title></title>
<script languaje="JavaScript">
//
window.close()
//->
</script>
</head>
<body topmargin="0" leftmargin="0">
</body>
</html>

