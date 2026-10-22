<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");

require("../configuracion/autentifica_admin.php");

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

// Datos de POST
$id_caja=$_GET["id_caja"];

db_query("DELETE FROM cuentas_gastos WHERE id_cuenta=".$id_caja);

db_close();

header("Location: listado_cajas.php");

?>
