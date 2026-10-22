<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// Datos
$ejercicio=$_GET["ejercicio"];
$id_cliente=$_GET["id_cliente"];
$id_usu_alu=$_GET["id_usu_alu"];
$nif_cliente=$_GET["nif"];

$busca_datos_cliente=db_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' GROUP BY alumnos.id_usuario");

if (db_num_rows($busca_datos_cliente)!=0) {

$numero_hijos=db_num_rows($busca_datos_cliente);

$datos_del_cliente=db_fetch_array($busca_datos_cliente);

// Busca totales facturado en cada concepto

$consulta1="SELECT *,usuarios.id_usuario AS id_usu FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' AND year(facturas_emitidas.fecha_emision)=".$ejercicio;


$consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

$contador=0;
$contador1=0;

$total_desglose_facturas=array();
$total_desglose_hijo=array();
$total_desglose_categoria=array();
$total_facturado_abs=0;

$cosas_facturadas="";

while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

        // Mira si los albaranes de la factura y calcula los totales
        $id_factura=$datos_factura["id_factura"];

        // Calcula totales

        // Busca albaranes
        $consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (db_error());

        while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){
                
                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
                $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
                
                $base_linea=$total_linea-$total_dto;
                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                $total_abs=$base_linea+$iva_linea;

                // Consulta el codigo de cuenta
                
                $codigo_tarifa=$datos_albaranes["articulo"];
                $intentar_localizar=false;

                $codigo_cuenta=0;
                
                if ($codigo_tarifa==0) {
                
                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="co_") {
                                $codigo_cuenta=2;
                                }
                                
                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="he_") {
                                $codigo_cuenta=1;
                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,6)=="cod_e_") {
                                
                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],6,(strlen($datos_albaranes["cod_facturacion"])-6));

                                
                                $busca_servicio=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_servicio_consumido=$id_servicio_actual;");

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);
                                        
                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }
                                
                        if ((substr($datos_albaranes["cod_facturacion"],0,4)=="cod_") and (substr($datos_albaranes["cod_facturacion"],0,6)!="cod_e_")) {

                                $codigo_cuenta=2;

                                }
                                
                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="au_") {

                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],3,(strlen($datos_albaranes["cod_facturacion"])-3));

                                $busca_servicio=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_servicio_contratado=$id_servicio_actual");

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);

                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }

                        } else {
                        // Busca en tarifa
                        
                        $busca_servicio=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_servicio_contratado=".$codigo_tarifa) or die(db_error());

                        if (db_num_rows($busca_servicio)!=0) {
                                $datos_servicio=db_fetch_array($busca_servicio);
                                
                                $codigo_tarifa=$datos_servicio["ID"];
                                $codigo_cuenta=$datos_servicio["desglosar_como"];
                                
                                } else {
                                $intentar_localizar=true;
                                }
                        
                        }

                 if ($intentar_localizar==true) {
                        // Intentando determinar mediante el concepto...

                        $palabras0=Array("chandal","uniforme","Material escolar","material escolar");

                        for ($c_pal=0;$c_pal<=count($palabras0);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras0[$c_pal]))) {
                                        $codigo_cuenta=0;
                                        break;
                                        }

                                }

                        $palabras1=Array("hora extra","salida","visita","gabinete","inscripción","inscripcion","Inscripción","Inscripcion");

                        for ($c_pal=0;$c_pal<=count($palabras1);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras1[$c_pal]))) {
                                        $codigo_cuenta=1;
                                        break;
                                        }

                                }

                        $palabras2=Array("comedor","comida","paella","Comedor");

                        for ($c_pal=0;$c_pal<=count($palabras2);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras2[$c_pal]))) {
                                        $codigo_cuenta=2;
                                        break;
                                        }

                                }

                        $palabras3=Array("educacion","educación","Educación","Educación");

                        for ($c_pal=0;$c_pal<=count($palabras3);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras3[$c_pal]))) {
                                        $codigo_cuenta=3;
                                        break;
                                        }

                                }

                        }
                if (($codigo_cuenta==2) or ($codigo_cuenta==3)) {
                        $cosas_facturadas=$cosas_facturadas.$datos_factura["id_usu"].",".$datos_factura["num_factura"].",".$datos_albaranes["concepto"].",".$total_abs."<br>";
                        $total_desglose_facturas[$datos_factura["id_usu"]][$codigo_cuenta]=$total_desglose_facturas[$datos_factura["id_usu"]][$codigo_cuenta]+$total_abs;
                        $total_desglose_hijo[$datos_factura["id_usu"]]=$total_desglose_hijo[$datos_factura["id_usu"]]+$total_abs;
                        $total_desglose_categoria[$codigo_cuenta]=$total_desglose_categoria[$codigo_cuenta]+$total_abs;
                        $total_facturado_abs=$total_facturado_abs+$total_abs;
                        }
                }

        }

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

