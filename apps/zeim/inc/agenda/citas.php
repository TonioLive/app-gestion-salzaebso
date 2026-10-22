<?php
header("Content-Type: text/html; charset=windows-1252");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 01 Jan 2007 01:00:00 GMT");

require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$dia_sel=$_GET["dia"];
if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}

$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]="Actividades para ".$lista_centros["nombre_centro"];
        }

$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY id_aula");
$nombres_aulas=array();
$nombres_aulas[0]="Actividades comunes";
while($lista_aulas=db_fetch_array($busca_aulas)){
        $nombres_aulas[$lista_aulas["id_aula"]]="Aula ".$lista_aulas["nombre"];
        }

$fecha_agenda=date("d/m/Y",$fecha_time_stamp);

include("tabla_citas.php");

?>

