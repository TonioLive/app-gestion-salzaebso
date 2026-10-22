<?php 
// Recibimos los datos
$usuario_curso=$_POST["curso"];
$proceso_decision=$_POST["proceso_decision"];
$usuario_fechamat_dia=$_POST["usuario_fechamat_dia"];
$usuario_fechamat_mes=$_POST["usuario_fechamat_mes"];
$usuario_fechamat_ano=$_POST["usuario_fechamat_ano"];

$usuario_fechabaja_dia=$_POST["usuario_fechabaja_dia"];
$usuario_fechabaja_mes=$_POST["usuario_fechabaja_mes"];
$usuario_fechabaja_ano=$_POST["usuario_fechabaja_ano"];

$tipo_mensualidad=$_POST["tipo_mensualidad"];

$num_dias_asiste=$_POST["num_dias_asiste"];
$nivel_actual=$_POST["nivel_actual"];
$nivel_proximo=$_POST["nivel_proximo"];

$comedor=$_POST["comedor"];
if (strlen($comedor)==0) {
        $comedor=0;
        }
$tipo_menu_comedor=$_POST["tipo_menu_comedor"];
if (strlen($tipo_menu_comedor)==0) {
        $tipo_menu_comedor=0;
        }
$numero_tomas=$_POST["numero_tomas"];
if (strlen($numero_tomas)==0) {
        $numero_tomas=0;
        }
$banco_entidad=$_POST["banco_entidad"];
$banco_poblacion=$_POST["banco_poblacion"];
$banco_titular=$_POST["banco_titular"];
$banco_dni_titular=$_POST["banco_dni_titular"];
$banco_domicilio=$_POST["banco_domicilio"];
$banco_cuenta1=$_POST["banco_cuenta1"];
$banco_cuenta2=$_POST["banco_cuenta2"];
$banco_cuenta3=$_POST["banco_cuenta3"];
$banco_cuenta4=$_POST["banco_cuenta4"];
$forma_de_pago=$_POST["forma_de_pago"];

$banco_cuenta=$banco_cuenta1." ".$banco_cuenta2." ".$banco_cuenta3." ".$banco_cuenta4;

// comprobamos los datos opcionales que necesiten ser comprobados
if ($usuario_fechamat_dia!="--" and $usuario_fechamat_mes!="--" and $usuario_fechamat_ano!="----") {
        $fecha_matricula=$usuario_fechamat_ano."/".$usuario_fechamat_mes."/".$usuario_fechamat_dia;
        if (!checkdate(intval($usuario_fechamat_mes),intval($usuario_fechamat_dia),intval($usuario_fechamat_ano))) {
                        $fecha_matricula="";
                        }
                } else {
                        $fecha_matricula="";
                }

if ($usuario_fechabaja_dia!="--" and $usuario_fechabaja_mes!="--" and $usuario_fechabaja_ano!="----") {
        $fecha_baja=$usuario_fechabaja_ano."/".$usuario_fechabaja_mes."/".$usuario_fechabaja_dia;
        if (!checkdate(intval($usuario_fechabaja_mes),intval($usuario_fechabaja_dia),intval($usuario_fechabaja_ano))) {
                        $fecha_baja="";
                        }
                } else {
                        $fecha_baja="";
                }

?>
