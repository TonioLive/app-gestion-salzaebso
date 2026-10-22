<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$destino[0]="/mailing/enviar_mensaje_todos.php";
$destino[1]="vista_previa_mensaje.php";
$destino[2]="/mailing/enviar_prueba.php";

$id_centro=$_POST["id_centro"];
$id_curso_formulario=$_POST["id_curso_formulario"];
$contactos=$_POST["contactos"];
$asunto=$_POST["asunto"];
$accion=$_POST["accion"];

if (strlen($texto_editor)!=0) {
        $asunto=UTF8_encode($asunto);
        }
        
$texto_editor=db_real_escape_string($_POST["texto_editor"]);

if (strlen($texto_editor)!=0) {
        $texto_editor=UTF8_encode($texto_editor);
        }

$cen=$_POST["cen"];
if (count($cen)!=0) {
        $array_cen=implode(",", $cen);
        } else {
        $array_cen=0;
        }

/* Antiguos alumnos NO ¡¡¡PELIGRO!!!
$ant=$_POST["ant"];
if (count($ant)!=0) {
        $array_ant=implode(",", $ant);
        } else {
        $array_ant=0;
        }
*/
        

$aul=$_POST["aul"];

$retorno=$_POST["retorno"];

$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_validacion);

$introduce_mensaje=db_query("INSERT INTO mensajes_temporal (asunto,texto_editor,id_centro,cen,aul,fecha,hora,retorno,enviado_por,id_curso,ant,contactos) VALUES ('$asunto','$texto_editor','$id_centro','$array_cen','$aul','".date("Y-m-d")."','".date("H:i:s")."','$retorno','$identificador','$id_curso_formulario','$array_ant','$contactos');") or die(db_error());

$id_nuevo_mensaje=db_insert_id();

db_close();


// Retorno
header("Location: ".$destino[$accion]."?id=$id_nuevo_mensaje");


?>
<html>
<head>
</head>
<body>
</body>
</html>
