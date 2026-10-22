<?php  require("../configuracion/config.php");

ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);


$formasDePago=array(
        '0' => 'Contado',
        '1' => 'Domiciliacion bancaria, Cuenta: ',
        '2' => 'Transferencia',
        '5' => 'Ticket Guardería',        
);

function calcular_dc($entidad, $oficina, $cuenta){
        $dc = "";
        $pesos = array(6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
    
        foreach(array($entidad . $oficina, $cuenta) as $cadena){
            $suma = 0;
            for($i=0, $len=strlen($cadena); $i<$len; $i++){
                $suma += $pesos[$i] * substr($cadena, $len-$i-1, 1);
            }
            $digito = 11-$suma%11;
            if($digito==11){
                $digito = 0;
            }elseif($digito==10){
                $digito = 1;
            }
            $dc .= $digito;
        }
    
        return $dc;
    }

$nuevo_filtro_facturas=$_POST['filtro_facturas'];

if (strlen($nuevo_filtro_facturas)==0) {
        $filtro_facturas=$_SESSION['filtro_facturas'];
        } else {
        if ($nuevo_filtro_facturas=="0") {
                $_SESSION['filtro_facturas']="";
                $filtro_facturas="";
                } else {
                $_SESSION['filtro_facturas']=$nuevo_filtro_facturas;
                $filtro_facturas=$nuevo_filtro_facturas;
                }
        }

if (strlen($filtro_facturas)!=0) {
        if ($filtro_facturas!="OS") {
                $filtro_facturas_sql=" AND facturas_emitidas.serie='$filtro_facturas'";
                } else {
                $filtro_facturas_sql=" AND facturas_emitidas.serie is Null";
                }
        } else {
        $filtro_facturas_sql="";
        }


$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
$id_aula_facturas=$_GET["id_aula_facturas"];

$id_curso_facturas=$_GET["curso_mostrado"];


if ($id_curso_facturas!=0) {
        $busca_datos_curso_mostrado=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_facturas");
        $datos_curso_mostrado=db_fetch_array($busca_datos_curso_mostrado);
        
        $filtros="year(facturas_emitidas.fecha_emision)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(facturas_emitidas.fecha_emision)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));
        $filtros_asistencia="year(servicios_consumidos.fecha)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(servicios_consumidos.fecha)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));

        }

if (strlen($ejercicio)!=0) {
        $filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;
        $filtros_asistencia="year(servicios_consumidos.fecha)=".$ejercicio;
        }

if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        }
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }
if (strlen($id_aula_facturas)!=0) {
        $filtros=$filtros." AND alumnos.aula=".$id_aula_facturas;
        }
if (strlen($id_curso_facturas)!=0) {
        $filtros=$filtros." AND alumnos.id_curso=".$id_curso_facturas;
        }
        
$filtros=$filtros.$filtro_facturas_sql;

$nombre_archivo_csv="Listado_Facturas_".date('dmy_his');

header("Content-Type:text/csv");
header('Content-Disposition: attachment; filename="'.$nombre_archivo_csv.'.csv"');

echo("Fecha;Num. Factura;Cod Cliente;Cliente;NIF;Base 0%;Cuota 0%;Base 4%;Cuota 4%;Base 10%;Cuota 10%;Base 21%;Cuota 21%;Retencion %;Total Retencion;Total;E.I.;Aula;Alumno;\n");

$consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre DESC";

$consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

$numRecords = db_num_rows($consulta_albaranes_cliente);

