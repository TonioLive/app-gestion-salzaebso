<?php 
require("../../configuracion/config.php");

$total=$_POST["Total"];
$submenu=$_GET["Submenu"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$query=db_query("SELECT * FROM NoticiasN ORDER BY Cod DESC;");

$contador=1;

while($rsslc=db_fetch_array($query))
        {
        $matriz_de_codigos[$contador]=$rsslc["Cod"];
        $contador=$contador+1;
        }

for ($x=1; $x<=$contador-1; $x=$x+1)
{
  $act=$_POST["ACT".$matriz_de_codigos[$x]];
  if ($act=="SI")
  {
    $act1="1";
  }
    else
  {
    $act1="0";
  } 
  $query2=db_query("UPDATE NoticiasN SET Activada=".$act1." WHERE Cod=".$matriz_de_codigos[$x]);

} 

db_close();

mt_srand((double)microtime()*1000000);
header("Location: "."modif_noticia.php?Submenu=".$submenu."&r=".(mt_rand(0,10000000)/10000000));
?>

