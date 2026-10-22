<?php  require("../../configuracion/config.php"); ?>
<?php  require("parte_superior_galeria.php");
    
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

    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

	if (db_num_rows($busca_datos_alumno)==1) {
		$tuHijo="tu hijo/a";
	}
	if (db_num_rows($busca_datos_alumno)>1) {
		$tuHijo="tus hijos/as";
	}

?>
El próximo 1 de junio volvemos a abrir el centro.<br />
Nos gustaría conocer si vas a traer a <?=$tuHijo;?> y así poder planificar las aulas.<br /><br />
Aquí tienes una tabla con los datos de <?=$tuHijo?>, lo único que debes hacer<br />
es marcar la casilla de la columna <b>"¿Asistirá?"</b> para indicar si vendrá a la escuela.<br />
Si piensas que no lo vas a traer desde el primer día pero quieres traerlo a partír<br />de una fecha determinada, indica la fecha en la columna <b>"Desde el día"</b>.<br /><br />
Una vez rellenados los datos, pulsa el botón <b>"Guardar"</b><br />
y te tendremos en cuenta para planificar el reinicio de las clases.<br /><br />
<form name="reservas" id="reservas" method="POST" action="guardar_reserva.php">
<table width=80% class="tabla_datos">
<?php 
	if (db_num_rows($busca_datos_alumno)!=0) {
		?>
		<tr>
			<td class="tabla_celda_encabezado">Curso</td>
			<td class="tabla_celda_encabezado">Escuela</td>
			<td class="tabla_celda_encabezado">Aula</td>
			<td class="tabla_celda_encabezado">Nombre</td>
			<td class="tabla_celda_encabezado">¿Asistirá?</td>
			<td class="tabla_celda_encabezado">Desde el día</td>
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
		
		// Busca datos guardados
		$buscaReserva = db_query("SELECT * FROM alumnos_interes_escuelas WHERE id_padre=$id_usuario AND id_alumno=".$datos_hijos["id_alumno"]." AND id_servicio=$id_curso_actual");
		
		$estadoChecbox="";
		$fechaDesde="";
		
		if (db_num_rows($buscaReserva)!=0) {
			$datosReserva=db_fetch_array($buscaReserva);
			if ($datosReserva['asistira']==1) {
				$estadoChecbox=" checked";
			}
			if ((strlen($datosReserva['desde_fecha'])!=0) and ($datosReserva['desde_fecha']!='0000-00-00 00:00:00')) {
				$fechaDesde=date("d/m/Y",strtotime($datosReserva['desde_fecha']));
			}
			
			$actualiza=db_query("UPDATE alumnos_interes_escuelas SET fecha_registro='".date("Y-m-d H:i:s")."', asistira=".$asistira.", desde_fecha='".$fechaDesde."'");
		}
		?>
		<tr valing="top">
			<td class="tabla_celda_datos" align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
			<td class="tabla_celda_datos" align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
			<td class="tabla_celda_datos" align="center"><font size=2><?=$datos_aula["nombre"];?></font></td>
			<td class="tabla_celda_datos" align="center"><font size=2><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></font></td>
			<td class="tabla_celda_datos" align="center"><input type="checkbox" id="asistira_<?=$datos_hijos["id_alumno"]?>" name="asistira_<?=$datos_hijos["id_alumno"]?>" value="1"<?=$estadoChecbox?>></td>
			<td class="tabla_celda_datos" align="center"><input type="text" id="desde_el_dia_<?=$datos_hijos["id_alumno"]?>" name="desde_el_dia_<?=$datos_hijos["id_alumno"]?>" value="<?=$fechaDesde?>" class="form-control"></td>
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
<button type="button" class="btn btn-danger" onclick="window.location.href='/webcam/menu.php';">Salir</button> <button type="button" class="btn btn-primary" onclick="$('#reservas').submit();">Guardar</submit>
<?php 
}
?>
</form>
</center></div>
</div>
  <script>
	  $( function() {
		$( '[id^="desde_el_dia_"]' ).datepicker({
		  dateFormat: "dd/mm/yy"
	  });
	  } );
  </script>
    <?php 
    }
    }
    ?></div>    
<?php  require("../../configuracion/parte_inferior_padres.php"); ?>
