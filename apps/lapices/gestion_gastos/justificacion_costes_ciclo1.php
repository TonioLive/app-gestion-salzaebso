<?php  require("../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
include("cuentas_y_tipos.php");

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$id_centro_sel_costes=$centro_actual;

$inicio_periodo=strtotime("2008-09-01");
$fin_periodo=strtotime("2009-04-30");

if (strlen($id_centro_sel_costes)==0) {
                $id_centro_sel_costes="0";
                $filtro_sc="";
                } else {
                $filtro_sc=" WHERE id_centro=$id_centro_sel_costes";
                }
                
$filtros=" WHERE finalizado=1 ";
$filtros_fechas="";
$filtros_fechas1="";

$filtros=$filtros." AND fecha_vencimiento>='".date("Y-m-d",$inicio_periodo)."' AND fecha_vencimiento<='".date("Y-m-d",$fin_periodo)."'";
$filtros_fechas=$filtros_fechas." AND personal_pagos.fecha_pago>='".date("Y-m-d",$inicio_periodo)."' AND personal_pagos.fecha_pago<='".date("Y-m-d",$fin_periodo)."'";

        
$filtros_fechas1=$filtros_fechas;
if ($id_centro_sel_costes!="0") {
        $filtros=$filtros." AND (id_centro=".$id_centro_sel_costes." OR id_centro=0)";
        $filtros_fechas=$filtros_fechas." AND usuarios.id_centro=".$id_centro_sel_costes;
        }

// Busca alumnos por escuela

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_fin DESC");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }

$alumnos_por_escuela=array();
$alumnos_por_comedor=array();
$total_alumnos_centro=0;
$total_alumnos_comedor=0;
$consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");
$numero_de_escuelas=db_num_rows($consulta_centros);
while($centros=db_fetch_array($consulta_centros)){

                $busca_alumnos_escuela=db_query("SELECT * FROM alumnos WHERE id_curso=$id_curso_actual AND id_centro=".$centros["id_centro"]);

                $alumnos_por_escuela[$centros["id_centro"]]=db_num_rows($busca_alumnos_escuela);
                $total_alumnos_centro=$total_alumnos_centro+$alumnos_por_escuela[$centros["id_centro"]];

                $busca_alumnos_comedor=db_query("SELECT * FROM alumnos WHERE id_curso=$id_curso_actual AND comedor=2 AND id_centro=".$centros["id_centro"]);

                $alumnos_por_comedor[$centros["id_centro"]]=db_num_rows($busca_alumnos_comedor);
                $total_alumnos_comedor=$total_alumnos_comedor+$alumnos_por_comedor[$centros["id_centro"]];

                }

// calcula numero ninios
        // Busca el curso actual
        $id_curso_actual=$_GET["curso_seleccionado"];
        if (strlen($id_curso_actual)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
                } else {
                $id_curso_actual=0;
                }
        } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
        }
        
$concepto_buscado="Noviembre de ".$anio_inicio_curso;
$total_cheque=0;

