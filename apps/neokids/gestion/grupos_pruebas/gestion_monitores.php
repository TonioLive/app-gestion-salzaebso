<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$nombre_mostrar=utf8_decode($_GET["nombre_mostrar"]);


$colores_niveles=array(""," text-success"," text-info"," text-danger");

// Busca monitores en grupo

$monitor=array();

$busca_monitores=db_query_params('SELECT * FROM aulas_grupos_monitores WHERE id_grupo=? AND id_subgrupo=?', 'ss', array($id_grupo, $id_subgrupo)) or die(db_error());

if (db_num_rows($busca_monitores)==0) {
	$inserta1=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,1);', 'ss', array($id_grupo, $id_subgrupo));
	$inserta2=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,2);', 'ss', array($id_grupo, $id_subgrupo));
	$inserta3=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,3);', 'ss', array($id_grupo, $id_subgrupo));
	$inserta4=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,4);', 'ss', array($id_grupo, $id_subgrupo));	} else {
	while($lista_monitores=db_fetch_array($busca_monitores)) {
		$monitor[$lista_monitores["num_monitor"]]=$lista_monitores["id_usuario"];
		}
	}

$busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=? AND id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

$datos_subgrupo=db_fetch_array($busca_datos_subgrupo);

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<script>

function gestion_programacion() {
	window.open("gestion_programacion.php?fecha=<?=$fecha?>&id_grupo=<?=$id_grupo;?>&id_subgrupo=<?=$id_subgrupo;?>&nombre_mostrar=<?=urlencode($nombre_mostrar);?>","edicion_programacion","toolbars=no,scrollbars=yes,width=1024,height=768");
	}       

