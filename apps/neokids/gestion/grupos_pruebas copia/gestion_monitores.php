<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$nombre_mostrar=$_GET["nombre_mostrar"];


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
        window.open("gestion_programacion.php?fecha=<?=$fecha?>&id_grupo=<?=$id_grupo;?>&id_subgrupo=<?=$id_subgrupo;?>&nombre_mostrar=<?=$nombre_mostrar;?>","edicion_programacion","toolbars=no,scrollbars=yes,width=1024,height=768");
        }

</script>
</head>
<body>
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Editar grupo</h2><?=$_GET["nombre_mostrar"];?>, Grupo <?=$_GET["id_subgrupo"]+1;?></div>
  <div class="panel-body">
    <form method="post" action="guarda_monitores.php" name="monitores">
    <input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_subgrupo?>" name="id_subgrupo">
    <input type="hidden" value="<?=$id_mostrar?>" name="nombre_mostrar">
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
                                                                                </select></td>
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
                                                                                </select></td>
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
                                                                                </select></td>
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
                                                                                </select></td>
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
    <td class="col-md-3"><select name="tipo_subgrupo" id="tipo_subgrupo" class="form-control" onchange="comprueba_tipo('tipo_subgrupo',4,'#collapseEvento');">
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
	
	$busca_programacion=db_query_params('SELECT * FROM agenda_programacion LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion.id_actividad=agenda_programacion_actividades_piscina.id_actividad LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo  WHERE id_grupo=? AND id_subgrupo=? AND fecha=? ORDER BY agenda_programacion_objetivos_piscina.nivel, agenda_programacion_objetivos_piscina.nombre_objetivo, agenda_programacion_actividades_piscina.id_actividad', 'sss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha))));
	
	if (db_num_rows($busca_programacion)!=0) {
	
	?>
	<h4>Programación día <?=date("d/m/Y",$fecha);?></h4>
	<?php 
	
	$objetivo_actual=0;
	
	while($programacion=db_fetch_array($busca_programacion)){
	
		if ($programacion["id_objetivo"]!=$objetivo_actual) {
				?>
				<h5 class="text-warning"><?=$programacion["nombre_objetivo"];?></h5>
				<?php 
				$objetivo_actual=$programacion["id_objetivo"];
			}
	
	?>
	<h6 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h6>
	<?php 
		}
	}
	
	?>
	<button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <button type="button" onclick="gestion_programacion();" class="btn btn-info"><i class="fa fa-graduation-cap"></i> Gestionar programación</button> <button type="submit" class="btn btn-success">Guardar</button>
    </form>
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

</script>

</body>
</html>
