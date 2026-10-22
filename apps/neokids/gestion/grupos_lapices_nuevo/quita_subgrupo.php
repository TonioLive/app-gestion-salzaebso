<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
        
$grupo=$_POST["id_grupo"];
if (strlen($grupo)==0) {
        $grupo=$_GET["id_grupo"];
        }

$actualiza=db_query("UPDATE aulas_grupos SET num_subgrupos=(num_subgrupos-1) WHERE id_grupo=".$grupo) or die(db_error());

// busca datos subgrupo

$busca_subgrupo=db_query("SELECT * FROM aulas_grupos WHERE id_grupo=".$grupo);

$datos_grupo=db_fetch_array($busca_subgrupo);

$datos_subgrupo=db_query("DELETE FROM aulas_grupos_subgrupos WHERE id_grupo=".$grupo." AND id_subgrupo=".($datos_grupo["num_subgrupos"]+1).";");

header("Location: index.php");

?>
