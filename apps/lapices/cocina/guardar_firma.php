<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$dia = $_GET["dia"];
$id_centro = $_GET["id_centro"];
$observaciones = db_real_escape_string($_GET["observaciones"]);
$firmaRecibi = $_POST["firmaRecibi"];

$resultado="Error: No se ha recibido ninguna firma.";

// Busca firma
$buscaFirma=db_query("SELECT * FROM albaranes_entrega_comida WHERE fecha='".date("Y-m-d",$dia)."' AND id_centro=".$id_centro.";");

if (db_num_rows($buscaFirma)!=0) {
    // Modifica
    $datosFirma=db_fetch_array($buscaFirma);

    $modificaFirma=db_query("UPDATE albaranes_entrega_comida SET fecha_entrega='".date('Y-m-d')."', hora_entrega='".date("Y-m-d H:i:s")."', firma='".$firmaRecibi."', observaciones='".$observaciones."' WHERE id_albaran=".$datosFirma["id_albaran"].";") or die(db_error());
    $resultado="OK: La firma ha sido registrada.";

} else {
    // Crea
    $creaFirma=db_query("INSERT INTO albaranes_entrega_comida (fecha,id_centro,fecha_entrega,hora_entrega,firma,observaciones) VALUES ('".date("Y-m-d",$dia)."','".$id_centro."','".date('Y-m-d')."','".date("Y-m-d H:i:s")."','".$firmaRecibi."','".$observaciones."');") or die(db_error());
    $resultado="OK: La firma ha sido registrada.";

}

exit($resultado);
?>
