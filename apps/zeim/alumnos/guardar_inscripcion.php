<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_alumno=$_POST["id_alumno"];

$usuario_fechains_dia=$_POST["usuario_fechains_dia"];
$usuario_fechains_mes=$_POST["usuario_fechains_mes"];
$usuario_fechains_ano=$_POST["usuario_fechains_ano"];

$usuario_fechamat_dia=$_POST["usuario_fechamat_dia"];
$usuario_fechamat_mes=$_POST["usuario_fechamat_mes"];
$usuario_fechamat_ano=$_POST["usuario_fechamat_ano"];

$usuario_fechabaja_dia=$_POST["usuario_fechabaja_dia"];
$usuario_fechabaja_mes=$_POST["usuario_fechabaja_mes"];
$usuario_fechabaja_ano=$_POST["usuario_fechabaja_ano"];

$motivo_baja=$_POST["motivo_baja"];

$sustituye_a=$_POST["sustituye_a"];
if ($sustituye_a=="0") {
        // $sustituye_a=0;
        $numero_de_orden_nuevo="";
        } else {
        // Busca el nº de orden del alumno al que sustituye
        $busca_datos_sustituido=db_query("SELECT * FROM alumnos WHERE id_alumno=$sustituye_a") or die(db_error());
        $datos_sustituido=db_fetch_array($busca_datos_sustituido);
        
        $numero_de_orden_nuevo=" orden_en_aula='".$datos_sustituido["orden_en_aula"]."', sustituye_a='$sustituye_a',";
        
        }

$proceso_decision=$_POST["proceso_decision"];
$tipo_mensualidad=$_POST["tipo_mensualidad"];
$comedor=$_POST["comedor"];
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
$comedor_especial=$_POST["comedor_especial"];
$comedor_especial_precio=$_POST["comedor_especial_precio"];
$codigo_inscripcion=$_POST["codigo_inscripcion"];

$no_merienda=$_POST["no_merienda"];

$menu_actual=$_POST["menu_actual"];

$banco_cuenta=$banco_cuenta1." ".$banco_cuenta2." ".$banco_cuenta3." ".$banco_cuenta4;

// comprobamos los datos opcionales que necesiten ser comprobados
if ($usuario_fechains_dia!="--" and $usuario_fechains_mes!="--" and $usuario_fechains_ano!="----") {
        $fecha_inscripcion=$usuario_fechains_ano."/".$usuario_fechains_mes."/".$usuario_fechains_dia;
        if (!checkdate(intval($usuario_fechains_mes),intval($usuario_fechains_dia),intval($usuario_fechains_ano))) {
                        $fecha_inscripcion="";
                        }
                } else {
                        $fecha_inscripcion="";
                }
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

db_query("UPDATE alumnos SET$numero_de_orden_nuevo codigo_inscripcion='$codigo_inscripcion', menu_actual='$menu_actual', no_merienda='$no_merienda', motivo_baja='$motivo_baja', fecha_inscripcion='$fecha_inscripcion', fecha_matricula='".$fecha_matricula."', fecha_baja='$fecha_baja', tipo_mensualidad='$tipo_mensualidad', comedor='$comedor', tipo_menu_comedor='$tipo_menu_comedor', numero_tomas='$numero_tomas', forma_de_pago='$forma_de_pago', banco_entidad='$banco_entidad', banco_poblacion='$banco_poblacion', banco_titular='$banco_titular', banco_dni_titular='$banco_dni_titular', banco_domicilio='$banco_domicilio', banco_cuenta='$banco_cuenta', proceso_decision='$proceso_decision', comedor_especial='$comedor_especial', comedor_especial_precio='$comedor_especial_precio' WHERE id_alumno=$id_alumno") or die(db_error());

?>
<script language="Javascript">
window.close();
self.opener.window.location.href='/alumnos/nuevo_alumno4.php?u=<?=$_POST["u"]?>';
</script>
