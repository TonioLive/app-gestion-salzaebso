<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Datos
$fecha_selec=$_GET["fecha"];

if (!$fecha_selec) {

	$fecha_selec=time();

}

$completo=$_GET["completo"];

$mes=date("n",$fecha_selec);
$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_selec);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_selec);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-01");
$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-01");

// Busca curso actual
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_selec)), (string)(date("Y-m-d", $fecha_selec))));

if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_actual=$datos_curso_actual["id_curso"];
	} else {
		$id_curso_actual=0;
	}
	
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Resumen de edades alumnos a fecha <?=date("d/m/Y",$fecha_selec)?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<style>

.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 

</style>
<script>

function mostrar(nombreModal) {

	$(nombreModal).modal('show')

}

function mostrarListado(tipo,edad,nombre_edad) {

	$('#listadoAlumnos').modal('show')
	
	$('#nombreListado').html('<h4 class="modal-title" id="grupoModalLabel">Listado alumnos ' + nombre_edad + '</h4>');
	$('#detalles_grupo').attr('src', 'listado_edades_grupo.php?bajas='+tipo+'&fecha_selec=<?=$fecha_selec;?>&completo=<?=$completo?>&edad='+edad);		

	$('#listadoAlumnos').modal('show');		

}

</script>
</head>
<body>
<?php 
if ($id_curso_actual!=0) {                               

$nombre_grupos[0]="HASTA 12 MESES";
$nombre_grupos[1]="DE 13 A 24 MESES";
$nombre_grupos[2]="DE 2 A 3 AÑOS";
$nombre_grupos[3]="DE 3 A 4 AÑOS";
$nombre_grupos[4]="DE 4 A 5 AÑOS";
$nombre_grupos[5]="DE 5 A 6 AÑOS";
$nombre_grupos[6]="DE 7 A 14 AÑOS";
$nombre_grupos[7]="ADULTOS";

$desglose_motivos=array();
$desglose_motivos_todos=array();

$totales_grupos=array(0,0,0,0,0,0,0,0,0);
$totales_bajas_grupos=array(0,0,0,0,0,0,0,0,0);
$totales_reenganches_grupos=array(0,0,0,0,0,0,0,0,0);
$totales_grupos_desglose=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

if ($completo==1) {

	$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_matricula<=?) AND (fecha_baja>=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)($datos_curso_actual["fecha_fin"]), (string)($datos_curso_actual["fecha_inicio"]))) or die(db_error()."-2");

} else {

	$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_matricula<=?) AND (fecha_baja>=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)(date("Y-m-d", $fecha_selec)), (string)(date("Y-m-d", $fecha_selec)))) or die(db_error()."-2");

}

while($alumnos=db_fetch_array($busca_inscripcion_curso_actual)) {

	$edad_decimal=date("Y",$fecha_selec)-date("Y",strtotime($alumnos["fecha_nacimiento"]));	
	$mes_d_l=date("m",strtotime($alumnos["fecha_nacimiento"]));
	$mes_d_n=date("m",$fecha_selec);
	$dif_meses=$mes_d_n-$mes_d_l;

	$decimales=0;
	
	if ($dif_meses!=0) {

		if ($dif_meses>0) {
		
			$decimales=round(($dif_meses/12),2);
		
			} else {
			
			$edad_decimal-=1;
			$decimales=round(((12+$dif_meses)/12),2);
			
			}

		}
		
	$edad_decimal+=$decimales;	

	$edad_grupo=floor($edad_decimal);

	if ((floor($edad_decimal)>=6) and (floor($edad_decimal)<15)) {
			$edad_grupo=6;
		}
		
	if (floor($edad_decimal)>=15) {
			$edad_grupo=7;
		}

	$totales_grupos[$edad_grupo]++;

	if ($edad_grupo<2) {
	
			$grupo_desglose=ceil($edad_decimal*12);
	
			$totales_grupos_desglose[$grupo_desglose]++;			
	
		}

	}

if ($completo==1) {

	// Busca bajas mes actual	
$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_baja>=?) AND (fecha_baja<=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)($datos_curso_actual["fecha_inicio"]), (string)(date("Y-m-d", strtotime($datos_curso_actual["fecha_fin"]) - 86400 * 10)))) or die(db_error()."-2");

	} else {
	
	// Busca bajas mes actual	
$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_baja>=?) AND (fecha_baja<=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)(date("Y-m-01", $fecha_selec)), (string)(date("Y-m-t", $fecha_selec)))) or die(db_error()."-2");

	}
	


