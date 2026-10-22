<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

function trasforma_cols($numero_col) {

        $alfabeto=($numero_col/26);
        if ((round($alfabeto,0)>$alfabeto) or (round($alfabeto,0)==$alfabeto)) {
                $alfabeto=round($alfabeto,0)-1;
                } else {
                $alfabeto=round($alfabeto,0);
                }
        if ($alfabeto>0) {
                $letra_col=chr($alfabeto+64).chr(($numero_col-($alfabeto*26))+64);
                } else {
                $letra_col=chr($numero_col+64);
                }

        return $letra_col;
        }

// Datos de inicio

$curso_seleccionado=$_GET["id_curso_act"];
$curso_siguiente=$_GET["id_curso_sig"];
if (strlen($curso_seleccionado)==0) {
        $curso_seleccionado=3;
        $curso_siguiente=4;
        }

$busca_datos_del_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));

$datos_del_curso=db_fetch_array($busca_datos_del_curso);
$nombre_curso_ini=$datos_del_curso["nombre"];

$anio_inicial=date("Y",strtotime($datos_del_curso["fecha_inicio"]));
$anio_final=date("Y",strtotime($datos_del_curso["fecha_fin"]));
$mes_inicial=date("n",strtotime($datos_del_curso["fecha_inicio"]));
$mes_final=date("n",strtotime($datos_del_curso["fecha_fin"]));

$busca_datos_del_curso_fin=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_siguiente");

$datos_del_curso_fin=db_fetch_array($busca_datos_del_curso_fin);
$nombre_curso_fin=$datos_del_curso_fin["nombre"];

$ocupacion_aulas_06=array();
$faturacion_aula_0607=array();
$ocupacion_aulas_c=array();
$ocupacion_aulas_t=array();
$facturacion_aulas=array();
$facturacion_comedor=array();
$facturacion_edu=array();

// Bucle 1

$anio_seleccionado=$anio_inicial;

