<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$curso_seleccionado=$_GET["curso_seleccionado"];
  
if (strlen($curso_seleccionado)!=0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
        $fecha_time_stamp=strtotime($datos_curso_actual["fecha_inicio"]);
        $tipo_fecha_mostrada="fecha inicio del curso";
        if ($fecha_time_stamp<time()) {
			// Si el curso está iniciado vamos al día actual
        	$fecha_time_stamp=time();
        	$tipo_fecha_mostrada="fecha actual";
        }
        
        if (strtotime($datos_curso_actual["fecha_fin"])<time()) {
			// Si el curso está finalizado vamos a la fecha de finalización
			$fecha_time_stamp=strtotime($datos_curso_actual["fecha_fin"]);
			$tipo_fecha_mostrada="fecha finalización del curso";
        }
        
}

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        $nombre_curso_actual=$datos_curso_actual["nombre"];
                        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } 


if (strlen($_GET["fecha"])!=0) {
        $fecha_time_stamp=$_GET["fecha"];
        $tipo_fecha_mostrada="fecha inicio semana calendario";
    }

$fecha_actual=$fecha_time_stamp;
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Detalles Bebes</title>
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

</head>
<body>
<div class="container-fluid">
<div class="panel panel-danger">
  <div class="panel-heading">Los bebes marcados en rojo cumplen (o han cumplido) 23 meses dentro de los próximos 90 días a partir de la fecha <?=date("d/m/Y",$fecha_actual)?> (<i><?=$tipo_fecha_mostrada?></i>).<br />
* La edad mostrada es la que tendría el bebe el día <?=date("d/m/Y",$fecha_actual)?>.</div>
</div>
<?php 
// Busca Bebes
?>
<table class="table table-condensed table-striped">
        <tr>
        		<td class="col-md-1"><b>&nbsp;</b></td>
                <td class="col-md-2"><b>Nombre</b></td>
                <td class="col-md-1"><b>Edad *</b></td> 
                <td class="col-md-1"><b>Fecha 23 meses</b></td>                                       
                <td class="col-md-1"><b>Fecha Inicio</b></td>
                <td class="col-md-1"><b>Fecha Baja</b></td>
                <td class="col-md-2"><b>Grupo</b></td>
                <td class="col-md-1"><b>Teléfono</b></td>
                <td class="col-md-2"><b>Email</b></td>                       
        </tr>
<?php 
        // Busca alumnos subgrupo
        
        $contador_ninios=0;
        
        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.tipo_baja=0 AND alumnos.nivel_actual=0 AND alumnos.id_curso=? AND alumnos.fecha_baja>=? ORDER BY usuarios.fecha_nacimiento', 'ss', array($id_curso_actual, (string)(date("Y-m-d", $fecha_actual)))) or die(db_error());

        while($alumnos=db_fetch_array($busca_alumnos_grupo)) {
        
        		$contador_ninios++;
        		
				// Fecha de incorporación
                $fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
                $fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
                if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d",$fecha_actual))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y",$fecha_actual)-date("Y",strtotime($alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y",$fecha_actual)."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
         
        $fecha_23_m="-";
        $timestamp_23_m=0;         
        if ($edad_alumno<2) {               
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y",$fecha_actual)-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m",$fecha_actual);
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                                
        		// Fecha 23 meses
        		$ano_23_m=date("Y",strtotime($alumnos["fecha_nacimiento"]))+2; 
        		$mes_23_m=date("n",strtotime($alumnos["fecha_nacimiento"]))-1;
        		$dia_23_m=date("j",strtotime($alumnos["fecha_nacimiento"]));        		
        		if ($mes_23_m==0) {
        			$mes_23_m=12;
        			$ano_23_m=$ano_23_m-1;
        			}
        			
        		$timestamp_23_m=strtotime($ano_23_m."-".$mes_23_m."-".$dia_23_m);		
        		$fecha_23_m=date("d/m/Y",$timestamp_23_m);               
                
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y",$fecha_actual)-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m",$fecha_actual);
                $dif_meses=$mes_d_n-$mes_d_l;
                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }                                
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
        
		$lista_grupos_alumno="";
        
        $busca_grupos_alumno=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=? AND alumnos.id_alumno=?', 'ss', array($id_curso_actual, $alumnos["id_alumno"])) or die(db_error());

        while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
        
        		$nombre_subgrupo=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]));
        
                $lista_grupos_alumno.='<a href="javascript: top.muestra_grupo('.$grupos_donde["id_grupo"].','.$grupos_donde["id_subgrupo"].',\''.$nombre_subgrupo.'\',\''.date("Y-m-d",$fecha_actual).'\')" title="Mostrar detalles Grupo">'.$nombre_subgrupo."</a><br />";
                }        
        
        
        $color_fuente_linea="";
    if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_actual)) {
                $color_fuente_linea=" class='text-danger'";
                }
         
         
        if (($timestamp_23_m==0) or ($timestamp_23_m<=$fecha_actual+(86400*90))) {
        	$color_fuente_linea=" class='danger'";
        } 
        
						// Array con todos los emails del alumno

                        $listado_emails=str_replace(" ","",$alumnos["email"]);

                        $array_to = explode(",",$listado_emails);
        				
        				$lista_emails="";
        
                        foreach ($array_to as $to) {        
        
                                if (comprobar_email($to)!=0) {
                                
                                	$lista_emails.='<a href="mailto: '.$to.'">'.$to.'</a> ';
                                
                                }
                        }         
                                                        
        ?>
        <tr<?=$color_fuente_linea;?> >
        		<td><?=$contador_ninios?></td>
                <td><?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></td>
                <td><?=$edad_alumno;?><br /><small>Fecha Nac. <?=$fecha_nac_alumno?></small></td>
               	<td><?=$fecha_23_m;?></td>                
                <td><?=$fecha_incorporacion;?></td>
                <td><?=$fecha_baja;?></td>
                <td><?=$lista_grupos_alumno?></td>                
                <td><?=$alumnos["telefono1"]."<br />".$alumnos["telefono2"]."<br />".$alumnos["movil"];?></td>  
                <td><?=$lista_emails?></td>                                 
        </tr>
        <?php 
        }
?>
</table>        
</div>
</body>
</html>
