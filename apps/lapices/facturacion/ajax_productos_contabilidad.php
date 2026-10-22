<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$codigo_familia = isset($_GET["codigo_familia"]) ? intval($_GET["codigo_familia"]) : 0;
$codigo_producto_actual = isset($_GET["codigo_producto"]) ? intval($_GET["codigo_producto"]) : 0;

echo '<option value="0">-- Sin producto contable --</option>';

if ($codigo_familia == 0) {
    exit;
}

$sql = "SELECT codigo_producto, nombre_producto
        FROM tarifa_productos_contabilidad
        WHERE codigo_familia = $codigo_familia
        ORDER BY nombre_producto";

$rs = db_query($sql);

while ($row = db_fetch_assoc($rs)) {
    $selected = ($codigo_producto_actual == $row["codigo_producto"]) ? ' selected' : '';

    echo '<option value="'.$row["codigo_producto"].'"'.$selected.'>'
        . $row["nombre_producto"]." - ".$row["codigo_producto"]
        . '</option>';
}
?>