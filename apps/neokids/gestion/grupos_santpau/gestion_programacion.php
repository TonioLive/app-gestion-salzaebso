<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];

$cargar_plantilla=$_GET["cargar_plantilla"];

$array_filtro_niveles=array();

$busca_alumnos_grupo=db_query_params('SELECT * FROM escuelamunicipal.usuarios,aulas_alumnos_santpau_grupos,escuelamunicipal.alumnos WHERE escuelamunicipal.usuarios.id_usuario=escuelamunicipal.alumnos.id_usuario AND aulas_alumnos_santpau_grupos.id_alumno=escuelamunicipal.alumnos.id_alumno AND aulas_alumnos_santpau_grupos.id_subgrupo=? AND aulas_alumnos_santpau_grupos.id_grupo=?;', 'ss', array($id_subgrupo, $id_grupo)) or die(db_error());

// $niveles_en_grupo=array(0,0,0,0,0,0,0,0);
$niveles_en_grupo=array(1,1,1,1,1,1,1,1);

while($alumnos=db_fetch_array($busca_alumnos_grupo)) {

	$niveles_en_grupo[$alumnos["nivel_actual"]]=1;	

}

$contador=0;
foreach($niveles_en_grupo as $fila) {
	if ($fila==1) {
		$array_filtro_niveles[]="agenda_programacion_objetivos_piscina.nivel=".$contador;	
	}
	$contador++;
}

if ($contador!=0) {
	$filtro_niveles=implode(" OR ",$array_filtro_niveles);
}

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

