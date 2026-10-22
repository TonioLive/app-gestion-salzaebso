<?php  require("../configuracion/config.php");

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


// Dias asistidos en el próximo trimestre

// Busca inicio proximo trimestre

$diasemana_inicio=date("w",(time()+(86400*14)));
$fecha_control=(time()+(86400*14))-($diasemana_inicio*86400)+86400;

$campos_inicio_dias=array("d","l","m","x","j","v","s","d");
$nombres_campos_dias=array("","l","m","x","j","v","s","d");

$num_dias_asiste=array();

for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1) {

	$diasemana=date("w",$fecha_control);

	if ($diasemana==0) {
		$diasemana=7;
	}

	$busca_proximo_trimestre=db_query("SELECT * FROM aulas_grupos_trimestres WHERE ini_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."' AND fin_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."';") or die(db_error());

	if (db_num_rows($busca_proximo_trimestre)!=0) {
        $datos_trimestre=db_fetch_array($busca_proximo_trimestre);
        
        $fecha_renovacion=true;
        $fecha_inicio_curso=$datos_trimestre["fecha_inicio"];
        $fecha_inicio_proximo_trimestre=$datos_trimestre["ini_".$campos_inicio_dias[$diasemana]];
        $fecha_fin_proximo_trimestre=$datos_trimestre["fin_".$campos_inicio_dias[$diasemana]]; 

        // buscamos numero de sesiones previstas para el proximo trimestre
        $num_dias_trimestre=0;
        $inicio_bucle=strtotime($fecha_inicio_proximo_trimestre)+7200;
        $fin_bucle=strtotime($fecha_fin_proximo_trimestre)+7200;

                while($inicio_bucle<=$fin_bucle) {
                                
                        // Comprueba si es festivo
                        $busca_cal_laboral=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1', 's', array((string)(date("Y-m-d", $inicio_bucle))));                
                        if (db_num_rows($busca_cal_laboral)==0) {
                                $num_dias_trimestre++;                        
                        }
                                
                        $inicio_bucle=$inicio_bucle+(86400*7);
                }
	}

	$num_dias_asiste[$diasemana]=$num_dias_trimestre;

	$fecha_control=$fecha_control+86400;

}

$total_facturas_bebes=0;
$total_facturas_resto=0;

$total_sesiones_infantil=0;
$total_sesiones_bebes=0;

$importe_total_sesiones_infantil=0;
$importe_total_sesiones_bebes=0;

$total_facturas_pagadas=0;
$total_facturas_pagadas_bebes=0;
$total_facturas_pagadas_resto=0;
$total_sesiones_infantil_pagadas=0;
$total_sesiones_bebes_pagadas=0;

$importe_total_sesiones_infantil_pagadas=0;
$importe_total_sesiones_bebes_pagadas=0;

?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" align="center">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<?php 
$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
$id_aula_facturas=$_GET["id_aula_facturas"];

$id_curso_facturas=$_GET["curso_mostrado"];


if ($id_curso_facturas!=0) {
        $busca_datos_curso_mostrado=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_facturas));
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
if ((strlen($id_curso_facturas)!=0) and ($id_curso_facturas!=0)) {
        $filtros=$filtros." AND alumnos.id_curso=".$id_curso_facturas;
        $filtros.=" AND alumnos.fecha_matricula>='".$fecha_inicio_curso."'";
        }
        
$filtros=$filtros.$filtro_facturas_sql;

$morisidades[0]="<font color=green>En plazo</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";


                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas LEFT JOIN detalles_factura ON facturas_emitidas.id_factura=detalles_factura.id_factura, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND detalles_factura.concepto like '%RESERVA PLAZA%' AND detalles_factura.cantidad>0 AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre DESC";

                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        $contador=0;
                        $contador1=0;

                        $total_absoluto=0;
                        $total_cobrado=0;
                        $total_no_cobrado=0;
                        $total_subvenciones=0;
                        $total_cobrado_subvenciones=0;
                        $aula_actual=0;
                        $total_devoluciones=0;
                        $curso_actual=0;
                        $total_cobrado_absoluto=0;

						$total_subvenciones_proveedor=array();
						$total_subvenciones_pagadas_proveedor=array();

                        $meses_facturacion=array();

                        while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){
// Busca datos del centro
$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
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


                        if ($curso_actual!=$datos_factura["id_curso"]) {
                        // Datos del curso
                        $nombre_curso="";
                        $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_factura["id_curso"]));
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $numero_color_curso=intval(substr($datos_factura["id_curso"],strlen($datos_factura["id_curso"])-1,1));
                                $nombre_curso=$datos_curso["nombre"];
                                }

                        $curso_actual=$datos_factura["id_curso"];
                        }

                        if ($aula_actual!=$datos_factura["aula"]) {
                        // Datos del aula
                        $nombre_aula="";
                        $busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_factura["aula"]));
                        if (db_num_rows($busca_aula)!=0) {
                                $datos_aula=db_fetch_array($busca_aula);
                                $nombre_aula=$datos_aula["nombre"];
                                }

                        $aula_actual=$datos_factura["aula"];
                        }


                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];


