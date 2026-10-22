<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$num_errores=0;

                // Recibimos datos del formulario

                $dia_seleccionado=time();

                $id_producto=$_POST["id_producto"];
                $observaciones=$_POST["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=htmlentities($observaciones);
                        }
                $cantidad=$_POST["cantidad"];
                if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }


        // Introduce información

        $inserta=db_query_params('INSERT INTO movimientos_material (id_material,fecha,hora,concepto,cantidad) VALUES (?,?,?,?,?)', 'sssss', array((string)($id_producto), (string)(date("Y-m-d", $dia_seleccionado)), (string)(date("H:i:s", $dia_seleccionado)), (string)($observaciones), (string)($cantidad))) or die(db_error()."L:41");

        

header("Location: resumen_entradas_producto.php?id_producto=".$id_producto);

?>
<html>
<head>
<script Language="JavaScript">
self.opener.history.go(0);
window.close();
</SCRIPT>
</head>
<body>
</body>
</html>

