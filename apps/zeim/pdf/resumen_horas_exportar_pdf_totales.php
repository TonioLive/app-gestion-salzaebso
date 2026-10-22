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
$razon_social_empresa=utf8_encode("ZAEBSAL, s.l.");
$nif_empresa=utf8_encode("B97324123");
$ccc_empresa=utf8_encode("46118861376");
$servicio_comun=$datos_aula["servicio_comun"];
$horas_semana=$datos_aula["horas_semana"];
if ($horas_semana>0) {
	$horas_dia=round(($horas_semana/5),2);
	} else {
	$horas_dia=0;			
	}

$nombre_archivo=utf8_encode($datos_aula["Nombre"]."_".$datos_aula["Apellido1"]."_".$datos_aula["Apellido2"]."-".$ano_sel);

/*
header("Content-Type:   application/pdf");
header("Content-Disposition: inline; filename=Registro_Anual_".$nombre_archivo.".pdf");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
*/

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');
$posicion_inicial=775;
$posicion_inicial_tabla=655;

$pdf -> ezSetMargins(20,0,20,20);

// Color de las lineas Negro
$pdf->setStrokeColor('0.00','0.00','0.00','');

for($mes_sel=1;$mes_sel<=12;$mes_sel++) { // Inicio bucle meses

	$periodo_liquidacion=utf8_encode($nombre_mes[$mes_sel]." ".$ano_sel);

	$pdf->ezSetY($posicion_inicial);

	$pdf->selectFont('./fonts/Helvetica-Bold');

	$pdf->ezText("REGISTRO DIARIO DE JORNADA",14,array('justification'=>'center'));

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
	$cols = array("Dia"=>"Día del mes", "Horas_o"=>"Horas Ordinarias", "Horas_c"=>"Horas Complementarias");

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

        $hora_entrada_man="";
        $hora_entrada_tar="";
        $hora_salida_man="";
        $hora_salida_tar="";
        $horas_diferencia="0";

        if ($dia_mes<=$numero_dias_mes) {

        $busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND fecha=? ORDER BY fecha,turno', 'ss', array($id_aula, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));                
        
        if (db_num_rows($busca_asistencia)!=0) {
	     
	     	$horas_diferencia=$horas_dia;   
	        
	        $horas_mostrar=number_format($horas_diferencia,2,",",".");
	        
        } else {
	        
	        $horas_diferencia=0;
	        $horas_mostrar="";
	        
        }  

		if (($horas_mostrar=="") and ($servicio_comun==1)) {
			// Busca en el calendario laboral
			
			$busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes))))) or die(db_error());
			if (db_num_rows($busca_dia_en_calendario)!=0) {
					$datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
					if ($datos_dia_en_calendario["estado"]!=1) {
							$horas_diferencia=$horas_dia;
							}
					} else {
					if ($id_curso_actual!=0) {
						$horas_diferencia=$horas_dia;
						}
					}
			if ($horas_diferencia!=0) {
				$horas_mostrar=number_format($horas_diferencia,2,",",".");				
			}
		}
        
        $data[]=array('Dia'=>$dia_mes, "Horas_o"=>$horas_mostrar, "Horas_c"=>"");                
        
        $total_mes+=$horas_diferencia;
        
	   }
	}

    // Totales

	$data[]=array('Dia'=>"Total Mes", "Horas_o"=>$total_mes, "Horas_c"=>"");
	
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
	
	$pdf->ezSetY(125);
	$pdf->ezText("Firma y sello de la empresa",9,array('justification'=>'left','aleft'=>46));
	$pdf->ezSetY(125);
	$pdf->ezText("Firma trabajador/a",9,array('justification'=>'left','aleft'=>321));	
	
	$pdf->rectangle(42,50,225,75);	
	
	$pdf->rectangle(317,50,225,75);	
	
	// Nueva págnina
	if ($mes_sel<12) {
		$pdf->ezNewPage();
	}

} // Fin bucle meses	

$pdf->ezStream();

?>