while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){
    // Busca datos del centro
    $busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_factura["centro_factura"]);
    if (db_num_rows($busca_centro)!=0) {
            $datos_centro=db_fetch_array($busca_centro);
            $nombre_centro=$datos_centro["nombre_centro"];
            $prefijo_centro=$datos_centro["prefijo_centro"];
            $razon_social=$datos_centro["razon_social"];
            $domicilio=$datos_centro["domicilio_centro"];
            $cp=$datos_centro["CP_centro"];
            $poblacion=$datos_centro["poblacion_centro"];
            $telefono=$datos_centro["telefono_centro"];
            $fax=$datos_centro["fax_centro"];
            $cif=$datos_centro["CIF_centro"];
    }

    $nombre_aula="";
    $busca_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_factura["aula"]);
    if (db_num_rows($busca_aula)!=0) {
            $datos_aula=db_fetch_array($busca_aula);
            $nombre_aula=$datos_aula["nombre"];
            }


    if ($curso_actual!=$datos_factura["id_curso"]) {
        // Datos del curso
        $nombre_curso="";
        $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_factura["id_curso"]);
        if (db_num_rows($busca_curso)!=0) {
            $datos_curso=db_fetch_array($busca_curso);
            $numero_color_curso=intval(substr($datos_factura["id_curso"],strlen($datos_factura["id_curso"])-1,1));
            $nombre_curso=$datos_curso["nombre"];
        }

        $curso_actual=$datos_factura["id_curso"];
    }

    // Mira si los albaranes de la factura y calcula los totales
    $id_factura=$datos_factura["id_factura"];

    // Calcula totales
    // Desde aqui

    // Busca albaranes
    $consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (db_error());

    $total_dto=0;
    $total_base=0;
    $total_base_0=0;
    $total_base_4=0;
    $total_base_10=0;
    $total_base_21=0;
    $total_iva=0;
    $total_iva_0=0;
    $total_iva_4=0;
    $total_iva_10=0;
    $total_iva_21=0;
    $total_recargo=0;
    $total_factura=0;    


    $contador_meses_facturacion=0;
    $meses_facturacion[0]="";
    $meses_facturacion[1]="";
    $meses_facturacion[2]="";
    $meses_facturacion[3]="";
    $meses_facturacion[4]="";
    $meses_facturacion[5]="";
    $meses_facturacion[6]="";
    $meses_facturacion[7]="";
    $meses_facturacion[8]="";
    $meses_facturacion[9]="";
    $meses_facturacion[10]="";
    $meses_facturacion[11]="";
    $meses_facturacion[12]="";
    $meses_facturacion[13]="";
    $meses_facturacion[14]="";
    $meses_facturacion[15]="";
    $meses_facturacion[16]="";
    $meses_facturacion[17]="";
    $meses_facturacion[18]="";
    $meses_facturacion[19]="";
    $meses_facturacion[20]="";
    $meses_facturacion[21]="";
    $meses_facturacion[22]="";
    $meses_facturacion[23]="";
    $meses_facturacion[24]="";
    $meses_facturacion[25]="";
    $meses_facturacion[26]="";
    $meses_facturacion[27]="";
    $meses_facturacion[28]="";
    $meses_facturacion[29]="";

    $importe_subvenciones_factura=0;
    $importe_subvenciones_cobradas_factura=0;

    while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

            $concepto_linea=$datos_albaranes["concepto"];
            if (strlen($concepto_linea)!=0) {
                    $concepto_linea=strtoupper($concepto_linea);
                    }

            if ((is_numeric(strpos($concepto_linea,"EDUCACION"))) or (is_numeric(strpos($concepto_linea,"EDUCACIÓN")))) {

                    $fecha_emision_factura=$datos_factura["fecha_emision"];
                    $anio_factura=date("Y",strtotime($fecha_emision_factura));
                    $mes_emision_factura=date("n",strtotime($fecha_emision_factura));

                    $encontrado=false;

                    // 11 Meses posteriores
                    for ($c_meses=1;$c_meses<=11;$c_meses++) {
                    $mes_buscado=$mes_emision_factura+$c_meses;
                    if ($mes_buscado>=13) {
                            $anio_buscado=$anio_factura+1;
                            $mes_buscado=12-$mes_buscado;
                            } else {
                            $anio_buscado=$anio_factura;
                            }
                    $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                    if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                            $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                            $contador_meses_facturacion++;
                            $encontrado=true;
                            break;
                            }
                    }

                    if ($encontrado==false) {
                            // Mes emision factura
                            $mes_buscado=$mes_emision_factura;
                            $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                            $anio_buscado=$anio_factura;
                            if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                                    $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                                    $contador_meses_facturacion++;
                                    $encontrado=true;
                                    }
                            }
                            
                    if ($encontrado==false) {
                            // 11 Meses anteriores
                            for ($c_meses=1;$c_meses<=11;$c_meses++) {
                            $mes_buscado=$mes_emision_factura-$c_meses;
                            if ($mes_buscado<=0) {
                                    $anio_buscado=$anio_factura-1;
                                    $mes_buscado=12+$mes_buscado;
                                    } else {
                                    $anio_buscado=$anio_factura;
                                    }
                            $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                            if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                                    $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                                    $contador_meses_facturacion++;
                                    break;
                                    }
                            }
                            }


                    }

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
                    case 0.10:
                            $total_iva_10=$total_iva_10+$iva_linea;
                            $total_base_10=$total_base_10+$base_linea;
                    break;
                    case 0.21:
                            $total_iva_21=$total_iva_21+$iva_linea;
                            $total_base_21=$total_base_21+$base_linea;
                    break;
                    }
                    
                    if ((substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") or (strtolower(substr($datos_albaranes["concepto"],0,13))=="abono bono infantil")  or (strtolower(substr($datos_albaranes["concepto"],0,13))=="bono infantil") or (strtolower(substr($datos_albaranes["concepto"],0,17))=="subvencion centro")) {
                                                            $proveedor_actual=141;
                                                            }                               
                    if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
                                                            $proveedor_actual=120;                                                        
                                                            }         
                    
            // Comprueba si es subvencion
            if ($datos_albaranes["subvencion"]==1) {
                    $total_subvenciones=$total_subvenciones+$total_abs;
                    $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                    $total_subvenciones_proveedor[$proveedor_actual]=$total_subvenciones_proveedor[$proveedor_actual]+$total_abs;
                    }
            if ($datos_albaranes["subvencion_cobrada"]==1) {
                    $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                    $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                    $total_subvenciones_pagadas_proveedor[$proveedor_actual]=$total_subvenciones_pagadas_proveedor[$proveedor_actual]+$total_abs;
                    }
            }

    $total_factura=round(($total_base+$total_iva+$total_recargo),2);

    if ($meses_facturacion[0]!="") {
            $filtros_asistencia="(month(fecha)=".date("n",strtotime($meses_facturacion[0]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[0])).")";
            for ($c_meses=1;$c_meses<=$contador_meses_facturacion;$c_meses++) {
                    if ($meses_facturacion[$c_meses]!="") {
                            $filtros_asistencia=$filtros_asistencia." OR (month(fecha)=".date("n",strtotime($meses_facturacion[$c_meses]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[$c_meses])).")";
                            }
                    }
            }

    // Hasta aquí el calculo

    $total_absoluto=$total_absoluto+$total_factura;

    if ($datos_factura["serie"]!="EDU") {
        $num_factura="R".$datos_factura["id_factura"];
    } else {
        $num_factura=$datos_factura["num_factura"];
    }   
     
    echo(date("j/n/Y",strtotime($datos_factura["fecha_emision"])).";");
    echo($datos_factura["serie"].$num_factura."/".$datos_factura["ejercicio_factura"].";");
    echo($datos_factura["id_usuario"].";");
    echo($datos_factura["banco_titular"].";");
    echo($datos_factura["banco_dni_titular"].";");
    echo(number_format($total_base_0,2,",","").";");
    echo(number_format($total_iva_0,2,",","").";"); 
    echo(number_format($total_base_4,2,",","").";");
    echo(number_format($total_iva_4,2,",","").";");    
    echo(number_format($total_base_10,2,",","").";");
    echo(number_format($total_iva_10,2,",","").";");    
    echo(number_format($total_base_21,2,",","").";");
    echo(number_format($total_iva_21,2,",","").";"); 
    echo("0;"); 
    echo("0.00;"); 
    echo(number_format($total_factura,2,",","").";");
    echo($nombre_centro.";"); 
    echo("'".$nombre_aula."';");  
    echo($datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"].";");  
    echo("\n");

    $contador=$contador+1;
}
?>
