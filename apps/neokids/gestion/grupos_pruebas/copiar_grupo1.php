<?php 
// die("En construcción");
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha_limite=$_POST["fecha_limite"];
$id_nuevo_curso=$_POST["id_nuevo_curso"];
$id_grupo_nuevo=$_POST["id_grupo_nuevo"];
$id_alumno=$_POST["id_alumno"];

$grupo_subgrupo=explode("_",$id_grupo_nuevo);

$id_nuevo_grupo=$grupo_subgrupo[0];
$id_nuevo_subgrupo=$grupo_subgrupo[1];

$nombres_campos_dias=array("","l","m","x","j","v","s","d");

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

		$id_grupo_subgrupo=$datos_grupo["id_grupo_subgrupo"];
        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
  
  		$fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
  
		$nombre_horario_mostrado=$dia_sem_largo[$datos_grupo["dia"]]." de ".date("H:i",strtotime($datos_grupo["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));        
		$nombre_subgrupo=$nombre_horario_mostrado.", Grupo: ".($id_subgrupo+1);    
		
		$dia_grupo=$datos_grupo["dia"];
		$hora_inicio_grupo=$datos_grupo["hora_inicio"];
		$hora_fin_grupo=$datos_grupo["hora_fin"];
        
}

if (strlen($id_nuevo_subgrupo)!=0) {

        $busca_datos_nuevo_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_nuevo_grupo, $id_nuevo_subgrupo));

        $datos_nuevo_grupo=db_fetch_array($busca_datos_nuevo_subgrupo);

        $c_nuevo_dia=$datos_nuevo_grupo["dia"];
  
		$nombre_nuevo_horario_mostrado=$dia_sem_largo[$datos_nuevo_grupo["dia"]]." de ".date("H:i",strtotime($datos_nuevo_grupo["hora_inicio"]))." a ".date("H:i",strtotime($datos_nuevo_grupo["hora_fin"]));        
		$nombre_nuevo_subgrupo=$nombre_nuevo_horario_mostrado.", Grupo: ".($id_nuevo_subgrupo+1);  								  		
        
}

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

// Busca nuevo curso
$busca_nuevo_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_nuevo_curso));

if (db_num_rows($busca_nuevo_curso)!=0) {

		$datos_nuevo_curso=db_fetch_array($busca_nuevo_curso);
        $nombre_curso_siguente=$datos_nuevo_curso["nombre"];
        $fecha_inicio_nuevo_curso=$datos_nuevo_curso["fecha_inicio"];
		
		$busca_fechas_grupos=db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE fecha_inicio>=? ORDER BY fecha_inicio LIMIT 0,1', 's', array((string)($fecha_inicio_nuevo_curso)));
			
		$datos_trimestre=db_fetch_array($busca_fechas_grupos);   		
       
}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Copiar Grupo</title>
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

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<link rel="stylesheet" href="style.css">

<style>
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                } 
                
.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
</style>

<script>


</script> 
</head>
<body>
<?php 
// Busca alumnos subgrupo
$filtro_alumno="";        
if ($id_alumno) {
	$filtro_alumno=" AND alumnos.id_alumno=$id_alumno";
	}
	        
$busca_alumnos_grupo=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno".$filtro_alumno." AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_subgrupo=$id_subgrupo AND aulas_alumnos_grupos.id_grupo=".$id_grupo." AND alumnos.fecha_baja>='".$fecha_limite."') OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_subgrupo=$id_subgrupo AND aulas_alumnos_grupos.id_grupo=".$id_grupo." AND alumnos.fecha_baja>='".$fecha_limite."'))") or die(db_error());
 
$total_exitos=0; 
$total_ya=0;
        