$consulta_centros=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_sel_costes ORDER BY nombre_centro;");
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];

                        $busca_datos_subvencion=db_query("SELECT * FROM centros_cursos WHERE id_curso=$id_curso_actual AND id_centro=$id_centro_sel_costes");
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
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro_sel_costes AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_3=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_3=0;
                        $ratio_3=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.aula=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
                                $alumnos_3=$alumnos_3+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_3=$ratio_3+$datos_aula_actual["capacidad"];
                                
                                // busca cheque escolar septiembre
                                while($datos_alumnos_cheque=db_fetch_array($busca_inscripcion_curso_actual)){
                                        $busca_facturado_por_alumno=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=".$datos_alumnos_cheque["id_alumno"]." AND detalles_factura.concepto like 'Cheque%' AND detalles_factura.concepto like '%$concepto_buscado';") or die(db_error());
                                        while($facturado_por_alumno=db_fetch_array($busca_facturado_por_alumno)){
                                                $total_cheque=$total_cheque+round($facturado_por_alumno["cantidad"]*$facturado_por_alumno["precio"],2);
                                                }
                                        }
                                
                                }
                        if ($aulas_3>0) {
                                $ratio_3=round(($ratio_3/$aulas_3),2);
                                } else {
                                $ratio_3=0;
                                }

                        // Alumnos 4
                        $edad_aula=$anio_inicio_curso-4;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro_sel_costes AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_4=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_4=0;
                        $ratio_4=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.aula=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
                                $alumnos_4=$alumnos_4+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_4=$ratio_4+$datos_aula_actual["capacidad"];
                                
                                // busca cheque escolar septiembre
                                while($datos_alumnos_cheque=db_fetch_array($busca_inscripcion_curso_actual)){
                                        $busca_facturado_por_alumno=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=".$datos_alumnos_cheque["id_alumno"]." AND detalles_factura.concepto like 'Cheque%' AND detalles_factura.concepto like '%$concepto_buscado';");
                                        while($facturado_por_alumno=db_fetch_array($busca_facturado_por_alumno)){
                                                $total_cheque=$total_cheque+round($facturado_por_alumno["cantidad"]*$facturado_por_alumno["precio"],2);
                                                }
                                        }
                                
                                }
                        $ratio_4=round(($ratio_4/$aulas_4),2);

                        // Alumnos 5
                        $edad_aula=$anio_inicio_curso-5;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro_sel_costes AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_5=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_5=0;
                        $ratio_5=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.aula=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
                                $alumnos_5=$alumnos_5+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_5=$ratio_5+$datos_aula_actual["capacidad"];
                                
                                // busca cheque escolar septiembre
                                while($datos_alumnos_cheque=db_fetch_array($busca_inscripcion_curso_actual)){
                                        $busca_facturado_por_alumno=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=".$datos_alumnos_cheque["id_alumno"]." AND detalles_factura.concepto like 'Cheque%' AND detalles_factura.concepto like '%$concepto_buscado';");
                                        while($facturado_por_alumno=db_fetch_array($busca_facturado_por_alumno)){
                                                $total_cheque=$total_cheque+round($facturado_por_alumno["cantidad"]*$facturado_por_alumno["precio"],2);
                                                }
                                        }
                                
                                }
                        $ratio_5=round(($ratio_5/$aulas_5),2);

                        // Busca cuota aula

                        $busca_linea_presupuesto_aula=db_query("SELECT * FROM tarifa,servicios_contratados_aula WHERE tarifa.ID=servicios_contratados_aula.id_servicio AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.desglosar_como=3 AND tarifa.SUBVENCION=0") or die(db_error());
                        if (db_num_rows($busca_linea_presupuesto_aula)!=0) {

                                        $datos_linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);

                                        $precio_edu=$datos_linea_presupuesto_aula["PRECIO_VENTA"];

                                        }

                        // Busca aulas ciclo 2
                        $ocupacion_actual=$alumnos_3+$alumnos_4+$alumnos_5;

                        // Numero de aulas del 2º ciclo
                        $busca_aulas2_ciclo=db_query("SELECT * FROM centro_aulas WHERE id_curso=$id_curso_actual AND ciclo=2") or die(db_error()."-2");
                        $numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);

                        $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                        $SM=($subvencion2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                        $couta_maxima_mensual=$CM-$SM;

                        }

                        } else {
                        $nombre_centro="";
                        }

$numero_ninios=$alumnos_3+$alumnos_4+$alumnos_5;
$numero_ninios=72;

