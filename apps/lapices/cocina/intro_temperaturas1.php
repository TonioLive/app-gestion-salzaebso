<?php

require("../configuracion/config.php");

$fecha=$_POST["dia"];
$hora_salida=$_POST["hora_salida"];
$hora_temperatura=$_POST["hora_temperatura"];
$hora_temperatura_fria=$_POST["hora_temperatura_fria"];

$array_temperaturas=array();
$array_numescuelas=array();
$array_nummenus=array();
$array_comensales=array();
$array_alergias=array();
$array_lotes=array();

foreach ($_POST as $param_name => $param_val) {
	
	if (substr($param_name,0,12)=="temperatura_") {
		
		$id_plato=str_replace("temperatura_","",$param_name);
		
		$array_temperaturas[$id_plato]=$param_val;
		
	}

	if (substr($param_name,0,12)=="numescuelas_") {
		
		$id_plato=str_replace("numescuelas_","",$param_name);
		
		$array_numescuelas[$id_plato]=$param_val;
		
	}
	
	if (substr($param_name,0,9)=="nummenus_") {
		
		$id_plato=str_replace("nummenus_","",$param_name);
		
		$array_nummenus[$id_plato]=$param_val;
		
	}	

	if (substr($param_name,0,11)=="comensales_") {
		
		$id_plato=str_replace("comensales_","",$param_name);
		
		$array_comensales[$id_plato]=app_safe_unserialize($param_val);
		
	}	
	
	if (substr($param_name,0,9)=="alergias_") {
		
		$id_plato=str_replace("alergias_","",$param_name);
		
		$array_alergias[$id_plato]=app_safe_unserialize($param_val);
		
	}	

	if (substr($param_name,0,20)=="comensales_alergias_") {
		
		$id_plato=str_replace("comensales_alergias_","",$param_name);
		
		$array_comensales_alergias[$id_plato]=app_safe_unserialize($param_val);
		
	}

	if (substr($param_name,0,5)=="lote_") {
		
			$array_lotes[$param_name]=$param_val;
	
	}

}

$string_array_temperaturas=serialize($array_temperaturas);	
$string_array_numescuelas=serialize($array_numescuelas);
$string_array_nummenus=serialize($array_nummenus);
$string_array_comensales=serialize($array_comensales);		
$string_array_alergias=serialize($array_alergias);
$string_array_comensales_alergias=serialize($array_comensales_alergias);
$string_array_lotes=serialize($array_lotes);	

$fecha_albaran=date("Y-m-d",$fecha);

$busca_alabaran=db_query("SELECT * FROM cocina_albaranes_entrega WHERE fecha='".$fecha_albaran."';");

if (db_num_rows($busca_alabaran)!=0) {
	
	// actualiza
	$actualiza=db_query("UPDATE cocina_albaranes_entrega SET hora_salida='".$hora_salida."', hora_temperatura='".$hora_temperatura."',hora_temperatura_fria='".$hora_temperatura_fria."', temperaturas_salida='".$string_array_temperaturas."', lotes_ingredientes='".$string_array_lotes."', numescuelas='".$string_array_numescuelas."', comensales='".$string_array_comensales."', alergias='".$string_array_alergias."', comensales_alergias='".$string_array_comensales_alergias."', nummenus='".$string_array_nummenus."' WHERE fecha='".$fecha_albaran."';");

} else {
	
	// Introduce
	$actualiza=db_query("INSERT INTO cocina_albaranes_entrega (hora_salida, hora_temperatura, hora_temperatura_fria, temperaturas_salida, lotes_ingredientes, fecha, numescuelas, comensales, alergias, comensales_alergias, nummenus) VALUES ('".$hora_salida."', '".$hora_temperatura."', '".$hora_temperatura_fria."','".$string_array_temperaturas."', '".$string_array_lotes."', '".$fecha_albaran."', '".$string_array_numescuelas."', '".$string_array_comensales."', '".$string_array_alergias."', '".$string_array_comensales_alergias."', '".$string_array_nummenus."');");
	
}

header("Location: albaranes_entrega_platos.php?dia=".$fecha);

?>
