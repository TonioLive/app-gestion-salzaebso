<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");


$campo_busqueda=$_POST["campo_busqueda"];
$valor_busqueda=$_POST["valor_busqueda"];
$tabla=$_POST["tabla"];
$campo=$_POST["campo"];
$valor_campo=$_POST["valor_campo"];

if (strlen($valor_campo)!=0) {
	$valor_campo=app_text_utf8($valor_campo);
	}

if (strlen($campo_busqueda)==0) {
$campo_busqueda=$_GET["campo_busqueda"];
$valor_busqueda=$_GET["valor_busqueda"];
$tabla=$_GET["tabla"];
$campo=$_GET["campo"];
$valor_campo=$_GET["valor_campo"];

if (strlen($valor_campo)!=0) {
	$valor_campo=app_text_utf8($valor_campo);
	}
	}

$fecha=date("Y-m-d",$dia);

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass") or die(db_error());
db_select_db("$sql_db",$db) or die(db_error());

$actualiza_dia=db_query("UPDATE $tabla SET $campo='$valor_campo' WHERE $campo_busqueda=$valor_busqueda");

db_close();

?>