$totales_doc=array();

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 

                        $total_costes=0;
                        $total_costes_pagado=0;

                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=2 face="<?= $fuente_general; ?>"><b>Total gastos por partida</b></font></td>
                        </tr>
                          <?php 

                          $busca_costes_por_partida=db_query("SELECT gestion_costes.partida FROM gestion_costes$filtros GROUP BY gestion_costes.partida ORDER BY gestion_costes.partida;");
                          while($costes_por_partida=db_fetch_array($busca_costes_por_partida)) {

                        // Comprueba si la partida debe ser contabilizada
                        $partida_coste_actual=$costes_por_partida["partida"];

                        $busca_equivalencia=db_query("SELECT * FROM equivalencias_partidas WHERE partida='$partida_coste_actual' AND partida_consell<>-1");

                        if (db_num_rows($busca_equivalencia)!=0) {

                          $datos_equivalencia=db_fetch_array($busca_equivalencia);
                          
                          $id_equivalencia=$datos_equivalencia["partida_consell"];

                          $total_pagado_part_act=0;
                          $total_part=0;
                          
                          $busca_costes_por_partida_pagados=db_query("SELECT * FROM gestion_costes$filtros AND ((gestion_costes.partida)='".$costes_por_partida["partida"]."');");
                          while($datos_costes_por_partida=db_fetch_array($busca_costes_por_partida_pagados)) {
                          
                        if ($datos_costes_por_partida["id_centro"]==0) {

                                $proporcional=$datos_costes_por_partida["proporcional"];

                                $escuelas1=$datos_costes_por_partida["escuelas"];

                                $contador_escuelas=0;

                                if (strlen($escuelas1)!=0) {

                                        $escuelas2=explode(",",$escuelas1);

                                        for ($cs=0;$cs<=count($escuelas2);$cs++) {
                                                if ($proporcional==1) {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_escuela[$escuelas2[$cs]]/$total_alumnos_centro),2)),2);
                                                        } else {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]/$numero_de_escuelas),2);
                                                        }
                                                $total_centros[$escuelas2[$cs]]=$total_centros[$escuelas2[$cs]]+$parte_esta;

                                                if ($escuelas2[$cs]==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                if ($parte_esta>0) { $contador_escuelas++; }
                                                }

                                        } else {
                                        // En todas
                                        $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");

                                        while($centros=db_fetch_array($consulta_centros)){

                                                $id_escuela_fila=$centros["id_centro"];

                                                if ($proporcional==1) {
                                                        if ($datos_costes_por_partida["partida"]=="SUMINISTROS: Comida") {
                                                                $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_comedor[$id_escuela_fila]/$total_alumnos_comedor),2)),2);
                                                                } else {
                                                                $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_escuela[$id_escuela_fila]/$total_alumnos_centro),2)),2);
                                                                }
                                                        } else {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]/$numero_de_escuelas),2);
                                                        }

                                                $total_centros[$id_escuela_fila]=$total_centros[$id_escuela_fila]+$parte_esta;

                                                if ($id_escuela_fila==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                $contador_escuelas++;
                                                }

                                        }

                                if ($proporcional==1) {
                                        if ($datos_costes_por_partida["partida"]=="SUMINISTROS: Comida") {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños con comedor contratado en cada una.";
                                                } else {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños matriculados en cada una.";
                                                }
                                        } else {
                                        $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas en partes iguales.";
                                        }

                                } else {
                                $parte_centro_sel=$datos_costes_por_partida["importe"];
                                }


                        if ($id_centro_sel_costes!=0) {

                                $importe_total_factura=$datos_costes_por_partida["importe"];
                                $importe_contabilizado=$parte_centro_sel;

                                } else {

                                $importe_total_factura=$datos_costes_por_partida["importe"];
                                $importe_contabilizado=$importe_total_factura;

                                }
                          $importe_contabilizado=round((10*$importe_contabilizado*0.4)/8,2);
                          $total_costes=$total_costes+$importe_contabilizado;
                          
                          $total_part=$total_part+$importe_contabilizado;
                          
                          if ($datos_costes_por_partida["pagado"]==1) {
                                  $total_pagado_part_act=$total_pagado_part_act+$importe_contabilizado;
                                  $total_costes_pagado=$total_costes_pagado+$importe_contabilizado;
                                }
                          
                }
                
                $totales_doc[$id_equivalencia]=$totales_doc[$id_equivalencia]+$total_part;

                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b><?=$costes_por_partida["partida"]?></b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_part, 2, ',', '.')?> €</font></td>
                          </tr>
                                <?php 
                                }
                          }
?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Total gastos</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_costes, 2, ',', '.')?> €</font></td>
                        </tr>
