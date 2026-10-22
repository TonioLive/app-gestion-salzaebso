<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];
$id_alumno=$_GET["id_alumno"];

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

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

// Busca nuevo curso
$busca_nuevo_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso<>? AND fecha_inicio>=?', 'ss', array($id_curso_actual, (string)(date("Y", strtotime($fecha_fin_curso_actual))) . '-08-31'));

if (db_num_rows($busca_nuevo_curso)!=0) {

		$datos_nuevo_curso=db_fetch_array($busca_nuevo_curso);
        $nombre_curso_siguente=$datos_nuevo_curso["nombre"];
        $id_nuevo_curso=$datos_nuevo_curso["id_curso"];

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
<form method="POST" name="detalles_grupo" action="copiar_grupo1.php">
<input type="hidden" name="id_grupo" value="<?=$_GET["id_grupo"];?>">
<input type="hidden" name="id_subgrupo" value="<?=$_GET["id_subgrupo"];?>">
<input type="hidden" name="fecha_limite" value="<?=$_GET["fecha_limite"];?>">
<input type="hidden" name="id_nuevo_curso" value="<?=$id_nuevo_curso;?>">
<input type="hidden" name="id_alumno" value="<?=$id_alumno;?>">
<?php 
// Busca alumnos subgrupo
$filtro_alumno="";        
if ($id_alumno) {
	$filtro_alumno=" AND alumnos.id_alumno=$id_alumno";
	}
        
$busca_alumnos_grupo=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno".$filtro_alumno." AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_subgrupo=$id_subgrupo AND aulas_alumnos_grupos.id_grupo=".$id_grupo." AND alumnos.fecha_baja>='".$fecha_limite."') OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_subgrupo=$id_subgrupo AND aulas_alumnos_grupos.id_grupo=".$id_grupo." AND alumnos.fecha_baja>='".$fecha_limite."'))") or die(db_error());
        
$contador_alumnos=db_num_rows($busca_alumnos_grupo);
              
?>
<div class="container-fluid">
	<div class="panel panel-default">
  		<div class="panel-body">
			<h2>Copiar grupo al nuevo curso</h2>
			<p class="lead">
			Se van a copiar los <?=$contador_alumnos?> alumnos del grupo <i>"<?=$nombre_subgrupo?>"</i> del curso <b><?=$nombre_curso_actual?></b> al grupo...<br /><br />
			<select name="id_grupo_nuevo" class="form-control">
    							<?php 
                                $busca_grupos_similares=db_query_params('SELECT * FROM aulas_grupos WHERE id_curso=? ORDER BY dia, hora_inicio;', 's', array($id_nuevo_curso));
                                
                                if (db_num_rows($busca_grupos_similares)!=0) {
                                ?>
                                <option value="">Selecciona el grupo donde se copiarán.</option>
                                <?php 
                                } else {
                                ?>
                                <option value="">No se han creado grupos en el día seleccionado.</option>
                                <?php 
                                }
                                
                                $dia_actual=0;
                                while($datos_grupos=db_fetch_array($busca_grupos_similares)) {                                
									
									if ($dia_actual!=$datos_grupos["dia"]) {
										$dia_actual=$datos_grupos["dia"];
										if ($dia_actual!=0) {
										?>
										</optgroup>
										<?php 
										}
									?>
									<optgroup label="<?=$dia_sem_largo[$datos_grupos["dia"]];?>">
									<?php 
									}
									
									$nombre_horario_n=$dia_sem_largo[$datos_grupos["dia"]]." de ".date("H:i",strtotime($datos_grupos["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupos["hora_fin"]));   
                                
                                	$num_subgrupos=$datos_grupos["num_subgrupos"];
         
         							for ($c_subgrupos=0;$c_subgrupos<$num_subgrupos;$c_subgrupos++) {
                                
                                		$nombre_subgrupo_n=", Grupo: ".($c_subgrupos+1);                                 
                                
		                                ?>
        		                        <option value="<?=$datos_grupos["id_grupo"];?>_<?=$c_subgrupos?>"<?php  if (($datos_grupos["hora_inicio"]==$hora_inicio_grupo) and ($c_subgrupos==$id_subgrupo) and ($datos_grupos["dia"]==$dia_grupo)) { print " selected class='text-danger'"; } ?>><?=$nombre_horario_n.$nombre_subgrupo_n;?></option>
                		                <?php 
                		                
                                		}                		                
                                }
                                ?>
                                </optgroup>
			</select>
			<br />
			...del curso <b><?=$nombre_curso_siguente?></b>.
			<br />
			<br />
			</p>
			<button class="btn btn-success" type="submit">Confirmar</button>
			</div>			    
  		</div>
	</div>      
</div>
</form>
</body>
</html>
