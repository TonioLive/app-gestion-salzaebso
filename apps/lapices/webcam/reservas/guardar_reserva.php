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
    <a type="button" class="btn btn-danger" href="/webcam/menu.php">Volver</a>
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

// Guarda información
	while($datos_hijos=db_fetch_array($busca_datos_alumno)) {

		$asistira = $_POST["asistira_".$datos_hijos["id_alumno"]];
		$desde_el_dia = $_POST["desde_el_dia_".$datos_hijos["id_alumno"]];

		$fechaDesde='';
		
		if (strlen($desde_el_dia)!=0) {
			
			$arrayFecha = explode("/",$desde_el_dia);
			
			$fechaDesde = $arrayFecha[2]."-".$arrayFecha[1]."-".$arrayFecha[0]." 00:00:00";

		}

		// Comprueba si está guardado ya
		$buscaReserva = db_query("SELECT * FROM alumnos_interes_escuelas WHERE id_padre=$id_usuario AND id_alumno=".$datos_hijos["id_alumno"]." AND id_servicio=$id_curso_actual");
		
		if (db_num_rows($buscaReserva)!=0) {
			// Lo Actualiza
			$actualiza=db_query("UPDATE alumnos_interes_escuelas SET fecha_registro='".date("Y-m-d H:i:s")."', asistira=".$asistira.", desde_fecha='".$fechaDesde."'");
		} else {
			// Lo crea
			$inserta=db_query("INSERT INTO alumnos_interes_escuelas (id_servicio,id_alumno,id_padre,asistira,desde_fecha) VALUES ('".$id_curso_actual."','".$datos_hijos["id_alumno"]."','".$id_usuario."','".$asistira."','".$fechaDesde."');");			
		}

	}

?>
Muchas gracias por responder.<br />
Esta información nos ayudará a planificar los centros.<br /><br />
Esperamos volver a vernos pronto por la esuela.<br /><br />
<button type="button" class="btn btn-danger" onclick="window.location.href='/webcam/menu.php';">Salir</button> <button type="button" class="btn btn-primary" onclick="window.location.href='/webcam/reservas/';">Volver al formulario y cambiar la información guardada</button>

<?php 
}
?>
</center></div>
</div>
    <?php 
    }
    }
    ?></div>    
<?php  require("../../configuracion/parte_inferior_padres.php"); ?>
