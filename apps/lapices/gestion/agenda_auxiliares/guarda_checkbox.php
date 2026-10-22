<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$campo_busqueda=$_POST["campo_busqueda"];
$valor_busqueda=$_POST["valor_busqueda"];
$tabla=$_POST["tabla"];
$campo=$_POST["campo"];

/*
$campo_busqueda=$_GET["campo_busqueda"];
$valor_busqueda=$_GET["valor_busqueda"];
$tabla=$_GET["tabla"];
$campo=$_GET["campo"];
*/

$fecha=date("Y-m-d",$dia);

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass") or die(db_error());
db_select_db("$sql_db",$db) or die(db_error());


// Comprueba el estado actual
$busca_estado=db_query("SELECT * FROM $tabla WHERE $campo_busqueda=$valor_busqueda");
$datos_estado=db_fetch_array($busca_estado);

$estado_actual=$datos_estado[$campo];

if ($estado_actual==1) {
        $valor_campo=0;
        } else {
        $valor_campo=1;
        }

$actualiza_dia=db_query("UPDATE $tabla SET $campo='$valor_campo' WHERE $campo_busqueda=$valor_busqueda");

db_close();

?>