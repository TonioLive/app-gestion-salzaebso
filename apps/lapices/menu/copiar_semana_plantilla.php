<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio_semana_origen=$_POST["inicio_semana_origen"];
$inicio_semana_destino=$_POST["inicio_semana_destino"];
$id_menu=$_POST["id_menu"];
$plantilla=$_POST["plantilla"];
$id_menu_destino=$id_menu;

$inicio_semana_destino=$inicio_semana_destino+7200;
$incremento=86400;

$semanas_copiar=$_POST["semanas_copiar"];

$semana_inicial = $inicio_semana_origen;
$semana_final = $semana_inicial;
if ($semanas_copiar==2) {
    $semana_inicial = 1;
    $semana_final = 6;
}

for ($semana_actual=$semana_inicial;$semana_actual<=$semana_final;$semana_actual++) {

    for ($contador_repeticiones=0;$contador_repeticiones<7;$contador_repeticiones++) {

        $busca_platos_dia=db_query("SELECT * FROM menu_calendario_plantilla WHERE id_menu=$id_menu AND plantilla=$plantilla AND semana='".$semana_actual."' and dia='".($contador_repeticiones+1)."';");

        echo "Día ".($contador_repeticiones+1)." a ".date("d/m/Y",$inicio_semana_destino)."<br>";

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
            $busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_semana_destino)."'");
            if (db_num_rows($busca_platos_dia)!=0) {
                $datos_dia_actual=db_fetch_array($busca_platos_dia);
                $id_dia_actual=$datos_dia_actual["id_dia"];
                $inserta_platos=db_query("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2',cena='$cena' WHERE id_dia=$id_dia_actual;") or die(db_error());
                // echo("UPDATE menu_calendario SET comida1='$comida1',comida2='$comida2',comida3='$comida3',comida4='$comida4',comida5='$comida5',postre1='$postre1',merienda1='$merienda1',merienda2='$merienda2' WHERE id_dia=$id_dia_actual;<br>");
            } else {
                $inserta_platos=db_query("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2,cena) VALUES ('$id_menu_destino','".date("Y-m-d",$inicio_semana_destino)."','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2','$cena');") or die(db_error());
                // echo("INSERT INTO menu_calendario (id_menu,fecha,comida1,comida2,comida3,comida4,comida5,postre1,merienda1,merienda2) VALUES ('$id_menu_destino','".date("Y-m-d",$inicio_semana_destino)."','$comida1','$comida2','$comida3','$comida4','$comida5','$postre1','$merienda1','$merienda2');<br>");
            }
        } else {
            $borra_platos_dia=db_query("DELETE FROM menu_calendario WHERE id_menu=$id_menu_destino AND fecha='".date("Y-m-d",$inicio_semana_destino)."'");
        }

        $inicio_semana_destino=$inicio_semana_destino+$incremento;
    }

}

$inicioMes = strtotime(date("Y-m-01",$inicio_semana_destino));
$fecha = date("01/m/Y",$inicio_semana_destino);

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

