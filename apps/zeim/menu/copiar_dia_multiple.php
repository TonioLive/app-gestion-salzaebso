<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$array_dias=$_POST["array_dias"];
$id_menu_destino=$_POST["id_menu_destino"];


$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_dia=?', 's', array($id_dia_menu));

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
$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu_destino, (string)(date("Y-m-d", $inicio_time_stamp)))) or die(db_error());
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query_params('UPDATE menu_calendario SET comida1=?,comida2=?,comida3=?,comida4=?,comida5=?,postre1=?,merienda1=?,merienda2=?,cena=? WHERE id_dia=?;', 'ssssssssss', array((string)($comida1), (string)($comida2), (string)($comida3), (string)($comida4), (string)($comida5), (string)($postre1), (string)($merienda1), (string)($merienda2), (string)($cena), $id_dia_actual)) or die(db_error());
                } else {
                $inserta_platos=db_query_params('INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES (?,?,?,?,?,?,?,?,?,?,?);', 'sssssssssss', array((string)($id_menu_destino), (string)(date("Y-m-d", $inicio_time_stamp)), (string)($comida1), (string)($comida2), (string)($comida3), (string)($comida4), (string)($comida5), (string)($postre1), (string)($merienda1), (string)($merienda2), (string)($cena))) or die(db_error());
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

