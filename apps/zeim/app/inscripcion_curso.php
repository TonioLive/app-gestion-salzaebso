<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}

// $estadoBotonesInscripcionVerano=' disabled="disabled"';
// $estadoBotonesInscripcionOrdinario=' disabled="disabled"';
$estadoBotonesInscripcionVerano='';
$estadoBotonesInscripcionOrdinario='';
$nombreCursoNormal = "2026-2027";
$nombreCursoVerano = "2026";
$curso_seleccionado=44;
$curso_seleccionado_verano=45;

?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inscripción próximo curso 
        <small>Selección de alumnos</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
        <li><i class="fa fa-money"></i> Inscripción próximo curso</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	  <?php
      // Calcula curso actual
$id_curso_actual=$_GET["id_curso"];

if (strlen($id_curso_actual)==0) {

	$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

	if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_actual=$datos_curso_actual["id_curso"];
	} else {
		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

		if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso_actual=$datos_curso_actual["id_curso"];
		} else {
			$id_curso_actual=0;
		}
	}
}

if ($id_curso_actual==0) {
	$errores="- El curso todavía no ha comenzado -";
}
                                        
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <input type="button" onclick="window.location.href='/webcam/menu.php'" value="Volver">
        </div>
    <?php
    } else {
?>
    <div class="box box-primary">
        <div class="box-header with-border">	  
<?php
        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", time() - 86400 * 30)))) or die(db_error()."- No se encuentran los datos del alumno.");

	if (db_num_rows($busca_datos_alumno)==1) {
		$tuHijo="tu hijo/a";
	}
	if (db_num_rows($busca_datos_alumno)>1) {
		$tuHijo="tus hijos/as";
	}

?>
Para rellenar la inscripción del próximo curso, haz clic en el botón "Rellenar inscripción" en cada uno de tus hijos/as.<br /><br />
<div class="container">
<?php
	
	while($datos_hijos=db_fetch_array($busca_datos_alumno)) {
		$busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
		if (db_num_rows($busca_datos_cursos)!=0) {
				$datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
				$nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
				} else {
				$nombre_curso_mostrado="";
				}
				
		// Datos de la escuela del ni?o
		$busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));

		$datos_escuela=db_fetch_array($busca_datos_escuela);

		// Datos del aula del ni?o
		$busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

		$datos_aula=db_fetch_array($busca_datos_aula);
		
		// Comprueba si se ha rellenado curso normal
		$busca_inscripcion=db_query_params('SELECT * FROM contactos_web WHERE id_usuario_alumno=? AND id_curso=?', 'ss', array($datos_hijos["padre_de"], $curso_seleccionado));
		
		if (db_num_rows($busca_inscripcion)!=0) {
			$datosContacto=db_fetch_array($busca_inscripcion);
			// $boton_formulario='<a href="/formulario/contacto.php?idc='.$datos_hijos["id_centro"].'&id_contacto='.$datosContacto["id_contacto"].'&id_curso='.$curso_seleccionado.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionOrdinario.'><font color="#fff">Editar Inscripción<br>Curso '.$nombreCursoNormal.'</font></a>';
			$boton_formulario='<span class="btn btn-danger disabled"><font color="#fff">Ya has rellenado la Inscripción para el<br>Curso '.$nombreCursoNormal.'</font></span>';
		} else {
			$boton_formulario='<a href="/formulario/contacto.php?idc='.$datos_hijos["id_centro"].'&id_curso='.$curso_seleccionado.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionOrdinario.'><font color="#fff">Rellenar Inscripción de '.$datos_hijos["Nombre"].'<br>Curso '.$nombreCursoNormal.'</font></a>';
		}
		
		// Comprueba si se ha rellenado escuela de verano
		$busca_inscripcion_verano=db_query("SELECT * FROM contactos_web WHERE id_usuario_alumno=".$datos_hijos["padre_de"]." AND id_curso=$curso_seleccionado_verano");
		
		if (db_num_rows($busca_inscripcion_verano)!=0) {
			$datosContactoVerano=db_fetch_array($busca_inscripcion_verano);
			$boton_formulario_verano='<a href="/formulario/contacto_ev.php?idc='.$datos_hijos["id_centro"].'&id_contacto='.$datosContactoVerano["id_contacto"].'&id_curso='.$curso_seleccionado_verano.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionVerano.'><font color="#fff">Editar Inscripción<br>Verano '.$nombreCursoVerano.'</font></a>';
		} else {
			$boton_formulario_verano='<a href="/formulario/contacto_ev.php?idc='.$datos_hijos["id_centro"].'&id_curso='.$curso_seleccionado_verano.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionVerano.'><font color="#fff">Rellenar Inscripción de '.$datos_hijos["Nombre"].'<br>Verano '.$nombreCursoVerano.'</font></a>';
		}
		
		?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <h3 class="panel-title"><b><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></b></h3>                          
                    </div>
                    <div class="panel-body">
                    <b>Curso actual:</b> <?=$nombre_curso_mostrado;?><br />
                    <b>Escuela: </b><?=$datos_escuela["nombre_centro"];?><br />
                    <b>Aula: </b><?=security_escape($datos_aula["nombre"]);?><br />                    
                    <?=$boton_formulario;?><br />
                    <!--<?=$boton_formulario_verano;?>-->                    
                    </div>
                </div>         
		<?php
	}

	if (db_num_rows($busca_datos_alumno)==0) {
		?>
		<div class="row">
			<div class="col-md-12 text-center"><font size=3><b>No hay alumnos en el curso actual.</b></font></td>
		</div>
		<?php
	}
?>
</div>
<br />
<button type="button" class="btn btn-danger" onclick="window.location.href='/app/';">Salir</button>
<?php
}
?>
    </div>
</div>
    <?php
    }
    ?>
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>