// Calcula totales

// Comprueba si ya tenemos los datos en la tabla factura

if (($datos_factura["total_factura"]!=0) and ($datos_factura["total_subvenciones"]==$datos_factura["total_subvenciones_cobrado"])) {
        $no_calcular=true;
        } else {
        $no_calcular=false;
        }

if ($no_calcular==true) {
        $total_factura=$datos_factura["total_factura"];
        $total_subvenciones=$total_subvenciones+$datos_factura["total_subvenciones"];
        $total_cobrado_subvenciones=$total_cobrado_subvenciones+$datos_factura["total_subvenciones_cobrado"];
        if (strlen($datos_factura["filtros_educacion"])!=0) {
                $filtros_asistencia=urldecode($datos_factura["filtros_educacion"]);
                }

        } else {

// Desde aqui

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (db_error());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
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
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
                
				if (substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") {
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

// Hasta aquí

// Introduce la info en la tabla para no tener que recalcular
$update_factura=db_query_params('UPDATE facturas_emitidas SET total_factura=?,total_subvenciones=?,total_subvenciones_cobrado=?,filtros_educacion=? WHERE id_factura=?', 'sssss', array((string)($total_factura), (string)($importe_subvenciones_factura), (string)($importe_subvenciones_cobradas_factura), (string)(urlencode($filtros_asistencia)), $id_factura)) or die (db_error());

}

$total_absoluto=$total_absoluto+$total_factura;

$total_dias_asistidos=0;

// Comprueba dias asistidos en el mes de facturacion
if ($filtros_asistencia!="") {
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$datos_factura["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        }

// Comprueba pagos a cuenta
$total_a_cuenta=0;

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if (($total_a_cuenta-$total_factura)!=0) {
        if ($total_a_cuenta>0) {
                $total_cobrado=$total_cobrado+$total_a_cuenta;
                $estado_factura="<font color=red><small>Pendiente cobro: ".number_format(($total_factura-$total_a_cuenta), 2, ',', '.')." €</small></font>";
                } else {
                $estado_factura="<font color=red><small>No cobrada</small></font>";
                }
        $morosidad=true;
        $total_cobrado_absoluto=$total_cobrado_absoluto+$total_a_cuenta;
        } else {
        $total_cobrado=$total_factura;
        $total_facturas_pagadas++;
        if ($datos_factura["nivel_actual"]==0) {
        	$total_facturas_pagadas_bebes++;
        } else {
        	$total_facturas_pagadas_resto++;
        }
        
        $estado_factura="<font color=green><small>Cobrada</small></font>";
        $morosidad=false;
        $total_cobrado_absoluto=$total_cobrado_absoluto+$total_factura;
        }

$total_no_cobrado=$total_no_cobrado+($total_factura-$total_a_cuenta);


// Comprueba dia de la semana asiste alumno
$busca_inscripcion_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($datos_factura["id_alumno"], $datos_factura["id_curso"]));

if (db_num_rows($busca_inscripcion_grupo_alumno)!=0) {

	$datos_inscripcion_grupo_alumno=db_fetch_array($busca_inscripcion_grupo_alumno);
	
	$dia_semana_asiste=$datos_inscripcion_grupo_alumno["dia"];
	
	if ($datos_factura["nivel_actual"]==0) {
				
			$precio_sesion=13;
			if ($dia_semana_asiste==7) {
				$precio_sesion=10;
				}
	
			$total_sesiones_bebes+=$num_dias_asiste[$dia_semana_asiste];
			$importe_total_sesiones_bebes+=($num_dias_asiste[$dia_semana_asiste]*$precio_sesion);
			if ($morosidad==false) {		
					$total_sesiones_bebes_pagadas+=$num_dias_asiste[$dia_semana_asiste];
					$importe_total_sesiones_bebes_pagadas+=($num_dias_asiste[$dia_semana_asiste]*$precio_sesion);								
				}
	
		} else {
			
			$precio_sesion=11;
			if ($datos_inscripcion_grupo_alumno["hora_inicio"]=="17:45:00") {
				$precio_sesion=12;
				}	
			if ($dia_semana_asiste==6) {
				$precio_sesion=11;
				}				
			if ($dia_semana_asiste==7) {
				$precio_sesion=10;
				}											

			$total_sesiones_infantil+=$num_dias_asiste[$dia_semana_asiste];
			$importe_total_sesiones_infantil+=($num_dias_asiste[$dia_semana_asiste]*$precio_sesion);			
			if ($morosidad==false) {					
					$total_sesiones_infantil_pagadas+=$num_dias_asiste[$dia_semana_asiste];
					$importe_total_sesiones_infantil_pagadas+=($num_dias_asiste[$dia_semana_asiste]*$precio_sesion);						
				}
	
		}

}

$tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

$morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==1) {
                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$datos_factura["banco_cuenta"];
                        if (($tiempo_desde_emision<=15) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>15) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>30) {
                                $morosidad_actual=4;
                                }
                        } else {
                        $forma_de_pago="Contado";
                        if ($datos_factura["forma_de_pago"]==5) {
                                $forma_de_pago="Cheque guardería";
                                }
                        if (($tiempo_desde_emision<=5) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>5) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>10) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=4;
                                }
                        }

