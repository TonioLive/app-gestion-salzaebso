<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$array_dias=$_POST["array_dias"];
$id_menu_destino=$_POST["id_menu_destino"];


$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_dia=$id_dia_menu");

$datos_platos_dia=db_fetch_array($busca_platos_dia);

$id_menu=$datos_platos_dia["id_menu"];
$comida1=$datos_platos_dia["comida1"];
$comida2=$datos_platos_dia["comida2"];
$comida3=$datos_platos_dia["comida3"];
$comida4=$datos_platos_dia["comida4"];
$comida5=$datos_platos_dia["comida5"];
$postre1=$datos_platos_dia["postre1"];
$merienda1=$datos_platos_dia["merienda1"];
$merienda2=$datos_platos_dia["merienda2"];
$cena=$datos_platos_dia["cena"];

for ($contador_dias=0;$contador_dias<count($array_dias);$contador_dias++) {

$inicio_time_stamp=$array_dias[$contador_dias];

// Comprueba si ese día hay platos
$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_time_stamp)."'") or die(db_error());
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_actual;") or die(db_error());
                } else {
                $inserta_platos=db_query("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES ('$id_menu_destino','".date("Y-m-d",$inicio_time_stamp)."','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2','$cena');") or die(db_error());
                }

}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.opener.location.href='calendario_menu.php?id_menu=<?=$id_menu_destino?>';
self.opener.close();
window.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

