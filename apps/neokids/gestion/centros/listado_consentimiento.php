<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$muestraCheck=array(
	0 => 'fa fa-thumbs-down text-danger',
	1 => 'fa fa-thumbs-up text-success'
);

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
		$fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
		$fecha_fin_curso=$datos_curso_actual["fecha_fin"];
		$nombre_curso_actual=$datos_curso_actual["nombre"];
		// Busca curso anterior
		$busca_curso_anterior=db_query("SELECT * FROM cursos_academicos WHERE year(fecha_inicio)<".date("Y",strtotime($fecha_inicio_curso))." ORDER BY fecha_fin DESC LIMIT 0,1");
		
		if (db_num_rows($busca_curso_anterior)!=0) {
			
			$datos_curso_anterior=db_fetch_array($busca_curso_anterior);
			$id_curso_anterior=$datos_curso_anterior["id_curso"];
			$fecha_inicio_curso_anterior=$datos_curso_anterior["fecha_inicio"];
			$fecha_fin_curso_anterior=$datos_curso_anterior["fecha_fin"];
			$nombre_curso_anterior=$datos_curso_anterior["nombre"];
			
		} else {
			// No hay curso anterior, no se puede realizar la comparativa
			$id_curso_actual=0;
			
		}
		
		// Busca curso siguiente
		$busca_curso_siguiente=db_query("SELECT * FROM cursos_academicos WHERE year(fecha_inicio)>=".date("Y",strtotime($fecha_fin_curso))." AND year(fecha_inicio)<>year(fecha_fin) AND id_curso<>1 ORDER BY fecha_fin LIMIT 0,1");		
		
		if (db_num_rows($busca_curso_siguiente)!=0) {
			
			$datos_curso_siguiente=db_fetch_array($busca_curso_siguiente);
			$id_curso_siguiente=$datos_curso_siguiente["id_curso"];
			$fecha_inicio_curso_siguiente=$datos_curso_siguiente["fecha_inicio"];
			$fecha_fin_curso_siguiente=$datos_curso_siguiente["fecha_fin"];
			$nombre_curso_siguiente=$datos_curso_siguiente["nombre"];
			
		} else {
			// No hay curso anterior, no se puede realizar la comparativa
			$id_curso_siguiente=0;
			
		}		
		
	} else {
		$id_curso_actual=0;
	}
	
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Listado de alumnos que dan su consentimiento para publicar fotografías</title>

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

	// Busca alumnos que aceptan publicar fotografías
	
	$busca_alumnos_completo=array('SELECT * FROM alumnos,usuarios,contactos WHERE alumnos.id_usuario=usuarios.id_usuario AND usuarios.id_usuario=contactos.id_usuario AND alumnos.id_curso=? AND (contactos.consentimiento_web=1 OR contactos.consentimiento_blog=1 OR contactos.consentimiento_redes=1)', 's', array($id_curso_actual));
	
	$alumnos_completo=db_query_params($busca_alumnos_completo[0], $busca_alumnos_completo[1], $busca_alumnos_completo[2]);
	
?>
<h3>Listado de alumnos que dan su consentimiento para publicar fotografías</h3>
<p>
<?php
if ($id_curso_anterior!=0) {	
?>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=strtotime($fecha_fin_curso_anterior);?>"><i class="fa fa-angle-double-left"></i> Curso <?=$nombre_curso_anterior?></a> 
<?php
	}
if ($id_curso_siguiente!=0) {	
?>
 <a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=strtotime($fecha_inicio_curso_siguiente);?>"><i class="fa fa-angle-double-right"></i> Curso <?=$nombre_curso_siguiente?></a> 
<?php
	}	
?>
<a class="btn btn-xs btn-primary" href="/"><i class="fa fa-home"></i> salir</a>
</p>
<table class="table table-striped table-bordered table-hover">
	<tr>
		<td class="col-md-1">Id Alumno</td>
		<td class="col-md-3">Nombre</td>
		<td class="col-md-2">Situación</td>
		<td class="col-md-1">Fecha nacimiento</td>
		<td class="col-md-1">Inicio curso actual</td>
		<td class="col-md-1">Fin curso actual</td>
		<td class="col-md-1">Web</td>
		<td class="col-md-1">Blog</td>
		<td class="col-md-1">Redes Sociales</td>
	</tr>