// Busca aula para inscribirlos
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=?', 's', array($id_nuevo_curso)) or die(db_error()."-2");  
if (db_num_rows($consulta_datos_aula)!=0) {
	$datos_nueva_aula=db_fetch_array($consulta_datos_aula);
	$id_nueva_aula=$datos_nueva_aula["id_aula"];
  
        
while($alumnos=db_fetch_array($busca_alumnos_grupo)) {

		// Comprueba si están inscritos en el curso siguiente
		$busca_alumnos_n=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($alumnos["id_usuario"], $id_nuevo_curso));
		
		if (db_num_rows($busca_alumnos_n)==0) {	
		
			// Busca fechas de inicio y fin	
				
			$busca_dias_asiste=db_query_params('SELECT * FROM aulas_grupos,aulas_alumnos_grupos WHERE aulas_grupos.id_grupo=aulas_alumnos_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? ORDER BY aulas_grupos.dia', 's', array($alumnos["id_alumno"])) or die(db_error());


			$proximo_inicio=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$c_nuevo_dia]]);
			$proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$c_nuevo_dia]]);

			/*
				
			$proximo_inicio=0;
			$proximo_fin=0;
							
			while($datos_dias_asiste=mysql_fetch_array($busca_dias_asiste)) {

        		if ($proximo_inicio==0) {
                		$proximo_inicio=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                	} else {
                		if ($proximo_inicio>=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]])) {
                        	$proximo_inicio=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                        }
                	}


        		if ($proximo_fin==0) {
                		$proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                	} else {
                		if ($proximo_fin<=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]])) {
                        	$proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                        }
                	}
        
        		}    			
			*/
			
			// Inscribe en el curso
			
			$tipo_mensualidad=$alumnos["tipo_mensualidad"];
			$comedor=$alumnos["comedor"];
			$tipo_menu_comedor=$alumnos["tipo_menu_comedor"];
			$numero_tomas=$alumnos["numero_tomas"];
			$forma_de_pago=$alumnos["forma_de_pago"];
			$banco_entidad=$alumnos["banco_entidad"];
			$banco_poblacion=$alumnos["banco_poblacion"];
			$banco_titular=$alumnos["banco_titular"];
			$banco_dni_titular=$alumnos["banco_dni_titular"];
			$banco_domicilio=$alumnos["banco_domicilio"];
			$banco_cuenta=$alumnos["banco_cuenta"];
			$num_dias_asiste=$alumnos["num_dias_asiste"];
			$nivel_actual=$alumnos["nivel_actual"];
			$nivel_proximo=$alumnos["nivel_proximo1"];
			
			$fecha_inicio_periodo=date("Y-m-d",$proximo_inicio);
			$fecha_fin_periodo=date("Y-m-d",$proximo_fin);
			
			$inscribe_alumno=db_query_params('INSERT INTO alumnos (id_usuario, id_curso, fecha_inscripcion, fecha_matricula, fecha_baja, id_centro, tipo_mensualidad, comedor, tipo_menu_comedor, numero_tomas, forma_de_pago, banco_entidad, banco_poblacion, banco_titular, banco_dni_titular, banco_domicilio, banco_cuenta, aula, num_dias_asiste, nivel_actual,nivel_proximo,nivel_maximo_superado,nivel_maximo_trabajado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?,?,?,?)', 'sssssssssssssssssssssss', array((string)($alumnos["id_usuario"]), (string)($id_nuevo_curso), (string)(date("Y-m-d")), (string)($fecha_inicio_periodo), (string)($fecha_fin_periodo), (string)($alumnos["id_centro"]), (string)($tipo_mensualidad), (string)($comedor), (string)($tipo_menu_comedor), (string)($numero_tomas), (string)($forma_de_pago), (string)($banco_entidad), (string)($banco_poblacion), (string)($banco_titular), (string)($banco_dni_titular), (string)($banco_domicilio), (string)($banco_cuenta), (string)($id_nueva_aula), (string)($num_dias_asiste), (string)($nivel_actual), (string)($nivel_proximo), (string)($nivel_maximo_superado), (string)($nivel_maximo_trabajado))) or die(db_error()."-a");
			
			$id_nuevo_alumno=db_insert_id();
		
			// Inscribe en el grupo
			$inserta_en_grupo=db_query_params('INSERT INTO aulas_alumnos_grupos (id_alumno,id_grupo,id_curso,id_subgrupo) VALUES (?,?,?,?)', 'ssss', array((string)($id_nuevo_alumno), (string)($id_nuevo_grupo), (string)($id_nuevo_curso), (string)($id_nuevo_subgrupo)))or die(db_error()."-b");
		
			// Inserta servicio de reserva de plaza
			$id_servicio_nuevo=356;
			if ($id_nuevo_curso==32) {
				$id_servicio_nuevo=365;
				}	
			if ($id_nuevo_curso>32) {
				$id_servicio_nuevo=376;
				}						
			// Inserta servicio
			$inserta_servicio=db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento) VALUES (?,?,?,\'2\',\'1\',\'0\')', 'sss', array((string)($id_nuevo_alumno), (string)($id_servicio_nuevo), (string)(date("Y-m-d", $proximo_inicio))))or die(db_error()."-c");
			$id_actividad=db_insert_id();
			
			// Inserta recibo

			$ejercicio_factura=date("Y",$proximo_inicio);
			if (date("n",$proximo_inicio)>7) {
				$ejercicio_factura=date("Y",$proximo_inicio)+1;
			}
			
			$consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=$id_actividad";
			$busca_servicio_contratado=db_query($consulta1);
			$datos_albaranes=db_fetch_array($busca_servicio_contratado) or die(db_error()."-d");

			$num_nueva_factura="00001";

			$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision) VALUES (?, ?, ?, ?);', 'ssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($id_nuevo_alumno), (string)(date("Y-m-d", $proximo_inicio))))or die(db_error()."-f");
			$id_nueva_factura=db_insert_id();


			$concepto_desglose=$datos_albaranes["desglosar_como"];
			$nombre_servicio=$datos_albaranes["ARTICULO"];
			$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, \'1\', ?, ?, ?, ?, ?);', 'ssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose)))or die(db_error()."-g");			
			
		
			$total_exitos++;
		} else {
			// Ya está matriculado
			$total_ya++;
		}								

	}             
?>
<div class="container-fluid">
	<div class="panel panel-default">
  		<div class="panel-body">
			<h2>Copiar grupo al nuevo curso</h2>
			<p class="lead">
			Se han a copiado <?=$total_exitos?> alumnos del grupo <i>"<?=$nombre_subgrupo?>"</i> del curso <b><?=$nombre_curso_actual?></b>...<br /> ...al grupo <i>"<?=$nombre_nuevo_subgrupo?>"</i> del curso <b><?=$nombre_curso_siguente?></b><br /><br />	
			<?php 
			if ($total_ya!=0) {
			?>
			<?=$total_ya?> alumnos no se han copiado porqué ya estaban inscritos en el nuevo curso.
			<br />
			<br />
			<?php 
			}
			?>	
			</p>	
			<button class="btn btn-success" type="button" onclick="top.window.location.href='index.php?curso_seleccionado=<?=$id_nuevo_curso?>'">Salir</button>
			</div>			    
  		</div>
	</div>      
</div>
<?php 
} else {
?>
<h3>No se ha podido realizar la tarea solicitada, el curso no está configurado completamente. Hasta que no se realice la configuración de los datos del curso no será posible inscribir alumnos en el.</h3>
<?php 
}  
?>
</body>
</html>
