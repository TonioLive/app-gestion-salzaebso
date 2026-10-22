<?php require("../../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php
$id_centro=$centro_actual;


// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

	if (db_num_rows($busca_curso_actual)!=0) {
		
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        
    } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

        if (db_num_rows($busca_curso_actual)!=0) {
        	$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso_actual=$datos_curso_actual["id_curso"];
			$nombre_curso_actual=$datos_curso_actual["nombre"];
        } else {
        	$id_curso_actual=0;
        	$nombre_curso_actual=" todos los cursos";
        }
    }
} else {
	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
	$id_curso_actual=$curso_seleccionado;
}
if ($id_curso_actual!=0) {
	$filtro=" AND alumnos.id_curso=$id_curso_actual";
} else {
	$filtro="";
}

$consulta_datos_alergias=db_query("SELECT *, centro_aulas.nombre AS nombre_aula FROM usuarios, alumnos LEFT JOIN centro_aulas ON alumnos.aula=centro_aulas.id_aula WHERE usuarios.id_usuario=alumnos.id_usuario AND (alergias_lacteos=1 OR alergias_huevo=1 OR alergias_proternera=1 OR alergias_frutossecos=1 OR alergias_marisco=1 OR alergias_pielmelocoton=1 OR alergias_gluten=1 OR alergias_pescado=1 OR alergias_soja=1 OR alergias_sulfitos=1 OR alergias<>'' OR observaciones<>'')".$filtro." ORDER BY centro_aulas.nombre");

$nombres_alergias=array(
    'alergias_lacteos' => 'Leche y sus derivados',
    'alergias_huevo' => 'Huevo',
    'alergias_frutossecos' => 'Frutos de cáscara',
    'alergias_marisco' => 'Marisco',
    'alergias_gluten' => 'Gluten',               
    'alergias_pescado' => 'Pescado',                                
    'alergias_soja' => 'Soja',
    'alergias_sulfitos' => 'Sulfitos',
    'alergias_cacahuetes' => 'Frutos secos sin cáscara',
    'alergias_altramuces' => 'Altramuces',
    'alergias_apio' => 'Apio',
    'alergias_mostaza' => 'Mostaza',
    'alergias_sesamo' => 'Sésamo',
    'alergias_crustaceos' => 'Crustaceos',
    'alergias_moluscos' => 'Moluscos',
    'alergias_proternera' => 'Intolerancia a la Proteina de Ternera',
    'alergias_pielmelocoton' => 'Intolerancia a la Piel de Melocotón',
    'alergias_cerdo' => 'Intolerancia al Cerdo',
    'alergias_berenjena' => 'Intolerancia a la Berenjena',
    );


?>
<div class="text-center">
<h3>Curso <?=$nombre_curso_actual;?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></h3>
<table class="table table-bordered table-striped table-hover">
	<tr>
        <td colspan=7 class="text-center"><h3>Listado de alumnos con alergias o problemas médicos</h3></td>
	</tr>
	<tr>
		<td><small><b>Aula</b></small></td>
        <td><small><b>Nombre</b></small></td>
        <td><small><b>Contactos</b></small></td>
        <td><small><b>Contacto médico</b></small></td>
        <td><small><b>Observaciones</b></small></td>
        <td colspan=2><small><b>Alergias</b></small></td>        
	</tr>
    <?php
    while($datos_alergias=db_fetch_array($consulta_datos_alergias)) {
	    
	$array_alegias_alumno = array();
	
	foreach ($nombres_alergias as $clave => $valor) {
	
		if ($datos_alergias[$clave]==1) {
			$array_alegias_alumno[]=$valor;
		}
		
	}    
	
	$listado_alegias_alumno = implode(",",$array_alegias_alumno);
	    
	?> 
	<tr>
		<td><small><?=$datos_alergias["nombre_aula"];?></small></td>
        <td><small><a href="/alumnos/nuevo_alumno4.php?u=<?=$datos_alergias["id_usuario"]?>" target="_blank"><?=$datos_alergias["Nombre"]." ".$datos_alergias["Apellido1"]." ".$datos_alergias["Apellido2"];?></a></small></td>
        <td>
	        <small>
	        <?=$datos_alergias["contacto1"];?> <?=$datos_alergias["telefono_contacto1"];?><br />
			<?=$datos_alergias["contacto2"];?> <?=$datos_alergias["telefono_contacto2"];?>
			</small>
		</td>
        <td><small><?=$datos_alergias["contacto_medico"];?><br /><?=$datos_alergias["telefono_medico"];?></small></td>
        <td><small><?=$datos_alergias["observaciones"];?></small></td>
        <td><small><?=$datos_alergias["alergias"];?></small></td>
        <td><small><?=$listado_alegias_alumno;?></small></td>     
	</tr>	   
	<?php         
    }    
	?>
	</table>
</div>
<?php

require("../../configuracion/parte_inferior.php"); ?>
