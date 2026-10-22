<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_GET["id_plantilla"];
$sesion_editada=$_GET["sesion_editada"];

$busca_datos=db_query("SELECT * FROM agenda_programacion_plantillas WHERE id_plantilla=$id_plantilla");
	
if (db_num_rows($busca_datos)!=0) {
	
	$datos_plantilla=db_fetch_array($busca_datos);
		
	$descripcion=$datos_plantilla["descripcion"];
	$nivel=$datos_plantilla["nivel"];
	$num_sesiones=$datos_plantilla["num_sesiones"];
			
	}

$filtro_niveles="agenda_programacion_ingles_objetivos.version=".$version_actual_prog_ingles." AND (agenda_programacion_ingles_objetivos.edad=".$nivel." OR agenda_programacion_ingles_objetivos.edad=9999)";	

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Programación - Sesión nº <?=$sesion_editada?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/bootstrap-duallistbox.css" type="text/css">
<link rel="stylesheet" href="/src/chosen.css">

<style>

.objetivo_general {
    font-weight: bold;
}

.objetivo_especifico {
	font-weight: bold;
	font-style: oblique;
}

.actividad {

}

</style>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/jquery.bootstrap-duallistbox.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $(".chosen-select").chosen()
});	

function guardar() {

	ActividadesProgramadas.bootstrapDualListbox('refresh', true);
	
	document.monitores.submit();

}


function nueva_actividad() {

	ActividadesProgramadas.bootstrapDualListbox('refresh', true);

	// Comprueba si el formulario está completo
	actividad_nueva = $("#actividad_nueva").val();
	objetivo_nueva = $("#objetivo_nueva").val();

	if ((actividad_nueva!="")&&(objetivo_nueva!="")) {
	
		$("#nueva_act").val(1);
		document.monitores.submit();
	
	} else {
	
		alert("Debes rellenar los datos de la nueva actividad (Objetivo y descripción).");
	
	}

}

function toggler(divId1,divId2,accion) {
	
	
	
	if (accion==0) {
		
	    $("#" + divId2).hide();
	    $("#" + divId1).show();		
		
	} else {
		
		$('#volver').val(1);
		$('#pagina_activa').val(divId1);
		document.monitores.submit();	
		
	}

}

