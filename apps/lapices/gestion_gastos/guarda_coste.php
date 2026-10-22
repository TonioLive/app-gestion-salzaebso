<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

// Datos de GET
$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$id_centro_sel_costes=$_GET["id_centro_sel_costes"];
$partida_sel_costes=$_GET["partida_sel_costes"];
$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
$tipo_seleccionado=$_GET["tipo"];

if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
                }
if (strlen($tipo_seleccionado)==0) {
                $tipo_seleccionado="0";
                }
                
// Datos de POST
$id_coste=$_POST["id_coste"];
$id_centro_coste=$_POST["id_centro_coste"];
$tipo_coste=$_POST["tipo_coste"];
$forma_pago=$_POST["forma_pago"];
$fecha_factura_ano=$_POST["fecha_factura_ano"];
$fecha_factura_mes=$_POST["fecha_factura_mes"];
$fecha_factura_dia=$_POST["fecha_factura_dia"];
$fecha_factura=$fecha_factura_ano."/".$fecha_factura_mes."/".$fecha_factura_dia;
$fecha_vencimiento_mes=$_POST["fecha_vencimiento_mes"];
$fecha_vencimiento_dia=$_POST["fecha_vencimiento_dia"];
$fecha_vencimiento_ano=$_POST["fecha_vencimiento_ano"];
$fecha_vencimiento=$fecha_vencimiento_ano."/".$fecha_vencimiento_mes."/".$fecha_vencimiento_dia;
$num_factura=$_POST["num_factura"];
$proveedor=$_POST["proveedor"];
$importe=$_POST["importe"];
$banco=$_POST["banco"];

$escuelas1=$_POST["escuelas"];
$escuelas="";
for ($cs=0;$cs<=count($escuelas1);$cs++) {
                if (strlen($escuelas1[$cs])!=0) {
                $escuelas=$escuelas.$escuelas1[$cs].",";
                }
                }
if (strlen($escuelas)!=0) {
        $escuelas=substr($escuelas,0,strlen($escuelas)-1);
        }

$proporcional=$_POST["proporcional"];


if (is_numeric($banco)) {
        // Comprueba si existe
        $busca_banco=db_query("SELECT * FROM cuentas_gastos WHERE id_cuenta=$banco");
        if (db_num_rows($busca_banco)!=0) {
                // Todo OK
                } else {
                // Es un nombre, lo metemos
                $intro_banco=db_query("INSERT INTO cuentas_gastos (nombre_cuenta) VALUES ('$banco');");
                $banco=db_insert_id();
                }
        } else {
        // Comprueba si existe
        $busca_banco=db_query("SELECT * FROM cuentas_gastos WHERE nombre_cuenta='$banco'") or die(db_error());
        
        if (db_num_rows($busca_banco)!=0) {
                $datos_busca_banco=db_fetch_array($busca_banco);
                $banco=$datos_busca_banco["id_cuenta"];
                } else {
                // Lo metemos
                $intro_banco=db_query("INSERT INTO cuentas_gastos (nombre_cuenta) VALUES ('$banco');");
                $banco=db_insert_id();
                }
        
        }

$partida=$_POST["partida"];
$observaciones=$_POST["observaciones"];

$id_ingreso=$_POST["id_ingreso"];
$cuenta_ingreso=$_POST["cuenta_ingreso"];
$transpaso=$_POST["transpaso"];

if (($transpaso==1) and ($id_ingreso!="0")) {
        db_query("UPDATE gestion_ingresos SET partida='".db_real_escape_string($partida)."', id_centro='$id_centro_coste', fecha_entrada='".$fecha_vencimiento."', tipo_ingreso='".$forma_pago."', num_albaran='', num_factura='', cliente='0', importe='".$importe."', cuenta_ingreso='".$cuenta_ingreso."', observaciones='".$observaciones."', fecha_vencimiento='".$fecha_vencimiento."', finalizado=1 WHERE id_ingreso=".$id_ingreso);
        }
        
if (($transpaso==0) and ($id_ingreso!="0")) {
        db_query("DELETE FROM gestion_ingresos WHERE id_ingreso=".$id_ingreso);
        $id_ingreso="0";
        }
        
if (($transpaso==1) and ($id_ingreso=="0")) {
        db_query("INSERT INTO gestion_ingresos (partida, id_centro, fecha_entrada, tipo_ingreso, importe, cuenta_ingreso, observaciones, fecha_vencimiento, fecha_insercion, introducido_por, finalizado, cliente) VALUES ('$partida', '$id_centro_coste', '".$fecha_vencimiento."', '".$forma_pago."', '".$importe."', '".$cuenta_ingreso."', '".$observaciones."', '".$fecha_vencimiento."', '".date("%Y/%m/%d")."', '".$idUsuario."', 1, 0)") or die(db_error());
        $id_ingreso=db_insert_id();
        }
        
db_query("UPDATE gestion_costes SET escuelas='$escuelas', proporcional='$proporcional', partida='".db_real_escape_string($partida)."', id_centro='$id_centro_coste', fecha_factura='".$fecha_factura."', tipo_coste='".$tipo_coste."', forma_pago='".$forma_pago."', num_factura='".$num_factura."', proveedor='".$proveedor."', importe='".$importe."', banco='".$banco."', observaciones='".$observaciones."', fecha_vencimiento='".$fecha_vencimiento."', finalizado=1, id_ingreso='".$id_ingreso."' WHERE id_coste=".$id_coste);

db_close();

header("Location: costes.php?partida_sel_costes=$partida_sel_costes&id_centro_sel_costes=$id_centro_sel_costes&tipo=".$tipo_seleccionado."&cuenta_seleccionada=".$cuenta_seleccionada."&ejercicio=".$ejercicio."&mes=".$mes_seleccionado);
?>