if ($morosidad==true) {
        if ($morosidad_actual>=$nivel_maximo_morosidad) {
                $nivel_maximo_morosidad=$morosidad_actual;
                }
        $texto_morosidad="<br>".$morisidades[$morosidad_actual];
        } else {
        $texto_morosidad="";
        }

if ($datos_factura["fecha_orden_cobro"]!="0000-00-00") {
        $fecha_orden_de_cobro=date("j/n/Y",strtotime($datos_factura["fecha_orden_cobro"]));
        } else {
        $fecha_orden_de_cobro="";
        }

        $fecha_baja=$datos_factura["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_factura["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($datos_factura["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#45A9F6";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }

                                  if (($datos_factura["forma_de_pago"]==1) and ($estado_factura!="<font color=green><small>Cobrada</small></font>")) {
                                  $contador1++;
                                  }
                                  
                                  if ($datos_factura["serie"]!="EDU") {
                                        $num_factura="R".$datos_factura["id_factura"];
                                        } else {
                                        $num_factura=$datos_factura["num_factura"];
                                        }
                                  
                                $contador=$contador+1;
                                
        						if ($datos_factura["nivel_actual"]==0) {
        								$total_facturas_bebes++;
        							} else {
        								$total_facturas_resto++;
        							}  
                                
                        }

                         if ($nivel_acceso==4) {
                         ?>
                        <table class="table table-bordered table-hover table-striped">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Facturas de reserva de plaza emitidas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Bebés:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$total_facturas_bebes;?></font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Infantil:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$total_facturas_resto;?></font></td>
                        </tr>                                                  
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Facturas de reserva de plaza pagadas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$total_facturas_pagadas;?></font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Bebés:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$total_facturas_pagadas_bebes;?></font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Infantil:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$total_facturas_pagadas_resto;?></font></td>
                        </tr>                                                
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total facturado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_cobrado_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_no_cobrado, 2, ',', '.');?> €</font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Estimación Nº de sesiones contratadas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_infantil+$total_sesiones_bebes,0,",",".");?> (<?=number_format($importe_total_sesiones_infantil+$importe_total_sesiones_bebes,2,",",".");?> €)</font></td>
                        </tr>  
						<tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Bebés:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_bebes,0,",",".");?> (<?=number_format($importe_total_sesiones_bebes,2,",",".");?> €)</font></td>
                        </tr>  
<tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Infantil:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_infantil,0,",",".");?> (<?=number_format($importe_total_sesiones_infantil,2,",",".");?> €)</font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Estimación ingresos próximo trimestre descontando reservas de plaza:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($importe_total_sesiones_infantil+$importe_total_sesiones_bebes)-$total_absoluto,2,",",".");?> €</font></td>
                        </tr>                                                                          
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de sesiones confirmadas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_infantil_pagadas+$total_sesiones_bebes_pagadas,0,",",".");?> (<?=number_format($importe_total_sesiones_infantil_pagadas+$importe_total_sesiones_bebes_pagadas,2,",",".");?> €)</font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Bebés:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_bebes_pagadas,0,",",".");?> (<?=number_format($importe_total_sesiones_bebes_pagadas,2,",",".");?> €)</font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Infantil:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_sesiones_infantil_pagadas,0,",",".");?> (<?=number_format($importe_total_sesiones_infantil_pagadas,2,",",".");?> €)</font></td>
                        </tr> 
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Ingresos confirmados próximo trimestre descontando reservas de plaza:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($importe_total_sesiones_infantil_pagadas+$importe_total_sesiones_bebes_pagadas)-$total_cobrado_absoluto,2,",",".");?> €</font></td>
                        </tr>                                                                                               
                        </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_facturas.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><?php 
}
?><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<script labguage="Javascript">
<?php 
if ($contador==1) {
?>
var solo_hay_una=true;
<?php  } else { ?>
var solo_hay_una=false;
<?php 
}
if ($contador1==1) {
?>
var solo_hay_una1=true;
<?php  } else { ?>
var solo_hay_una1=false;
<?php 
}
?>
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
