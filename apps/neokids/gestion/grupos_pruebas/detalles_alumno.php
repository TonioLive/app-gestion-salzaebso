<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

function MultiSort($data, $sortCriteria, $caseInSensitive = true)
{
  if( !is_array($data) || !is_array($sortCriteria))
    return false;      
  $args = array();
  $i = 0;
  foreach($sortCriteria as $sortColumn => $sortAttributes) 
  {
    $colList = array();
    foreach ($data as $key => $row)
    {
      $convertToLower = $caseInSensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
      $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
      $colLists[$sortColumn][$key] = $rowData;
    }
    $args[] = &$colLists[$sortColumn];
     
    foreach($sortAttributes as $sortAttribute)
    {     
      $tmp[$i] = $sortAttribute;
      $args[] = &$tmp[$i];
      $i++;     
     }
  }
  $args[] = &$data;
  call_user_func_array('array_multisort', $args);
  return end($args);
} 

$id_usuario=$_GET["id_usuario"];

$nombres_cursos=array();
$busca_nombres_cursos=db_query("SELECT * FROM cursos_academicos");
while($lista_cursos=db_fetch_array($busca_nombres_cursos)) {
        $nombres_cursos[$lista_cursos["id_curso"]]=$lista_cursos["nombre"];
        }

$id_curso_actual=0;
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
	$datos_curso_actual=db_fetch_array($busca_curso_actual);
	$id_curso_actual=$datos_curso_actual["id_curso"];
	}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Detalles Alumno</title>
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
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                } 
                
</style>

<script>

function eliminar_nota(id_nota, nombre_nota) {
	
	if (confirm("Vas a eliminar la nota de seguimiento del día "+nombre_nota+". ¿Estás seguro/a?")) {
	
		window.location.href="eliminar_nota.php?id_usuario=<?=$id_usuario?>&id_nota="+id_nota;
	
	}

}

