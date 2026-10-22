<?php

require("../configuracion/config.php");

$fecha=$_POST["dia"];
$id_centro=$_POST["id_centro"];

$array_temperaturas=array();

foreach ($_POST as $param_name => $param_val) {
	
	if (substr($param_name,0,12)=="temperatura_") {
		
		$id_plato=str_replace("temperatura_","",$param_name);
		
		$array_temperaturas[$id_plato]=$param_val;
		
	}

}
	
$string_array_temperaturas=serialize($array_temperaturas);		

$fecha_albaran=date("Y-m-d",$fecha);

$busca_alabaran=db_query("SELECT * FROM albaranes_entrega_comida WHERE fecha='".$fecha_albaran."' AND id_centro=".$id_centro.";");

if (db_num_rows($busca_alabaran)!=0) {
	
	// actualiza
	$actualiza=db_query("UPDATE albaranes_entrega_comida SET temperaturas_entrada='".$string_array_temperaturas."' WHERE fecha='".$fecha_albaran."';");

} else {
	
	// Introduce
	$actualiza=db_query("INSERT INTO albaranes_entrega_comida (temperaturas_entrada, fecha, id_centro) VALUES ('".$string_array_temperaturas."', '".$fecha_albaran."', '".$id_centro."');");
	
}

header("Location: albaranes_entrega_platos_firma.php?id_centro=".$id_centro."&dia=".$fecha);

?>