$contador=0;
while($alumnos=db_fetch_array($busca_inscripcion_curso_actual)) {

	// Comprueba si se reengancha
	$reenganche=false;
	
	if ($completo==1) {
	
		$busca_inscripcion=db_query_params('SELECT * FROM alumnos WHERE fecha_baja>? AND id_usuario=?', 'ss', array((string)(date("Y-m-d", strtotime($datos_curso_actual["fecha_fin"]) - 86400 * 10)), $alumnos["id_usuario"]));
		
		} else {
		
		$busca_inscripcion=db_query_params('SELECT * FROM alumnos WHERE fecha_baja>? AND id_usuario=?', 'ss', array((string)($alumnos["fecha_baja"]), $alumnos["id_usuario"]));
		
		}
	
	
	if (db_num_rows($busca_inscripcion)!=0) {
			$reenganche=true;
		}

	$edad_decimal=date("Y",$fecha_selec)-date("Y",strtotime($alumnos["fecha_nacimiento"]));
	$mes_d_l=date("m",strtotime($alumnos["fecha_nacimiento"]));
	$mes_d_n=date("m",$fecha_selec);
	$dif_meses=$mes_d_n-$mes_d_l;

	$decimales=0;
	
	if ($dif_meses!=0) {

		if ($dif_meses>0) {
		
			$decimales=round(($dif_meses/12),2);
		
			} else {
			
			$edad_decimal-=1;
			$decimales=round(((12+$dif_meses)/12),2);
			
			}

		}
		
	$edad_decimal+=$decimales;	

	$edad_grupo=floor($edad_decimal);

	if ((floor($edad_decimal)>=6) and (floor($edad_decimal)<15)) {
			$edad_grupo=6;
		}
		
	if (floor($edad_decimal)>=15) {
			$edad_grupo=7;
		}

	$totales_bajas_grupos[$edad_grupo]++;
	
	if ($reenganche==true) {
	
			$totales_reenganches_grupos[$edad_grupo]++;
		
		} else {				
		
			$desglose_motivos[$edad_grupo][$alumnos["motivo_baja"]]++;
			$desglose_motivos_todos[$alumnos["motivo_baja"]]++;
			$contador++;
		}
		
	}	

if ($completo==1) {
?>
<h3>Resumen de edades durante el curso del <?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> al <?=date("d/m/Y",(strtotime($datos_curso_actual["fecha_fin"])-(86400*10)));?></h3>
<?php 
} else {
?>
<h3>Resumen de edades alumnos a fecha <?=date("d/m/Y",$fecha_selec)?> (<?=$nombre_mes[date("n",$fecha_selec)];?> de <?=date("Y",$fecha_selec);?>)</h3>
<?php 
}
?>
<?php 
if ($completo==1) {
?>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?completo=0&fecha=<?=$fecha_selec;?>">Mostrar solo el mes actual</a> 
<?php 
} else {
?>
<a class="btn btn-xs btn-primary pull-right" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a> <a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?completo=1&fecha=<?=$fecha_selec;?>">Mostrar el curso completo</a> 
<?php 
}
?>
<table class="table table-striped table-bordered table-hover">
	<tr>
		<td class="col-md-4">Grupo de edad</td>
		<td class="col-md-7">Nº de alumnos matriculados</td>
		<td class="col-md-1">&nbsp;</td>
	</tr>
<?php 
$suma_total=0;
foreach ($nombre_grupos as $clave => $valor) {
	$suma_total+=$totales_grupos[$clave];
?>
	<tr>
		<?php 
		if ($clave<2) {		
		?>
		<td><a href="javascript:mostrar('#desgloseModal');"><?=$valor?></a></td>
		<?php 
		} else {
		?>
		<td><?=$valor?></td>
		<?php 
		}
		?>
		<td><?=$totales_grupos[$clave]?></td>
		<td><button class="btn btn-sm btn-primary" onclick="mostrarListado(0,<?=$clave?>,'<?=$valor?>')"><i class="fa fa-th-list" aria-hidden="true"></i></button></td>
	</tr>
<?php 
}
?>
	<tr>
		<td>Total</td>
		<td><?=$suma_total?></td>
	</tr>
