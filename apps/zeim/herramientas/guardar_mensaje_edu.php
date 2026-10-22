<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_validacion);

$destino[0]="$url_mensajes/mailing/enviar_mensaje_todos_edu.php";
$destino[1]="vista_previa_mensaje.php";
$destino[2]="$url_mensajes/mailing/enviar_prueba.php";

$destino1="";
$busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
while($datos_escuela=db_fetch_array($busca_centros)) {
        $edu=$_POST["edu_".$datos_escuela["id_centro"]];
        if ($edu==1) {
                $destino1=$destino1." OR id_centro=".$datos_escuela["id_centro"];
                }
        }
$destino2="";
$busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
while($datos_escuela=db_fetch_array($busca_centros)) {
        $coo=$_POST["coo_".$datos_escuela["id_centro"]];
        if ($coo==1) {
                $destino2=$destino2." OR id_centro=".$datos_escuela["id_centro"];
                }
        }

$destino3=$_POST["adm"];

$asunto=$_POST["asunto"];
$accion=$_POST["accion"];

if (strlen($texto_editor)!=0) {
        $asunto=htmlentities($asunto);
        }
        
$texto_editor=db_real_escape_string($_POST["texto_editor"]);

if (strlen($texto_editor)!=0) {
        $texto_editor=UTF8_encode($texto_editor);
        }

$cen=$_POST["cen"];
$aul=$_POST["aul"];
$retorno=$_POST["retorno"];

$introduce_mensaje=db_query("INSERT INTO mensajes_temporal (asunto,texto_editor,id_centro,fecha,hora,retorno,destino1,destino2,destino3,enviado_por) VALUES ('$asunto','$texto_editor','$id_centro','".date("Y-m-d")."','".date("H:i:s")."','$retorno','$destino1','$destino2','$destino3','$identificador');") or die(db_error());

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
