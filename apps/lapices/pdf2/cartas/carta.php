<?php
// ----------- Datos ------------

require("../../configuracion/config.php"); 
require("../../configuracion/autentifica.php");

$id_escuela=$_GET["id_escuela"];
$id_alumno=$_GET["id_alumno"];
$id_padre=$_GET["id_padre"];

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
    $datos_curso_actual=db_fetch_array($busca_curso_actual);
    $id_curso=$datos_curso_actual["id_curso"];
} else {
    $id_curso=0;
}
                

$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_escuela");
if (db_num_rows($busca_datos_centro)!=0) {
    $datos_centro=db_fetch_array($busca_datos_centro);

    $db_nombre_centro=$datos_centro["nombre_centro"];
}

$busca_alumnos_escuela=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_alumno") or die(db_error());

$alumnos_escuela=db_fetch_array($busca_alumnos_escuela);

$busca_padres_alumnos_escuela=db_query("SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.id_padre=$id_padre");

if (db_num_rows($busca_padres_alumnos_escuela)!=0) {
    // Busca padres
    $padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela);

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
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }
    }

    // create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Escuelas Infantiles lápices');
    $pdf->SetTitle('Contraseñas Acceso Área Privada');
    $pdf->SetSubject('Contraseñas Acceso Área Privada');
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

    // set font
    $pdf->SetFont('helvetica', '', 12);

    // add a page
    $pdf->AddPage();

    $x_logo=80;
    $y_logo=3;
    $pdf->Image('../../pdf/logo_redondo.jpg',$x_logo,$y_logo,40);

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica', '', 10);

    $textoCarta = "
<br><br><br><br><br><br>
Estimado/a ".$padres_alumnos_escuela["Nombre"].":<br><br><br>
A partir de este momento, ya puedes disponer del servicio de cámaras web de Lápices. Para ello, únicamente tienes que seguir los siguientes pasos:<br><br>
1º- Accede a la página web de nuestras escuelas: www.escuelas-infantiles.com<br><br>
2º- En la parte superior derecha, podrás ver un botón con el texto \"ZONA PADRES\".<br><br>
2º- Haz clic en ese botón y accederás a una página donde deberás introducir tu nombre de usuario y contraseña.<br><br>
4º- Introduce el nombre de usuario y la contraseña que te proporcionamos a continuación:<br><br>
USUARIO: ".$padres_alumnos_escuela["usuario_webcam"]."<br><br>
CONTRASEÑA: ".$padres_alumnos_escuela["password_webcam"]."<br><br>
5º- Pulsa sobre la palabra ENVIAR.<br><br>
6º- Acabas de acceder a una zona privada exclusiva de los padres de los alumnos de Lápices.<br><br><br><br> 
Esperamos que disfrutéis de este servicio. Un saludo.<br><br><br>
ESCUELA INFANTIL LÁPICES";

    $pdf->writeHTMLCell(0,0, '', '', $textoCarta, 0, 1, 0, true, '', true);          

    //Close and output PDF document
    $pdf->Output("Datos-Acceso-".$padres_alumnos_escuela["Nombre"]."-".$padres_alumnos_escuela["Apellido1"]."-".$padres_alumnos_escuela["Apellido2"].".pdf", 'I');


} else {
// No se encuentra el padre
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><font size=2 face="Trebuchet MS">No se han encontrado los datos del usuario seleccionado<br><br><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 
}

//============================================================+
// END OF FILE                                                
//============================================================+
