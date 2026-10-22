<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_docente.php");


$id_mensaje=$_GET["id_mensaje"];
$id_alumno=$_GET["id_alumno"];

    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE padre_de=?', 's', array($id_alumno));
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR respondido_por=".$padres["id_usuario"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	}

$marca_mensaje=db_query_params('UPDATE agenda_notas_padres SET estado=1, leido_por=CONCAT(leido_por,?) WHERE id_nota_padre=?', 'ss', array('[' . (string)($idUsuario) . ']', $id_mensaje));

$marca_respuestas_no_leidas=db_query("UPDATE agenda_notas_padres_respuestas SET leida=1, leido_por=CONCAT(leido_por,'[$idUsuario]') WHERE id_nota=".$id_mensaje." AND ($filtro_padres)") or die(db_error());

header("Location: ver_mensaje.php?id_alumno=$id_alumno&id_mensaje=$id_mensaje");

?>