<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

require("importes_como_texto.php");

// Datos

$id_material=$_GET["id_material"];
$id_pago=$_GET["id_pago"];
$id_factura=$_GET["id_factura"];

if (strlen($id_pago)==0) {

        $id_factura=$_GET["id_factura"];
        $id_factura_pago=$id_factura;        
        $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura)) or die(db_error());
       
        if (db_num_rows($busca_pagos)!=0) {
                $datos_pagos=db_fetch_array($busca_pagos);
                
                $id_pago=$datos_pagos["id_pago"];                                    

                $importe_pago=$datos_pagos["importe_pago"];
                $forma_de_pago=$datos_pagos["forma_pago"];
                $fecha_pago=$datos_pagos["fecha_pago"];                                                                              
                                             
                } else {
                $id_pago=0;
                
                // Calcula importe factura
                
                                // Busca albaranes
                                $consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE detalles_factura.id_factura=".$id_factura.$filtro_otras_series) or die (db_error());

                                $total_dto=0;
                                $total_base=0;
                                $total_base_0=0;
                                $total_base_4=0;
                                $total_base_7=0;
                                $total_base_16=0;
                                $total_iva=0;
                                $total_iva_0=0;
                                $total_iva_4=0;
                                $total_iva_7=0;
                                $total_iva_16=0;
                                $total_recargo=0;

                                while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){
        
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
                                $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
        
                                $total_base=$total_base+$base_linea;
                                $total_iva=$total_iva+$iva_linea;

                                $total_factura=($total_base+$total_iva+$total_recargo);

                                }
                
                $importe_pago=$total_factura;
                $forma_de_pago="";
                $fecha_pago="";
                
                }
        } else {
        
                        // Datos pago a cuenta
                        $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_pago=?', 's', array($id_pago));

                        $datos_pago=db_fetch_array($busca_pagos);

                        $id_factura_pago=$datos_pago["id_factura"]; 
                        
                        $importe_pago=$datos_pago["importe_pago"];
                        $forma_de_pago=$datos_pago["forma_pago"];
                        $fecha_pago=$datos_pago["fecha_pago"];                                 
        
        }       

// Busca datos de la factura
$consulta_factura="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=".$id_factura_pago;
$consulta_datos_factura = db_query($consulta_factura) or die (db_error()."-1");

$datos_de_factura=db_fetch_array($consulta_datos_factura);

$id_alumno_recibo=$datos_de_factura["id_cliente"];

// Busca servicio contratado
$consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=$id_material";
$busca_servicio_contratado=db_query($consulta1);
$datos_albaranes=db_fetch_array($busca_servicio_contratado);

$servicios_contratados=$datos_albaranes["concepto"]."\n";

$busca_datos_asistencia=db_query_params('SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM servicios_consumidos_asistencia, alumnos, usuarios WHERE servicios_consumidos_asistencia.id_alumno=alumnos.id_usuario AND alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno_recibo)) or die(db_error()."-C");

$datos_asistencia=db_fetch_array($busca_datos_asistencia);

$nombre_alumno_pago=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]." (Alumno nº: ".$datos_alumno["id_usuario"].")";
$id_usuario_alumno_pago=$datos_alumno["id_usuario"];
$fecha_baja_alumno=$datos_alumno["fecha_baja"];
$servicios_contratados.="El servicio contratado finaliza el dí­a: ".date("d/m/Y",strtotime($fecha_baja_alumno));

$lista_grupos_alumno="";
        
$busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($id_alumno_recibo)) or die(db_error());

while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
                                }
                                
if (strlen($lista_grupos_alumno)!=0) {
                                        $lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
                                }

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_de_factura["centro_factura"]));
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


if ($id_factura) {

$consulta1=db_query_params('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND id_factura=? ORDER BY fecha_emision DESC', 's', array($id_factura));
$datos_factura = db_fetch_array($consulta1);


                                                // Busca datos alumno
                                                $busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($datos_factura["id_cliente"])) or die(db_error());
                                                if (db_num_rows($busca_alumno)!=0) {
                                                        $datos_alumno=db_fetch_array($busca_alumno);
                                                        $nombre_alumno_pago=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]." (Alumno nº: ".$datos_alumno["id_usuario"].")";
                                                        $id_usuario_alumno_pago=$datos_alumno["id_usuario"];
                                                        $fecha_baja_alumno=$datos_alumno["fecha_baja"];
                                                        
                                                        $servicios_contratados.="El servicio contratado finaliza el día: ".date("d/m/Y",strtotime($fecha_baja_alumno));
                                                }                                        

                                        $lista_grupos_alumno="";
        
                                        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($datos_factura["id_cliente"])) or die(db_error());

                                        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                                        $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
                                }
                                
                                if (strlen($lista_grupos_alumno)!=0) {
                                        $lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
                                }


} 


if ((strlen($fecha_pago)==0) or ($fecha_pago=="0000-00-00")) {
        $fecha_pago="_________";
        } else {
        $fecha_pago=date("d/m/Y",strtotime($fecha_pago));        
        }
$nombre_cliente_facturas=$datos_de_factura["banco_titular"];

if (strlen($nombre_cliente_facturas)==0) {
        $nombre_cliente_facturas=$datos_de_factura["contacto1"];
        }

$dni_cliente_facturas=$datos_de_factura["banco_dni_titular"];
$nombre_alumno_facturas=$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"]." (Alumno nº: ".$datos_de_factura["id_usuario"].")";

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