</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
<div class="container-fluid">
<div class="panel panel-default">
  <div class="panel-heading"><h3>Actividades de la sesión nº <?=$sesion_editada?>, Programación: <?=$descripcion?>.</h3></div>
  <div class="panel-body">
    <form method="post" action="guarda_programacion_plantilla.php" name="monitores">
    <input type="hidden" value="0" id="nueva_act" name="nueva_act">
    <input type="hidden" value="<?=$id_plantilla?>" name="id_plantilla">
    <input type="hidden" value="<?=$sesion_editada?>" name="sesion_editada">  
    <input type="hidden" value="0" name="volver" id="volver"> 
    <input type="hidden" value="seleccion" name="pagina_activa" id="pagina_activa">      
    
	<div id="seleccion" class="collapse<?php if (($_GET['pagina_activa']=='seleccion') OR (strlen($_GET['pagina_activa'])==0)) { ?> in<?php } ?>">
			<ul class="nav nav-tabs">								
				<li role="presentation" class="active"><a href="javascript:toggler('seleccion','orden',0)">Añadir Actividades</a></li>					<li role="presentation"><a href="javascript:toggler('orden','seleccion',1)">Ordenar Actividades</a></li>
			</ul>
		
			<select multiple="multiple" size="10" name="duallistbox_ActividadesProgramadas[]" class="ActividadesProgramadas" tabindex=1>
			<?php	
		   		                							
		$busca_actividades=db_query("SELECT * FROM agenda_programacion_ingles_actividades LEFT JOIN agenda_programacion_ingles_objetivos ON agenda_programacion_ingles_actividades.id_objetivo=agenda_programacion_ingles_objetivos.id_objetivo WHERE ($filtro_niveles) ORDER BY agenda_programacion_ingles_objetivos.nivel, agenda_programacion_ingles_objetivos.id_objetivo, agenda_programacion_ingles_actividades.id_actividad") or die(db_error());		
		
		if (db_num_rows($busca_actividades)!=0) {
		
		$objetivo_actual=0;				
		
		while($actividades=db_fetch_array($busca_actividades)) {
		
			if ($actividades["id_objetivo"]!=$objetivo_actual) {
				?>
				<option value="" disabled="disabled" class="text-warning objetivo_especifico"><?=ucfirst($actividades["nombre_objetivo"]);?></option>
				<?php 
				$objetivo_actual=$actividades["id_objetivo"];
			}	
		
		// Comprueba si están
		$busca_si_esta=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_editada AND id_actividad=".$actividades["id_actividad"]);	
			
		?>
		<option class="<?=$colores_niveles[$actividades["edad"]]?> input-sm" value="<?=$actividades["id_actividad"]?>"<?php  if (db_num_rows($busca_si_esta)!=0) { echo(" selected"); } ?>>- <?=ucfirst($actividades["descripcion_actividad"]);?></option>
		<?php 
				}
        	}
        ?>
      </select> 
    </div> 
        
    <div id="orden" class="collapse<?php if ($_GET['pagina_activa']=='orden') { ?> in<?php } ?>">
    
			<ul class="nav nav-tabs">
				<li role="presentation"><a href="javascript:toggler('seleccion','orden',1)">Añadir Actividades</a></li>	
				<li role="presentation" class="active"><a href="javascript:toggler('orden','seleccion',0)">Ordenar Actividades</a></li>				</ul>
			
			<ul class="list-group" id="listaOrden">
				<?php
				$contador_actividades=0;
				
				$busca_actividades=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_ingles_actividades ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_ingles_actividades.id_actividad WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_editada ORDER BY orden_actividad,id_prog");
				
				while($actividades=db_fetch_array($busca_actividades)) {
					?>
					<li class="list-group-item" id="actividad_<?=$actividades["id_prog"];?>"><?=ucfirst($actividades["descripcion_actividad"]);?></li>
					<?php 	
					$contador_actividades++;	
				}	
				?>
			</ul>
		<?php 
		if ($contador_actividades<12) {
		
			for ($filas=0;$filas<=12;$filas++) {
		
		?>
		<br />	
		<?php 	
			}
		}	
		?>		
    </div>      
    
    <br />
    <h4>Nueva actividad</h4>
    <div class="row">
  		<div class="col-md-5">
    			<select data-placeholder="Objetivo" name="objetivo_nueva" id="objetivo_nueva" tabindex=2 class="form-control">
  					<option>Selecciona el objetivo de la nueva actividad</option>
					<?php 
					$busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE ($filtro_niveles) ORDER BY agenda_programacion_ingles_objetivos.nivel, agenda_programacion_ingles_objetivos.id_objetivo");
		
					while($objetivos=db_fetch_array($busca_objetivos)) {
					
						// Busca si tienen actividades
						$color_fila="";
						$busca_a=db_query("SELECT * FROM agenda_programacion_ingles_actividades WHERE id_objetivo=".$objetivos["id_objetivo"]);
						
						if (db_num_rows($busca_a)==0) {
							$color_fila=" class='text-danger'";
						}
						
					?>
					<option value="<?=$objetivos["id_objetivo"]?>"<?=$color_fila?>><?=ucfirst($objetivos["nombre_objetivo"]);?></option>
					<?php 
        			}
					?>
        		</select>
  		</div>
  		<div class="col-md-5">
			<input type="text" name="actividad_nueva" id="actividad_nueva" placeholder="Descripción nueva actividad" class="form-control">
  		</div>
  		<div class="col-md-2">
  		<button type="button" class="btn btn-default" onclick="nueva_actividad();">Añadir</button>
  		</div>
	</div>
	<p><br /><a type="button" href="gestion_plantillas.php?id_plantilla=<?=$id_plantilla?>" class="btn btn-danger">Salir sin guardar</a> <button type="button" onclick="guardar();" class="btn btn-success">Guardar</button> <a class="btn btn-info" href="editar_objetivos_plantillas.php?id_plantilla=<?=$id_plantilla;?>&sesion_editada=<?=$sesion_editada;?>">Editar actividades y objetivos</a></p>
	<?php 
