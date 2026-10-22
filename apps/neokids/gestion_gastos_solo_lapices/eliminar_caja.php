<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");

require("../configuracion/autentifica_admin.php");

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

// Datos de POST
$id_caja=$_GET["id_caja"];

db_query_params('DELETE FROM cuentas_gastos WHERE id_cuenta=?', 's', array($id_caja));

db_close();

header("Location: listado_cajas.php");

?>
