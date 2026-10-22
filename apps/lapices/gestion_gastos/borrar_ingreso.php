<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 
$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

// Datos de GET
$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
                }
// Datos de POST
$id_ingreso=$_GET["id_ingreso"];

db_query("DELETE FROM gestion_ingresos WHERE id_ingreso=".$id_ingreso);

db_close();
odbc_close($odbc);
 
header("Location: ingresos.php?cuenta_seleccionada=".$cuenta_seleccionada."&ejercicio=".$ejercicio."&mes=".$mes_seleccionado);

?>
