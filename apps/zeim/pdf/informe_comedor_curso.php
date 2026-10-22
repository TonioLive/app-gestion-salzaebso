<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$id_escuela=$_GET["id_escuela"];
$id_alumno=$_GET["id_alumno"];
$id_padre=$_GET["id_padre"];
$id_curso=$_GET["id_curso"];

if (strlen($id_curso)==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
        } else {
                $id_curso=0;
        }   
}

$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_escuela));
if (db_num_rows($busca_datos_centro)!=0) {
        $datos_centro=db_fetch_array($busca_datos_centro);
        $db_nombre_centro=$datos_centro["nombre_centro"];
}

$urlWeb="";

switch ($id_escuela) {
        case 10:
        $urlWeb="https://santpau.escuelas-infantiles.com/app";
        $nombreEscuela = "Sant Pau";
        break;
        case 23:
        $urlWeb="https://gestion.escuelainfantilbenicalap.com/app";
        $nombreEscuela = "Benicalap";
        break;   
        case 24:
        $urlWeb="https://gestion.escuelainfantilminipoli.com/app";
        $nombreEscuela = "Minipoli";
        break;
        case 25:
        $urlWeb="https://gestion.escuelainfantillacreu.com/app";
        $nombreEscuela = "La Creu";
        break;        
        } 

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

// Aquí el logo
$x_logo=75;
$y_logo=715;
$pdf->addJpegFromFile('logo_factura'.$id_escuela.'.jpg',$x_logo,$y_logo,150,54);

$busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_alumno)) or die(db_error());

$alumnos_escuela=db_fetch_array($busca_alumnos_escuela);

$busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.padre_de=? AND padres.parentesco=1;', 's', array($id_alumno));

// Busca padres
$padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela);

// Calcula totales

$numeroMeses = 0;
$mesesComedor = array();
$listado_conceptos = "";

$buscaFacturas = db_query_params('
    SELECT * 
    FROM facturas_emitidas, detalles_factura 
    WHERE facturas_emitidas.id_factura = detalles_factura.id_factura 
    AND facturas_emitidas.id_cliente = ? 
    AND (
        detalles_factura.concepto LIKE \'%Comedor%\' 
        OR detalles_factura.concepto LIKE \'%Merienda%\'
        OR detalles_factura.concepto LIKE \'%Almuerzo%\'
    )
    ORDER BY facturas_emitidas.fecha_emision
', 's', array($id_alumno)) or die(db_error());

$importeTotalNumero = 0;

$meses = array(
    'Enero' => '01',
    'Febrero' => '02',
    'Marzo' => '03',
    'Abril' => '04',
    'Mayo' => '05',
    'Junio' => '06',
    'Julio' => '07',
    'Agosto' => '08',
    'Septiembre' => '09',
    'Octubre' => '10',
    'Noviembre' => '11',
    'Diciembre' => '12'
);

while ($facturas = db_fetch_array($buscaFacturas)) {

    $total_linea = $facturas["cantidad"] * $facturas["precio"];
    $total_dto = $total_linea * round(($facturas["dto"] / 100), 2);
    $base_linea = $total_linea - $total_dto;
    $iva_linea = $base_linea * $facturas["tipo_iva"];
    $total_abs = $base_linea + $iva_linea;

    $importeTotalNumero += $total_abs;

    $concepto = $facturas["concepto"];

    // Busca textos tipo: Octubre de 2025
    if (preg_match('/(Enero|Febrero|Marzo|Abril|Mayo|Junio|Julio|Agosto|Septiembre|Octubre|Noviembre|Diciembre) de ([0-9]{4})/i', $concepto, $coincidencias)) {

        $nombreMes = ucfirst(strtolower($coincidencias[1]));
        $anio = $coincidencias[2];

        if (isset($meses[$nombreMes])) {
            $claveMes = $anio . '-' . $meses[$nombreMes];
            $mesesComedor[$claveMes] = true;
        }
    }

    $listado_conceptos .= " - " . $concepto . " - " . number_format($total_abs, 2, ',', '.') . " €\n";
}

$numeroMeses = count($mesesComedor);

$importeTotal = number_format($importeTotalNumero, 2, ',', '.') . " €";

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');

// Aquí el logo
$x_logo=75;
$y_logo=715;
$pdf->addJpegFromFile('logo_factura'.$id_escuela.'.jpg',$x_logo,$y_logo,150,54);

$posicion_inicial=625;
$posicion_actual=638;

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Alumno/a: ".$alumnos_escuela["Nombre"]." ".$alumnos_escuela["Apellido1"]." ".$alumnos_escuela["Apellido2"]."

",11,array('justification'=>'center'));
$pdf->ezText((getenv('ZEIM_CATERING_CERTIFICATE_SIGNER_TEXT') ?: throw new RuntimeException('Configure ZEIM_CATERING_CERTIFICATE_SIGNER_TEXT before generating certificates.'))."

Certifica: que don/doña ".$padres_alumnos_escuela["Nombre"]." ".$padres_alumnos_escuela["Apellido1"]." ".$padres_alumnos_escuela["Apellido2"]." ha abonado durante el curso 2025-2026 ".$numeroMeses." meses de comedor escolar, al cual asiste regularmente su hijo/a: ".$alumnos_escuela["Nombre"]." ".$alumnos_escuela["Apellido1"]." ".$alumnos_escuela["Apellido2"].", las siguientes cuantías:

Concepto, fecha e importe:

".$listado_conceptos."

Total: ".$importeTotal."

Y para que conste a efectos oportunos, firmo y sello la presente.
",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("ESCUELA INFANTIL ".strtoupper($nombreEscuela),9,array('justification'=>'right'));


$pdf->ezStream();

?>