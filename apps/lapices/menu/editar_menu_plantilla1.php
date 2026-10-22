<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$dia=$_POST["dia"];
$semana=$_POST["semana"];
$id_menu=$_POST["id_menu"];
$plantilla=$_POST["plantilla"];

if ($id_dia_menu==0) {
    // Busca si hay
    $busca_platos_dia=db_query("SELECT * FROM menu_calendario_plantilla WHERE id_menu=$id_menu AND plantilla=$plantilla AND dia=".$dia." AND semana=".$semana);
    if (db_num_rows($busca_platos_dia)!=0) {
        $datos_platos_dia=db_fetch_array($busca_platos_dia);
        $id_dia_menu=$datos_platos_dia["id_dia"];
    }  else {
        $nuevo_dia=db_query("INSERT INTO menu_calendario_plantilla (id_menu,plantilla,dia,semana) VALUES ($id_menu,$plantilla,".$dia.", ".$semana.") ");
        $id_dia_menu=db_insert_id();
    } 
}
                $comida1=$_POST["comida1"];
                $comida2=$_POST["comida2"];
                $comida3=$_POST["comida3"];
                $comida4=$_POST["comida4"];
                $comida5=$_POST["comida5"];
                $postre1=$_POST["postre1"];
                $merienda1=$_POST["merienda1"];
                $merienda2=$_POST["merienda2"];
                $cena=$_POST["cena"];


$inserta_platos=db_query("UPDATE menu_calendario_plantilla SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_menu;");

header("Location:editar_menu_plantilla.php?dia=$dia&semana=$semana&id_menu=$id_menu");

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

