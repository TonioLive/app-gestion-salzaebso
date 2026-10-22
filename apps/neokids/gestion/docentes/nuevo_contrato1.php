<?php 
require("../../configuracion/config.php");

$id_docente=$_POST["id_docente"];
$id_contrato=$_POST["id_contrato"];


$url_retorno="horario_docente.php?id_docente=$id_docente";

if ($id_contrato==0) {
        $intro_nuevo_contrato=db_query_params('INSERT INTO personal_contratos (id_empleado) VALUES (?);', 's', array($id_docente));
        $id_contrato=db_insert_id();
        }


                $id_categoria=$_POST["id_categoria"];
                $tipo_contrato=$_POST["tipo_contrato"];
                $salario_base=$_POST["salario_base"];
                $especialidad=$_POST["especialidad"];
                $irpf=$_POST["irpf"];
                $salario_bruto=$_POST["salario_bruto"];
                $observaciones=$_POST["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=addslashes($observaciones);
                        }

                        $fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                }
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                }

                if ($fecha_inicio_dia!="--" and $fecha_inicio_mes!="--" and $fecha_inicio_ano!="----") {
                        $fecha_inicio=$fecha_inicio_ano."/".$fecha_inicio_mes."/".$fecha_inicio_dia;
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio="";
                                }
                        } else {
                        $fecha_inicio="";
                        }
                if ($fecha_fin_dia!="--" and $fecha_fin_mes!="--" and $fecha_fin_ano!="----") {
                        $fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin="";
                                }
                        } else {
                        $fecha_fin="";
                        }
                        
                        // Guarda cambios
                        db_query("UPDATE personal_contratos SET especialidad='$especialidad', salario_bruto='$salario_bruto', irpf='$irpf', fecha_inicio='$fecha_inicio', fecha_fin='$fecha_fin', id_categoria='$id_categoria', tipo_contrato='$tipo_contrato', salario_base='$salario_base', observaciones='$observaciones' WHERE id_contrato=$id_contrato") or die(db_error());

header("Location: $url_retorno");

?>
