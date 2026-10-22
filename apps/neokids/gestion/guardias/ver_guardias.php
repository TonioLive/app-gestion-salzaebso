<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


// A continuación detecta en que dia cae el primer lunes de esta mes
// Inicio=fecha del lunes
// mesini=Nombre del mes en el que empieza la semana
// mesfin=Nombre del mes en el que acaba la semana
// Si hoy es domingo pasa automaticamente a la semana siguiente

$inicio=$_GET["fecha"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
} 

$mes=date("n",$fecha_time_stamp);
$anio=date("Y",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-01");
$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-01");

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Gestión de guardias</title>
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

<link rel="stylesheet" href="style.css">
<style>

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
         
</style> 
  
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-2"><a class="btn btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a></div>
		<div class="text-center col-md-8"><h3>Guardias, <?=$mesnombre?> de <?=$anio?></h3></div>
		<div class="text-right col-md-2"><a class="btn btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a></div>
	</div>

	<table class="table table-condensed table-striped table-bordered">
		<tr>
		<?php  
		for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1) {
		?>
			<th class="text-center">
				<?php    echo $dia_sem[$dia_s]; ?>
			</th>
		<?php  
		}
		?>
		</tr>
<?php  
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {
  
  if ($mes==date("n",$inicio_time_stamp))
    {
    $fondo_celda_dia="#EFEFEF";
    } else {
    $fondo_celda_dia="#AAAAAA";
    }
  if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#DD9999";
    }
?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>">
<?php 
    if ($mes==date("n",$inicio_time_stamp))
    {
      print "<b>";    } 
    if ($dias==7)
    {
      print "<font color=#800000>";    } 
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    print "<font color='blue'>";    }
    print (date("j",$inicio_time_stamp));
    if ($mes==date("n",$inicio_time_stamp))
    {
    print "</b>";
    }


?>
</font>
<div align="center">
<?php 
	// Busca monitor de guardia
	$fecha_control=date("Y-m-d",$inicio_time_stamp);
    							
	$busca_guardia=db_query_params('SELECT * FROM personal_guardias WHERE dia=?', 's', array((string)($fecha_control)));
    
    $id_guardia=0;
	$monitor_guardia=0;
    							
	if (db_num_rows($busca_guardia)!=0) {    							
    							
		$datos_guardia=db_fetch_array($busca_guardia);
    								
		$id_guardia=$datos_guardia["id_guardia"];
		$monitor_guardia=$datos_guardia["id_usuario"];
    							
	}   							
    							
	$busca_docentes=db_query("SELECT * FROM usuarios WHERE id_usuario=$monitor_guardia;");
                                
	if (db_num_rows($busca_docentes)!=0) {
                                
		$datos_docentes=db_fetch_array($busca_docentes);
                                	
		echo(mb_convert_case($datos_docentes["Nombre"]." ".$datos_docentes["Apellido1"]." ".$datos_docentes["Apellido2"], MB_CASE_TITLE));
                                
		} else {
		
		?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php 
		
		}

?>
</div>
</td>
<?php 
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php  
}

if ($dias!=7) {
?>
</tr>
<?php  
}

?>
	</table>        
<?php 
if (($nivel_acceso==3) or ($nivel_acceso==4)) {
?>
<a class="btn btn-primary" href="index.php?fecha=<?=$fecha_time_stamp?>">Editar calendario de guardias</a>
<?php 
}
?>                        
</div>

</body>
</html>