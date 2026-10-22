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
$id_coste=$_GET["id_coste"];

db_query_params('DELETE FROM gestion_costes WHERE id_coste=?', 's', array($id_coste));

db_close();

header("Location: costes.php?partida_sel_costes=$partida_sel_costes&id_centro_sel_costes=$id_centro_sel_costes&tipo=".$tipo_seleccionado."&cuenta_seleccionada=".$cuenta_seleccionada."&ejercicio=".$ejercicio."&mes=".$mes_seleccionado);

?>
