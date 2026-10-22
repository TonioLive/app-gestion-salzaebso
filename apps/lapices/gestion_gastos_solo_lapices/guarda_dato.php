<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$fila=$_POST["fila"];
$nombre_fila=$_POST["nombre_fila"];
$tabla=$_POST["tabla"];
$campo=$_POST["campo"];
$es_checkbox=$_POST["es_checkbox"];
$valor_modificado=$_POST["valor"];

if ($es_checkbox=="1") {
	// Consulta valor actual
	$busca_dato=db_query("SELECT * FROM $tabla WHERE $nombre_fila=$fila");
	if (db_num_rows($busca_dato)!=0) {
		$dato_actual=db_fetch_array($busca_dato);
		if ($dato_actual[$campo]==1) {
			$valor_modificado=0;
			} else {
			$valor_modificado=1;
			}
		}
	}

if (strlen($valor_modificado)!=0) {

	$cambia_linea=db_query("UPDATE $tabla SET $campo=$valor_modificado WHERE $nombre_fila=$fila") or die(db_error());

	}
?>