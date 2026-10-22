<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$base_datos_alumnos="escuelamunicipal.";

$lista_alumnos=$_GET["lista_alumnos"]; 

$lista_alumnos=str_replace("L","",$lista_alumnos);                
        
$lista_alumnos_array=explode(",",$lista_alumnos);
if (count($lista_alumnos_array)==1) {
        $id_alumno=$lista_alumnos_array[0];
	}
        
if (strlen($id_alumno)==0) {
        $id_alumno=$_GET["id_alumno"];
	}

if ($id_alumno) {
        // Uno solo
        $busca_alumnos_escuela=db_query("SELECT *, escuelamunicipal.alumnos.id_centro AS centro_alumno FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=".$id_alumno.";") or die(db_error()."-1");
	} else {             
        // Muchos   
        $filtro_alumnos="";
        foreach ($lista_alumnos_array as $valor) {
            $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";                
        }
        if (strlen($filtro_alumnos)!=0) {
                $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }
        $busca_alumnos_escuela=db_query("SELECT *, escuelamunicipal.alumnos.id_centro AS centro_alumno FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-2");                           
	}


error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

// Inicio bucle

$contador=0;

while($alumnos_escuela=db_fetch_array($busca_alumnos_escuela)) {

	if ($contador>0) {
		$pdf->ezNewPage();
	}

	$busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE id_curso=?', 's', array($alumnos_escuela["id_curso"])) or die(db_error());

    if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso=$datos_curso_actual["id_curso"];
			$nombre_curso=$datos_curso_actual["nombre"];
		} else {
			$id_curso=0;
			$nombre_curso="";
		}
                
	$id_centro_alumno=$alumnos_escuela["centro_alumno"];
	
	if ($id_centro_alumno<=10) {
	
	$busca_datos_centro=db_query_params('SELECT * FROM escuelamunicipal.centros WHERE id_centro=?', 's', array($alumnos_escuela["centro_alumno"]));
	if (db_num_rows($busca_datos_centro)!=0) {
			$datos_centro=db_fetch_array($busca_datos_centro);
			$db_nombre_centro=$datos_centro["nombre_centro"];
		}
                        
	// Nombre foto
	$nombre_foto="foto_usu".$alumnos_escuela["id_usuario"].".jpg";

	$upload_path = "/var/www2/escuelamunicipal/fotos_usuarios";

	if (!file_exists($upload_path."//$nombre_foto")) {
    		$nombre_foto="nada.jpg";
    	}
                        
	// Aquí el logo
	$x_logo=0;
	$y_logo=0;
	$pdf->addJpegFromFile('portada_informe'.$id_centro_alumno.'.jpg',$x_logo,$y_logo,595.28,841.89);

	// Color de las lineas Negro
	$pdf->setStrokeColor('0.0','0.00','0.00','');

	$posicion_texto1=346;
	$posicion_texto2=180;

	$pdf->selectFont('./fonts/Helvetica-Bold');
	$pdf->ezSetY($posicion_texto1);
	$pdf->ezText("CURSO $nombre_curso",24,array('justification'=>'left','aleft'=>266));
	$pdf->selectFont('./fonts/Helvetica-Bold');
	$pdf->ezSetY($posicion_texto2);
	$pdf->ezText("Nombre alumno:",12,array('justification'=>'left','aleft'=>175));
	$pdf->selectFont('./fonts/Helvetica');
	$pdf->ezSetY($posicion_texto2);
	$pdf->ezText($alumnos_escuela["Nombre"]." ".$alumnos_escuela["Apellido1"]." ".$alumnos_escuela["Apellido2"],12,array('justification'=>'left','aleft'=>275));
	$pdf->selectFont('./fonts/Helvetica-Bold');
	$pdf->ezSetY($posicion_texto2-22);
	$pdf->ezText("Fecha de nacimiento:",12,array('justification'=>'left','aleft'=>175));
	$pdf->selectFont('./fonts/Helvetica');
	$pdf->ezSetY($posicion_texto2-22);
	$pdf->ezText(date("d/m/Y",strtotime($alumnos_escuela["fecha_nacimiento"])),12,array('justification'=>'left','aleft'=>300));

	$pdf->addJpegFromFile($upload_path.'/'.$nombre_foto,80,110,75,100);

	$contador++;
	}
}

if ($contador>0) {

	$pdf->ezStream();

	} else {
	?>
	<h1>No se ha generado ninguna portada. Esta función solo está disponible para alumnos de Lápices y Peter Pan</h1>
	<?php 
	}
?>