<?php 



                          
$sueldo_personal3=1289.25;
$sueldo_personal4=(1289.25+1516.76);
$sueldo_personal5=1516.76;
$sueldo_personallimp=721.62;
$sueldo_personalapo=(2000+1508.08+1516.76+1744.35);
                          
                          $personal3=round($sueldo_personal3*10,2);
                          $personal4=round($sueldo_personal4*10,2);
                          $personal5=round($sueldo_personal5*10,2);
                          $personallimp=round($sueldo_personallimp*4,2);
                          $personalapo=round($sueldo_personalapo*4,2);
                          
                          
                          $totales_doc[10]=$personal3;
                          $totales_doc[11]=$personal4;
                          $totales_doc[12]=$personal5;
                          $totales_doc[13]=$personallimp;
                          $totales_doc[14]=$personalapo;
                          
                          $total_costes=$total_costes+$personal3+$personal4+$personal5+$personallimp+$personalapo;
                          ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>Sueldos</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Personal 3 años</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personal3, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Personal 4 años</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personal4, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Personal 5 años</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personal5, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Personal de limpieza</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personallimp, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Personal de apoyo</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personalapo, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Total gastos personal</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($personal3+$personal4+$personal5+$personallimp+$personalapo, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Total gastos</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_costes, 2, ',', '.')?> €</font></td>
                        </tr>
                        <?php 
                        
                        $cuotasense=round(106.91*$numero_ninios*10,2);
                        $matriculas=round(150*$numero_ninios,2);
                        
                        $subvenconse=round(119.42*$numero_ninios*10,2);

                        $subvendipu=0;
                        $otrassubven=0;
                        
                        $subvenayun=round($total_cheque*-10,2);
                        
                        $total_ingresos=$cuotasense+$matriculas+$subvenconse+$subvenayun;
                        
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>Ingresos</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Cuotas enseñanza</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($cuotasense, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Matriculas</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($matriculas, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Nº de alumnos</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=$numero_ninios?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Subvenciones Consellería</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($subvenconse, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Subvenciones ayuntamiento</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($subvenayun, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Total ingresos</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_ingresos, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>Beneficio</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_ingresos-$total_costes, 2, ',', '.')?> €</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>% Beneficio</b></font></td>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format(100*($total_ingresos-$total_costes)/$total_ingresos, 2, ',', '.')?> %</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table><br><br>
<input type="button" onclick="window.open('descarga_modelo.php')" value="Descargar documento de justificación de gastos">
<?php 

// Genera documento

        // Abre plantilla
        $nombre_archivo=$web_path."/modelos_sub/JUSTIF_2C_2008-09.rtf";
        $gestor = fopen($nombre_archivo, "r");
        $cuerpo_documento = fread($gestor, filesize($nombre_archivo));
        fclose($gestor);

        for($contador=0;$contador<=17;$contador++) {
                $cuerpo_documento=str_replace("[".$contador."]",number_format($totales_doc[$contador], 2, ',', '.'),$cuerpo_documento);
                }
                
        $cuerpo_documento=str_replace("[TOTAL_G]",number_format($total_costes, 2, ',', '.'),$cuerpo_documento);
        $cuerpo_documento=str_replace("[CUOTAS]",number_format($cuotasense, 2, ',', '.')." €",$cuerpo_documento);
        $cuerpo_documento=str_replace("[MATRICULA]",number_format($matriculas, 2, ',', '.')." €",$cuerpo_documento);
        $cuerpo_documento=str_replace("[ALU]",$numero_ninios,$cuerpo_documento);

        $cuerpo_documento=str_replace("subvenconse",number_format($subvenconse, 2, ',', '.'),$cuerpo_documento);
        $cuerpo_documento=str_replace("subvendipu",number_format($subvendipu, 2, ',', '.'),$cuerpo_documento);
        $cuerpo_documento=str_replace("subvenayun",number_format($subvenayun, 2, ',', '.'),$cuerpo_documento);
        $cuerpo_documento=str_replace("otrassubven",number_format($otrassubven, 2, ',', '.'),$cuerpo_documento);
        $cuerpo_documento=str_replace("[TOTAL_I]",number_format($total_ingresos, 2, ',', '.'),$cuerpo_documento);


        // Escribe documento
        $nombre_archivo="/var/descarga/JUSTIF_2C_2008-09.rtf";
        if (file_exists($nombre_archivo)) {
                unlink($nombre_archivo);
                }

        $gestor_nuevo = fopen($nombre_archivo, "w+");
        fwrite($gestor_nuevo,$cuerpo_documento);
        fclose($gestor_nuevo);



 db_close();

 require("../configuracion/parte_inferior.php"); ?>