<?php
	while($listado_alumnos_completo=db_fetch_array($alumnos_completo)) {	
		
		// Busca grupo
		if ($listado_alumnos_completo["fecha_baja"]) {
				
			if ($listado_alumnos_completo["fecha_baja"]>date("Y-m-d")) {
				
				$lista_grupos_alumno="";
					
			    if ($listado_alumnos_completo["id_alumno"]) {
				    $busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($id_curso_actual, $listado_alumnos_completo["id_usuario"])) or die(db_error()."-4");
			
		            while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
			            $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))." ";
		            }
			    } 
			     
				$situacion_actual="<span class='text-success'>Actualmente en el grupo ".$lista_grupos_alumno."</span>";	
			     
			    if ($listado_alumnos_completo["fecha_matricula"]>date("Y-m-d")) {
					$situacion_actual="<span class='text-success'>Próximamente en el grupo ".$lista_grupos_alumno."</span>";
			    }    			
					
			} else {
				
				// Continuó pero ya ha finalizado
				$situacion_actual="<span class='text-primary'>Ha finalizado</span>";
				
			}
		}
		
	?>
	<tr>
		<td><?=$listado_alumnos_completo["id_usuario"]?></td>
		<td><?=$listado_alumnos_completo["Nombre"]?> <?=$listado_alumnos_completo["Apellido1"]?> <?=$listado_alumnos_completo["Apellido2"]?></td>
		<td><?=$situacion_actual?></td>
		<td><?=date("d/m/Y",strtotime($listado_alumnos_completo["fecha_nacimiento"]));?></td>
		<td><?php if ($listado_alumnos_completo["fecha_matricula"]) { echo date("d/m/Y",strtotime($listado_alumnos_completo["fecha_matricula"])); } ?></td>
		<td><?php if ($listado_alumnos_completo["fecha_baja"]) { echo date("d/m/Y",strtotime($listado_alumnos_completo["fecha_baja"])); } ?></td>
		<td class="text-center"><i style="cursor: pointer;" data-nombre="<?=$listado_alumnos_completo["Nombre"]?> <?=$listado_alumnos_completo["Apellido1"]?> <?=$listado_alumnos_completo["Apellido2"]?>" data-consentimiento="Web" data-campo="consentimiento_web" data-id_contacto="<?=$listado_alumnos_completo["id_contacto"]?>" id="consentimiento_web_<?=$listado_alumnos_completo["id_contacto"]?>" class="<?=$muestraCheck[$listado_alumnos_completo["consentimiento_web"]]?>" aria-hidden="true"></i></td>
		<td class="text-center"><i style="cursor: pointer;" data-nombre="<?=$listado_alumnos_completo["Nombre"]?> <?=$listado_alumnos_completo["Apellido1"]?> <?=$listado_alumnos_completo["Apellido2"]?>" data-consentimiento="Blog" data-campo="consentimiento_blog" data-id_contacto="<?=$listado_alumnos_completo["id_contacto"]?>" id="consentimiento_blog_<?=$listado_alumnos_completo["id_contacto"]?>" class="<?=$muestraCheck[$listado_alumnos_completo["consentimiento_blog"]]?>" aria-hidden="true"></i></td>
		<td class="text-center"><i style="cursor: pointer;" data-nombre="<?=$listado_alumnos_completo["Nombre"]?> <?=$listado_alumnos_completo["Apellido1"]?> <?=$listado_alumnos_completo["Apellido2"]?>" data-consentimiento="Redes Sociales" data-campo="consentimiento_redes" data-id_contacto="<?=$listado_alumnos_completo["id_contacto"]?>" id="consentimiento_redes_<?=$listado_alumnos_completo["id_contacto"]?>" class="<?=$muestraCheck[$listado_alumnos_completo["consentimiento_redes"]]?>" aria-hidden="true"></i></td>
	</tr>	
	<?php	
	}	
?>	
</table>
<?php
}	
?>
</p>
<script>
	
	$(document).ready(function() {
	
        $('[id^=consentimiento_]').on('click', function(e) {
            
            var id_contacto = $(this).data("id_contacto");
            var campo = $(this).data("campo");
			var nombre = $(this).data("nombre");
			var consentimiento = $(this).data("consentimiento");

			var valorActual = 2;

            if ($(this).hasClass( "fa-thumbs-down" )) {
	            classQuitada="fa-thumbs-down text-danger";
	            classPuesta="fa-thumbs-up text-success";
	        	valorActual = 0;  
	        	valorNuevo = 1;  
            }
            if ($(this).hasClass( "fa-thumbs-up" )) {
	            classPuesta="fa-thumbs-down text-danger";
	            classQuitada="fa-thumbs-up text-success";
	            valorActual = 1;
	            valorNuevo = 0;	            
            }          
            
            if (valorActual!=2) {
	            
	            var advertenciaUltimo = '';
	            
	            if (valorNuevo==0) {
		            // Cuenta ceros
		            var totalCeros = 0;
		            if ($('#consentimiento_web_'+id_contacto).hasClass( 'fa-thumbs-up text-success' )) {
			            totalCeros++;
		            }
		            if ($('#consentimiento_blog_'+id_contacto).hasClass( 'fa-thumbs-up text-success' )) {
			            totalCeros++;
		            }
		            if ($('#consentimiento_redes_'+id_contacto).hasClass( 'fa-thumbs-up text-success' )) {
			            totalCeros++;
		            }
		            if (totalCeros==1) {
			        	advertenciaUltimo = '\n\nAl hacerlo eliminarías el último consentimiento de este alumno y desaparecería de esta lista.';    
		            }
		            
	            }
	            
	            if (confirm("¿Quieres modificar el consentimiento de "+nombre+" para "+consentimiento+"?"+advertenciaUltimo)) {
	            
		            // Guarda valor

		            $.ajax({
		                url: '/herramientas/guarda_dato.php',
		                type: 'GET',
		                data: {
			                nombre_campo_busqueda: 'id_contacto',
		                    campo_busqueda: id_contacto,
		                    campo: campo,
		                    valor_nuevo: valorNuevo,
		                    tabla: 'contactos'
		                },
		                success: function(resultadoConsulta) {
		
		                    if (resultadoConsulta=='ok') {
		                        var mensaje = 'Dato guardado: '+consentimiento+".";
		                        $('#'+campo+'_'+id_contacto).removeClass( classQuitada ).addClass( classPuesta );
		                        
		                    } else {
		                        var mensajeError = 'No se ha podido guardar el dato ('+consentimiento+'). Error al guardar.';
		                        alert(mensajeError);
		                    }
		
		                },
		                error: function(xhr) {
		                    var mensajeError = 'No se ha podido guardar el dato ('+consentimiento+'). Error de conexión.';
		                    alert(mensajeError);
		                }
		            });	   
	            
	            }        
	            
            }  

        });		
		
	});
	
</script>
</body>
</html>
