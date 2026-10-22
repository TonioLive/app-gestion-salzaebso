<?php 
require("../configuracion/config.php");

$id_proveedor=$_POST["u"];
$id_evaluacion=$_POST["e"];

                // Comprobamos si es nueva
                if ($id_evaluacion==0) {
                        $consulta_insert=array('INSERT INTO proveedores_evaluaciones (id_proveedor,introducida_por,fecha_insercion) VALUES (?,?,?)', 'sss', array((string)($id_proveedor), (string)($idUsuario), (string)(strftime("%Y/%m/%d"))));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_evaluacion=db_insert_id();
                        }


                // Recibimos datos del formulario

                $valoracion_fecha_dia=$_POST["valoracion_fecha_dia"];
                $valoracion_fecha_mes=$_POST["valoracion_fecha_mes"];
                $valoracion_fecha_ano=$_POST["valoracion_fecha_ano"];
                $fecha_evaluacion=$valoracion_fecha_ano."-".$valoracion_fecha_mes."-".$valoracion_fecha_dia;
                $valorada_por=$_POST["valorada_por"];
                
                // Cuestiones
                $consulta_cuestiones="";
                
                $consulta_cuest=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");

                while($datos_cuestiones=db_fetch_array($consulta_cuest)) {
                        $consulta_cuestiones=$consulta_cuestiones."cuestion".$datos_cuestiones["id_cuestion"]."='".$_POST["cuestion".$datos_cuestiones["id_cuestion"]]."',";
                        }
                
                $observaciones=$_POST["observaciones"];

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query("UPDATE proveedores_evaluaciones SET  ".$consulta_cuestiones."fecha_valoracion='$fecha_evaluacion',valorada_por='$valorada_por',observaciones='".$observaciones."' WHERE id_evaluacion=".$id_evaluacion) or die(db_error()."-2");
        
        }
        
header("Location: nuevo_proveedor_b.php?u=$id_proveedor");

?>
