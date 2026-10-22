<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_dia_menu=$_GET["id_dia_menu"];
$plantilla=$_GET["plantilla"];
$dia=$_GET["dia"];
$mes=$_GET["mes"];
$ano=$_GET["ano"];
$id_menu_destino=$_GET["id_menu_destino"];

// Datos dia copiado
$busca_platos_dia=db_query("SELECT * FROM menu_calendario_plantilla WHERE id_dia=$id_dia_menu");

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

// Comprueba si ese día hay platos
$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='$ano-$mes-$dia'") or die(db_error());
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_actual;") or die(db_error());
                } else {
                $inserta_platos=db_query("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES ('$id_menu_destino','$ano-$mes-$dia','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2','$cena');") or die(db_error());
                }

$inicioMes = strtotime($ano-$mes-01);
$fecha = "01/".$mes."/".$ano;

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.history.go(0);
window.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

