<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");   
 
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

    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-09-01")."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

	if (db_num_rows($busca_datos_alumno)==1) {
		$tuHijo="tu hijo/a";
	}
	if (db_num_rows($busca_datos_alumno)>1) {
		$tuHijo="tus hijos/as";
	}

?>
<p class="text-center">Estos son los datos de tus hijos/as para poder completar la solicitud de Bono Infantil para el próximo curso.</p>
<table class="table table-bordered table-hover">
<?php 
	if (db_num_rows($busca_datos_alumno)!=0) {
		?>
		<tr>
			<td class="text-center">Curso</td>
			<td class="text-center">Nombre<br>del centro</td>
            <td class="text-center">Código<br>de centro</td>
			<td class="text-center">Nombre</td>	
            <td class="text-center">Código de Matrícula<br>(ID Solicitud)</td>					
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
				
		// Datos de la escuela del niño
		$busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

		$datos_escuela=db_fetch_array($busca_datos_escuela);

		// Datos del aula del niño
		$busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

		$datos_aula=db_fetch_array($busca_datos_aula);				
		
		?>
		<tr valing="top">
			<td class="text-center" style="font-family: Helvetica, sans-serif!important;"><?=$nombre_curso_mostrado;?></td>
			<td class="text-center" style="font-family: Helvetica, sans-serif!important;"><?=$datos_escuela["nombre_completo"];?></td>
			<td class="text-center" style="font-family: Helvetica, sans-serif!important;"><?=$datos_escuela["codigo_centro"];?></td>            
			<td class="text-center" style="font-family: Helvetica, sans-serif!important;"><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></td>
			<td class="text-center" style="font-family: Helvetica, sans-serif!important;"><?=$datos_hijos["codigo_inscripcion"];?></td>            
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