</script> 
</head>
<body>
<div class="container-fluid">
<?php 
        
        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario)) or die(db_error());
        
        while($alumnos=db_fetch_array($busca_alumnos_grupo)) {
        
// Fecha de incorporación
                $fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
                $fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
                if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
                
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
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
        
        $color_fuente_linea="";
    if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_actual)) {
                $color_fuente_linea=" class='text-danger'";
                }
         
        if ($alumnos["tipo_baja"]==2) {
        			$color_fuente_linea=" class='fondo_nivelB textoBlanco'";	
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
                        
                        
  	$array_eventos=array();
  	
  	// busca eventos... grupos donde ha estado
  	$busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_usuario=?', 's', array($id_usuario)) or die(db_error()."-4");

	$array_grupos_alumno=array();

	while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
	
	
		if ($grupos_donde["id_curso"]==$id_curso_actual) {
			$array_grupos_alumno[]=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]));
			}
	
		$array_eventos[]=array('timestamp' => strtotime($grupos_donde["fecha_matricula"]), 'fecha' => $grupos_donde["fecha_matricula"], 'texto' => '<b>Curso: '.$nombres_cursos[$grupos_donde["id_curso_alu"]].':</b> Empieza a venir. ' . $dia_sem_largo[$grupos_donde["dia"]] . ' de '.date("H:i",strtotime($grupos_donde["hora_inicio"])).' a '.date("H:i",strtotime($grupos_donde["hora_fin"])).'.', 'id_nota' => 0);
		$array_eventos[]=array('timestamp' => strtotime($grupos_donde["fecha_baja"]), 'fecha' => $grupos_donde["fecha_baja"], 'texto' => '<b>Curso: '.$nombres_cursos[$grupos_donde["id_curso_alu"]].':</b> finaliza su asistencia. ' . $dia_sem_largo[$grupos_donde["dia"]].' de '.date("H:i",strtotime($grupos_donde["hora_inicio"])).' a '.date("H:i",strtotime($grupos_donde["hora_fin"])).'.', 'id_nota' => 0);
	}
	
	$lista_grupos_alumno=implode(", ",$array_grupos_alumno);
  	
  	$busca_notas_seguimiento=db_query_params('SELECT * FROM alumnos_seguimiento WHERE id_usuario=? ORDER BY fecha_creacion', 's', array($id_usuario));
  	
  	while($nota=db_fetch_array($busca_notas_seguimiento)) {
  		$array_eventos[]=array('timestamp' => strtotime($nota["fecha_creacion"]), 'fecha' => $nota["fecha_creacion"], 'texto' => $nota["comentarios"], 'id_nota' => $nota["id_nota"]);
  	}
  	
  	// Busca servicios contratados
  	
	$busca_servicios=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios, alumnos WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=alumnos.id_alumno AND alumnos.id_usuario=? ORDER BY servicios_contratados.fecha_inicio', 's', array($id_usuario));
                                                                                                
	while($servicios=db_fetch_array($busca_servicios)) {
		// Calcula importe
		$base_imponible=round(($servicios["PRECIO_VENTA"]*$servicios["cantidad"]),4);

        $descuento=round($base_imponible*round(($servicios["descuento"]/100),4),2);
 		$base_inc_dto=$base_imponible-$descuento;
   		$importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),4);	
		$array_eventos[]=array('timestamp' => strtotime($servicios["fecha_inicio"]), 'fecha' => $servicios["fecha_inicio"], 'texto' => 'Agregado el servicio: '.$servicios["ARTICULO"]." ".number_format($importe,2,",",".")." €" , 'id_nota' => 0);
    }                                                                                            
  	
  	// Busca recibos pagados
	$busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura, alumnos WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=? ORDER BY facturas_emitidas.fecha_emision DESC', 's', array($id_usuario)) or die (db_error()); 	
  	
	while($facturas=db_fetch_array($busca_factura)) {

		// Calcula totales

		// Busca albaranes
		$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($facturas["id_factura"])) or die (db_error());

		$total_dto=0;
		$total_base=0;
		$total_iva=0;
		$total_recargo=0;

		while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

                
        	$total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        	$total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        	$base_linea=$total_linea-$total_dto;
        	$iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
       		$total_abs=$base_linea+$iva_linea;

        	$total_base=$total_base+$base_linea;
        	$total_iva=$total_iva+$iva_linea;

        	}

		$total_factura=round(($total_base+$total_iva+$total_recargo),2);

		$array_eventos[]=array('timestamp' => strtotime($facturas["fecha_emision"]), 'fecha' => $facturas["fecha_emision"], 'texto' => 'Emitido el recibo nº: '.$facturas["id_factura"]." por importe de: ".number_format($total_factura,2,",",".")." €" , 'id_nota' => 0);
		
		$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($facturas["id_factura"]));
		while ($pagos=db_fetch_array($busca_pagos)) {
			$array_eventos[]=array('timestamp' => strtotime($pagos["fecha_pago"]), 'fecha' => $pagos["fecha_pago"], 'texto' => 'Pagados '.number_format($pagos["importe_pago"],2,",",".").' € correspondientes al recibo nº: '.$pagos["id_factura"] , 'id_nota' => 0);
        }
		
    }  	
  	
  	// Busca Contactos
  	
  	
  	// Busca asistencia  	

	if (count($array_eventos)!=0) {
	
		// Ordena el array por fecha
		$sortCriteria =
  			array('timestamp' => array(SORT_DESC, SORT_NUMERIC)
  			);	
		$array_ordenado = MultiSort($array_eventos, $sortCriteria, true);  	
	
	} else {
	
		$array_ordenado = $array_eventos;
	
	}                                
                                                         
        ?>
        
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><h4>Seguimiento de <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></h4></div>
  <div class="panel-body">    
    Edad: <?=$edad_alumno;?>. Fecha Nac. <?=$fecha_nac_alumno?><br />
    Teléfonos: <?=$alumnos["telefono1"]." ".$alumnos["telefono2"]." ".$alumnos["movil"];?><br />
    Email: <?=$lista_emails;?><br />
    Grupos: <?=$lista_grupos_alumno;?>
  </div>
</div>       

<div class="panel panel-default">
  <div class="panel-heading">Nueva nota de seguimiento</div>
  <div class="panel-body">
    <form name="editar_nota" action="editar_nota.php" method="POST">
    	<input type="hidden" name="id_usuario" value="<?=$id_usuario;?>">
   		<textarea class="form-control" rows="2" id="texto_nota" name="texto_nota"></textarea>
   		<br />
   		<button type="submit" class="btn btn-primary">Introducir nota</button> 
    </form>
  </div>
</div>       

<div class="panel panel-default">
  <div class="panel-heading"><h4>Seguimiento:</h4></div>    
  <div class="panel-body">
  <!-- List group -->
  <ul class="list-group">
  	<?php 	
  	
  	foreach ($array_ordenado as $nota) {
  	  	  	
  	?>
    <li class="list-group-item"><?php  if ($nota["id_nota"]!=0) { ?><button onclick='eliminar_nota(<?=$nota["id_nota"]?>,"<?=date("d/m/Y",strtotime($nota["fecha"]))?>");' class="btn btn-danger btn-xs pull-right"><i class="fa fa-trash"></i></button><?php  } ?><span class="text-info"><?=date("d/m/Y",strtotime($nota["fecha"]))?></span> <?=$nota["texto"]?></li>
    <?php 
    }
    ?>
  </ul>
</div>
<?php 
      }
?>
</div>
</body>
</html>
