<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$listado=$_POST["listado"];
$fecha_ini_dia=$_POST["fecha_ini_dia"];
$fecha_ini_mes=$_POST["fecha_ini_mes"];
$fecha_ini_ano=$_POST["fecha_ini_ano"];
$fecha_ini=$fecha_ini_ano."/".$fecha_ini_mes."/".$fecha_ini_dia;

$inicio_periodo=strtotime($fecha_ini);

$fecha_fin_dia=$_POST["fecha_fin_dia"];
$fecha_fin_mes=$_POST["fecha_fin_mes"];
$fecha_fin_ano=$_POST["fecha_fin_ano"];
$fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;

$fin_periodo=strtotime($fecha_fin);

if ($listado==1) {
        header("Location: asignacion_de_menus.php?dia=$inicio_periodo&fin=$fin_periodo");
        } else {
        header("Location: prevision_compras.php?dia=$inicio_periodo&fin=$fin_periodo");
        }

?>
