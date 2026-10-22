<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio_mes_origen=$_POST["inicio_mes_origen"];
$inicio_mes_destino=$_POST["inicio_mes_destino"];
$id_menu=$_POST["id_menu"];
$mes_destino=$_POST["mes_destino"];
$id_menu_destino=$_POST["id_menu_destino"];

$semanas_a_copiar=$_POST["semanas_a_copiar"];
if (strlen($semanas_a_copiar)==0) {
        $semanas_a_copiar=4;
        }

$inicio_mes_origen=$inicio_mes_origen+7200;
$inicio_mes_destino=$inicio_mes_destino+7200;
$incremento=86400;

for ($contador_repeticiones=0;$contador_repeticiones<($semanas_a_copiar*7);$contador_repeticiones++) {

$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_mes_origen)."'");

echo date("d/m/Y",$inicio_mes_origen)." a ".date("d/m/Y",$inicio_mes_destino)."<br>";

if (db_num_rows($busca_platos_dia)!=0) {
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

// Comprueba si ese día hay platos
$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_mes_destino)."'");
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_actual;") or die(db_error());
                // echo("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2' WHERE id_dia=$id_dia_actual;");
                } else {
                $inserta_platos=db_query("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES ('$id_menu_destino','".date("Y-m-d",$inicio_mes_destino)."','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2','$cena');") or die(db_error());
                // echo("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2) VALUES ('$id_menu_destino','".date("Y-m-d",$inicio_mes_destino)."','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2');");
                }
} else {
$borra_platos_dia=db_query("DELETE FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_mes_destino)."'");
// echo("DELETE FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_mes_destino)."'");
}

$inicio_mes_destino=$inicio_mes_destino+$incremento;
$inicio_mes_origen=$inicio_mes_origen+$incremento;

}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.location.href='calendario_menu.php?id_menu=<?=$id_menu_destino?>';
window.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

