<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php"); 

$id_aula=$_GET["id_aula"];
$ano_sel=$_GET["ano"];

// Busca datos del aula
$consulta_datos_educadora=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_aula, $centro_actual)) or die(db_error());
$datos_aula=db_fetch_array($consulta_datos_educadora);


$nombre_trabajador=$datos_aula["Nombre"]." ".$datos_aula["Apellido1"]." ".$datos_aula["Apellido2"];
$nif_trabajador=utf8_encode($datos_aula["DNI"]);
$nss_trabajador=utf8_encode($datos_aula["NSS"]);
$razon_social_empresa=utf8_encode("Neokids, s.l.");
$nif_empresa=utf8_encode("B98640428");
$ccc_empresa=utf8_encode("46142219986");
$servicio_comun=$datos_aula["servicio_comun"];
$horas_semana=$datos_aula["horas_semana"];
$horas[1]=$datos_aula["horas_l"];
$horas[2]=$datos_aula["horas_m"];
$horas[3]=$datos_aula["horas_x"];
$horas[4]=$datos_aula["horas_j"];
$horas[5]=$datos_aula["horas_v"];
$horas[6]=$datos_aula["horas_s"];
$horas[7]=$datos_aula["horas_d"];   

$nombre_archivo=utf8_encode($datos_aula["Nombre"]."_".$datos_aula["Apellido1"]."_".$datos_aula["Apellido2"]."-".$ano_sel);

/*
header("Content-Type:   application/pdf");
header("Content-Disposition: inline; filename=Registro_Anual_".$nombre_archivo.".pdf");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
*/

// error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');
$posicion_inicial=795;
$posicion_inicial_tabla=680;

$pdf -> ezSetMargins(20,0,20,20);

// Color de las lineas Negro
$pdf->setStrokeColor('0.00','0.00','0.00','');

