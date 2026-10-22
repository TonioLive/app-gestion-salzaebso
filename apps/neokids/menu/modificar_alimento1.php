<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_alimento=$_POST["id_alimento"];
$nombre_alimento=$_POST["nombre_alimento"];
if (strlen($nombre_alimento)!=0) {
        $nombre_alimento=urlencode($nombre_alimento);
        }
$kcal_gramo=$_POST["kcal_gramo"];
$hc_gramo=$_POST["hc_gramo"];
$prot_gramo=$_POST["prot_gramo"];
$grasas_gramo=$_POST["grasas_gramo"];
$unidad_medida=$_POST["unidad_medida"];
$peso_unidad=$_POST["peso_unidad"];

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_valores_alimentos=array();
$string_alimentos="";

$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten");
$array_valores_alergias=array();
$string_alergias="";


for ($c=0;$c<count($array_alimentos);$c++) {
                        $array_valores_alimentos[$c]=$_POST[$array_alimentos[$c]];
                        if ($array_valores_alimentos[$c]==1) {
                                $array_valores_alimentos[$c]=1;
                                } else {
                                $array_valores_alimentos[$c]=0;
                                }
                        $string_alimentos=$string_alimentos.$array_alimentos[$c]."=".$array_valores_alimentos[$c].",";
                        }

for ($c=0;$c<count($array_alergias);$c++) {
                        $array_valores_alergias[$c]=$_POST[$array_alergias[$c]];
                        if ($array_valores_alergias[$c]==1) {
                                $array_valores_alergias[$c]=1;
                                } else {
                                $array_valores_alergias[$c]=0;
                                }
                        $string_alergias=$string_alergias.$array_alergias[$c]."=".$array_valores_alergias[$c].",";
                        }

        if ($id_alimento==0) {
        $inserta_alimento=db_query_params('INSERT INTO menu_alimentos (nombre_alimento) VALUES (?)', 's', array((string)($nombre_alimento)));
        $id_alimento=db_insert_id();
        }
        
        $nuevo_articulo = db_query("UPDATE menu_alimentos SET $string_alimentos $string_alergias nombre_alimento='$nombre_alimento', kcal_gramo='$kcal_gramo', hc_gramo='$hc_gramo', prot_gramo='$prot_gramo', grasas_gramo='$grasas_gramo', unidad_medida='$unidad_medida', peso_unidad='$peso_unidad' WHERE id_alimento=$id_alimento") or die (db_error());

        
        header("Location:gestion_alimentos.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

