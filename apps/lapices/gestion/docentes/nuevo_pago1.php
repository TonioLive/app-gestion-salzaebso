<?php 
require("../../configuracion/config.php");


$id_pago=$_POST["id_pago"];
$id_docente=$_POST["id_docente"];
$id_contrato=$_POST["id_contrato"];


$url_retorno="resumen_contrato.php?id_docente=$id_docente&id_contrato=$id_contrato";

if ($id_pago==0) {
        $intro_nuevo_pago=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato) VALUES ($id_docente,$id_contrato);");
        $id_pago=db_insert_id();
        }


                $tipo_pago=$_POST["tipo_pago"];
                $cuenta_pago=$_POST["cuenta_pago"];
                $importe_pago=$_POST["importe_pago"];
                $observaciones_pago=$_POST["observaciones_pago"];
                if (strlen($observaciones_pago)!=0) {
                        $observaciones_pago=addslashes($observaciones_pago);
                        }

                        $fecha_pago_dia=$_POST["fecha_pago_dia"];
                        $fecha_pago_mes=$_POST["fecha_pago_mes"];
                        $fecha_pago_ano=$_POST["fecha_pago_ano"];
                        if (!checkdate(intval($fecha_pago_mes),intval($fecha_pago_dia),intval($fecha_pago_ano))) {
                                $fecha_pago_dia="";
                                $fecha_pago_mes="";
                                $fecha_pago_ano="";
                                }


                if ($fecha_pago_dia!="--" and $fecha_pago_mes!="--" and $fecha_pago_ano!="----") {
                        $fecha_pago=$fecha_pago_ano."/".$fecha_pago_mes."/".$fecha_pago_dia;
                        if (!checkdate(intval($fecha_pago_mes),intval($fecha_pago_dia),intval($fecha_pago_ano))) {
                                $fecha_pago="";
                                }
                        } else {
                        $fecha_pago="";
                        }

                        
                        // Guarda cambios
                        db_query("UPDATE personal_pagos SET fecha_pago='$fecha_pago', tipo_pago='$tipo_pago', cuenta='$cuenta_pago', importe='$importe_pago', observaciones='$observaciones_pago' WHERE id_pago=$id_pago") or die(db_error());

header("Location: $url_retorno");

?>