if ($cargar_plantilla==1) {

		// Comprueba si hay plantilla
		$busca_plantilla=db_query_params('SELECT * FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=\'0000-00-00\'', 'ss', array($id_grupo, $id_subgrupo));
	
		while($datos_plantilla=db_fetch_array($busca_plantilla)) {
	
			// Comprueba si esté está
			
			$busca_si_esta=db_query_params('SELECT * FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=? AND id_actividad=?', 'ssss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha)), $datos_plantilla['id_actividad']));
			
			if (db_num_rows($busca_si_esta)==0) {
				$inserta=db_query_params('INSERT INTO agenda_programacion_externos (id_grupo,id_subgrupo,fecha,id_actividad) VALUES (?,?,?,?);', 'ssss', array((string)($id_grupo), (string)($id_subgrupo), (string)(date("Y-m-d", $fecha)), (string)($datos_plantilla['id_actividad'])));			
				}
					
		}

}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Programación del Grupo</title>

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

function cargar_plantilla() {

<?php 
$estado_boton_plantillas="";
$busca_plantilla=db_query_params('SELECT * FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=\'0000-00-00\'', 'ss', array($id_grupo, $id_subgrupo));

if (db_num_rows($busca_plantilla)==0) {
$estado_boton_plantillas=" disabled='disabled'";
?>
	alert("Este grupo no tiene plantilla definida.");
<?php 
} else {
?>
	if (confirm("Al cargar la plantilla se añadirán las actividades de la plantilla a la programación de hoy.\n\n¿Estás seguro/a?")) {
		window.location.href="gestion_programacion.php?cargar_plantilla=1&fecha=<?=$_GET["fecha"];?>&id_grupo=<?=$_GET["id_grupo"];?>&id_subgrupo=<?=$_GET["id_subgrupo"];?>&nombre_mostrar=<?=$_GET["nombre_mostrar"];?>";
	}
<?php 
}
?>

}

function guarda_plantilla() {

	ActividadesProgramadas.bootstrapDualListbox('refresh', true);
	if (confirm("Al guardar modificarás la plantilla para todos los días de este grupo.\n\n¿Estás seguro/a?")) {
		document.monitores.action="guarda_programacion_generica.php";	
		document.monitores.submit();
	}


}

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

</script>
</head>
<body>
<div class="container-fluid">
<div class="panel panel-default">
  <div class="panel-heading"><h3>Programación para el <?=date("d/m/Y",$fecha);?>, <?=$_GET["nombre_mostrar"];?>, Grupo <?=$_GET["id_subgrupo"]+1;?>.</h3></div>
  <div class="panel-body">
    <form method="post" action="guarda_programacion.php" name="monitores">
    <input type="hidden" value="0" id="nueva_act" name="nueva_act">
    <input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_subgrupo?>" name="id_subgrupo">
    <input type="hidden" value="<?=$_GET["nombre_mostrar"];?>" name="nombre_mostrar"> 
    <input type="hidden" value="<?=$fecha?>" name="fecha">  
          <h4>Actividades</h4>
<select multiple="multiple" size="20" name="duallistbox_ActividadesProgramadas[]" class="ActividadesProgramadas" tabindex=1>
		<?php	
		
$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
     
    $objetivo_g_actual=0;
                
	while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {                 				
			
		$busca_actividades=db_query("SELECT * FROM agenda_programacion_actividades_piscina LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo WHERE ($filtro_niveles) AND agenda_programacion_objetivos_piscina.id_objetivo_g=".$objetivos_generales["id_objetivo"]."  ORDER BY agenda_programacion_objetivos_piscina.nivel, agenda_programacion_objetivos_piscina.id_objetivo, agenda_programacion_actividades_piscina.id_actividad") or die(db_error());		
		
		if (db_num_rows($busca_actividades)!=0) {
		
		$objetivo_actual=0;			
		
		?>
		<option value="" disabled="disabled" class="text-default objetivo_general"><?=$objetivos_generales["id_objetivo"];?>. <?=$objetivos_generales["nombre_objetivo"];?></option>
		<?php 
		$objetivo_g_actual=$objetivos_generales["id_objetivo"];		
		
		while($actividades=db_fetch_array($busca_actividades)) {
		
			if ($actividades["id_objetivo"]!=$objetivo_actual) {
				?>
				<option value="" disabled="disabled" class="text-warning objetivo_especifico"><?=$actividades["nombre_objetivo"];?></option>
				<?php 
				$objetivo_actual=$actividades["id_objetivo"];
			}	
		
		// Comprueba si están
		$busca_si_esta=db_query_params('SELECT * FROM agenda_programacion WHERE id_grupo=? AND id_subgrupo=? AND fecha=? AND id_actividad=?', 'ssss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha)), $actividades["id_actividad"]));	
			
		?>
		<option class="<?=$colores_niveles[$actividades["nivel"]]?> input-sm" value="<?=$actividades["id_actividad"]?>"<?php  if (db_num_rows($busca_si_esta)!=0) { echo(" selected"); } ?>>- <?=$actividades["descripcion_actividad"];?></option>
		<?php 
				}
        	}
        }
        ?>
      </select>      
    <br />
    <h4>Nueva actividad</h4>
    <div class="row">
  		<div class="col-md-5">
    			<select data-placeholder="Objetivo" name="objetivo_nueva" id="objetivo_nueva" tabindex=2 class="form-control">
  					<option>Selecciona el objetivo de la nueva actividad</option>
					<?php 
					$busca_objetivos=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE ($filtro_niveles) AND id_objetivo_g!=0 ORDER BY agenda_programacion_objetivos_piscina.nivel, agenda_programacion_objetivos_piscina.id_objetivo");
		
					while($objetivos=db_fetch_array($busca_objetivos)) {
					
						// Busca si tienen actividades
						$color_fila="";
						$busca_a=db_query_params('SELECT * FROM agenda_programacion_actividades_piscina WHERE id_objetivo=?', 's', array($objetivos["id_objetivo"]));
						
						if (db_num_rows($busca_a)==0) {
							$color_fila=" class='text-danger'";
						}
						
					?>
					<option value="<?=$objetivos["id_objetivo"]?>"<?=$color_fila?>><?=$objetivos["nombre_objetivo"];?></option>
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
	<br />
	<button type="button"<?=$estado_boton_plantillas?> onclick="cargar_plantilla();" class="btn btn-default">Cargar plantilla</button> <button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <button type="button" onclick="guardar();" class="btn btn-success">Guardar</button> <button type="button" class="btn btn-primary" onclick="guarda_plantilla();">Guardar como plantilla</button> <a class="btn btn-info" href="editar_objetivos.php?fecha=<?=$_GET["fecha"];?>&id_grupo=<?=$_GET["id_grupo"];?>&id_subgrupo=<?=$_GET["id_subgrupo"];?>&nombre_mostrar=<?=$_GET["nombre_mostrar"];?>">Editar actividades y objetivos</a>
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
</script>      
</body>
</html>