for($mes_sel=1;$mes_sel<=12;$mes_sel++) { // Inicio bucle meses

	$periodo_liquidacion=utf8_encode($nombre_mes[$mes_sel]." ".$ano_sel);

	$pdf->ezSetY($posicion_inicial);

	$pdf->selectFont('./fonts/Helvetica-Bold');

	$pdf->ezText("LISTADO RESUMEN MENSUAL DEL REGISTRO DE JORNADA (HORAS)",14,array('justification'=>'center'));

	$pdf->selectFont('./fonts/Helvetica');

	$data0=array();
	$cols0=array("EMPRESA"=>"EMPRESA","TRABAJADOR"=>"TRABAJADOR/A");
	$data0[]=array("EMPRESA"=>"Nombre o Razón Social:     ".$razon_social_empresa,"TRABAJADOR"=>"Nombre:     ".$nombre_trabajador);
	$data0[]=array("EMPRESA"=>"CIF:     ".$nif_empresa,"TRABAJADOR"=>"NIF:     ".$nif_trabajador);
	$data0[]=array("EMPRESA"=>"C.C.C.:     ".$ccc_empresa,"TRABAJADOR"=>"NAF:     ".$nss_trabajador);
	$data0[]=array("EMPRESA"=>"Período de liquidación:     ".$periodo_liquidacion,"TRABAJADOR"=>"Fecha:     ");

	$pdf->ezSetY($posicion_inicial-32);

	$pdf->ezTable($data0,$cols0,'',array(
		'xPos'=>'center',
		'xOrientation'=>'center',
		'fontSize'=>8,			
		'gridlines'=> EZ_GRIDLINE_DEFAULT,
		'shadeHeadingCol'=>array(0.8,0.8,0.8),
		'width'=>500
	));

	$pdf->ezSetY($posicion_inicial_tabla);

	$data = array();
	$cols = array("Dia"=>"Día del mes", "Horas_o"=>"Horas Ordinarias", "Horas_c"=>"Horas Complementarias Pactadas", "Horas_v"=>"Horas Complementarias Voluntarias");

	$contador_data=0;
	
	$numero_dias_mes=date("t",strtotime($ano_sel."-".$mes_sel."-1"));
	$total_mes=0;
	
	for ($dia_mes=1;$dia_mes<=31;$dia_mes++) {

		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes))), (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));
		
		if (db_num_rows($busca_curso_actual)!=0) {
		        $datos_curso_actual=db_fetch_array($busca_curso_actual);
		        $id_curso_actual=$datos_curso_actual["id_curso"];
		        } else {
		        $id_curso_actual=0;
		        }

	    $horas_diferencia=$horas[date("N",strtotime($ano_sel."-".$mes_sel."-".$dia_mes))];
	    $horas_mostrar="";
	    
		// Comprueba si tiene contrato en vigor
  		$fecha_control=date("Y-m-d",strtotime($ano_sel."-".$mes_sel."-".$dia_mes));
  		$busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($id_aula, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());	    
	    
	    $en_contrato=db_num_rows($busca_contratos);
	    
		if (($horas_diferencia!=0) and ($en_contrato!=0)) {
			// Busca en el calendario laboral
			
			$busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes))))) or die(db_error());
			if (db_num_rows($busca_dia_en_calendario)!=0) {
					$datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
					if ($datos_dia_en_calendario["estado"]==1) {
							$horas_diferencia=0;
							}
					}
			if ($horas_diferencia!=0) {
				$horas_mostrar=number_format($horas_diferencia,2,",",".");				
			}
		}       
        
        $data[]=array('Dia'=>$dia_mes, "Horas_o"=>$horas_mostrar, "Horas_c"=>"", "Horas_v"=>"");                
        
        $total_mes+=$horas_diferencia;
        
	}

    // Totales

	$data[]=array('Dia'=>"Total Mes", "Horas_o"=>$total_mes, "Horas_c"=>"", "Horas_v"=>"");
	
	$pdf->ezTable($data,$cols,'',array(
		'xPos'=>'center',
		'xOrientation'=>'center',
		'fontSize'=>10,			
		'gridlines'=> EZ_GRIDLINE_DEFAULT,
		'shadeHeadingCol'=>array(0.8,0.8,0.8),
		'width'=>500,
		'cols'=> array(
			'Horas_o'=>array('justification'=>'right'),
			'Horas_c'=>array('justification'=>'right')
		)
	));
	
	$pdf->setLineStyle(1,'','','','');
	
	$pdf->ezSetY(155);
	$pdf->ezText("Firma y sello de la empresa",9,array('justification'=>'left','aleft'=>46));
	$pdf->ezSetY(155);
	$pdf->ezText("Firma trabajador/a",9,array('justification'=>'left','aleft'=>321));	
	
	$pdf->rectangle(42,80,225,75);	
	
	$pdf->rectangle(317,80,225,75);
	
	$pdf->ezSetY(70);
	$pdf->ezText('Registro realizado en cumplimiento de la letra h) del artículo 1 del R.D.-Ley 16/2013, de 20 de diciembre por el que se modifica el artículo 12.5 del E.T., por el que se establece que "La jornada de los trabajadores a tiempo parcial se registrará día a día y se totalizará mensualmente, entregando copia al trabajador, junto con el recibo de salarios, del resumen de todas las horas realizadas en cada mes, tanto de las ordinarias como de las complementarias en sus distintas modalidades.
	
	El empresario deberá conservar los resúmenes mensuales de los registros de jornada durante un periodo mínimo de cuatro años. El incumplimiento empresarial de estas obligaciones de registro tendrá por consecuencia jurídica la de que el contrato presuma celebrado a jornada completa, salvo prueba en contrario que acredite el carácter parcial de los servicios.',6,array('justification'=>'left','aleft'=>42, 'aright'=>542));
	
	
	// Nueva págnina
	if ($mes_sel<12) {
		$pdf->ezNewPage();
	}

} // Fin bucle meses	

$pdf->ezStream();

?>
