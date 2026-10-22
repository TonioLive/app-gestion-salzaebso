<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];
$id_padre=$_GET["id_padre"];
$id_curso=$_GET["id_curso"];
$id_escuela=$centro_actual;

$id_alumno_alumno=$id_alumno;


if (strlen($id_curso)==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                } else {
                        // Busca el próximo en iniciarse        
                        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>? AND fecha_fin>=? ORDER BY fecha_inicio LIMIT 1;', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));
                        if (db_num_rows($busca_curso_actual)!=0) {
                                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                $id_curso=$datos_curso_actual["id_curso"];
                        } else {
                                $id_curso=0;
                        }
                }
}                

$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_escuela));
if (db_num_rows($busca_datos_centro)!=0) {
    $datos_centro=db_fetch_array($busca_datos_centro);

    $db_nombre_centro=$datos_centro["nombre_centro"];
}
// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

if (strlen($id_alumno)==0) {
        $busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND alumnos.id_curso=? AND alumnos.id_centro=? ORDER BY centro_aulas.nombre', 'ss', array($id_curso, $id_escuela)) or die(db_error());
} else {
        $busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND alumnos.id_curso=? AND alumnos.id_centro=? AND alumnos.id_alumno=? ORDER BY centro_aulas.nombre', 'sss', array($id_curso, $id_escuela, $id_alumno)) or die(db_error());
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
    case 26:
        $urlWeb="https://gestion.escuelainfantilalbalat.com/app";
        $nombreEscuela = "Albalat dels Sorells";
    break;        
} 

while($alumnos_escuela=db_fetch_array($busca_alumnos_escuela)) {

$id_alumno = $alumnos_escuela["id_usuario"];

if (strlen($id_padre)==0) {        
        $busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.padre_de=? AND padres.parentesco=1 LIMIT 1;', 's', array($alumnos_escuela["id_usuario"]));

} else {
        $busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.padre_de=? AND padres.parentesco=1 AND padres.id_usuario=? LIMIT 1;', 'ss', array($alumnos_escuela["id_usuario"], $id_padre));
}

// Busca padres
while($padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela)){

// Calcula totales

$numeroMeses = 0;
$mesesComedor = array();
$listado_conceptos = "";

/*
        OR detalles_factura.concepto LIKE '%Merienda%'
        OR detalles_factura.concepto LIKE '%Almuerzo%'
*/

$buscaFacturas = db_query_params('
    SELECT * 
    FROM facturas_emitidas, detalles_factura 
    WHERE facturas_emitidas.id_factura = detalles_factura.id_factura 
    AND facturas_emitidas.id_cliente = ? 
    AND (
        detalles_factura.concepto LIKE \'%Comedor%\' 
    )
    ORDER BY facturas_emitidas.fecha_emision
', 's', array($id_alumno_alumno)) or die(db_error());

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

    $listado_conceptos .= " - " . $facturas["id_factura"] . " - " .$concepto . ". " . number_format($total_abs, 2, ',', '.') . " €\n";
}

$numeroMeses = count($mesesComedor);

$importeTotal = number_format($importeTotalNumero, 2, ',', '.') . " €";

if ($importeTotalNumero>0) {

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');

// Aquí el logo
$x_logo=50;
$y_logo=715;
$pdf->addJpegFromFile('logo_factura'.$id_escuela.'.jpg',$x_logo,$y_logo,150,54);
if ($id_escuela==25) {
    $pdf->addJpegFromFile('logo_ayto_pobla.jpg',$x_logo+275,$y_logo,150,66);
}

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
$pdf->ezText("ESCUELA INFANTIL MUNICIPAL ".strtoupper($nombreEscuela),9,array('justification'=>'right'));

// Separación debajo del último texto
$yFirma = $pdf->y - 100;

// Coordenadas de la firma
$xFirma = 325;
$anchoFirma = 189;
$altoFirma = 94;

// Insertar imagen JPG
$pdf->addJpegFromFile(
    "firma_la_creu.jpg",
    $xFirma,
    $yFirma,
    $anchoFirma,
    $altoFirma
);

if (strlen($id_alumno)==0) {
        $pdf->ezNewPage();
}
        }
}

}

$pdf->ezStream();

?>
