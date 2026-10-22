<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$id_usuario=$_GET["Cod"];
$submenu_activo=$_GET["Submenu"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

//consultamos nombre de usuario
$datos_1=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario);
$rsdatos_1=db_fetch_array($datos_1);
$nombre_usuario=$rsdatos_1["usuario"];

// Consultamos eliminamos todas sus inscripciones

$datos_usuario=db_query("SELECT * FROM inscripciones WHERE id_alumno=".$id_usuario) or die(db_error()."-1");
while($rsdatos_usu=db_fetch_array($datos_usuario))
{
$codigo=$rsdatos_usu["id_inscripcion"];

// Borramos de la tabla inscripciones...
db_query("DELETE FROM inscripciones WHERE id_inscripcion=".$codigo) or die(db_error()."-2");

// ...los mensajes
db_query("DELETE FROM circulares WHERE Enviado_por='".$nombre_usuario."'") or die(db_error()."-3");
db_query("DELETE FROM registro_circulares WHERE id_destino=".$codigo) or die(db_error()."-4");

// ...los ejercicios
db_query("DELETE FROM resultados_Completos WHERE id_inscripcion=".$codigo) or die(db_error()."-5");
db_query("DELETE FROM resultados WHERE id_inscripcion=".$codigo) or die(db_error()."-6");

// ...las calificaciones
db_query("DELETE FROM calificaciones WHERE id_inscripcion=".$codigo) or die(db_error()."-7");

// ...sus consultas
db_query("DELETE FROM sugerencias_alumnos WHERE id_inscripcion=".$codigo) or die(db_error()."-8");

// ...y su diploma
db_query("DELETE FROM diplomas WHERE id_inscripcion=".$codigo) or die(db_error()."-9");

// Sus mensajes en la agenda
db_query("DELETE FROM agenda WHERE Nombre_usu='".$nombre_usuario."'") or die(db_error()."-10");

}

//Borramos sus inscripciones
db_query("DELETE FROM inscripciones WHERE id_alumno=".$id_usuario) or die(db_error()."-11");

//Borramos de la tabla de usuarios
db_query("DELETE FROM usuarios WHERE ID=".$id_usuario) or die(db_error()."-12");

db_close();

mt_srand((double)microtime()*1000000);
header("Location: "."gestion_u.php?Submenu=".$submenu_activo."&ran=".(mt_rand(0,10000000)/10000000));
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