for ($contador_meses=$mes_inicial;$contador_meses<=($mes_final+(12*($anio_final-$anio_inicial)));$contador_meses++) {
        if ($contador_meses>12) {
                $mes_seleccionado=$contador_meses-12;
                if ($contador_meses==13) {
                        $anio_seleccionado++;
                        }
                } else {
                $mes_seleccionado=$contador_meses;
                }

        $mes_mostrar="0".$mes_seleccionado;
        $mes_mostrar=substr($mes_mostrar,strlen($mes_mostrar)-2,2);

        $primer_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-01";
        $ultimo_dia_mes_seleccionado=$anio_seleccionado."-".$mes_mostrar."-".date("t",strtotime($primer_dia_mes_seleccionado));

        // Paso 1: Calcula ocupación de las aulas durante cada mes del curso

        $busca_ocupacion_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? ORDER BY id_centro, nombre', 's', array($curso_seleccionado));

        while($ocupacion_aulas=db_fetch_array($busca_ocupacion_aulas)) {

                // Busca numero de alumnos
                $busca_alumnos_en_aula=db_query_params('SELECT * FROM alumnos WHERE aula=? AND fecha_matricula<=? AND fecha_baja>=?', 'sss', array($ocupacion_aulas["id_aula"], (string)($ultimo_dia_mes_seleccionado), (string)($primer_dia_mes_seleccionado)));
                $total_alumnos=db_num_rows($busca_alumnos_en_aula);
                $ocupacion_aulas_c[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$total_alumnos;
                $ocupacion_aulas_t[$ocupacion_aulas["id_aula"]]=$ocupacion_aulas_t[$ocupacion_aulas["id_aula"]]+$total_alumnos;

                // Busca ocupacion aula en curso 06-07
                $bucas_aula_0607=db_query("SELECT * FROM centro_aulas WHERE edad_minima='".$ocupacion_aulas["edad_minima"]."' AND edad_maxima='".$ocupacion_aulas["edad_maxima"]."' AND id_curso=".$curso_siguiente." AND id_centro=".$ocupacion_aulas["id_centro"]);
                
                $ocupacion_aulas_06[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=0;
                $faturacion_aula_0607[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=0;
                
                while ($datos_aula_0607=db_fetch_array($bucas_aula_0607)) {
                        $busca_alumnos_0607=db_query_params('SELECT * FROM alumnos WHERE aula=?', 's', array($datos_aula_0607["id_aula"]));
                        $total_alumnos_0607=db_num_rows($busca_alumnos_0607);
                        $ocupacion_aulas_06[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$ocupacion_aulas_06[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+$total_alumnos_0607;
                        
                        // Busca servicios que se deben contar
                        $fecha_servicio_buscar=$nombre_mes[$mes_seleccionado]." de ".$anio_seleccionado;

                                // Busca servicios facturados en el mes actual

                                // Servicios propios

                                $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE alumnos.id_alumno=facturas_emitidas.id_cliente AND facturas_emitidas.id_factura=detalles_factura.id_factura AND ((detalles_factura.concepto like '%$fecha_servicio_buscar%') OR (detalles_factura.cod_facturacion='he_".date("n_Y",strtotime($primer_dia_mes_seleccionado))."') OR (detalles_factura.cod_facturacion='co_".date("n_Y",strtotime($primer_dia_mes_seleccionado))."') OR (detalles_factura.concepto like '%".date("/m/Y",strtotime($primer_dia_mes_seleccionado)).",%'))  AND alumnos.aula=".$datos_aula_0607["id_aula"].";";
                                $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());

                                while($datos_factura=db_fetch_array($consulta_factura)) {
                                        if ($datos_factura["subvencion"]==1) {
                                                $multiplicador=-1;
                                                } else {
                                                $multiplicador=1;
                                                }
                                        $faturacion_aula_0607[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$faturacion_aula_0607[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                }

                        }


                // Calcula facturacion

                        // Consulta facturación por aula
                        $faturacion_aula=0;
                        $total_pagado_aula=0;
                        $faturacion_he_aula=0;
                        $total_pagado_he_aula=0;
                        $faturacion_edu_aula=0;
                        $faturacion_co_aula=0;
                        $faturacion_cos_aula=0;

                        // Busca servicios que se deben contar
                        $fecha_servicio_buscar=$nombre_mes[$mes_seleccionado]." de ".$anio_seleccionado;

                                // Busca servicios facturados en el mes actual

                                // Servicios propios

                                $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE alumnos.id_alumno=facturas_emitidas.id_cliente AND facturas_emitidas.id_factura=detalles_factura.id_factura AND ((detalles_factura.concepto like '%$fecha_servicio_buscar%') OR (detalles_factura.cod_facturacion='he_".date("n_Y",strtotime($primer_dia_mes_seleccionado))."') OR (detalles_factura.cod_facturacion='co_".date("n_Y",strtotime($primer_dia_mes_seleccionado))."') OR (detalles_factura.concepto like '%".date("/m/Y",strtotime($primer_dia_mes_seleccionado)).",%'))  AND alumnos.aula=".$ocupacion_aulas["id_aula"].";";
                                $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());

                                while($datos_factura=db_fetch_array($consulta_factura)) {
                                        if ($datos_factura["subvencion"]==1) {
                                                $multiplicador=-1;
                                                } else {
                                                $multiplicador=1;
                                                }
                                        $faturacion_aula=$faturacion_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);

                                        if ((substr($datos_factura["concepto"],0,10)=="Hora extra") or (substr($datos_factura["cod_facturacion"],0,3)=="he_") or (substr($datos_factura["concepto"],0,11)=="Horas extra")) {
                                                $faturacion_he_aula=$faturacion_he_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                               }
                                        if ((substr($datos_factura["concepto"],0,9)=="Educación") or (substr($datos_factura["concepto"],0,9)=="Educacion")) {
                                                $faturacion_edu_aula=$faturacion_edu_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                               }
                                        if ((substr($datos_factura["concepto"],0,14)=="Comedor suelto") or (substr($datos_factura["cod_facturacion"],0,3)=="co_")) {
                                                $faturacion_co_aula=$faturacion_co_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                }
                                        if ($multiplicador==1) {
                                                // Comprueba si está pagado
                                                // Comprueba pagos a cuenta
                                                $total_a_cuenta=0;

                                                $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
                                                while ($pagos=db_fetch_array($busca_pagos)) {
                                                        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                                                        }
                                                if ($total_a_cuenta>=($datos_factura["cantidad"]*$datos_factura["precio"])) {
                                                        $total_pagado_aula=$total_pagado_aula+($datos_factura["cantidad"]*$datos_factura["precio"]);
                                                        }
                                                } else {
                                                // Comprueba si se ha cobrado la subvención
                                                if ($datos_factura["subvencion_cobrada"]==1) {
                                                        $total_pagado_aula=$total_pagado_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                        }
                                                }
                                }

                $facturacion_aulas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$facturacion_aulas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+$faturacion_aula;
                $facturacion_comedor[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$facturacion_comedor[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+$faturacion_co_aula;
                $facturacion_horas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$facturacion_horas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+$faturacion_he_aula;
                $facturacion_edu[$ocupacion_aulas["id_aula"]][$mes_seleccionado]=$facturacion_edu[$ocupacion_aulas["id_aula"]][$mes_seleccionado]+$faturacion_edu_aula;

                }

        }

$coletilla=" nuevos";

// escribe la parte superior del archivo
$fecha_informe=date("d")."-".date("m")."-".date("Y");
$nombre_archivo = "informes/Informe_facturacion_".$c2.$fecha_informe.".xls";
$Titulo_hoja="Facturacion $fecha_informe";
if (file_exists($nombre_archivo)) {
        unlink($nombre_archivo);
        }

$gestor = fopen($nombre_archivo, 'a');

fwrite($gestor, $contenido);

fwrite($gestor,"<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">");
fwrite($gestor,"<head>");
fwrite($gestor,"<!--[if gte mso 9]><xml>");
fwrite($gestor,"<x:ExcelWorkbook>");
fwrite($gestor,"<x:ExcelWorksheets>");
fwrite($gestor,"<x:ExcelWorksheet>");
fwrite($gestor,"<x:Name>".$Titulo_hoja."</x:Name>");
fwrite($gestor,"<x:WorksheetOptions>");
fwrite($gestor,"<x:Print>");
fwrite($gestor,"<x:ValidPrinterInfo/>");
fwrite($gestor,"</x:Print>");
fwrite($gestor,"</x:WorksheetOptions>");
fwrite($gestor,"</x:ExcelWorksheet>");
fwrite($gestor,"</x:ExcelWorksheets>");
fwrite($gestor,"</x:ExcelWorkbook>");
fwrite($gestor,"</xml>");
fwrite($gestor,"<![endif]--> ");
fwrite($gestor,"<style>");
fwrite($gestor,"<!--table");
fwrite($gestor,".style20        {mso-number-format:0%; mso-style-name:Porcentual; mso-style-id:5;}");
fwrite($gestor,".xl26        {mso-style-parent:style20; mso-number-format:Percent; white-space:normal;}");
fwrite($gestor,"-->");
fwrite($gestor,"</style>");
fwrite($gestor,"</head>");
fwrite($gestor,"<body>");
fwrite($gestor,"<table style='border-collapse: collapse;table-layout:fixed;width:960pt'>");

fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
fwrite($gestor,"<td width=67></td>");

$busca_ocupacion_aulas=db_query_params('SELECT * FROM centro_aulas,centros WHERE centro_aulas.id_centro=centros.id_centro AND centro_aulas.id_curso=? AND centro_aulas.tipo=0 ORDER BY centro_aulas.id_centro, centro_aulas.nombre', 's', array($curso_seleccionado));
while($ocupacion_aulas=db_fetch_array($busca_ocupacion_aulas)) {
        if (intval($ocupacion_aulas_t[$ocupacion_aulas["id_aula"]])!=0) {

                fwrite($gestor,"<td colspan=12>".$ocupacion_aulas["nombre_centro"]." ".$ocupacion_aulas["nombre"]."</td>");
                }
        }
fwrite($gestor,"</tr>");

fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
fwrite($gestor,"<td width=217></td>");

$busca_ocupacion_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND tipo=0 ORDER BY id_centro, nombre', 's', array($curso_seleccionado));
while($ocupacion_aulas=db_fetch_array($busca_ocupacion_aulas)) {
        if (intval($ocupacion_aulas_t[$ocupacion_aulas["id_aula"]])!=0) {
                fwrite($gestor,"<td>Fact. Mes</td>");
                fwrite($gestor,"<td>Educación</td>");
                fwrite($gestor,"<td>Comedor</td>");
                fwrite($gestor,"<td>Horas extra</td>");
                fwrite($gestor,"<td>Fact. Otros</td>");
                fwrite($gestor,"<td>Ocupación $nombre_curso_ini</td>");
                fwrite($gestor,"<td>Fact. Media Alumno</td>");
                fwrite($gestor,"<td>Ocupación $nombre_curso_fin</td>");
                fwrite($gestor,"<td>Incremento precios $nombre_curso_fin</td>");
                fwrite($gestor,"<td>Incremento ocupación mes</td>");
                fwrite($gestor,"<td>Fact. prevista Aula $nombre_curso_fin</td>");
                fwrite($gestor,"<td>Fact. prevista Alumno $nombre_curso_fin</td>");
                fwrite($gestor,"<td>Fact. Real Aula $nombre_curso_fin</td>");
                fwrite($gestor,"<td>Fact. Real Alumno $nombre_curso_fin</td>");
                }
        }
fwrite($gestor,"</tr>");


// Filas
$anio_seleccionado=$anio_inicial;
$conta_filas=3;
$conta_inicio=$conta_filas;
for ($contador_meses=$mes_inicial;$contador_meses<=($mes_final+(12*($anio_final-$anio_inicial)));$contador_meses++) {
        if ($contador_meses>12) {
                $mes_seleccionado=$contador_meses-12;
                if ($contador_meses==13) {
                        $anio_seleccionado++;
                        }
                } else {
                $mes_seleccionado=$contador_meses;
                }

                // Escribe fila
                fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
                fwrite($gestor,"<td>".$nombre_mes[$mes_seleccionado]." de ".$anio_seleccionado."</td>");

                $busca_ocupacion_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND tipo=0 ORDER BY id_centro, nombre', 's', array($curso_seleccionado));

                $conta_col=2;

                while($ocupacion_aulas=db_fetch_array($busca_ocupacion_aulas)) {
                        if (intval($ocupacion_aulas_t[$ocupacion_aulas["id_aula"]])!=0) {

                                $facturacion_resto=$facturacion_aulas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]-$facturacion_edu[$ocupacion_aulas["id_aula"]][$mes_seleccionado]-$facturacion_comedor[$ocupacion_aulas["id_aula"]][$mes_seleccionado]-$facturacion_horas[$ocupacion_aulas["id_aula"]][$mes_seleccionado];
                                fwrite($gestor,"<td>".number_format($facturacion_aulas[$ocupacion_aulas["id_aula"]][$mes_seleccionado],2,",",".")."</td>");
                                fwrite($gestor,"<td>".number_format($facturacion_edu[$ocupacion_aulas["id_aula"]][$mes_seleccionado],2,",",".")."</td>");
                                fwrite($gestor,"<td>".number_format($facturacion_comedor[$ocupacion_aulas["id_aula"]][$mes_seleccionado],2,",",".")."</td>");
                                fwrite($gestor,"<td>".number_format($facturacion_horas[$ocupacion_aulas["id_aula"]][$mes_seleccionado],2,",",".")."</td>");
                                fwrite($gestor,"<td>".number_format($facturacion_resto,2,",",".")."</td>");
                                fwrite($gestor,"<td>".$ocupacion_aulas_c[$ocupacion_aulas["id_aula"]][$mes_seleccionado]."</td>");
                                if ($ocupacion_aulas_c[$ocupacion_aulas["id_aula"]][$mes_seleccionado]!=0) {
                                        $facturacion_media=$facturacion_aulas[$ocupacion_aulas["id_aula"]][$mes_seleccionado]/$ocupacion_aulas_c[$ocupacion_aulas["id_aula"]][$mes_seleccionado];
                                        } else {
                                        $facturacion_media=0;
                                        }
                                fwrite($gestor,"<td>".number_format($facturacion_media,2,",",".")."</td>");
                                fwrite($gestor,"<td>".$ocupacion_aulas_06[$ocupacion_aulas["id_aula"]][$mes_seleccionado]."</td>");
                                fwrite($gestor,"<td>".number_format(0,2,",",".")."%</td>");
                                fwrite($gestor,"<td>".number_format(0,2,",",".")."%</td>");
                                if ($faturacion_aula_0607[$ocupacion_aulas["id_aula"]][$mes_seleccionado]!=0) {
                                        fwrite($gestor,"<td>".number_format($faturacion_aula_0607[$ocupacion_aulas["id_aula"]][$mes_seleccionado],2,",",".")."</td>");
                                        } else {
                                        fwrite($gestor,"<td x:fmla=\"=".trasforma_cols($conta_col+11).$conta_filas."*".trasforma_cols($conta_col+7).$conta_filas."*(1+".trasforma_cols($conta_col+9).$conta_filas.")\"></td>");
                                        }
                                fwrite($gestor,"<td x:fmla=\"=".trasforma_cols($conta_col+6).$conta_filas."*(1+".trasforma_cols($conta_col+8).$conta_filas.")\"></td>");
                                $conta_col=$conta_col+12;
                                }
                        }
                 $conta_filas++;
                 fwrite($gestor,"</tr>");
}
$conta_filas=$conta_filas-1;
// Parte inferior del archivo
fwrite($gestor,"
 <tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt'>Totales</td>");
$conta_col=2;

$formula_total_alu_0506="=0";
$formula_total_0506="=0";

$formula_total_alu_0607="=0";
$formula_total_0607="=0";
$centro_actual=0;
$formulas_centros_0506=array();

$busca_ocupacion_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND tipo=0 ORDER BY id_centro, nombre', 's', array($curso_seleccionado));
while($ocupacion_aulas=db_fetch_array($busca_ocupacion_aulas)) {
        if (intval($ocupacion_aulas_t[$ocupacion_aulas["id_aula"]])!=0) {

        fwrite($gestor,"<td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col)."$conta_inicio:".trasforma_cols($conta_col)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+1)."$conta_inicio:".trasforma_cols($conta_col+1)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+2)."$conta_inicio:".trasforma_cols($conta_col+2)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+3)."$conta_inicio:".trasforma_cols($conta_col+3)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+4)."$conta_inicio:".trasforma_cols($conta_col+4)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=AVERAGE(".trasforma_cols($conta_col+5)."$conta_inicio:".trasforma_cols($conta_col+5)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+6)."$conta_inicio:".trasforma_cols($conta_col+6)."$conta_filas)\"></td>
        <td class=xl29 align=right x:fmla=\"=AVERAGE(".trasforma_cols($conta_col+7)."$conta_inicio:".trasforma_cols($conta_col+7)."$conta_filas)\"></td>
        <td colspan=2 class=xl29 style='mso-ignore:colspan'></td>
        <td class=xl24 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+10)."$conta_inicio:".trasforma_cols($conta_col+10)."$conta_filas)\"></td>
        <td class=xl24 align=right x:fmla=\"=SUM(".trasforma_cols($conta_col+11)."$conta_inicio:".trasforma_cols($conta_col+11)."$conta_filas)\"></td>");

        $formula_total_0506=$formula_total_0506."+".trasforma_cols($conta_col).($conta_filas+1);

        $formula_total_0607=$formula_total_0607."+".trasforma_cols($conta_col+10).($conta_filas+1);

        $formulas_centros_0506[$ocupacion_aulas["id_centro"]][0]=$formulas_centros_0506[$ocupacion_aulas["id_centro"]][0]."+".trasforma_cols($conta_col).($conta_filas+1);
        $formulas_centros_0506[$ocupacion_aulas["id_centro"]][1]=$formulas_centros_0506[$ocupacion_aulas["id_centro"]][1]."+".trasforma_cols($conta_col+10).($conta_filas+1);

        $conta_col=$conta_col+12;
        }
 }

fwrite($gestor,"</tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'><td height=17 style='height:12.75pt'></td></tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt'>Facturación total 2005-2006</td>
  <td height=17 style='height:12.75pt' x:fmla=\"$formula_total_0506\"></td>
  </tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt'>Facturación prevista 2006-2007</td>
  <td height=17 style='height:12.75pt' x:fmla=\"$formula_total_0607\"></td>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'><td height=17 style='height:12.75pt'></td></tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'><td height=17 style='height:12.75pt'><b>Resultados por escuelas</b></td></tr>");

$busca_centros_resultado=db_query("SELECT * FROM centros ORDER BY nombre_centro");

while($centros_resultado=db_fetch_array($busca_centros_resultado)){
fwrite($gestor,"<tr height=17 style='height:12.75pt'><td height=17 style='height:12.75pt'><i>".$centros_resultado["nombre_centro"]."</i></td></tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt'>Facturación total 2005-2006</td>
  <td height=17 style='height:12.75pt' x:fmla=\"".$formulas_centros_0506[$centros_resultado["id_centro"]][0]."\"></td>
  </tr>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt'>Facturación prevista 2006-2007</td>
  <td height=17 style='height:12.75pt' x:fmla=\"".$formulas_centros_0506[$centros_resultado["id_centro"]][1]."\"></td>
  </tr>");
}

fwrite($gestor,"</tr>");
fwrite($gestor,"</table>");
fwrite($gestor,"</body>");
fwrite($gestor,"</html>");

header("Location: ".$nombre_archivo);

?>
