<?php

// ----------- Datos ------------

require("../../configuracion/config.php"); 
require("../../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];
$copia=$_GET["copia"];
$mostrar_domicilio=$_GET["dom"];


if (strlen($id_factura)!=0) {

// Busca datos de la factura
$consulta_factura=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=?', 's', array($id_factura));
$consulta_datos_factura = db_query_params($consulta_factura[0], $consulta_factura[1], $consulta_factura[2]) or die (db_error());

$numRecords = db_num_rows ($consulta_datos_factura);

if ($numRecords!=0) {

$datos_de_factura=db_fetch_array($consulta_datos_factura);

$busca_facturas_de_otras_series=db_query_params('SELECT * FROM facturas_emitidas WHERE id_cliente=? AND fecha_emision=? AND serie<>?', 'sss', array($datos_de_factura["id_cliente"], (string)($datos_de_factura["fecha_emision"]), (string)($datos_de_factura["serie"])));

$filtro_otras_series="";

$observaciones_otras_series="";

/*
$numero_de_otras_facturas=mysql_num_rows($busca_facturas_de_otras_series);

while($facturas_de_otras_series=mysql_fetch_array($busca_facturas_de_otras_series)) {
        $filtro_otras_series=" OR detalles_factura.id_factura=".$facturas_de_otras_series["id_factura"];
        $observaciones_otras_series=$observaciones_otras_series." ".$facturas_de_otras_series["serie"].$facturas_de_otras_series["num_factura"]."/ ".$facturas_de_otras_series["ejercicio_factura"];
        }
        
if ($numero_de_otras_facturas>1) {
        $observaciones_otras_series="Los conceptos incluidos en esta factura corresponden a los albaranes: ".$observaciones_otras_series;
        } else {
        $observaciones_otras_series="";
        }
*/

if ($datos_de_factura["forma_de_pago"]==1) {
                $forma_de_pago="Domiciliación bancaria
nº de cuenta: ".$datos_de_factura["banco_cuenta"];
        } else {
                $forma_de_pago="Contado";
        }

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE detalles_factura.id_factura=".$id_factura.$filtro_otras_series) or die (odbc_errormsg());

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
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
        }
/*
if ($datos_del_cliente["recargo_equivalencia"]==true) {
        $total_recargo=round(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }
*/
$total_factura=number_format(($total_base+$total_iva+$total_recargo), 2, ',', '.');
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$nombre_factura="factura_$id_factura";

// ---------Fin datos-----------

require_once('../config/lang/spa.php');
require_once('../tcpdf.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		/*
		// Logo
		$image_file = K_PATH_IMAGES.'logo_factura.jpg';
		$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 12);
		// Title
		$this->Cell(0, 15, 'FECHA: '.date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])), 0, false, 'R', 0, '', 0, false, 'M', 'M');
		*/
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 6);
		// Page number
		$this->Cell(0, 10, 'ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123                               Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Escuelas Infantiles lápices');
$pdf->SetTitle('Factura');
$pdf->SetSubject('Factura nº '.$datos_de_factura["num_factura"].'/ '.$datos_de_factura["ejercicio_factura"]);
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set certificate file
$certificate = 'file://../../../certificados/zaebsalpublica.pem';
$firma_privada = 'file://../../../certificados/zaebsalkey.crt';
$private_key_password = (getenv('NEOKIDS_PDF_SIGN_PASSWORD') ?: '');
$extracerts = '';

// set additional information
$info = array(
	'Name' => 'ZAEBSAL, S.L.',
	'Location' => 'Avda. General Aviles, 59. VALENCIA',
	'Reason' => 'ZAEBSAL, S.L. - C.I.F. B97324123',
	'ContactInfo' => 'http://www.escuelas-infantiles.com',
	);

// set document signature
$pdf->setSignature($certificate, $firma_privada, $private_key_password, $extracerts, 2, $info);

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// Logotipo
include "encabezado_pie_factura.php";




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// *** set signature appearance ***

// create content for signature (image and/or text)
$pdf->Image('../images/tcpdf_signature.png', 180, 60, 15, 15, 'PNG');

// define active area for signature appearance
$pdf->setSignatureAppearance(180, 60, 15, 15);

//Close and output PDF document
$pdf->Output("$nombre_factura.pdf", 'I');

} else {
// El nº de la factura no es correcto
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><font size=2 face="Trebuchet MS">No se encuentra la factura con el nº indicado<br><br><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 

}
} else {
// Falta el nº de la factura
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><font size=2 face="Trebuchet MS">No se ha indicado el nº de la factura<br><br><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 
}

//============================================================+
// END OF FILE                                                
//============================================================+