</script>
</head>
<body>
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Editar grupo</h2><?=urldecode($nombre_mostrar);?>, Grupo <?=$_GET["id_subgrupo"]+1;?></div>
  <div class="panel-body">
    <form method="post" action="guarda_monitores.php" name="monitores">
    <input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_subgrupo?>" name="id_subgrupo">
    <input type="hidden" value="<?=$nombre_mostrar?>" name="nombre_mostrar">
    <table class="table table-condensed">
    <tr>
    <td align="right" class="col-md-1">Monitor/a 1:</td>
    <td class="col-md-3"><select name="monitor1" class="form-control"><option value="0"></option>
    							<?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$monitor[1]) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
                                <?php 
                                }
                                ?>
                                                                                </select>
		<?php 
        $busca_sustitucion=db_query("SELECT * FROM personal_asistencia_sustituciones LEFT JOIN usuarios ON personal_asistencia_sustituciones.sustituido_por=usuarios.id_usuario WHERE personal_asistencia_sustituciones.id_grupo=$id_grupo AND personal_asistencia_sustituciones.id_subgrupo=$id_subgrupo AND personal_asistencia_sustituciones.id_usuario=".$monitor[1]." AND ((personal_asistencia_sustituciones.desde<='".date("Y-m-d",$fecha)."' AND personal_asistencia_sustituciones.hasta>='".date("Y-m-d",$fecha)."') OR (personal_asistencia_sustituciones.desde='".date("Y-m-d",$fecha)."'))") or die(db_error()); 
        $id_sustitucion=0;   
        if (db_num_rows($busca_sustitucion)!=0) {
        	$datos_sustitucion=db_fetch_array($busca_sustitucion);
        	
        	$id_sustitucion=$datos_sustitucion["id_sustitucion"];
        	?>
        	<span class="text-danger">Sustituido/a por <?=$datos_sustitucion["Nombre"]?> <?=$datos_sustitucion["Apellido1"]?> <?=$datos_sustitucion["Apellido2"]?> <?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?>d<?php  } ?>el <?=date("d/m/Y",strtotime($datos_sustitucion["desde"]));?><?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?> al <?=date("d/m/Y",strtotime($datos_sustitucion["hasta"]));?><?php  } ?>.</span>
        	<?php 
        }                                                                    
		?>
		</td>
		<td><button type="button" onclick="sustituye(1,<?=$id_sustitucion?>)" class="btn btn-xs btn-info"><i class="fa fa-exchange" aria-hidden="true"></i> Sustitución</button></td>                                                                                
    </tr>
    <tr>
    <td align="right" class="col-md-1">Monitor/a 2:</td>
    <td class="col-md-3"><select name="monitor2" class="form-control"><option value="0"></option>
    							<?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$monitor[2]) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
                                <?php 
                                }
                                ?>
			</select>
		<?php 
        $busca_sustitucion=db_query("SELECT * FROM personal_asistencia_sustituciones LEFT JOIN usuarios ON personal_asistencia_sustituciones.sustituido_por=usuarios.id_usuario WHERE personal_asistencia_sustituciones.id_grupo=$id_grupo AND personal_asistencia_sustituciones.id_subgrupo=$id_subgrupo AND personal_asistencia_sustituciones.id_usuario=".$monitor[2]." AND ((personal_asistencia_sustituciones.desde<='".date("Y-m-d",$fecha)."' AND personal_asistencia_sustituciones.hasta>='".date("Y-m-d",$fecha)."') OR (personal_asistencia_sustituciones.desde='".date("Y-m-d",$fecha)."'))") or die(db_error()); 
        $id_sustitucion=0;   
        if (db_num_rows($busca_sustitucion)!=0) {
        	$datos_sustitucion=db_fetch_array($busca_sustitucion);
        	
        	$id_sustitucion=$datos_sustitucion["id_sustitucion"];
        	?>
        	<span class="text-danger">Sustituido/a por <?=$datos_sustitucion["Nombre"]?> <?=$datos_sustitucion["Apellido1"]?> <?=$datos_sustitucion["Apellido2"]?> <?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?>d<?php  } ?>el <?=date("d/m/Y",strtotime($datos_sustitucion["desde"]));?><?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?> al <?=date("d/m/Y",strtotime($datos_sustitucion["hasta"]));?><?php  } ?>.</span>
        	<?php 
        }                                                                    
		?>			
		</td>
		<td><button type="button" onclick="sustituye(2,<?=$id_sustitucion?>)" class="btn btn-xs btn-info"><i class="fa fa-exchange" aria-hidden="true"></i> Sustitución</button></td>
    </tr>
    <tr>
    <td align="right" class="col-md-1">Monitor/a 3:</td>
    <td class="col-md-3"><select name="monitor3" class="form-control"><option value="0"></option>
    							<?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$monitor[3]) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
                                <?php 
                                }
                                ?>
			</select>
		<?php 
        $busca_sustitucion=db_query("SELECT * FROM personal_asistencia_sustituciones LEFT JOIN usuarios ON personal_asistencia_sustituciones.sustituido_por=usuarios.id_usuario WHERE personal_asistencia_sustituciones.id_grupo=$id_grupo AND personal_asistencia_sustituciones.id_subgrupo=$id_subgrupo AND personal_asistencia_sustituciones.id_usuario=".$monitor[3]." AND ((personal_asistencia_sustituciones.desde<='".date("Y-m-d",$fecha)."' AND personal_asistencia_sustituciones.hasta>='".date("Y-m-d",$fecha)."') OR (personal_asistencia_sustituciones.desde='".date("Y-m-d",$fecha)."'))") or die(db_error()); 
        $id_sustitucion=0;   
        if (db_num_rows($busca_sustitucion)!=0) {
        	$datos_sustitucion=db_fetch_array($busca_sustitucion);
        	
        	$id_sustitucion=$datos_sustitucion["id_sustitucion"];
        	?>
        	<span class="text-danger">Sustituido/a por <?=$datos_sustitucion["Nombre"]?> <?=$datos_sustitucion["Apellido1"]?> <?=$datos_sustitucion["Apellido2"]?> <?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?>d<?php  } ?>el <?=date("d/m/Y",strtotime($datos_sustitucion["desde"]));?><?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?> al <?=date("d/m/Y",strtotime($datos_sustitucion["hasta"]));?><?php  } ?>.</span>
        	<?php 
        }                                                                    
		?>			
		</td>
		<td><button type="button" onclick="sustituye(3,<?=$id_sustitucion?>)" class="btn btn-xs btn-info"><i class="fa fa-exchange" aria-hidden="true"></i> Sustitución</button></td>                                                                                  
    </tr>
    <tr>
    <td align="right" class="col-md-1">Monitor/a 4:</td>
    <td class="col-md-3"><select name="monitor4" class="form-control"><option value="0"></option>
    							<?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$monitor[4]) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
                                <?php 
                                }
                                ?>
			</select>
		<?php 
        $busca_sustitucion=db_query("SELECT * FROM personal_asistencia_sustituciones LEFT JOIN usuarios ON personal_asistencia_sustituciones.sustituido_por=usuarios.id_usuario WHERE personal_asistencia_sustituciones.id_grupo=$id_grupo AND personal_asistencia_sustituciones.id_subgrupo=$id_subgrupo AND personal_asistencia_sustituciones.id_usuario=".$monitor[4]." AND ((personal_asistencia_sustituciones.desde<='".date("Y-m-d",$fecha)."' AND personal_asistencia_sustituciones.hasta>='".date("Y-m-d",$fecha)."') OR (personal_asistencia_sustituciones.desde='".date("Y-m-d",$fecha)."'))") or die(db_error()); 
        $id_sustitucion=0;   
        if (db_num_rows($busca_sustitucion)!=0) {
        	$datos_sustitucion=db_fetch_array($busca_sustitucion);
        	
        	$id_sustitucion=$datos_sustitucion["id_sustitucion"];
        	?>
        	<span class="text-danger">Sustituido/a por <?=$datos_sustitucion["Nombre"]?> <?=$datos_sustitucion["Apellido1"]?> <?=$datos_sustitucion["Apellido2"]?> <?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?>d<?php  } ?>el <?=date("d/m/Y",strtotime($datos_sustitucion["desde"]));?><?php  if ($datos_sustitucion["hasta"]!='0000-00-00') { ?> al <?=date("d/m/Y",strtotime($datos_sustitucion["hasta"]));?><?php  } ?>.</span>
        	<?php 
        }                                                                    
		?>			
		</td>
		<td><button type="button" onclick="sustituye(4,<?=$id_sustitucion?>)" class="btn btn-xs btn-info"><i class="fa fa-exchange" aria-hidden="true"></i> Sustitución</button></td>                                                                                  
    </tr>
    <tr>
    <td align="right" class="col-md-1">Capacidad del grupo/Nº de asistentes:</td>
    <td class="col-md-3"><select name="capacidad" class="form-control">
    							<?php 
    							for ($cap_subgrupos=1;$cap_subgrupos<=20;$cap_subgrupos++) {
                                ?>
                                <option value="<?=$cap_subgrupos;?>"<?php  if ($datos_subgrupo["capacidad"]==$cap_subgrupos) { print " selected"; } ?>><?=$cap_subgrupos;?></option>
                                <?php 
                                }
                                ?>
                                                                                </select></td>
    </tr>
    <tr>
    <td align="right" class="col-md-1">Tipo de grupo:</td>
    <td class="col-md-3" colspan="2"><select name="tipo_subgrupo" id="tipo_subgrupo" class="form-control" onchange="comprueba_tipo('tipo_subgrupo',4,'#collapseEvento');">
    							<?php 
                                $busca_tipos_grupos=db_query("SELECT * FROM aulas_grupos_tipos;");
                                while($datos_grupos=db_fetch_array($busca_tipos_grupos)) {
                                ?>
                                <option value="<?=$datos_grupos["id_tipo_grupo"];?>"<?php  if ($datos_grupos["id_tipo_grupo"]==$datos_subgrupo["tipo_subgrupo"]) { print " selected"; } ?>><?=$datos_grupos["nombre_tipo_grupo"];?></option>
                                <?php 
                                }
                                ?>
                                                                                </select></td>
    </tr>          
	</table>
	<div class="collapse<?php  if ($datos_subgrupo["tipo_subgrupo"]==4) { print " in"; } ?>" id="collapseEvento">
  		<div class="well">
    	<b>Detalles evento/cumpleaños:</b>
    	<textarea name="descripcion_evento" id="descripcion_evento" class="form-control" rows=3 ><?=$datos_subgrupo["descripcion_evento"];?></textarea>
	  	</div>
	</div>
	<?php 
	// Busca si hay programación
	
	$busca_programacion=db_query_params('SELECT * FROM agenda_programacion LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion.id_actividad=agenda_programacion_actividades_piscina.id_actividad LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo  WHERE id_grupo=? AND id_subgrupo=? AND fecha=? ORDER BY agenda_programacion.id_prog', 'sss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha))));
	
$busca_programacion_plantillas=db_query("SELECT * FROM agenda_programacion_grupos_vinculados LEFT JOIN agenda_programacion_plantillas ON agenda_programacion_grupos_vinculados.id_plantilla=agenda_programacion_plantillas.id_plantilla WHERE id_grupo=$id_grupo AND id_subgrupo=".($id_subgrupo+1)." AND fecha_inicio<='".date("Y-m-d",$fecha)."'");
	

	$hay_plantillas=false;
	$lista_actividades_programadas=array();
	if (db_num_rows($busca_programacion_plantillas)!=0) {		
		
		$fecha_actual=$fecha+10801;
	
	
		while($programacion_plantillas=db_fetch_array($busca_programacion_plantillas)) {
			// Comprueba fechas
			$id_plantilla=$programacion_plantillas["id_plantilla"];
			$dia_prog=strtotime($programacion_plantillas["fecha_inicio"]." 03:00"); 
			
			$num_sesiones=$programacion_plantillas["num_sesiones"];
			
			if ($programacion_plantillas["periodicidad"]==1) {
						
				// Mensual
				$fecha_inicio=$dia_prog;
				$mes_inicio=date("n",$dia_prog);
				$anio_inicio=date("Y",$dia_prog);
				
				$mes_fin=$mes_inicio+($num_sesiones-1);
				$anio_fin=$anio_inicio;
				
				
				if ($mes_fin>12) {
				
					$mes_fin=$mes_fin-12;
					$anio_fin++;
				
				}
				
				$fecha_fin_previo=strtotime($anio_fin."-".$mes_fin."-01");
				$fecha_fin=strtotime($anio_fin."-".$mes_fin."-".date("t",$fecha_fin_previo));				
				
				if (($fecha_actual>=$fecha_inicio) and ($fecha_actual<=$fecha_fin)) {
				
					// Estamos dentro del periodo, buscamos cual es la semana actual
					$mes_actual=date("n",$fecha_actual);
					
					$sesion_actual=$mes_actual-$mes_inicio+1;
					
					if ($sesion_actual<0) {
					
						$sesion_actual=$sesion_actual+12;
					
					}
									
				}
								
			} else {
				// Semanal
				for($c_semanas=1;$c_semanas<=$num_sesiones;$c_semanas++) {
										
					if (date("Y-m-d",$fecha+10801)==date("Y-m-d",$dia_prog)) {
					
						$sesion_actual=$c_semanas;
					
					}
					
					$dia_prog = $dia_prog + (86400*7);					
					
				}												
			}			
			
			if ($sesion_actual) {
				
				// Busca programación sesion actual										
				$busca_sesion_actual=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_actividades_piscina.id_actividad LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo  WHERE agenda_programacion_plantillas_actividades.id_plantilla=$id_plantilla AND agenda_programacion_plantillas_actividades.numero_sesion=$sesion_actual ORDER BY agenda_programacion_plantillas_actividades.orden_actividad,agenda_programacion_plantillas_actividades.id_prog");	
					
				if (db_num_rows($busca_sesion_actual)!=0) {
					
					$hay_plantillas=true;
						
					}
			}
									
		}			
	}

	if ((db_num_rows($busca_programacion)!=0) or ($hay_plantillas==true)) {
	
	?>
	<h4>Programación día <?=date("d/m/Y",$fecha);?></h4>
	
	<table class="table table-condensed table-striped"> 
	<?php 
	
	$objetivo_actual=0;
	
	$contador=0;
	
	while($programacion=db_fetch_array($busca_programacion)){
		
		$contador++;
		
		if ($programacion["id_objetivo"]!=$objetivo_actual) {
				?>
				<tr>
					<td colspan="2"><h3 class="text-warning"><?=$programacion["nombre_objetivo"];?></h3></td>
				</tr>
				<?php 
				$objetivo_actual=$programacion["id_objetivo"];
			}
	
		// Busca imagen
		$target = $web_path."/gestion/grupos_pruebas/imagenes";
		if (file_exists($target."/".$programacion["id_actividad"].".jpg")) {
			?>
			<tr>
								
				<td class="col-md-9">
					<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
				</td>	
				<td class="col-md-3">
					<a href='javascript:mostrar_imagen(<?=$programacion["id_actividad"]?>,"<?=$programacion["descripcion_actividad"];?>");'><img width='100%' src='imagenes/<?=$programacion["id_actividad"];?>.jpg' border=0></a>
				</td>	
						
			</tr>
			<?php 
		} else {		
		?>
		<tr>
			<td class="col-md-9">
				<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
			</td>	
			<td class="col-md-9">
			</td>		
		</tr>
		<?php 		
		}
		
		
		}
		
	// Hay plantillas programadas
	if ($hay_plantillas==true) {
	
	$objetivo_actual=0;
	
	while($programacion=db_fetch_array($busca_sesion_actual)){
		
		$contador++;
		
		if ($programacion["id_objetivo"]!=$objetivo_actual) {
				?>
				<tr>
					<td colspan="2" class="text-warning"><?=$programacion["nombre_objetivo"];?></h3>
				</tr>
				<?php 
				$objetivo_actual=$programacion["id_objetivo"];
			}
	
		// Busca imagen
		$target = $web_path."/gestion/grupos_pruebas/imagenes";
		if (file_exists($target."/".$programacion["id_actividad"].".jpg")) {
			?>
			<tr>
								
				<td class="col-md-9">
					<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
				</td>	
				<td class="col-md-3">
					<a href='javascript:mostrar_imagen(<?=$programacion["id_actividad"]?>,"<?=$programacion["descripcion_actividad"];?>");'><img width='100%' src='imagenes/<?=$programacion["id_actividad"];?>.jpg' border=0></a>
				</td>	
						
			</tr>
			<?php 
		} else {		
		?>
		<tr>
			<td class="col-md-9">
				<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
			</td>	
			<td class="col-md-9">
			</td>		
		</tr>
		<?php 		
		}
		
		
		}
	
	}		
	// Final plantillas programadas		
		
	?>
	</table>
	<?php 	
	}
	
	?>
	<br />
	<button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <button type="button" onclick="gestion_programacion();" class="btn btn-info"><i class="fa fa-graduation-cap"></i> Gestionar programación</button> <button type="submit" class="btn btn-success">Guardar</button>
    </form>
  </div>
</div>

</div>

<div class="modal modal-wide fade" id="imagenModal" tabindex="-1" role="dialog" aria-labelledby="imagenModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">  
		<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombreImagen"><h4 class="modal-title" id="grupoModalLabel">Título</h4></div>
      </div>     
      <div class="modal-body" id="imagen_actividad_modal">
        <img src="imagen/nada.jpg" border=0 witdh="100%">
      </div>
    </div>
  </div>
</div>  

<div class="modal modal-wide fade" id="sustitucionModal" tabindex="-1" role="dialog" aria-labelledby="sustitucionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">  
		<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombreSustitucion"><h4 class="modal-title" id="sustitucionModalLabel">Título</h4></div>
      </div>     
      <div class="modal-body" id="sustitucionModalBody">
			<iframe src="" name="iframeSustituciones" id="iframeSustituciones" width=100% height=350 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div> 

<script>

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" }
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function comprueba_tipo(objeto,valor_positivo,capa_mostrada) {

	var tipo_actual =  document.getElementById(objeto).value;

	if (tipo_actual==valor_positivo) {
		$(capa_mostrada).show();
		} else {
		$(capa_mostrada).hide();
		}

	}

function mostrar_imagen(id_actividad,nombre_imagen) {

	url = 'imagenes/'+id_actividad+".jpg";
            
	$('#nombreImagen').html('<h4 class="modal-title" id="grupoModalLabel">' + nombre_imagen + '</h4>'); 
	$('#imagen_actividad_modal').html("<a href='"+url+"' target='_blank'><img src='"+url+"' border=0 width='100%'></a>");

	$('#imagenModal').modal('show');

}

function sustituye(monitor,id_sustitucion) {

		$('#nombreSustitucion').html('<h4 class="modal-title" id="sustitucionModalLabel">Sustitución del monitor/a ' + monitor + '</h4>'); 
		$('#iframeSustituciones').attr('src', 'sustituir_monitor.php?monitor='+monitor+'&nombre_mostrar=<?=$nombre_mostrar?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&fecha=<?=$fecha;?>&id_sustitucion='+id_sustitucion); 
		$('#sustitucionModal').modal('show');

	}

window.closeModal = function(){
    $('#sustitucionModal').modal('hide');
	};

</script>

</body>
</html>
