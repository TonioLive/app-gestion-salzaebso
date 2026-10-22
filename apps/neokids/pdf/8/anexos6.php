<?php 

require("../../configuracion/config.php");


// Datos
$id_centro=$_GET["id_centro"];

        // Busca el curso actual
        $id_curso_actual=$_GET["curso_seleccionado"];
        if (strlen($id_curso_actual)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
                } else {
                $id_curso_actual=0;
                }
        } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
        }

$consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro;', 's', array($id_centro));
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];

                        $busca_datos_subvencion=db_query_params('SELECT * FROM centros_cursos WHERE id_curso=? AND id_centro=?', 'ss', array($id_curso_actual, $id_centro));
                        $datos_busca_subvencion=db_fetch_array($busca_datos_subvencion);
                        $tope_maximo2=$datos_busca_subvencion["tope_maximo2"];
                        $subvencion2=$datos_busca_subvencion["subvencion2"];
                        $alumnos_ciclo2=$datos_busca_subvencion["alumnos_ciclo2"];

                        $precio_mes[0]=$centros["precio_mes_3"];
                        $precio_mes[1]=$centros["precio_mes_1"];
                        $precio_mes[2]=$centros["precio_mes_2"];
                        $precio_mes[3]=$centros["precio_mes_3"];
                        $precio_mes[4]=$centros["precio_mes_4"];
                        $precio_mes[5]=$centros["precio_mes_5"];
                        
                        $titular=$centros["titular"];
                        $codigo_centro=$centros["codigo_centro"];
                        
                        if ($alumnos_ciclo2>0) {
                        
                        // Alumnos 3
                        $edad_aula=$anio_inicio_curso-3;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_3=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_3=0;
                        $ratio_3=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_3=$alumnos_3+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_3=$ratio_3+$datos_aula_actual["capacidad"];
                                }
                        $ratio_3=round(($ratio_3/$aulas_3),2);

                        // Alumnos 4
                        $edad_aula=$anio_inicio_curso-4;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_4=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_4=0;
                        $ratio_4=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_4=$alumnos_4+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_4=$ratio_4+$datos_aula_actual["capacidad"];
                                }
                        $ratio_4=round(($ratio_4/$aulas_4),2);
                        
                        // Alumnos 5
                        $edad_aula=$anio_inicio_curso-5;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_5=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_5=0;
                        $ratio_5=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_5=$alumnos_5+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_5=$ratio_5+$datos_aula_actual["capacidad"];
                                }
                        $ratio_5=round(($ratio_5/$aulas_5),2);
                        
                        // Busca cuota aula
                        
                        $busca_linea_presupuesto_aula=db_query_params('SELECT * FROM tarifa,servicios_contratados_aula WHERE tarifa.ID=servicios_contratados_aula.id_servicio AND servicios_contratados_aula.id_aula=? AND tarifa.desglosar_como=3 AND tarifa.SUBVENCION=0', 's', array($id_aula)) or die(db_error());
                        if (db_num_rows($busca_linea_presupuesto_aula)!=0) {

                                        $datos_linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);

                                        $precio_edu=$datos_linea_presupuesto_aula["PRECIO_VENTA"];

                                        }
                        
                        // Busca aulas ciclo 2
                        $ocupacion_actual=$alumnos_3+$alumnos_4+$alumnos_5;

                        // Numero de aulas del 2º ciclo
                        $busca_aulas2_ciclo=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND ciclo=2', 's', array($id_curso_actual)) or die(db_error()."-2");
                        $numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);


                        if ($ocupacion_actual!=0) {

                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$ocupacion_actual;

                                } else {

                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$alumnos_ciclo2;
                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$alumnos_ciclo2;

                                }

                        $couta_maxima_mensual=$CM-$SM;
                        
                        }
                        
                        } else {
                        $nombre_centro="";
                        }

error_reporting(7);

include '../class.ezpdf.php';

$pdf = new Cezpdf('a4','landscape');

$pdf -> ezSetMargins(20,0,20,20);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

$tam_fuente=8;
$altura_fila=14.2;
$posicion_inicial=378;

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('C1AnexoIIc1.jpg',$x_logo,$y_logo,841.89,595.28);

$posicion_actual=$posicion_inicial;

$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY(480);
$pdf->ezText($codigo_centro,10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY(480);
$pdf->ezText($nombre_centro_completo,10,array('justification'=>'left','aleft'=>245));
$pdf->ezSetY(480);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>580));
$pdf->ezSetY(458);
$pdf->ezText($titular,10,array('justification'=>'left','aleft'=>75));

