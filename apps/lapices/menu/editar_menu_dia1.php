<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$dia=$_POST["dia"];
$curso=$_POST["curso"];
$id_menu=$_POST["id_menu"];

if ($id_dia_menu==0) {
    // Busca si hay
    $busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$dia)."'");
    if (db_num_rows($busca_platos_dia)!=0) {
        $datos_platos_dia=db_fetch_array($busca_platos_dia);
        $id_dia_menu=$datos_platos_dia["id_dia"];
    }  else {
        $nuevo_dia=db_query("INSERT INTO menu_calendario (id_menu,fecha) VALUES ($id_menu,'".date("Y-m-d",$dia)."') ");
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


$inserta_platos=db_query("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_menu;");

header("Location:editar_menu_dia.php?dia=$dia&curso=$curso&id_menu=$id_menu");

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

