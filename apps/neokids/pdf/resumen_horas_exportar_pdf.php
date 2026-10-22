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

// error_reporting(7);

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
	$cols = array("Dia"=>"Día del mes", "Entrada_m"=>"Entrada Mañana", "Entrada_t"=>"Entrada Tarde", "Salida_m"=>"Salida Mañana", "Salida_t"=>"Salida Tarde", "Horas_o"=>"Horas Ordinarias", "Horas_c"=>"Horas Complementarias");

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

        $hora_entrada_man='';
        $hora_entrada_tar='';
        $hora_salida_man='';
        $hora_salida_tar='';
        $horas_diferencia=0;

        if ($dia_mes<=$numero_dias_mes) {

        $busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND fecha=? ORDER BY fecha,turno', 'ss', array($id_aula, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));                
        
        /*
        
        if (mysql_num_rows($busca_asistencia)!=0) {
	     
	     	$horas_diferencia=$horas_dia;   
	        
	        $horas_mostrar=number_format($horas_diferencia,2,",",".");
	        
        } else {
	        
	        $horas_diferencia=0;
	        $horas_mostrar="";
	        
        } 
        */               
        
	$busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND fecha=? ORDER BY fecha,turno', 'ss', array($id_aula, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));		
	
	while($datos_asistencia=db_fetch_array($busca_asistencia)){
		
                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }
                
                $horas_diferencia=($diferencia_entrada/60)/60;
                if ($horas_diferencia<round((($diferencia_entrada/60)/60),0)) {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0)-1;
                        } else {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0);
                        }

                $minutos_diferencia=$horas_diferencia-$horas_diferencia1;
                $minutos_diferencia=round(($minutos_diferencia*60),0);
                
                $total_horas=intval($total_horas)+intval($horas_diferencia1);
                $total_minutos=intval($total_minutos)+intval($minutos_diferencia);
                
                $horas_diferencia1="0".$horas_diferencia1;
                $minutos_diferencia="0".$minutos_diferencia;
                
                $horas_diferencia1=substr($horas_diferencia1,strlen($horas_diferencia1)-2,2);
                $minutos_diferencia=substr($minutos_diferencia,strlen($minutos_diferencia)-2,2);
                
                $hora_entrada_texto=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));
                $hora_salida_texto=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));
                
				if ($hora_entrada_texto=="01:00:00") {
                	$hora_entrada_texto="No ficha entrada";
                }
                
                if ($hora_salida_texto=="01:00:00") {
                	$hora_salida_texto="No ficha salida";
                }                
                
                if ($diferencia_entrada!=0) {
                        $diferencia_horas=$horas_diferencia1.":".$minutos_diferencia;
                        } else {
                        $diferencia_horas="00:00";
                        }
                        
                if (date("G",strtotime($datos_asistencia["hora_entrada"]))<15) {
                	$turno=0;
                } else {
                	$turno=1;
                } 
                        
                if ($turno==0) {
	 	                $hora_entrada_man=$hora_entrada_texto;
	 	                $hora_salida_man=$hora_salida_texto;
                        } else {
                        $hora_entrada_tar=$hora_entrada_texto;
                        $hora_salida_tar=$hora_salida_texto;
                        }                                      	
		}        
        
        
        
		if (($horas_diferencia==0) and ($servicio_comun==1)) {
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
		
        $data[]=array('Dia'=>$dia_mes, "Entrada_m"=>$hora_entrada_man, "Entrada_t"=>$hora_entrada_tar, "Salida_m"=>$hora_salida_man, "Salida_t"=>$hora_salida_tar, "Horas_o"=>round($horas_diferencia,2), "Horas_c"=>"");                
        
        $total_mes+=$horas_diferencia;
        
	   }
	}

    // Totales

	$data[]=array('Dia'=>"Total Mes", "Horas_o"=>round($total_mes,2), "Horas_c"=>"");
	
	$pdf->ezTable($data,$cols,'',array(
		'xPos'=>'center',
		'xOrientation'=>'center',
		'fontSize'=>9,			
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