// Personal educador

  $consulta_usuarios=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre', 's', array($id_centro)) or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {

  $codigo=$rsslc["id_usuario"];

  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());

  if (db_num_rows($busca_contratos)!=0) {

    // Comprueba si es educador de este ciclo
    
    // AND personal_historial.fecha_inicio<='".date("Y-m-d")."' AND (personal_historial.fecha_fin>'".date("Y-m-d")."' OR personal_historial.fecha_fin is Null)
    
   $busca_cambios=db_query_params('SELECT * FROM personal_historial, centro_aulas WHERE personal_historial.id_aula=centro_aulas.id_aula AND personal_historial.id_escuela=? AND personal_historial.id_empleado=? AND centro_aulas.ciclo=1 AND centro_aulas.id_curso=? AND personal_historial.fecha_inicio<=? AND (personal_historial.fecha_fin>? OR personal_historial.fecha_fin is Null)', 'sssss', array($id_centro, $codigo, $id_curso_actual, (string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());
   $busca_cambios2=db_query_params('SELECT * FROM personal_historial, centro_aulas WHERE personal_historial.id_aula=centro_aulas.id_aula AND personal_historial.id_escuela=? AND personal_historial.id_empleado=? AND centro_aulas.ciclo=2 AND centro_aulas.id_curso=? AND personal_historial.fecha_inicio<=? AND (personal_historial.fecha_fin>? OR personal_historial.fecha_fin is Null)', 'sssss', array($id_centro, $codigo, $id_curso_actual, (string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());


   if ((db_num_rows($busca_cambios)==0) and (db_num_rows($busca_cambios2)==0)) {

    $datos_contrato=db_fetch_array($busca_contratos);

    $nombre_ins=strtoupper($rsslc["Apellido1"]." ".$rsslc["Apellido2"].", ".$rsslc["Nombre"]);
    
    $busca_horario_docente=db_query_params('SELECT * FROM horario_docentes WHERE id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($codigo, $id_centro));

    if (db_num_rows($busca_horario_docente)!=0) {
    
            $datos_horario_docente=db_fetch_array($busca_horario_docente);
            $horas_semana=$datos_horario_docente["horas_semana"];
            
            } else {
            
            $horas_semana=0;
            
            }
                    
    $id_categoria=$datos_contrato["id_categoria"];
    $tipo_contrato=$datos_contrato["tipo_contrato"];
    $salario_base=$datos_contrato["salario_base"];
    $especialidad=$datos_contrato["especialidad"];
    $irpf=$datos_contrato["irpf"];
    $salario_bruto=$datos_contrato["salario_bruto"];

    $busca_primer_contrato=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? ORDER BY fecha_inicio', 's', array($codigo));

    if (db_num_rows($busca_primer_contrato)!=0) {
            $datos_primer_contrato=db_fetch_array($busca_primer_contrato);
            $fecha_primer_contrato=date("d/m/Y",strtotime($datos_primer_contrato["fecha_inicio"]));
            } else {
            $fecha_primer_contrato=date("d/m/Y",strtotime($datos_contrato["fecha_inicio"]));
            }

        $nss=$rsslc["NSS"];

        $titulacion=$rsslc["titulacion"];
        if (strlen($titulacion)!=0) {
                        $titulacion=stripslashes($titulacion);
                        }
        $especialidad=$rsslc["especialidad"];
        if (strlen($especialidad)!=0) {
                        $especialidad=stripslashes($especialidad);
                        }
                        

        $busca_tipos_cat=db_query_params('SELECT * FROM personal_cat_laborales WHERE id_categoria=?', 's', array($id_categoria));
        if (db_num_rows($busca_tipos_cat)!=0) {
                $tipos_cat=db_fetch_array($busca_tipos_cat);
                $categoria_docente=$tipos_cat["nombre_categoria"];
                } else {
                $categoria_docente="";
                }

        if (is_numeric($especialidad)) {
        $busca_tipos_cat=db_query("SELECT * FROM personal_especialidades WHERE id_especialidad=$especialidad") or die(db_error());

        $tipos_cat=db_fetch_array($busca_tipos_cat);
        if (db_num_rows($busca_tipos_cat)!=0) {
                $tipos_cat=db_fetch_array($busca_tipos_cat);
                $especialidad_docente=$tipos_cat["nombre_especialidad"];
                } else {
                $especialidad_docente="";
                }
        } else {
                $especialidad_docente=$especialidad;
        }
                
        $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=$tipo_contrato");
        
        if (db_num_rows($busca_tipos_contrato)!=0) {
                $tipos_cont=db_fetch_array($busca_tipos_contrato);
                $tipo_contrato_abreviatura=$tipos_cont["abreviatura"];
                } else {
                $tipo_contrato_abreviatura="";
                }

                // Imprime fila
                
                $contador++;
      
                $pdf->selectFont('./fonts/Helvetica');
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($contador,$tam_fuente,array('justification'=>'left','aleft'=>65));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nombre_ins,$tam_fuente,array('justification'=>'left','aleft'=>100));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nss,$tam_fuente,array('justification'=>'left','aleft'=>310));

                $pdf->ezSetY($posicion_actual+1);
                if (strlen($especialidad_docente)!=0) {
                        $especialidad_docente=" ".$especialidad_docente;
                        }
                $pdf->ezText($categoria_docente.$especialidad_docente,$tam_fuente-3,array('justification'=>'left','aleft'=>415, 'aright'=>485));
                
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($tipo_contrato_abreviatura,$tam_fuente,array('justification'=>'left','aleft'=>518));
                
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($horas_semana,$tam_fuente,array('justification'=>'left','aleft'=>615));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText(number_format($salario_bruto, 2, ',', '.')." €",$tam_fuente,array('justification'=>'right','aright'=>725));
                $pdf->ezSetY($posicion_actual);
                // $pdf->ezText(number_format($irpf, 2, ',', '.')." %",$tam_fuente,array('justification'=>'right','aright'=>798));
                $pdf->ezText(number_format((($salario_bruto*$irpf)/100), 2, ',', '.')." €",$tam_fuente,array('justification'=>'right','aright'=>793));
                $posicion_actual=$posicion_actual-$altura_fila;

     }
    }
   }

$pdf->ezSetY(208);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>290));
$pdf->ezSetY(208);
$pdf->ezText(date("j"),10,array('justification'=>'left','aleft'=>430));
$pdf->ezSetY(208);
$pdf->ezText($nombre_mes[date("n")],10,array('justification'=>'left','aleft'=>540));
$pdf->ezSetY(208);
$pdf->ezText(date("Y"),10,array('justification'=>'left','aleft'=>730));


