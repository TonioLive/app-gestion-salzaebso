<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

require("importes_como_texto.php");

// Datos

$id_asistencia=$_GET["id_asistencia"];

$busca_datos_asistencia=db_query_params('SELECT servicios_consumidos_asistencia.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM servicios_consumidos_asistencia, alumnos, usuarios WHERE servicios_consumidos_asistencia.id_alumno=alumnos.id_usuario AND alumnos.id_usuario=usuarios.id_usuario AND id_servicio_consumido=?', 's', array($id_asistencia)) or die(db_error()."-C");

$datos_asistencia=db_fetch_array($busca_datos_asistencia);

$nombre_alumno_pago=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]." (Alumno nº: ".$datos_alumno["id_usuario"].")";
$id_usuario_alumno_pago=$datos_alumno["id_usuario"];
$fecha_baja_alumno=$datos_alumno["fecha_baja"];

$lista_grupos_alumno="";
        
$busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($id_alumno_recibo)) or die(db_error());

while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
                                }
                                
if (strlen($lista_grupos_alumno)!=0) {
                                        $lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
                                }

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_asistencia["centro_alu"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$nombre_centro=$datos_centro["nombre_centro"];
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}

// Datos pago a cuenta
$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_asistencia=?', 's', array($id_asistencia));

$datos_pago=db_fetch_array($busca_pagos);

$id_pago=$datos_pago["id_pago"];

$importe_pago=$datos_pago["importe_pago"];
$forma_de_pago=$pagos["forma_pago"];
$fecha_pago=$pagos["fecha_pago"];

if ((strlen($fecha_pago)==0) or ($fecha_pago=="0000-00-00")) {
        $fecha_pago=date("d/m/Y",strtotime($fecha_pago));        
        } else {
        $fecha_pago="_________";
        }

$nombre_cliente_facturas=$datos_asistencia["banco_titular"];

if (strlen($nombre_cliente_facturas)==0) {
        $nombre_cliente_facturas=$datos_asistencia["contacto1"];
        }

$dni_cliente_facturas=$datos_asistencia["banco_dni_titular"];
$nombre_alumno_facturas=$datos_asistencia["Apellido1"]." ".$datos_asistencia["Apellido2"].", ".$datos_asistencia["Nombre"]." (Alumno nº: ".$datos_asistencia["id_usuario"].")";

$margen_inferior=25;
$desplazamiento_y=25;

// error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

include 'include_recibos_asistencia.php';

$pdf->ezStream();
?>
