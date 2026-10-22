<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio=$_GET["dia"];

if (strlen($inicio)==0) {
    $inicio=time();  
    $fecha_time_stamp=time();
} else {
    $fecha_time_stamp=$inicio;
}
 
$fecha_albaran=date("Y-m-d",$fecha_time_stamp);

$busca_alabaran=db_query("SELECT * FROM cocina_albaranes_entrega WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."';");
             
$datos_albaran=db_fetch_array($busca_alabaran);

$temperatura_salida=unserialize($datos_albaran["temperaturas_salida"]);

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a7','landscape');

$pdf -> ezSetMargins(10,10,10,10);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');

// Aquí el logo
/*
$x_logo=75;
$y_logo=715;
$pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,150,54);

$x_logo=75;
$y_logo=635;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);
*/

$numPaginas = count($temperatura_salida);
$contadorPaginas=0;
foreach ($temperatura_salida as $clave => $valor) {

    // Busca plato
    $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$clave ORDER BY nombre_plato");
    if (db_num_rows($busca_comida)!=0) {
        $datos_comida=db_fetch_array($busca_comida);

        $id_plato=$datos_comida["id_plato"];
        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
        

        $alergenos="";
        $ingredientes="";
        $lista_alergenos=array();

        while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
            $ingredientes.=app_url_text_utf8($datos_ingredientes["nombre_alimento"]).", ";

            $contadorAlergias=0;
            foreach ($array_alergias as $alergia) {
                if (($datos_ingredientes[$alergia]==1) and (in_array($lista_alergenos, $array_nombres_alergias[$contadorAlergias])==false))  {
                    $alergiasYa[$alergia]=1;
                    $lista_alergenos[$alergia]=$array_nombres_alergias[$contadorAlergias];
                }
                $contadorAlergias++;
            }
        }

        if ($ingredientes!="") {
            $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
        }

        $alergenos = implode(", ", $lista_alergenos);        

        if (strlen($alergenos)==0) {
            $alergenos = "No contiene alergenos conocidos";
        }        
        
        $nombres_platos[$comidaActual]=app_url_text_utf8($datos_comida["nombre_plato"]);

        $pdf->selectFont('./fonts/Helvetica');
        $pdf->ezSetY(198);
        $pdf->ezText(app_url_text_utf8("ZAEBSAL SL - C/ RIO NERVION, 21-23. 46025 VALENCIA - 96 338 44 08"),5,array('justification'=>'left'));
        $pdf->ezSetY(198);
        $pdf->ezText(app_url_text_utf8("Nº RGSEAA: 26.09175/4
        "),5,array('justification'=>'right'));
        $pdf->selectFont('./fonts/Helvetica-Bold');
        $pdf->ezText(app_url_text_utf8($datos_comida["nombre_plato"]."
"),12,array('justification'=>'left'));
        $pdf->selectFont('./fonts/Helvetica');
        $pdf->ezText("Ingredientes: ".$ingredientes."
        
Tª salida: ".$valor." ºC
        
<b>Alérgenos:</b> ".$alergenos."

<b>Conservacion:</b> ".ucfirst(app_url_text_utf8($datos_comida["instrucciones_conservacion"]))."

<b>Fecha de caducidad / Lote:</b> ".date("d/m/Y",$fecha_time_stamp)."
",8,array('justification'=>'left'));

        $contadorPaginas++;
        if ($contadorPaginas<$numPaginas) {
            $pdf->ezNewPage(); 
        }
    }
            
            
}  

$pdf->ezStream();

?>