if ($total_facturado_abs<3005.06) {

if ($numero_hijos>3) {
        $pdf = new Cezpdf('a4','landscape');
        $posicion_inicio_tabla=325;
        $posicion_inicial=425;
        $tam_fuente_tabla=8;
        } else {
        $pdf = new Cezpdf('a4','portrait');
        $posicion_inicio_tabla=525;
        $posicion_inicial=625;
        $tam_fuente_tabla=(10-(($numero_hijos)*2));
        }
        
        $pdf -> ezSetMargins(20,0,20,20);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=100;

// encabezado y pie de página

$pdf->setLineStyle(1,'','','','');

$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica-Bold');

$pdf->addText(420,690,9,"C.I.F.:");

$pdf->selectFont('./fonts/Helvetica-Bold.afm');
$pdf->addText(75,690,9,$datos_del_cliente["banco_titular"]);
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->addText(450,690,9,$datos_del_cliente["banco_dni_titular"]);
$pdf->addText(75,678,9,$datos_del_cliente["Direccion"]);
$pdf->addText(75,666,9,$datos_del_cliente["cp"]." ".$datos_del_cliente["Poblacion"]." (".$datos_del_cliente["Provincia"].")");


$texto_informe1="Queremos informarles que según nuestros datos, la cantidad facturada en concepto de Educación";
$texto_informe2="";

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');

// Fin encabezado y pie de página

// Fin encabezado y pie de página


// Informe cuando paga por domiciliacion
$posicion_actual=638;

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Estimado cliente:



$texto_informe1 durante el ejercicio ".$ejercicio." ha sido de:

",9,array('justification'=>'left','aleft'=>75));

$data = array();

$contador_data=0;
$total_facturado=0;

$consulta_desglsoe = db_query("SELECT * FROM tipos_deslose_facturacion WHERE id_desglose=2 OR id_desglose=3 ORDER BY nombre_desglose") or die (db_error());
while($datos_desglsoe = db_fetch_array($consulta_desglsoe)){
        $id_desglose=$datos_desglsoe["id_desglose"];
        $nombre_desglose=$datos_desglsoe["nombre_desglose"];
        if ($total_desglose_categoria[$id_desglose]!=0) {
        
                // Busca alumnos de este padre
                $busca_datos_hijos=db_query("SELECT usuarios.* FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' GROUP BY alumnos.id_usuario");

                $data[$contador_data]=array('Concepto'=>$nombre_desglose);
        
                while($datos_hijos=db_fetch_array($busca_datos_hijos)){
        
                array_push($data[$contador_data],number_format($total_desglose_facturas[$datos_hijos["id_usuario"]][$id_desglose], 2, ',', '.')." € ");
                
                }
                $total_facturado=$total_facturado+$total_desglose_categoria[$id_desglose];
                array_push($data[$contador_data],number_format($total_desglose_categoria[$id_desglose], 2, ',', '.')." € ");
                
                $contador_data++;
                }
        }
        
        
$data[$contador_data]=array('Concepto'=>'Total facturación año '.$ejercicio);
$cols = array('Concepto'=>"Concepto");
$alineacion_tabla=array(array('justification'=>'right'));

$formato_columna=array('justification'=>'right');

// Busca alumnos de este padre
$busca_datos_hijos=db_query("SELECT usuarios.* FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' GROUP BY alumnos.id_usuario");

while($datos_hijos=db_fetch_array($busca_datos_hijos)){

        array_push($data[$contador_data],number_format($total_desglose_hijo[$datos_hijos["id_usuario"]], 2, ',', '.')." € ");
        array_push($cols,$datos_hijos["Nombre"].' '.$datos_hijos["Apellido1"].' '.$datos_hijos["Apellido2"]);
        array_push($alineacion_tabla,$formato_columna);
        $filas++;
        }

array_push($data[$contador_data],number_format($total_facturado_abs, 2, ',', '.')." € ");
array_push($alineacion_tabla,$formato_columna);

array_push($cols,'Total concepto');

$pdf->ezTable($data,$cols,'',array('xPos'=>'center','xOrientation'=>'center','fontSize'=>$tam_fuente_tabla,'cols'=>$alineacion_tabla));
$pdf->ezText("

$texto_informe2

Sin otro particular, reciba un cordial saludo:







",9,array('justification'=>'left','aleft'=>75));

} else {

if ($numero_hijos>3) {
        $pdf = new Cezpdf('a4','landscape');
        $posicion_inicio_tabla=325;
        $posicion_inicial=425;
        $tam_fuente_tabla=8;
        } else {
        $pdf = new Cezpdf('a4','portrait');
        $posicion_inicio_tabla=525;
        $posicion_inicial=625;
        $tam_fuente_tabla=(10-(($numero_hijos)*2));
        }

        $pdf -> ezSetMargins(20,0,20,20);

// Divide entre padres

$busca_padres=db_query("SELECT * FROM padres,usuarios WHERE padres.id_usuario=usuarios.id_usuario AND padres.padre_de=$id_usu_alu AND parentesco=1") or die(db_error());

$numero_padres=db_num_rows($busca_padres);
$c_padres=0;

while($array_padres=db_fetch_array($busca_padres)) {

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=100;

// encabezado y pie de página

$pdf->setLineStyle(1,'','','','');

$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica-Bold');

$pdf->addText(420,690,9,"C.I.F.:");

$pdf->selectFont('./fonts/Helvetica-Bold.afm');
$pdf->addText(75,690,9,$array_padres["Nombre"]." ".$array_padres["Apellido1"]." ".$array_padres["Apellido2"]);
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->addText(450,690,9,$array_padres["DNI"]);
$pdf->addText(75,678,9,$array_padres["Direccion"]);
$pdf->addText(75,666,9,$array_padres["cp"]." ".$array_padres["Poblacion"]." (".$array_padres["Provincia"].")");


$texto_informe1="Queremos informarles que según nuestros datos, la cantidad facturada en concepto de Educación";
$texto_informe2="";

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');

// Fin encabezado y pie de página

// Fin encabezado y pie de página


// Informe cuando paga por domiciliacion
$posicion_actual=638;

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Estimado cliente:



$texto_informe1 durante el ejercicio ".$ejercicio." ha sido de:

",9,array('justification'=>'left','aleft'=>75));

$data = array();

$contador_data=0;
$total_facturado=0;

$consulta_desglsoe = db_query("SELECT * FROM tipos_deslose_facturacion WHERE id_desglose=2 OR id_desglose=3 ORDER BY nombre_desglose") or die (db_error());
while($datos_desglsoe = db_fetch_array($consulta_desglsoe)){
        $id_desglose=$datos_desglsoe["id_desglose"];
        $nombre_desglose=$datos_desglsoe["nombre_desglose"];
        if ($total_desglose_categoria[$id_desglose]!=0) {

                // Busca alumnos de este padre
                $busca_datos_hijos=db_query("SELECT usuarios.* FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' GROUP BY alumnos.id_usuario");

                $data[$contador_data]=array('Concepto'=>$nombre_desglose);

                while($datos_hijos=db_fetch_array($busca_datos_hijos)){

                array_push($data[$contador_data],number_format(($total_desglose_facturas[$datos_hijos["id_usuario"]][$id_desglose]/$numero_padres), 2, ',', '.')." € ");

                }
                $total_facturado=$total_facturado+$total_desglose_categoria[$id_desglose];
                array_push($data[$contador_data],number_format(($total_desglose_categoria[$id_desglose]/$numero_padres), 2, ',', '.')." € ");

                $contador_data++;
                }
        }


$data[$contador_data]=array('Concepto'=>'Total facturación año '.$ejercicio);
$cols = array('Concepto'=>"Concepto");
$alineacion_tabla=array(array('justification'=>'right'));

$formato_columna=array('justification'=>'right');

// Busca alumnos de este padre
$busca_datos_hijos=db_query("SELECT usuarios.* FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.banco_dni_titular='$nif_cliente' GROUP BY alumnos.id_usuario");

while($datos_hijos=db_fetch_array($busca_datos_hijos)){

        array_push($data[$contador_data],number_format(($total_desglose_hijo[$datos_hijos["id_usuario"]]/$numero_padres), 2, ',', '.')." € ");
        array_push($cols,$datos_hijos["Nombre"].' '.$datos_hijos["Apellido1"].' '.$datos_hijos["Apellido2"]);
        array_push($alineacion_tabla,$formato_columna);
        $filas++;
        }

array_push($data[$contador_data],number_format(($total_facturado_abs/$numero_padres), 2, ',', '.')." € ");
array_push($alineacion_tabla,$formato_columna);

array_push($cols,'Total concepto');

$pdf->ezTable($data,$cols,'',array('xPos'=>'center','xOrientation'=>'center','fontSize'=>$tam_fuente_tabla,'cols'=>$alineacion_tabla));
$pdf->ezText("

$texto_informe2

Sin otro particular, reciba un cordial saludo:







",9,array('justification'=>'left','aleft'=>75));

if ($c_padres<($numero_padres-1)) {
        $pdf->ezNewPage();
        }
$c_padres++;
}

}

$pdf->ezStream();

// echo $cosas_facturadas;

} else {
?>
Error: No se encuentra el cliente indicado...
<?php 
}
?>