if ($num_sesiones>1) {
    			?>
    			<div class="panel panel-success">
    			<div class="panel-heading">Copiar en las sesiones</div>
  				<div class="panel-body">    	
  				Selecciona las sesiones donde quieres que se copien estas actividades.		
    			<table class="table">
    			<?php     		
    			$columna_actual=0;
    			for ($sesiones=1;$sesiones<=$num_sesiones;$sesiones++) {
    				if ($columna_actual==0) {
    				?>
    				<tr>
    				<?php 
    				}
    				
    				// Comprueba si está programada
    				$busca_programacion=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesiones");
    				
    				$color_boton="success";
    				$coletillas="";
    				if (db_num_rows($busca_programacion)!=0) {    					
    					$color_boton="warning";
    					$coletillas=" *";
    				}
    				
    				$estado_opcion="";
    				if ($sesiones==$sesion_editada) {
    					$estado_opcion=" disabled='disabled'";
    				}
    				
    				?>
    				<td class="col-md-2 text-center">
    				
    					<div class="has-<?=$color_boton?>">
  							<div class="checkbox">
    							<label>
      								<input type="checkbox"<?=$estado_opcion?> value="<?=$sesiones?>" name="sesiones_copiar[]" id="sesiones_copiar<?=$sesiones?>">
      								<?=$sesiones.$coletillas?>
    							</label>
  							</div>
						</div>
					</td>
    				<?php 
    				$columna_actual++;
    				if ($columna_actual==6) {
	    				$columna_actual=0;
    					?>
    				</tr>
    					<?php 
    				}
    			}
    			?>
    			</table> 
    			<h5>¿Qué quieres que ocurra con las actividades ya programadas en las sesiones seleccionadas?</h5>
    			<h5>Las sesiones marcadas con un * ya tienen actividades programadas, cuando copies estas encima de alguna de ellas pueden...</h5> 
    			<label class="radio-inline">
  					<input type="radio" name="borrar" id="borrar1" value="1" checked> Borrarse las actividades ya programadas y añadirse las aquí seleccionadas.
				</label>
				<label class="radio-inline">
  					<input type="radio" name="borrar" id="borrar2" value="0"> Sumarse las aquí seleccionadas a las que ya están programadas.
				</label> 
					</div>
				</div>   			    			
    			<?php 
    			}	
	?>
    </form> 
	<br />
	<br />	      
</div>
</div>    
<!-- JS -->        
<script>
var ActividadesProgramadas = $('.ActividadesProgramadas').bootstrapDualListbox({
	nonSelectedListLabel: 'Disponibles',
    selectedListLabel: 'Programadas',
    infoTextFiltered: '<span class="label label-warning">Lista filtrada</span> {0} de {1}',
    infoTextEmpty: 'Lista vacía',
    preserveSelectionOnMove: 'moved',
    filterTextClear: 'Mostrar todas',
    filterPlaceHolder: 'Buscar',
    moveSelectedLabel: 'Añadir seleccionadas',
    moveAllLabel: 'Añadir todas',
    removeSelectedLabel: 'Quitar seleccionadas',
    removeAllLabel: 'Quitar todas',
    moveOnSelect: false,
    infoText: '{0} actividades',
    nonSelectedFilter: ''
});

$(function() {	

	$( "#listaOrden" ).sortable({
		update: function(event, ui) {		
			var order = $(this).sortable('serialize');
			$.ajax({
				data: order,
				type: 'POST',
				url: 'editar_plantillas_orden1.php'
			});
		}
	}).disableSelection();
	
});


function comprueba() {
	
	var index_actual = $('#bootstrap-duallistbox-nonselected-list_duallistbox_ActividadesProgramadas\\[\\] > option:selected').prevAll().length;

	index_actual=index_actual+1;

	// $('#bootstrap-duallistbox-nonselected-list_duallistbox_ActividadesProgramadas\\[\\] > option').eq(index_actual).attr('selected','selected')
	
	alert($("select#bootstrap-duallistbox-nonselected-list_duallistbox_ActividadesProgramadas\\[\\] > option:nth-child("+index_actual+")").text());
	
}


</script>      
</body>
</html>
