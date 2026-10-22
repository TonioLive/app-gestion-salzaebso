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
$id_ingreso=$_POST["id_ingreso"];
$fecha_entrada_ano=$_POST["fecha_entrada_ano"];
$fecha_entrada_mes=$_POST["fecha_entrada_mes"];
$fecha_entrada_dia=$_POST["fecha_entrada_dia"];
$fecha_entrada=$fecha_entrada_ano."/".$fecha_entrada_mes."/".$fecha_entrada_dia;
$tipo_ingreso=$_POST["tipo_ingreso"];
$num_albaran=$_POST["num_albaran"];
$num_factura=$_POST["num_factura"];
$cliente=$_POST["cliente"];
$importe=$_POST["importe"];
$cuenta_ingreso=$_POST["cuenta_ingreso"];
$observaciones=$_POST["observaciones"];
if (($tipo_ingreso=="2") or ($tipo_ingreso=="3")) {
        $fecha_vencimiento_mes=$_POST["fecha_vencimiento_mes"];
        $fecha_vencimiento_dia=$_POST["fecha_vencimiento_dia"];
        $fecha_vencimiento_ano=$_POST["fecha_vencimiento_ano"];
        $fecha_vencimiento=$fecha_vencimiento_ano."/".$fecha_vencimiento_mes."/".$fecha_vencimiento_dia;
        } else {
        $fecha_vencimiento="";
        }

$id_coste=$_POST["id_coste"];

if ($id_coste!="0") {
        db_query("UPDATE gestion_costes SET fecha_factura='".$fecha_entrada."', forma_pago='".$tipo_ingreso."', importe='".$importe."', observaciones='".$observaciones."', fecha_vencimiento='".$fecha_entrada."', finalizado=1 WHERE id_coste=".$id_coste);
        }

db_query("UPDATE gestion_ingresos SET fecha_entrada='".$fecha_entrada."', tipo_ingreso='".$tipo_ingreso."', num_albaran='".$num_albaran."', num_factura='".$num_factura."', cliente='".$cliente."', importe='".$importe."', cuenta_ingreso='".$cuenta_ingreso."', observaciones='".$observaciones."', fecha_vencimiento='".$fecha_vencimiento."', finalizado=1 WHERE id_ingreso=".$id_ingreso);

db_close();
odbc_close($odbc);
 
header("Location: ingresos.php?cuenta_seleccionada=".$cuenta_seleccionada."&ejercicio=".$ejercicio."&mes=".$mes_seleccionado);
?>
