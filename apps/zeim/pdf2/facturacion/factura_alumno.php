<?php
// ----------- Datos ------------
require("../../configuracion/config.php"); 

$tokenOK=false;
$tokenDescarga = $_GET["tokendescarga"];
$id_factura=$_GET["id_factura"];
$copia=$_GET["copia"];
$mostrar_domicilio=$_GET["dom"];
$es_albaran=$_GET["alb"];    

if (strlen($tokenDescarga)!=0) {
        // Borra tokens expirados
        $numeroDiasCaducidad = 1;
        $fecha_control=date("Y-m-d H:i:s",(time()-(86400*$numeroDiasCaducidad)));
        $borra_token=db_query_params('DELETE FROM tokens_descarga WHERE creacion<?', 's', array((string)($fecha_control)));

        // Busca token 
        $busca_token=db_query_params('SELECT * FROM tokens_descarga WHERE token=?', 's', array((string)($tokenDescarga)));
        if (db_num_rows($busca_token)!=0) {
                $datos_token=db_fetch_array($busca_token);
                if(!app_rows('SELECT f.id_factura FROM facturas_emitidas f JOIN alumnos a ON a.id_alumno=f.id_cliente JOIN padres p ON p.padre_de=a.id_usuario WHERE f.id_factura=? AND p.id_usuario=? AND COALESCE(p.denegar_acceso_agenda,0)=0','ii',array((int)$datos_token['id_factura'],(int)$datos_token['id_usuario'])))security_stop();
                $tokenOK=true;
                $id_factura=$datos_token["id_factura"];
                $id_usuario_padre=$datos_token["id_usuario"];
                // Borra token usado
                $borra_token=db_query_params('DELETE FROM tokens_descarga WHERE token=?', 's', array((string)($tokenDescarga)));
        } else {
                ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="<?php  echo $meta_description ; ?>">
  <meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">  
  
  <link rel="shortcut icon" href="https://santpau.escuelas-infantiles.com/favicon.ico" type="image/vnd.microsoft.icon">
  <link rel="icon" href="https://santpau.escuelas-infantiles.com/favicon.ico" type="image/vnd.microsoft.icon">
    
  <title><?php  echo utf8_encode($titulo_web) ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/app/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/app/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/app/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-boxed TO GET A BOXED LAYOUT -->
<body class="hold-transition skin-blue layout-boxed sidebar-mini">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-12 col-xs-12 text-center"><img src="/app/imagenes/logo_redondo.jpg" border=0 width="100"></div>
  </div>
  <div class="row"> <!-- Principal -->
  
	<div class="panel panel-default">
	  <div class="panel-body text-center">

                <p class="text-center">
                        <h1>El código de descarga única no es válido. Ha expirado o ya ha sido usado</h1>
                        <h3>(El código de descarga solo puede usarse una vez y tiene una validaz de 24 horas desde el momento en que se solicita la descarga)</h3>
                        <h2>Vuelve y recarga la página donde has solicitado la descarga para generar un código nuevo.</h2>	
                </p>
	</div>
	</div>
</div>
<!-- Pie de página -->
<div class="row">
</div>
</div>
</body>
</html>
                <?php
                die();
        }
}

if ($tokenOK==false) {
        require("../../configuracion/autentifica_padre.php");    
}

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

$numero_de_otras_facturas=db_num_rows($busca_facturas_de_otras_series);

while($facturas_de_otras_series=db_fetch_array($busca_facturas_de_otras_series)) {
        $filtro_otras_series=" OR detalles_factura.id_factura=".$facturas_de_otras_series["id_factura"];
        $observaciones_otras_series=$observaciones_otras_series." ".$facturas_de_otras_series["serie"].$facturas_de_otras_series["num_factura"]."/ ".$facturas_de_otras_series["ejercicio_factura"];
        }
        
if ($numero_de_otras_facturas>1) {
        $observaciones_otras_series="Los conceptos incluidos en esta factura corresponden a los albaranes: ".$observaciones_otras_series;
        } else {
        $observaciones_otras_series="";
        }

if ($datos_de_factura["forma_de_pago"]==1) {
				$numero_de_cuenta=substr($datos_de_factura["banco_cuenta"], 0, 3)."* **** ** *******".substr($datos_de_factura["banco_cuenta"], strlen($datos_de_factura["banco_cuenta"])-3, 3);
                $forma_de_pago="Domiciliación bancaria
nº de cuenta: ".$numero_de_cuenta;
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

$nombre_factura='FACTURA_'.$serie_factura.$datos_de_factura["num_factura"].'-'.$datos_de_factura["ejercicio_factura"];

// Comprueba si el padre es el padre…


$id_alumno_cliente=$datos_de_factura["id_cliente"];

$consulta_datos_del_alumno=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno_cliente));
if (db_num_rows($consulta_datos_del_alumno)!=0) {
        				$datos_del_alumno=db_fetch_array($consulta_datos_del_alumno);
        				$id_usuario_alumno=$datos_del_alumno["id_usuario"];
						}

