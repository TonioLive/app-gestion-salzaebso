<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_POST["id_dia_menu"];
$dia=$_POST["dia"];
$curso=$_POST["curso"];
$id_menu=$_POST["id_menu"];

$numero_rep=$_POST["numero_rep"];
$intervalo=$_POST["intervalo"];

$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $dia))));

$datos_platos_dia=db_fetch_array($busca_platos_dia);

$comida1=$datos_platos_dia["comida1"];
$comida2=$datos_platos_dia["comida2"];
$comida3=$datos_platos_dia["comida3"];
$comida4=$datos_platos_dia["comida4"];
$comida5=$datos_platos_dia["comida5"];
$postre1=$datos_platos_dia["postre1"];
$merienda1=$datos_platos_dia["merienda1"];
$merienda2=$datos_platos_dia["merienda2"];
$cena=$datos_platos_dia["cena"];

$inicio_time_stamp=$dia+7200;

$incremento=(86400*$intervalo);

for ($contador_repeticiones=0;$contador_repeticiones<$numero_rep;$contador_repeticiones++) {
$inicio_time_stamp=$inicio_time_stamp+$incremento;
// Comprueba si ese día hay platos
$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $inicio_time_stamp))));
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query_params('UPDATE menu_calendario SET comida1=?,comida2=?,comida3=?,comida4=?,comida5=?,postre1=?,merienda1=?,merienda2=?,cena=? WHERE id_dia=?;', 'ssssssssss', array((string)($comida1), (string)($comida2), (string)($comida3), (string)($comida4), (string)($comida5), (string)($postre1), (string)($merienda1), (string)($merienda2), (string)($cena), $id_dia_actual));
                } else {
                $inserta_platos=db_query_params('INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES (?,?,?,?,?,?,?,?,?,?,?);', 'sssssssssss', array((string)($id_menu), (string)(date("Y-m-d", $inicio_time_stamp)), (string)($comida1), (string)($comida2), (string)($comida3), (string)($comida4), (string)($comida5), (string)($postre1), (string)($merienda1), (string)($merienda2), (string)($cena)));
                }
}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.history.go(0);
self.location.href='<?="editar_menu_dia.php?dia=$dia&curso=$curso&id_menu=$id_menu";?>';
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

