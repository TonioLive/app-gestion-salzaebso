<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");   

   // $estadoBotonesInscripcionVerano=' disabled="disabled"';
   // $estadoBotonesInscripcionOrdinario=' disabled="disabled"';
   $estadoBotonesInscripcionVerano='';
   $estadoBotonesInscripcionOrdinario='';
   $nombreCursoNormal = "2026-2027";
   $nombreCursoVerano = "2026";
   $curso_seleccionado=44;
   $curso_seleccionado_verano=45;

   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";

// Calcula curso actual
$id_curso_actual=$_GET["id_curso"];

if (strlen($id_curso_actual)==0) {

	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

	if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_actual=$datos_curso_actual["id_curso"];
	} else {
		$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

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
<div align="center"><center>
<?php 
        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",time()-(86400*30))."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

	if (db_num_rows($busca_datos_alumno)==1) {
		$tuHijo="tu hijo/a";
	}
	if (db_num_rows($busca_datos_alumno)>1) {
		$tuHijo="tus hijos/as";
	}

?>
Para rellenar la inscripción del próximo curso, haz clic en el botón "Rellenar inscripción" en cada uno de tus hijos/as.<br /><br />
<table class="table table-bordered table-hover">
<?php 
	if (db_num_rows($busca_datos_alumno)!=0) {
		?>
		<tr>
			<td>Curso actual</td>
			<td>Escuela</td>
			<td>Aula</td>
			<td>Nombre</td>			
			<td class="text-center">Escuela de Verano <?=$nombreCursoVerano?></td>
			<td class="text-center">Curso <?=$nombreCursoNormal?></td>			
        </tr>
		<?php 
	}

	
	while($datos_hijos=db_fetch_array($busca_datos_alumno)) {
		$busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
		if (db_num_rows($busca_datos_cursos)!=0) {
				$datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
				$nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
				} else {
				$nombre_curso_mostrado="";
				}
				
		// Datos de la escuela del ni?o
		$busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

		$datos_escuela=db_fetch_array($busca_datos_escuela);

		// Datos del aula del ni?o
		$busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

		$datos_aula=db_fetch_array($busca_datos_aula);
		
		// Comprueba si se ha rellenado curso normal
		$busca_inscripcion=db_query("SELECT * FROM contactos_web WHERE id_usuario_alumno=".$datos_hijos["padre_de"]." AND id_curso=$curso_seleccionado");
		
		if (db_num_rows($busca_inscripcion)!=0) {
			$datosContacto=db_fetch_array($busca_inscripcion);
			// $boton_formulario='<a href="/formulario/contacto.php?idc='.$datos_hijos["id_centro"].'&id_contacto='.$datosContacto["id_contacto"].'&id_curso='.$curso_seleccionado.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionOrdinario.'><font color="#fff">Editar Inscripción<br>Curso '.$nombreCursoNormal.'</font></a>';
			$boton_formulario='<span class="btn btn-danger disabled"><font color="#fff">Ya has rellenado la Inscripción para el<br>Curso '.$nombreCursoNormal.'</font></span>';

		} else {
			$boton_formulario='<a href="/formulario/contacto.php?idc='.$datos_hijos["id_centro"].'&id_curso='.$curso_seleccionado.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionOrdinario.'><font color="#fff">Rellenar Inscripción<br>Curso '.$nombreCursoNormal.'</font></a>';
		}
		
		// Comprueba si se ha rellenado escuela de verano
		$busca_inscripcion_verano=db_query("SELECT * FROM contactos_web WHERE id_usuario_alumno=".$datos_hijos["padre_de"]." AND id_curso=$curso_seleccionado_verano");
		
		if (db_num_rows($busca_inscripcion_verano)!=0) {
			$datosContactoVerano=db_fetch_array($busca_inscripcion_verano);
			$boton_formulario_verano='<a href="/formulario/contacto_ev.php?idc='.$datos_hijos["id_centro"].'&id_contacto='.$datosContactoVerano["id_contacto"].'&id_curso='.$curso_seleccionado_verano.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionVerano.'><font color="#fff">Editar Inscripción<br>Verano '.$nombreCursoVerano.'</font></a>';
		} else {
			$boton_formulario_verano='<a href="/formulario/contacto_ev.php?idc='.$datos_hijos["id_centro"].'&id_curso='.$curso_seleccionado_verano.'&id_alumno='.$datos_hijos["padre_de"].'" class="btn btn-primary"'.$estadoBotonesInscripcionVerano.'><font color="#fff">Rellenar Inscripción<br>Verano '.$nombreCursoVerano.'</font></a>';
		}
		
		if ($datos_escuela["escuela_verano"]==0) {
			$boton_formulario_verano='-';
		}

		?>
		<tr valing="top">
			<td class="text-center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
			<td class="text-center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
			<td class="text-center"><font size=2><?=$datos_aula["nombre"];?></font></td>
			<td class="text-center"><font size=2><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></font></td>
			<td class="text-center"><?=$boton_formulario_verano;?></td>
			<td class="text-center"><?=$boton_formulario;?></td>
		</tr>
		<?php 
	}

	if (db_num_rows($busca_datos_alumno)==0) {
		?>
		<tr>
			<td colspan=<?=$columnas?> align="center"><font size=3><b>No hay alumnos en el curso actual.</b></font></td>
		</tr>
		<?php 
	}
?>
</table>
<br />
<button type="button" class="btn btn-danger" onclick="window.location.href='/webcam/menu.php';">Salir</button>
<?php 
}
?>
</center></div>
</div>
    <?php 
    }
    }
    ?></div>    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
