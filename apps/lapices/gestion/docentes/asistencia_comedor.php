<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro_comedor"];
$menu_seleccionado_hoy=$_POST["menu_seleccionado_hoy"];
if ($menu_seleccionado_hoy==0) {
        $comedor=0;
        } else {
        $comedor=1;
        }
$id_centro_comida=$_POST["id_centro_comida"];
if (strlen($id_centro_comida)==0) {
        $id_centro_comida=$centro_actual;
        }
if ($comedor==0) {
        $id_centro_comida="0";
        }


db_query("UPDATE asistencia_docentes SET menu_seleccionado_hoy='$menu_seleccionado_hoy', comedor='$comedor', id_centro_asistencia='$id_centro_comida' WHERE id_asistencia=$id_registro") or die(db_error());

header("Location: /index.php");
?>