$pdf->ezNewPage();

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('C1AnexoIIc2.jpg',$x_logo,$y_logo,841.89,595.28);

$posicion_actual=$posicion_inicial;

$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY(480);
$pdf->ezText($codigo_centro,10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY(480);
$pdf->ezText($nombre_centro_completo,10,array('justification'=>'left','aleft'=>245));
$pdf->ezSetY(480);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>580));
$pdf->ezSetY(458);
$pdf->ezText($titular,10,array('justification'=>'left','aleft'=>75));

// Personal educador

  $consulta_usuarios=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre', 's', array($id_centro)) or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {

  $codigo=$rsslc["id_usuario"];

  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());

  if (db_num_rows($busca_contratos)!=0) {

    // Comprueba si es educador de este ciclo

    // AND personal_historial.fecha_inicio<='".date("Y-m-d")."' AND (personal_historial.fecha_fin>'".date("Y-m-d")."' OR personal_historial.fecha_fin is Null)

   $busca_cambios=db_query_params('SELECT * FROM personal_historial, centro_aulas WHERE personal_historial.id_aula=centro_aulas.id_aula AND personal_historial.id_escuela=? AND personal_historial.id_empleado=? AND centro_aulas.ciclo=1 AND centro_aulas.id_curso=? AND personal_historial.fecha_inicio<=? AND (personal_historial.fecha_fin>? OR personal_historial.fecha_fin is Null)', 'sssss', array($id_centro, $codigo, $id_curso_actual, (string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());
   $busca_cambios2=db_query_params('SELECT * FROM personal_historial, centro_aulas WHERE personal_historial.id_aula=centro_aulas.id_aula AND personal_historial.id_escuela=? AND personal_historial.id_empleado=? AND centro_aulas.ciclo=2 AND centro_aulas.id_curso=? AND personal_historial.fecha_inicio<=? AND (personal_historial.fecha_fin>? OR personal_historial.fecha_fin is Null)', 'sssss', array($id_centro, $codigo, $id_curso_actual, (string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());


   if ((db_num_rows($busca_cambios)==0) and (db_num_rows($busca_cambios2)==0)) {

    $datos_contrato=db_fetch_array($busca_contratos);

    $nombre_ins=strtoupper($rsslc["Apellido1"]." ".$rsslc["Apellido2"].", ".$rsslc["Nombre"]);

    $busca_horario_docente=db_query_params('SELECT * FROM horario_docentes WHERE id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($codigo, $id_centro));

    if (db_num_rows($busca_horario_docente)!=0) {

            $datos_horario_docente=db_fetch_array($busca_horario_docente);
            $horas_semana=$datos_horario_docente["horas_semana"];

            } else {

            $horas_semana=0;

            }

    $id_categoria=$datos_contrato["id_categoria"];
    $tipo_contrato=$datos_contrato["tipo_contrato"];
    $salario_base=$datos_contrato["salario_base"];
    $especialidad=$datos_contrato["especialidad"];
    $irpf=$datos_contrato["irpf"];
    $salario_bruto=$datos_contrato["salario_bruto"];

    $busca_primer_contrato=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? ORDER BY fecha_inicio', 's', array($codigo));

    if (db_num_rows($busca_primer_contrato)!=0) {
            $datos_primer_contrato=db_fetch_array($busca_primer_contrato);
            $fecha_primer_contrato=date("d/m/Y",strtotime($datos_primer_contrato["fecha_inicio"]));
            } else {
            $fecha_primer_contrato=date("d/m/Y",strtotime($datos_contrato["fecha_inicio"]));
            }

        $nss=$rsslc["NSS"];

        $titulacion=$rsslc["titulacion"];
        if (strlen($titulacion)!=0) {
                        $titulacion=stripslashes($titulacion);
                        }
        $especialidad=$rsslc["especialidad"];
        if (strlen($especialidad)!=0) {
                        $especialidad=stripslashes($especialidad);
                        }


        $busca_tipos_cat=db_query_params('SELECT * FROM personal_cat_laborales WHERE id_categoria=?', 's', array($id_categoria));
        if (db_num_rows($busca_tipos_cat)!=0) {
                $tipos_cat=db_fetch_array($busca_tipos_cat);
                $categoria_docente=$tipos_cat["nombre_categoria"];
                } else {
                $categoria_docente="";
                }

        if (is_numeric($especialidad)) {
        $busca_tipos_cat=db_query("SELECT * FROM personal_especialidades WHERE id_especialidad=$especialidad") or die(db_error());

        $tipos_cat=db_fetch_array($busca_tipos_cat);
        if (db_num_rows($busca_tipos_cat)!=0) {
                $tipos_cat=db_fetch_array($busca_tipos_cat);
                $especialidad_docente=$tipos_cat["nombre_especialidad"];
                } else {
                $especialidad_docente="";
                }
        } else {
                $especialidad_docente=$especialidad;
        }

        $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=$tipo_contrato");

        if (db_num_rows($busca_tipos_contrato)!=0) {
                $tipos_cont=db_fetch_array($busca_tipos_contrato);
                $tipo_contrato_abreviatura=$tipos_cont["abreviatura"];
                } else {
                $tipo_contrato_abreviatura="";
                }

                // Imprime fila

                $contador++;

                $pdf->selectFont('./fonts/Helvetica');
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($contador,$tam_fuente,array('justification'=>'left','aleft'=>65));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nombre_ins,$tam_fuente,array('justification'=>'left','aleft'=>100));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nss,$tam_fuente,array('justification'=>'left','aleft'=>310));

                $pdf->ezSetY($posicion_actual+1);
                if (strlen($especialidad_docente)!=0) {
                        $especialidad_docente=" ".$especialidad_docente;
                        }
                $pdf->ezText($categoria_docente.$especialidad_docente,$tam_fuente-3,array('justification'=>'left','aleft'=>415, 'aright'=>485));

                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($tipo_contrato_abreviatura,$tam_fuente,array('justification'=>'left','aleft'=>518));

                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($horas_semana,$tam_fuente,array('justification'=>'left','aleft'=>615));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText(number_format($salario_bruto, 2, ',', '.')." €",$tam_fuente,array('justification'=>'right','aright'=>725));
                $pdf->ezSetY($posicion_actual);
                // $pdf->ezText(number_format($irpf, 2, ',', '.')." %",$tam_fuente,array('justification'=>'right','aright'=>798));
                $pdf->ezText(number_format((($salario_bruto*$irpf)/100), 2, ',', '.')." €",$tam_fuente,array('justification'=>'right','aright'=>793));
                $posicion_actual=$posicion_actual-$altura_fila;

     }
    }
   }

$pdf->ezSetY(208);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>290));
$pdf->ezSetY(208);
$pdf->ezText(date("j"),10,array('justification'=>'left','aleft'=>430));
$pdf->ezSetY(208);
$pdf->ezText($nombre_mes[date("n")],10,array('justification'=>'left','aleft'=>540));
$pdf->ezSetY(208);
$pdf->ezText(date("Y"),10,array('justification'=>'left','aleft'=>730));
$pdf->ezStream();

?>
