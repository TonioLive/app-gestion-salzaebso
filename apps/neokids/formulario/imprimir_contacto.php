<?php

require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_contacto=$_GET["id_contacto"];

$id_plantilla=$_GET["id_plantilla"];

$nombre_plantilla="";

if (strlen($id_plantilla)!=0) {
	$nombre_plantilla="_".$id_plantilla;
	}

if ($id_contacto) {

$sino=array("No","Si");
$nosi=array("Si","No");

$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
$como_conocio=array();
while($datos_tipos=db_fetch_array($busca_tipos)) {
$como_conocio[$datos_tipos["id_tipo"]]=$datos_tipos["nombre_tipo"];
}

$busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));

$datos_contacto=db_fetch_array($busca_datos_contacto);

if (($datos_contacto["fecha_nacimiento"]=="0000-00-00") or ($datos_contacto["fecha_nacimiento"]=="")) {
                                $fecha_nacimiento_dia="";
                                $fecha_nacimiento_mes="";
                                $fecha_nacimiento_ano="";
                                $edad="";
                                } else {
                                $nacimiento=explode( '-', $datos_contacto["fecha_nacimiento"]);
                                $edad=date("Y")-$nacimiento[0];
                                $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                                $hoy=strtotime(date("Y/m/d"));
                                if (($cumple-$hoy)>0) {
                                        $edad=$edad-1;
                                        }
                                        if ($edad<2) {
                                                // 1. Comprueba la edad restando el año de nacimiento al año actual
                                                $fecha_l=$datos_contacto["fecha_nacimiento"];
                                                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                                // 1.1. Cacula edad con decimales
                                                $mes_d_l=date("m",strtotime($fecha_l));
                                                $mes_d_n=date("m");
                                                $dif_meses=$mes_d_n-$mes_d_l;

                                                $edad_d_l=($edad_a_l*12)+$dif_meses;
                                                $edad=$edad_d_l." meses";
                                                } else {
                                                $edad=$edad." años";
                                                }
                                }

// Abre plantilla
$nombre_archivo=$web_path."/formulario/modelos/NEOKIDS_FICHA_INSCRIPCION".$nombre_plantilla.".rtf";
$gestor = fopen($nombre_archivo, "r");
$cuerpo_documento = fread($gestor, filesize($nombre_archivo));

fclose($gestor);

$cuerpo_documento=str_replace("[FECHA_INSCRIPCION]",date("d/m/Y"),$cuerpo_documento);
$cuerpo_documento=str_replace("[horarios_quiere_asistir]",$datos_contacto["horarios_quiere_asistir"],$cuerpo_documento);
$cuerpo_documento=str_replace("[dias_quiere_asistir]",$datos_contacto["dias_quiere_asistir"],$cuerpo_documento);
$cuerpo_documento=str_replace("[nombre_completo]",$datos_contacto["Nombre"]." ".$datos_contacto["Apellido1"]." ".$datos_contacto["Apellido2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[fecha_nacimiento]",date("d/m/Y",strtotime($datos_contacto["fecha_nacimiento"])),$cuerpo_documento);
$cuerpo_documento=str_replace("[edad_actual]",$edad,$cuerpo_documento);
$cuerpo_documento=str_replace("[antiguo_alumno_lapices]",$sino[$datos_contacto["antiguo_alumno_lapices"]],$cuerpo_documento);
$cuerpo_documento=str_replace("[como_conocio]",$como_conocio[$datos_contacto["como_conocio"]],$cuerpo_documento);
$cuerpo_documento=str_replace("[persona_contacto]",$datos_contacto["persona_contacto"],$cuerpo_documento);
$cuerpo_documento=str_replace("[telefono]",$datos_contacto["telefono"],$cuerpo_documento);
$cuerpo_documento=str_replace("[email]",$datos_contacto["email"],$cuerpo_documento);
$cuerpo_documento=str_replace("[dni_padre1]",$datos_contacto["dni_padre1"],$cuerpo_documento);
$cuerpo_documento=str_replace("[nombre_padre2]",$datos_contacto["nombre_padre2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[telefono_padre2]",$datos_contacto["telefono_padre2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[email_padre2]",$datos_contacto["email_padre2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[dni_padre2]",$datos_contacto["dni_padre2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[calle]",$datos_contacto["calle"],$cuerpo_documento);
$cuerpo_documento=str_replace("[codigo_postal]",$datos_contacto["codigo_postal"],$cuerpo_documento);
$cuerpo_documento=str_replace("[poblacion]",$datos_contacto["poblacion"],$cuerpo_documento);
$cuerpo_documento=str_replace("[telefonos_familia]",$datos_contacto["telefonos_familia"],$cuerpo_documento);
$cuerpo_documento=str_replace("[email_familia]",$datos_contacto["email_familia"],$cuerpo_documento);
$cuerpo_documento=str_replace("[contacto_emergencia1]",$datos_contacto["contacto_emergencia1"],$cuerpo_documento);
$cuerpo_documento=str_replace("[telefono_contacto_emergencia1]",$datos_contacto["telefono_contacto_emergencia1"],$cuerpo_documento);
$cuerpo_documento=str_replace("[contacto_emergencia2]",$datos_contacto["contacto_emergencia2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[telefono_contacto_emergencia2]",$datos_contacto["telefono_contacto_emergencia2"],$cuerpo_documento);
$cuerpo_documento=str_replace("[persona_autorizada]",$datos_contacto["persona_autorizada"],$cuerpo_documento);
$cuerpo_documento=str_replace("[comentarios]",$datos_contacto["comentarios"],$cuerpo_documento);
$cuerpo_documento=str_replace("[semanas_embarazo]",$datos_contacto["semanas_embarazo"],$cuerpo_documento);
$cuerpo_documento=str_replace("[recibir_informacion]",$nosi[$datos_contacto["no_publicidad"]],$cuerpo_documento);

header ("Content-type: application/rtf");
header('Content-Disposition: attachment; filename="INSCRIPCION_NEO_KIDS_NUM_' . $id_contacto . '.doc"');

echo($cuerpo_documento);
}
?>