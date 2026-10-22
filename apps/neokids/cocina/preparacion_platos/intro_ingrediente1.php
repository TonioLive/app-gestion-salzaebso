<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$num_errores=0;

                // Recibimos datos del formulario
                
                $dia_seleccionado=$_POST["dia_sel"];
                
                if (strlen($dia_seleccionado)==0) {
                        $dia_seleccionado=time();
                        }
                
                $id_empleado=$_POST["id_empleado"];
                $id_plato=$_POST["id_plato"];
                $id_alimento=$_POST["id_alimento"];
                $peso_unidad=$_POST["peso_unidad"];

                $ingrediente=$_POST["ingrediente"];

                $cantidad=$_POST["cantidad"];
                if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }

                if ($peso_unidad>0) {
                        $cantidad=$cantidad*$peso_unidad;
                        }

if ($num_errores==0) {
        // Introduce información
        // Comprueba si ya existe
        
        $busca_consumo=db_query("SELECT * FROM menu_productos_consumo_cocina WHERE id_plato=$id_plato AND dia='".date("Y-m-d",$dia_seleccionado)."' AND id_producto_tarifa=$ingrediente") or die(db_error()."L:26");

        if (db_num_rows($busca_consumo)!=0) {
                $datos_actual=db_fetch_array($busca_consumo);
                $cantidad_actual=$datos_actual["cantidad"];
                
                $cantidad_resultante=$cantidad_actual+$cantidad;

                if ($cantidad_resultante>0) {
                        $actualiza=db_query_params('UPDATE menu_productos_consumo_cocina SET cantidad=? WHERE id_producto_consumido=?', 'ss', array($cantidad_resultante, $datos_actual["id_producto_consumido"])) or die(db_error()."L35");
                        } else {
                        $borra=db_query_params('DELETE FROM menu_productos_consumo_cocina WHERE id_producto_consumido=?', 's', array($datos_actual["id_producto_consumido"])) or die(db_error()."L:37");
                        }
                
                } else {
                $inserta=db_query_params('INSERT INTO menu_productos_consumo_cocina (id_producto_tarifa,dia,id_plato,cantidad,id_alimento) VALUES (?,?,?,?,?)', 'sssss', array((string)($ingrediente), (string)(date("Y-m-d", $dia_seleccionado)), (string)($id_plato), (string)($cantidad), (string)($id_alimento))) or die(db_error()."L:41");
                }
        
        }
        
if ($r==1) {
        header("Location: nueva_tasacion.php?id_expediente=".$id_expediente."&u=".$id_tasacion_seleccionada);
        }
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

