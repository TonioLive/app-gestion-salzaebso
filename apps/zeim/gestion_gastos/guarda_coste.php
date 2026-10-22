<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
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
                $intro_banco=db_query_params('INSERT INTO cuentas_gastos (nombre_cuenta) VALUES (?);', 's', array((string)($banco)));
                $banco=db_insert_id();
                }
        } else {
        // Comprueba si existe
        $busca_banco=db_query_params('SELECT * FROM cuentas_gastos WHERE nombre_cuenta=?', 's', array((string)($banco))) or die(db_error());
        
        if (db_num_rows($busca_banco)!=0) {
                $datos_busca_banco=db_fetch_array($busca_banco);
                $banco=$datos_busca_banco["id_cuenta"];
                } else {
                // Lo metemos
                $intro_banco=db_query_params('INSERT INTO cuentas_gastos (nombre_cuenta) VALUES (?);', 's', array((string)($banco)));
                $banco=db_insert_id();
                }
        
        }

$partida=$_POST["partida"];
$observaciones=$_POST["observaciones"];

$id_ingreso=$_POST["id_ingreso"];
$cuenta_ingreso=$_POST["cuenta_ingreso"];
$transpaso=$_POST["transpaso"];

if (($transpaso==1) and ($id_ingreso!="0")) {
        db_query_params('UPDATE gestion_ingresos SET partida=?, id_centro=?, fecha_entrada=?, tipo_ingreso=?, num_albaran=\'\', num_factura=\'\', cliente=\'0\', importe=?, cuenta_ingreso=?, observaciones=?, fecha_vencimiento=?, finalizado=1 WHERE id_ingreso=?', 'sssssssss', array((string)($partida), (string)($id_centro_coste), (string)($fecha_vencimiento), (string)($forma_pago), (string)($importe), (string)($cuenta_ingreso), (string)($observaciones), (string)($fecha_vencimiento), $id_ingreso));
        }
        
if (($transpaso==0) and ($id_ingreso!="0")) {
        db_query_params('DELETE FROM gestion_ingresos WHERE id_ingreso=?', 's', array($id_ingreso));
        $id_ingreso="0";
        }
        
if (($transpaso==1) and ($id_ingreso=="0")) {
        db_query_params('INSERT INTO gestion_ingresos (partida, id_centro, fecha_entrada, tipo_ingreso, importe, cuenta_ingreso, observaciones, fecha_vencimiento, fecha_insercion, introducido_por, finalizado, cliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)', 'ssssssssss', array((string)($partida), (string)($id_centro_coste), (string)($fecha_vencimiento), (string)($forma_pago), (string)($importe), (string)($cuenta_ingreso), (string)($observaciones), (string)($fecha_vencimiento), (string)(date("%Y/%m/%d")), (string)($idUsuario))) or die(db_error());
        $id_ingreso=db_insert_id();
        }
        
db_query_params('UPDATE gestion_costes SET escuelas=?, proporcional=?, partida=?, id_centro=?, fecha_factura=?, tipo_coste=?, forma_pago=?, num_factura=?, proveedor=?, importe=?, banco=?, observaciones=?, fecha_vencimiento=?, finalizado=1, id_ingreso=? WHERE id_coste=?', 'sssssssssssssss', array((string)($escuelas), (string)($proporcional), (string)($partida), (string)($id_centro_coste), (string)($fecha_factura), (string)($tipo_coste), (string)($forma_pago), (string)($num_factura), (string)($proveedor), (string)($importe), (string)($banco), (string)($observaciones), (string)($fecha_vencimiento), (string)($id_ingreso), $id_coste));

db_close();

header("Location: costes.php?partida_sel_costes=$partida_sel_costes&id_centro_sel_costes=$id_centro_sel_costes&tipo=".$tipo_seleccionado."&cuenta_seleccionada=".$cuenta_seleccionada."&ejercicio=".$ejercicio."&mes=".$mes_seleccionado);
?>
