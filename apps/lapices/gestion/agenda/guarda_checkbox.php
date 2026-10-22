<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$fila_modificada=$_POST["fila"];
$campo_modificado=$_POST["campo"];

// busca valor actual
$busca_valor=db_query("SELECT * FROM agenda_programacion WHERE id_prog=$fila_modificada");

if (db_num_rows($busca_valor)!=0) {
	$valor=db_fetch_array($busca_valor);
	$valor_actual=$valor[$campo_modificado];
	
	if ($valor_actual==0) {
		$valor_nuevo=1;	
		} else {
		$valor_nuevo=0;
		}

	$cambia_linea=db_query("UPDATE agenda_programacion SET $campo_modificado=$valor_nuevo WHERE id_prog=$fila_modificada") or die(db_error());

	}

?>