</table>
<?php 
if ($completo==1) {
?>
<h3>Bajas durante el curso seleccionado (<?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> a <?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> al <?=date("d/m/Y",(strtotime($datos_curso_actual["fecha_fin"])-(86400*10)));?></h3>
<?php 
} else {
?>
<h3>Bajas durante el mes actual (<?=$nombre_mes[date("n",$fecha_selec)];?> de <?=date("Y",$fecha_selec);?>)</h3>
<?php 
}
?>
<table class="table table-striped table-bordered table-hover">
	<tr>
		<td class="col-md-3">Grupo de edad</td>
		<td class="col-md-3">Nº de bajas</td>
		<td class="col-md-3">Nº de reenganches</td>
		<td class="col-md-2">Nº de bajas definitivas</td>
		<td class="col-md-1">&nbsp;</td>
	</tr>
<?php 
$suma_total=0;
$suma_reeng=0;
foreach ($nombre_grupos as $clave => $valor) {
	$suma_total+=$totales_bajas_grupos[$clave];
	$suma_reeng+=$totales_reenganches_grupos[$clave];
	
?>
	<tr>
		<td><?=$valor?></td>
		<td><?=$totales_bajas_grupos[$clave]?></td>
		<td><?=$totales_reenganches_grupos[$clave]?></td>
		<td><a href="javascript:mostrar('#desgloseMotivos<?=$clave?>');" data-toggle="tooltip" data-placement="right" title="Ver motivos de las bajas"><?=$totales_bajas_grupos[$clave]-$totales_reenganches_grupos[$clave]?></a></td>
		<td class="col-md-1"><button class="btn btn-sm btn-primary" onclick="mostrarListado(1,<?=$clave?>,'<?=$valor?>')"><i class="fa fa-th-list" aria-hidden="true"></i></button></td>
	</tr>
<?php 
}
?>
	<tr>
		<td>Totales</td>
		<td><?=$suma_total?></td>
		<td><?=$suma_reeng?></td>
		<td><a href="javascript:mostrar('#desgloseMotivosTodos');" data-toggle="tooltip" data-placement="right" title="Ver motivos de las bajas"><?=$suma_total-$suma_reeng?></a></td>
		<td class="col-md-1">&nbsp;</td>
	</tr>
</table>

<!-- Modal -->
<div class="modal fade" id="desgloseModal" tabindex="-1" role="dialog" aria-labelledby="desgloseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="desgloseModalLabel">Desglose niños de 0 a 24 meses</h4></div>
      </div>    
      <div class="modal-body">
		<table class="table table-striped table-bordered table-hover">
			<tr>
				<td class="col-md-4">Edad en meses</td>
				<td class="col-md-8">Nº de alumnos matriculados</td>
			</tr>
		<?php 
		$suma_total=0;
		for ($c_meses=0;$c_meses<=24;$c_meses++) {
			$suma_total+=$totales_grupos_desglose[$c_meses];
			
			if ($c_meses==1) {
				$coletilla="es";
				}
		?>
			<tr>
				<td><?=$c_meses?> mes<?=$coletilla?></td>
				<td><?=$totales_grupos_desglose[$c_meses]?></td>
			</tr>
		<?php 
		}
		?>
			<tr>
				<td>Total</td>
				<td><?=$suma_total?></td>
			</tr>
		</table>
      </div>
    </div>
  </div>
</div>

<?php 
foreach ($nombre_grupos as $clave => $valor) {
?>
<div class="modal fade" id="desgloseMotivos<?=$clave?>" tabindex="-1" role="dialog" aria-labelledby="desgloseMotivos<?=$clave?>Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="desgloseMotivos<?=$clave?>Label">Bajas en el grupo <?=$valor?></h4></div>
      </div>    
      <div class="modal-body">
		<table class="table table-striped table-bordered table-hover">
			<tr>
				<td class="col-md-4">Motivo</td>
				<td class="col-md-8">Nº de bajas</td>
			</tr>	
<?php 
	foreach ($desglose_motivos[$clave] as $clave_mot => $valor_mot) {
	
		if ($clave_mot=="") {
			$clave_mot="Desconocido";
			}
	
		?>
			<tr>
				<td><?=$clave_mot;?></td>
				<td><?=$valor_mot;?></td>
			</tr>
		<?php 
		}
		
?>
		</table>
      </div>
    </div>
  </div>
</div>
<?php 			
}
?>


<?php

/**
 * Comprueba si una fecha de MySQL está definida.
 *
 * @param mixed $fecha
 * @return bool
 */
function fechaPeriodoValida($fecha)
{
    return (
        $fecha !== null &&
        $fecha !== '' &&
        $fecha !== '0000-00-00'
    );
}


/**
 * Obtiene el intervalo seguro de un periodo.
 *
 * La fecha inicial segura es el inicio más tardío:
 * cuando todos los grupos ya han comenzado el periodo.
 *
 * La fecha final segura es el final más temprano:
 * antes de que alguno de los grupos haya terminado el periodo.
 *
 * Solo se tienen en cuenta los días que tengan definidas
 * tanto la fecha de inicio como la fecha de finalización.
 *
 * @param array $periodo
 * @return array
 */
function obtenerFechasSegurasPeriodo($periodo)
{
    $dias = array('l', 'm', 'x', 'j', 'v', 's', 'd');

    $fechasInicio = array();
    $fechasFin = array();

    foreach ($dias as $dia) {

        $campoInicio = 'ini_' . $dia;
        $campoFin = 'fin_' . $dia;

        $fechaInicio = isset($periodo[$campoInicio])
            ? $periodo[$campoInicio]
            : null;

        $fechaFin = isset($periodo[$campoFin])
            ? $periodo[$campoFin]
            : null;

        /*
         * Solo se incluye el día cuando tiene definidas
         * correctamente ambas fechas.
         */
        if (
            fechaPeriodoValida($fechaInicio) &&
            fechaPeriodoValida($fechaFin)
        ) {
            $fechasInicio[] = $fechaInicio;
            $fechasFin[] = $fechaFin;
        }
    }

    if (count($fechasInicio) == 0) {
        return array(
            'valido' => false,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'error' => 'El periodo no tiene fechas por día definidas.'
        );
    }

    $fechaInicioSegura = max($fechasInicio);
    $fechaFinSegura = min($fechasFin);

    if ($fechaInicioSegura > $fechaFinSegura) {
        return array(
            'valido' => false,
            'fecha_inicio' => $fechaInicioSegura,
            'fecha_fin' => $fechaFinSegura,
            'error' => 'No existe un intervalo común entre todos los días.'
        );
    }

    return array(
        'valido' => true,
        'fecha_inicio' => $fechaInicioSegura,
        'fecha_fin' => $fechaFinSegura,
        'error' => null
    );
}


/**
 * Cuenta los usuarios únicos que hayan tenido al menos una
 * inscripción activa durante el intervalo indicado.
 *
 * Un usuario se cuenta una sola vez aunque tenga varios
 * registros en la tabla alumnos.
 *
 * @param int $idCurso
 * @param string $fechaInicio Formato YYYY-MM-DD
 * @param string $fechaFin Formato YYYY-MM-DD
 * @return int
 */
function contarUsuariosUnicosPeriodo(
    $idCurso,
    $fechaInicio,
    $fechaFin
) {
    $idCurso = intval($idCurso);

    $fechaInicio = db_real_escape_string($fechaInicio);
    $fechaFin = db_real_escape_string($fechaFin);

    $sql = "
        SELECT COUNT(DISTINCT a.id_usuario) AS total_usuarios
        FROM alumnos AS a
        INNER JOIN usuarios AS u
            ON u.id_usuario = a.id_usuario
        WHERE a.id_curso = " . $idCurso . "
          AND a.fecha_matricula IS NOT NULL
          AND a.fecha_matricula <> '0000-00-00'
          AND a.fecha_matricula <= '" . $fechaFin . "'
          AND (
                a.fecha_baja IS NULL
                OR a.fecha_baja = ''
                OR a.fecha_baja = '0000-00-00'
                OR a.fecha_baja >= '" . $fechaInicio . "'
              )
    ";

    $consulta = db_query($sql) or die(db_error());

    $resultado = db_fetch_assoc($consulta);

    return intval($resultado['total_usuarios']);
}


/**
 * Formatea una fecha de MySQL como dd/mm/aaaa.
 *
 * @param string|null $fecha
 * @return string
 */
function mostrarFechaPeriodo($fecha)
{
    if (!fechaPeriodoValida($fecha)) {
        return '-';
    }

    return date('d/m/Y', strtotime($fecha));
}


/**
 * Obtiene el nombre que se mostrará para el periodo.
 *
 * @param array $periodo
 * @return string
 */
function obtenerNombrePeriodo($periodo)
{
    if (
        isset($periodo['nombre_trimestre']) &&
        trim($periodo['nombre_trimestre']) != ''
    ) {
        return $periodo['nombre_trimestre'];
    }

    if (
        isset($periodo['trimestre']) &&
        intval($periodo['trimestre']) > 0
    ) {
        return 'Periodo ' . intval($periodo['trimestre']);
    }

    return 'Periodo ' . intval($periodo['id_trimestre']);
}


/*
|--------------------------------------------------------------------------
| LISTADO DE PERIODOS DEL CURSO
|--------------------------------------------------------------------------
|
| Se supone que $id_curso_actual ya está definido en la página.
|
*/

$id_curso_actual = intval($id_curso_actual);

$sqlPeriodos = array('
    SELECT *
    FROM aulas_grupos_trimestres
    WHERE id_curso = ?
    ORDER BY trimestre, fecha_inicio, id_trimestre
', 's', array($id_curso_actual));

$consultaPeriodos = db_query_params($sqlPeriodos[0], $sqlPeriodos[1], $sqlPeriodos[2]) or die(db_error());

?>

<div class="table-responsive">
	<h4>Alumnos matriculados por periodo</h4>
    <table class="table table-bordered table-striped table-hover">

        <thead>
            <tr>
                <th>Periodo</th>
                <th>Inicio seguro</th>
                <th>Fin seguro</th>
                <th class="text-center">Alumnos únicos</th>
            </tr>
        </thead>

        <tbody>

        <?php

        $hayPeriodos = false;

        while ($periodo = db_fetch_assoc($consultaPeriodos)) {

            $hayPeriodos = true;

            $fechasSeguras = obtenerFechasSegurasPeriodo($periodo);

            $nombrePeriodo = htmlspecialchars(
                obtenerNombrePeriodo($periodo),
                ENT_QUOTES,
                'ISO-8859-1'
            );

            ?>

            <tr>

                <td>
                    <?php echo $nombrePeriodo; ?>
                </td>

                <?php if ($fechasSeguras['valido']) { ?>

                    <?php

                    $numeroUsuarios = contarUsuariosUnicosPeriodo(
                        $id_curso_actual,
                        $fechasSeguras['fecha_inicio'],
                        $fechasSeguras['fecha_fin']
                    );

                    ?>

                    <td>
                        <?php
                        echo mostrarFechaPeriodo(
                            $fechasSeguras['fecha_inicio']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo mostrarFechaPeriodo(
                            $fechasSeguras['fecha_fin']
                        );
                        ?>
                    </td>

                    <td class="text-center">
                        <strong>
                            <?php echo $numeroUsuarios; ?>
                        </strong>
                    </td>

                <?php } else { ?>

                    <td colspan="2">
                        <span class="text-danger">
                            <?php
                            echo htmlspecialchars(
                                $fechasSeguras['error'],
                                ENT_QUOTES,
                                'ISO-8859-1'
                            );
                            ?>
                        </span>
                    </td>

                    <td class="text-center">
                        -
                    </td>

                <?php } ?>

            </tr>

            <?php
        }

        if (!$hayPeriodos) {
            ?>

            <tr>
                <td colspan="4" class="text-center">
                    No hay periodos definidos para este curso.
                </td>
            </tr>

            <?php
        }
        ?>

        </tbody>

    </table>

</div>

<div class="modal fade" id="desgloseMotivosTodos" tabindex="-1" role="dialog" aria-labelledby="desgloseMotivosTodosLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="desgloseMotivosTodosLabel">Motivos de las bajas</h4></div>
      </div>    
      <div class="modal-body">
		<table class="table table-striped table-bordered table-hover">
			<tr>
				<td class="col-md-4">Motivo</td>
				<td class="col-md-8">Nº de bajas</td>
			</tr>	
<?php 
	foreach ($desglose_motivos_todos as $clave_mot_todos => $valor_mot_todos) {
	
		if ($clave_mot_todos=="") {
			$clave_mot_todos="Desconocido";
			}
	
		?>
			<tr>
				<td><?=$clave_mot_todos;?></td>
				<td><?=$valor_mot_todos;?></td>
			</tr>
		<?php 
		}
		
?>
		</table>
      </div>
    </div>
  </div>
</div>

<script>
$( document ).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

});
</script>

<?php 
} else {

if ($completo==1) {

?>
<h2>No hay alumnos activos en el curso seleccionado (<?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> a <?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> al <?=date("d/m/Y",(strtotime($datos_curso_actual["fecha_fin"])-(86400*10)));?></h2>
<?php 
	} else {

?>
<h2>No hay alumnos activos en el mes seleccionado (<?=$nombre_mes[date("n",$fecha_selec)];?> de <?=date("Y",$fecha_selec);?>).</h2>
<?php 	
	
	}

}
?>
<?php 
if ($completo==1) {
?>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?completo=0&fecha=<?=$fecha_selec;?>">Mostrar solo el mes actual</a> 
<?php 
} else {
?>
<a class="btn btn-xs btn-primary pull-right" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a> <a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?completo=1&fecha=<?=$fecha_selec;?>">Mostrar el curso completo</a> 
<?php 
}
?>

<!-- Modal -->
<div class="modal modal-wide fade" id="listadoAlumnos" tabindex="-1" role="dialog" aria-labelledby="listadoAlumnosLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombreListado"><h4 class="modal-title" id="listadoAlumnosLabel">Nombre Grupo</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="listado_edades_grupo.php?fecha_selec=<?=$fecha_selec?>&completo=<?=$completo?>&edad=0" name="detalles_grupo" id="detalles_grupo" width=100% height=450 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