if ($tokenOK==false) {
        $nombre_usuario_padre=$_SESSION['nombre_padre'];

        // Busca usuario
        $busca_usuario_padre=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario))) or die(db_error());
        if (db_num_rows($busca_usuario_padre)==0) {                
                $id_usuario_padre=0;
                } else {
                $datos_usuario_padre=db_fetch_array($busca_usuario_padre);
                $id_usuario_padre=$datos_usuario_padre["id_usuario"];
                }
}

$busca_hijo_de_padre=db_query_params('SELECT * FROM padres WHERE id_usuario=? AND padre_de=?', 'ss', array($id_usuario_padre, $id_usuario_alumno)) or die(db_error());

if (db_num_rows($busca_hijo_de_padre)!=0) {

// ---------Fin datos-----------

$limite_inferior=240;
$posicion_inicial=64;

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
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
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
/* Certificado WEB
$certificate = (getenv('ZEIM_SIGNING_CERTIFICATE') ?: '');
$firma_privada = (getenv('ZEIM_SIGNING_PRIVATE_KEY') ?: '');
$private_key_password = '';
$extracerts = '';
*/
/* Certificado FNMT */
$certificate = (getenv('ZEIM_SIGNING_CERTIFICATE') ?: '');
$firma_privada = (getenv('ZEIM_SIGNING_PRIVATE_KEY') ?: '');
$private_key_password = (getenv('ZEIM_CERT_PASSWORD_01') ?: '');
$extracerts = '';

// set additional information
$info = array(
	'Name' => 'ZAEBSAL, S.L.',
	'Location' => 'Avda. General Aviles, 59. VALENCIA',
	'Reason' => 'ZAEBSAL, S.L. - C.I.F. B97324123',
	'ContactInfo' => 'https://www.escuelas-infantiles.com',
	);

// set document signature
$pdf->setSignature($certificate, $firma_privada, $private_key_password, $extracerts, 2, $info);

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// Logotipo
include "encabezado_pie_factura.php";

// Tabla detalle

$desglose_actual=-1;
$nombre_concepto_actual="";

$total_desglose_actual=0;

$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE detalles_factura.id_factura=".$id_factura.$filtro_otras_series." ORDER BY concepto_desglosar DESC, precio DESC") or die (db_query());

$posicion_actual=$posicion_inicial;
$albaran_actual=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        if ($desglose_actual!=$datos_albaranes["concepto_desglosar"]) {
                
                if ($nombre_concepto_actual!="") {
                        $nombre_facturado="Total facturado en concepto de ".$nombre_concepto_actual;
                        if ($desglose_actual==3) {
                                $nombre_facturado="Cuota real por enseñanza a pagar por las familias";
                                }
                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->SetTextColor(0,0,0);
                        $posicion_actual=$posicion_actual+1;
                        $pdf->line(10,$posicion_actual,200,$posicion_actual);
                        $posicion_actual=$posicion_actual+1;
                        
                        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
                        
                        $pdf->MultiCell(150,5,$nombre_facturado,0,'L',false,1,10,$posicion_actual);                        
						$pdf->MultiCell(40,5,number_format($total_desglose_actual, 2, ',', '.')." €",0,'R',false,1,160,$posicion_actual);                          
                        $posicion_actual=$posicion_actual+5;

                        }
                        
                $desglose_actual=$datos_albaranes["concepto_desglosar"];
                $busca_nombre_concepto=db_query("SELECT * FROM tipos_deslose_facturacion WHERE id_desglose=$desglose_actual");
                $nombre_concepto=db_fetch_array($busca_nombre_concepto);
                $nombre_concepto_actual=$nombre_concepto["nombre_desglose"];
                $total_desglose_actual=0;
                
                $pdf->SetFont('helveticaB', '', 10);
                $pdf->SetTextColor(0,0,0);
                $posicion_actual=$posicion_actual+1;
                
                // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
                
                $pdf->MultiCell(200,5,$nombre_concepto_actual,0,'L',false,1,10,$posicion_actual);                
                $posicion_actual=$posicion_actual+5;
                                                
                $pdf->SetFont('helvetica', '', 9);                
				$pdf->MultiCell(120,5,"Concepto",0,'L',false,1,10,$posicion_actual);                                
                $pdf->MultiCell(20,5,"Precio",0,'R',false,1,130,$posicion_actual);                                
                $pdf->MultiCell(15,5,"Cantidad",0,'R',false,1,150,$posicion_actual);                                
                $pdf->MultiCell(20,5,"TOTAL",0,'R',false,1,180,$posicion_actual);                

				$posicion_actual=$posicion_actual+5;

				$pdf->setLineStyle(array('width' => 0.1));

                $pdf->line(10,$posicion_actual,200,$posicion_actual);
                
                }

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_desglose_actual=$total_desglose_actual+$total_abs;
		
        $nombre_articulo=$datos_albaranes["concepto"];
        if (strlen($nombre_articulo)>80) {
                $nombre_articulo=substr($nombre_articulo,0,80);
                }
                
        $pdf->SetFont('helvetica', '', 8); 
		$pdf->SetTextColor(0,0,0);                       
		$pdf->MultiCell(120,5,utf8_encode($nombre_articulo),0,'L',false,1,10,$posicion_actual,false,1);                                
        $pdf->MultiCell(20,5,number_format($datos_albaranes["precio"], 2, ',', '.')." €",0,'R',false,1,130,$posicion_actual,false,1);                                
        $pdf->MultiCell(15,5,number_format($datos_albaranes["cantidad"], 2, ',', '.'),0,'R',false,1,150,$posicion_actual,false,1); 
        
        if ($datos_albaranes["dto"]>0) {
                $pdf->MultiCell(15,5,number_format($datos_albaranes["dto"], 0, ',', '.')." %",0,'R',false,1,165,$posicion_actual,false,1);
                }         
                                     
        $pdf->MultiCell(20,5,number_format($total_abs, 2, ',', '.')." €",0,'R',false,1,180,$posicion_actual,false,1);                

		$posicion_actual=$posicion_actual+5;                               

        if ($posicion_actual>=$limite_inferior) {
                $posicion_actual=$posicion_inicial;
                $pdf->AddPage();
                include "encabezado_pie_factura.php";
                
                $pdf->SetFont('helvetica', '', 9);                
				$pdf->MultiCell(120,5,"Concepto",0,'L',false,1,10,$posicion_actual);                                
                $pdf->MultiCell(20,5,"Precio",0,'R',false,1,130,$posicion_actual);                                
                $pdf->MultiCell(15,5,"Cantidad",0,'R',false,1,150,$posicion_actual);                                
                $pdf->MultiCell(20,5,"TOTAL",0,'R',false,1,180,$posicion_actual);  
                
				$posicion_actual=$posicion_actual+5;

				$pdf->setLineStyle(array('width' => 0.1));

                $pdf->line(10,$posicion_actual,200,$posicion_actual);               
                }

        }
        
                $busca_nombre_concepto=db_query("SELECT * FROM tipos_deslose_facturacion WHERE id_desglose=$desglose_actual");
                $nombre_concepto=db_fetch_array($busca_nombre_concepto);
                $nombre_concepto_actual=$nombre_concepto["nombre_desglose"];

                        $nombre_facturado="Total facturado en concepto de ".$nombre_concepto_actual;
                        if ($desglose_actual==3) {
                                $nombre_facturado="Cuota real por enseñanza a pagar por las familias";
                                }
                                
                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->SetTextColor(0,0,0);
                        $posicion_actual=$posicion_actual+1;
                        $pdf->line(10,$posicion_actual,200,$posicion_actual);
                        $posicion_actual=$posicion_actual+1;
                        
                        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
                        
                        $pdf->MultiCell(150,5,$nombre_facturado,0,'L',false,1,10,$posicion_actual);                        
						$pdf->MultiCell(40,5,number_format($total_desglose_actual, 2, ',', '.')." €",0,'R',false,1,160,$posicion_actual);                          
                        $posicion_actual=$posicion_actual+5;
                
                
// Fin Tabla detalle                


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// *** set signature appearance ***

// create content for signature (image and/or text)
// $pdf->Image('../images/tcpdf_signature.png', 180, 60, 15, 15, 'PNG');

// define active area for signature appearance
// $pdf->setSignatureAppearance(180, 60, 15, 15);

//Close and output PDF document
$pdf->Output("$nombre_factura.pdf", 'I');

} else {
// No es el padre del niño de esta factura
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><br><br><font face="Arial" size="2">No tiene autorización para acceder a la información de este alumno</font><br><br></p>
<p align="center"><font size=2 face="Trebuchet MS"><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 
}

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
