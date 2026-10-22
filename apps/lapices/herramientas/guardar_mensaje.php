<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$destino[0]="$url_mensajes/mailing/enviar_mensaje_todos.php";
$destino[1]="vista_previa_mensaje.php";
$destino[2]="$url_mensajes/mailing/enviar_prueba.php";

$id_centro=$_POST["id_centro"];
$id_curso_formulario=$_POST["id_curso_formulario"];
$asunto=$_POST["asunto"];
$accion=$_POST["accion"];

$enviar_a_contactos=$_POST["enviar_a_contactos"];

$solo_activos=$_POST["solo_activos"];
if (strlen($solo_activos)==0) {
        $solo_activos=0;
        }


if (strlen($texto_editor)!=0) {
        $asunto=htmlentities($asunto);
        }
        
$texto_editor=$_POST["texto_editor"];

if (strlen($texto_editor)!=0) {
        $texto_editor=UTF8_encode($texto_editor);
        }

$cen=$_POST["cen"];
if (count($cen)!=0) {
        $array_cen=implode(",", $cen);
        } else {
        $array_cen=0;
        }
        
$ant=$_POST["ant"];
if (count($ant)!=0) {
        $array_ant=implode(",", $ant);
        } else {
        $array_ant=0;
        }
        

$aul=$_POST["aul"];

$retorno=$_POST["retorno"];

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_validacion);

$introduce_mensaje=db_query("INSERT INTO mensajes_temporal (asunto,texto_editor,id_centro,cen,aul,fecha,hora,retorno,enviado_por,id_curso,ant,solo_activos,enviar_a_contactos) VALUES ('$asunto','$texto_editor','$id_centro','$array_cen','$aul','".date("Y-m-d")."','".date("H:i:s")."','$retorno','$identificador','$id_curso_formulario','$array_ant','$solo_activos','$enviar_a_contactos');") or die(db_error());

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
