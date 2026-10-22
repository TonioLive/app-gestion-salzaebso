<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$id_usu_lista=$_GET["id_n"];

$busca_datos_lista=db_query("SELECT * FROM lista_espera WHERE id_usu_lista=$id_n");

$datos_lista=db_fetch_array($busca_datos_lista);

$edad_lista=$datos_lista["edad_espera"];
$curso_seleccionado=$datos_lista["id_curso"];

$borrar_articulo1 = db_query("DELETE FROM lista_espera WHERE id_usu_lista=".$id_usu_lista) or die (db_error());
$borrar_articulo2 = db_query("DELETE FROM lista_espera_respuestas WHERE id_usu_lista=".$id_usu_lista) or die (db_error());


header("Location: lista_espera.php?edad_lista=$edad_lista&id_curso_lista=$curso_seleccionado");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

