<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$dia=$_POST["dia"];
$curso=$_POST["curso"];
$id_menu=$_POST["id_menu"];

                $comida1=$_POST["comida1"];
                $comida2=$_POST["comida2"];
                $comida3=$_POST["comida3"];
                $comida4=$_POST["comida4"];
                $comida5=$_POST["comida5"];
                $postre1=$_POST["postre1"];
                $merienda1=$_POST["merienda1"];
                $merienda2=$_POST["merienda2"];


$inserta_platos=db_query_params('UPDATE menu_calendario SET comida1=?,comida2=?,comida3=?,comida4=?,comida5=?,postre1=?,merienda1=?,merienda2=? WHERE id_dia=?;', 'sssssssss', array((string)($comida1), (string)($comida2), (string)($comida3), (string)($comida4), (string)($comida5), (string)($postre1), (string)($merienda1), (string)($merienda2), $id_dia_menu));

header("Location:editar_menu_dia.php?dia=$dia&curso=$curso&id_menu=$id_menu");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

