<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	header("Location: login.php");	
	exit;
} else {
	require("parte_superior_padres.php");
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>&nbsp;</h1>
	  <ol class="breadcrumb">
		<li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
		<li><i class="fa fa-cutlery"></i> Comedor</li>
		<li class="active"><i class="fa fa-cutlery"></i> Resumen comida <?=date("d/m/Y")?></li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
    
if ($errores=="") {
?>
	<div class="row">
	  <div class="col-xs-12">

<?php
	// ============================================================
	// Variables
	// ============================================================
	$dia=strtotime(date("Y-m-d"));
	$fecha_time_stamp=$dia;

	$cantidades_comida=array("Todo","Mitad","Poco","Nada");
	$resultados_comida=array("Bien","Regular","Mal");
	$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

	// Alergias y Alimentos no introducidos
	$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_frutossecos","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos","alergias_cacahuetes","alergias_altramuces","alergias_apio","alergias_mostaza","alergias_sesamo","alergias_crustaceos","alergias_moluscos","alergias_proternera","alergias_pielmelocoton","alergias_cerdo","alergias_berenjena");
	$array_nombres_alergias=array("Leche y sus derivados","Huevo","Frutos de cáscara","Gluten","Pescado","Soja","Sulfitos","Frutos secos sin cáscara","Altramuces","Apio","Mostaza","Sésamo","Crustaceos","Moluscos","Intolerancia a la Proteina Ternera","Intolerancia a la Piel Melocotón","Intolerancia al  Cerdo","Intolerancia al la berenjena");
	$nombres_alergias=array("leche y sus derivados","huevo","frutos de cáscara","gluten","pescado","soja","sulfitos","frutos secos sin cáscara","altramuces","apio","mostaza","sésamo","crustaceos","moluscos","intolerancia a la proteina ternera","intolerancia a la piel melocotón","intolerancia al Cerdo","Intolerancia al la Berenjena");

	$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lentejas","alimentos_lacteos");
	$array_nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");
	$nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");

	// ============================================================
	// MOSTRADO ESCALADO POR HORA (nuevo)
	// ============================================================
	$horaHHMM = (int)date("Hi"); // 0000..2359

	$ANTES_12   = ($horaHHMM < 1200);
	$ENTRE_12_15 = ($horaHHMM >= 1200 && $horaHHMM < 1500);
	$ENTRE_15_17 = ($horaHHMM >= 1500 && $horaHHMM < 1700);
	$DESDE_17   = ($horaHHMM >= 1700);

	$MOSTRAR_ALMUERZO = ($horaHHMM >= 1200); // 12:00 en adelante
	$MOSTRAR_COMIDA   = ($horaHHMM >= 1500); // 15:00 en adelante (comida + postre)
	$MOSTRAR_MERIENDA = ($horaHHMM >= 1700); // 17:00 en adelante
	$MOSTRAR_NOTAS    = ($horaHHMM >= 1200); // notas desde 12:00

	// ============================================================
	// Busca hijos del padre
	// OJO: tu SELECT original tenía un "usuarios.id_usuario AS id_usuario_alumno" mal puesto.
	// Lo dejo equivalente y funcional:
	// ============================================================
	$busca_datos_alumno=db_query_params('
		SELECT usuarios.*, alumnos.*, padres.*,
			   usuarios.id_usuario AS id_usuario_alumno
		FROM usuarios, alumnos, padres
		WHERE usuarios.id_usuario=alumnos.id_usuario
		  AND alumnos.id_usuario=padres.padre_de
		  AND padres.id_usuario=?
		  AND alumnos.fecha_baja>=?
		ORDER BY alumnos.id_curso DESC
	', 'ss', array((int) $id_usuario, (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

	if (db_num_rows($busca_datos_alumno)!=0) {

		$contador_hijos=0;

		while($datos_alumno=db_fetch_array($busca_datos_alumno)){

			$busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array((int) $datos_alumno["id_curso"]));
			if (db_num_rows($busca_datos_cursos)!=0) {
				$datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
				$nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
			} else {
				$nombre_curso_mostrado="";
			}

			// Datos de la escuela del niño
			$busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array((int) $datos_alumno["id_centro"]));
			$datos_escuela=db_fetch_array($busca_datos_escuela);

			// Datos del aula
			$busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array((int) $datos_alumno["aula"]));
			$datos_aula=db_fetch_array($busca_datos_aula);

			$id_aula      = (int)$datos_alumno["aula"];
			$id_alumno    = (int)$datos_alumno["id_alumno"];        // inscripción (curso académico)
			$a            = (int)$datos_alumno["id_usuario_alumno"]; // id_usuario (persona/alumno)  <<< IMPORTANTE
			$no_merienda  = (int)$datos_alumno["no_merienda"];

			// Asistencia
			$ficha_tarde=0;
			$datos_asistencia=array(); // para evitar undefined

			$consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$a AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY turno;");

			while($tmp_asistencia=db_fetch_array($consulta_asistencia)) {
				$datos_asistencia=$tmp_asistencia; // guardamos la última fila
				if ($tmp_asistencia["turno"]==0) {
					// mañana
				} else {
					// tarde
					$ficha_tarde=1;
				}
			}

			// ============================================================
			// PANEL (Bootstrap 3) POR ALUMNO
			// ============================================================
			$nombre_nino = "";
			if (isset($datos_alumno["Nombre"])) $nombre_nino = $datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"].", ".$datos_alumno["Nombre"];
			if ($nombre_nino=="") $nombre_nino = "Alumno";
			?>
			<div class="col-md-6">
			  <div class="panel panel-info">
				<div class="panel-heading">
				  <h3 class="panel-title">
					<i class="fa fa-child"></i> <?php echo htmlspecialchars($nombre_nino); ?>
					<?php if ($nombre_curso_mostrado!="") { ?>
					  <small>— <?php echo htmlspecialchars($nombre_curso_mostrado); ?></small>
					<?php } ?>
				  </h3>
				  <br />
				<a class="btn btn-primary btn-sm" href="ver_menu.php?id_curso=<?=$datos_alumno["id_curso"]?>&id_hijo=<?=$datos_alumno["id_alumno"]?>">Ver menú</a>

				</div>
				<div class="panel-body">	
				<lable>Resumen comida de hoy - <?=date("d/m/Y")?></lable>
			<?php
			// ============================================================
			// Antes de las 12:00 -> no se muestra nada
			// ============================================================
			if ($ANTES_12) {
				?>
				<div class="alert alert-info" style="margin-bottom:0;">
				  La información del comedor estará disponible a partir de las <b>12:00</b>.
				</div>
				<?php
			} else {

				// ============================================================
				// DESDE AQUÍ: TU CÓDIGO COMPLETO (con la corrección $a <> $id_alumno)
				// y con el escalado por hora
				// ============================================================

				// Inicializaciones
				$descripcion_comidas="";
				$descripcion_almuerzo="";
				$texto_observaciones_linea="";
				$numero_filas_comidas=0;
				$numero_filas_almuerzo=0;
				$numero_filas_comida=0;
				$numero_filas_merienda=0;

				$hay_observaciones=false;
				$hay_comida=true;

				$minimo_minimorum=1000;

				$alimentos_ya_marcados=array();
				$alergias_ya_marcadas=array();
				$platos_no_introducidos=array();
				$platos_alaergias=array();

				// ------------------------------------------------------------
				// Busca registro de comida (POR INSCRIPCIÓN: $id_alumno)
				// ------------------------------------------------------------
				$busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());

				// Para evitar undefined
				$datos_registro_comida=array("id"=>0);

				if (db_num_rows($busca_registro_comida)!=0) {
					$datos_registro_comida=db_fetch_array($busca_registro_comida);
					$siesta1=$datos_registro_comida["siesta1"];
					$siesta2=$datos_registro_comida["siesta2"];
				}

				// ------------------------------------------------------------
				// Busca asistencia a menu especial
				// >>> CORRECCIÓN CLAVE: AQUÍ VA $a (id_usuario), NO $id_alumno
				// ------------------------------------------------------------
				$busca_actividad=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$a." AND (servicios_consumidos_asistencia.reserva=1 OR servicios_consumidos_asistencia.no_asiste=1)AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$dia)."'") or die(db_error());

				$menu_especial_hoy=false;
				if (db_num_rows($busca_actividad)!=0) {
					$menu_especial_hoy=true;
				}

				if (($datos_asistencia["hora_entrada_c"]!="") or ($menu_especial_hoy==true) or (db_num_rows($busca_registro_comida)!=0)){

					$merienda_de_comedor=true;

					// Busca comidas y resultado
					$descripcion_comidas="";
					$numero_filas_comidas=0;
					$numero_filas_almuerzo=0;
					$numero_filas_comida=0;
					$numero_filas_merienda=0;

					$id_registro_comida=$datos_registro_comida["id"];

					if ($id_registro_comida!=0) {

						if ($menu_especial_hoy==true) {
							// Comprueba si tiene un menú asignado en la ficha y busca por edad similar al menú asignado

							// Busca menu especial
							if  ($datos_alumno["menu_actual"]!=0) {

								$busca_menu_preseleccionado=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=".$datos_alumno["menu_actual"]);
								if (db_num_rows($busca_menu_preseleccionado)!=0) {

									$datos_menu_preseleccionada=db_fetch_array($busca_menu_preseleccionado);
									$edad_alumno=$datos_menu_preseleccionada["edad_ini_menu"];

								}

							} else {

								// Busca por edad
								$edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
								if (date("m",$fecha_time_stamp)<date("m",strtotime($datos_alumno["fecha_nacimiento"]))) {
									$edad_alumno=$edad_alumno-1;
								}
								$edad_alumno_anos=$edad_alumno;
								// Decimales
								$mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($datos_alumno["fecha_nacimiento"]));

								if ($mes_edad_alumno<0) {
									$edad_alumno_meses=(12+$mes_edad_alumno);
									$edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
								} else {
									$edad_alumno_meses=$mes_edad_alumno;
									$edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
								}

							}

							$filtro_regimen=" AND menu_menus.regimen=2";

							$busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$dia)."' AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
							$diferencia_actual=1000;
							$id_menu_seleccionado=0;

							while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
								if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
									$minimo_minimorum=$menu_apropiado["edad_ini_menu"];
								}
								$diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
								if ($diferencia<=$diferencia_actual) {
									$diferencia_actual=$diferencia;
									$id_menu_seleccionado=$menu_apropiado["id_menu"];
								}
							}

							$id_menu=$id_menu_seleccionado;
						} else {
							$id_menu=$datos_asistencia["menu_seleccionado_hoy"];
						}

						// Busca menu para hoy
						$busca_platos_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

						if (db_num_rows($busca_platos_dia)!=0) {

							$datos_platos_dia=db_fetch_array($busca_platos_dia);

							for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Bucle platos

								$procesar_plato=true;

								// NO procesar merienda si no_merienda
								if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($no_merienda==1)) {
									$procesar_plato=false;
								}

								// Si NO toca mostrar merienda todavía, evitamos incluso procesarla para el HTML
								if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($MOSTRAR_MERIENDA!=true)) {
									// pero dejamos alergias/ingredientes calculados si te interesa: si quieres que NO se calcule, pon false aquí.
									$procesar_plato=false;
								}

								if ($procesar_plato==true) { // Inicio Procesar plato

									$codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];

									if ($codigo_plato!=0) {

										$consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

										if (db_num_rows($consulta_datos_plato)!=0) {
											$datos_plato=db_fetch_array($consulta_datos_plato);

											// Comprueba platos
											$busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

											while($ingredientes=db_fetch_array($busca_ingredientes)) {
												for ($c=0;$c<count($array_alimentos);$c++) {
													if (($ingredientes[$array_alimentos[$c]]==1) and ($datos_alumno[$array_alimentos[$c]]==0)) {
														$hay_comida=false;
														if ($alimentos_ya_marcados[$codigo_plato][$id_alumno]!=1) {
															$platos_no_introducidos[$c][$id_alumno]=$platos_no_introducidos[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
															$alimentos_ya_marcados[$codigo_plato][$id_alumno]=1;
														}
														$hay_observaciones=true;
													}
												}

												for ($c=0;$c<count($array_alergias);$c++) {
													if (($ingredientes[$array_alergias[$c]]==1) and ($datos_alumno[$array_alergias[$c]]==1)) {
														$nombre_alergia=$nombres_alergias[$c];
														$hay_comida=false;
														if ($alergias_ya_marcadas[$codigo_plato][$id_alumno]!=1) {
															$platos_alaergias[$c][$id_alumno]=$platos_alaergias[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
															$alergias_ya_marcadas[$codigo_plato][$id_alumno]=1;
														}
														$hay_observaciones=true;
													}
												}
											}

										}

									}
								} // Fin procesar platos
							} // Fin bucle platos

							if ($hay_observaciones==true) {
								$numero_filas_comidas++;
							}

							$id_dia_menu=$datos_platos_dia["id_dia"];
							$comida1=$datos_platos_dia["comida1"];
							$comida2=$datos_platos_dia["comida2"];
							$comida3=$datos_platos_dia["comida3"];
							$comida4=$datos_platos_dia["comida4"];
							$comida5=$datos_platos_dia["comida5"];
							$postre1=$datos_platos_dia["postre1"];
							$merienda1=$datos_platos_dia["merienda1"];
							$merienda2=$datos_platos_dia["merienda2"];

							$color_alerta="#FF0000";

							// ============================================================
							// ESCALADO: COMIDA+POSTRE SOLO DESDE 15:00
							// ============================================================
							if ($MOSTRAR_COMIDA==true) {

								$descripcion_comidas.="<td rowspan=[filas_comida] class='tabla_comida'><b>COMIDA</b></td>";

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$comida1][$id_alumno]==1) or ($alimentos_ya_marcados[$comida1][$id_alumno]==1)) {
										$color_fuente_linea=$color_alerta;
									}
									$descripcion_comidas.="<td class='tabla_comida'><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_comida1=$resultados_comida[$datos_registro_comida["comida1"]];
									if ($datos_registro_comida["cant_comida1"]==3) { $resultado_comida1=""; }
									$descripcion_comidas.="</td><td class='tabla_comida'>".$cantidades_comida[$datos_registro_comida["cant_comida1"]]."</td><td class='tabla_comida'>".$resultado_comida1."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$comida2][$id_alumno]==1) or ($alimentos_ya_marcados[$comida2][$id_alumno]==1)) { $color_fuente_linea=$color_alerta; }
									$descripcion_comidas.="<tr class='tabla_comida'><td><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_comida2=$resultados_comida[$datos_registro_comida["comida2"]];
									if ($datos_registro_comida["cant_comida2"]==3) { $resultado_comida2=""; }
									$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida2"]]."</td><td>".$resultado_comida2."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$comida3][$id_alumno]==1) or ($alimentos_ya_marcados[$comida3][$id_alumno]==1)) { $color_fuente_linea=$color_alerta; }
									$descripcion_comidas.="<tr class='tabla_comida'><td><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_comida3=$resultados_comida[$datos_registro_comida["comida3"]];
									if ($datos_registro_comida["cant_comida3"]==3) { $resultado_comida3=""; }
									$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida3"]]."</td><td>".$resultado_comida3."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$comida4][$id_alumno]==1) or ($alimentos_ya_marcados[$comida4][$id_alumno]==1)) { $color_fuente_linea=$color_alerta; }
									$descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_comida4=$resultados_comida[$datos_registro_comida["comida4"]];
									if ($datos_registro_comida["cant_comida4"]==3) { $resultado_comida4=""; }
									$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida4"]]."</td><td>".$resultado_comida4."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$comida5][$id_alumno]==1) or ($alimentos_ya_marcados[$comida5][$id_alumno]==1)) { $color_fuente_linea=$color_alerta; }
									$descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_comida5=$resultados_comida[$datos_registro_comida["comida5"]];
									if ($datos_registro_comida["cant_comida5"]==3) { $resultado_comida5=""; }
									$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida5"]]."</td><td>".$resultado_comida5."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}

								$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
								if (db_num_rows($busca_comida)!=0) {
									$datos_comida=db_fetch_array($busca_comida);
									$color_fuente_linea="#000000";
									if (($alergias_ya_marcadas[$postre1][$id_alumno]==1) or ($alimentos_ya_marcados[$postre1][$id_alumno]==1)) { $color_fuente_linea=$color_alerta; }
									$descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
									$resultado_postre1=$resultados_comida[$datos_registro_comida["postre1"]];
									if ($datos_registro_comida["cant_postre1"]==3) { $resultado_postre1=""; }
									$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_postre1"]]."</td><td>".$resultado_postre1."</td></tr>";
									$numero_filas_comida++;
									$numero_filas_comidas++;
								}
							}

							// ============================================================
							// ESCALADO: MERIENDA SOLO DESDE 17:00
							// ============================================================
							if ($MOSTRAR_MERIENDA==true) {
								// Merienda solo si activada
								if ($no_merienda!=1) {

									// merienda (solo si ficha tarde)
									if ($ficha_tarde==1) {
										$descripcion_comidas.="<tr class='tabla_merienda'><td rowspan=[filas_merienda]><b>MERIENDA</b></td>";

										$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
										if (db_num_rows($busca_comida)!=0) {
											$datos_comida=db_fetch_array($busca_comida);

											if ($datos_comida["cant_merienda1"]<4) {
												$color_fuente_linea="#000000";
												if (($alergias_ya_marcadas[$merienda1][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda1][$id_alumno]==1)) {
													$color_fuente_linea=$color_alerta;
												}
												$descripcion_comidas.="<td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
												$resultado_merienda1=$resultados_comida[$datos_registro_comida["merienda1"]];
												if ($datos_registro_comida["cant_merienda1"]==3) { $resultado_merienda1=""; }
												$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_merienda1"]]."</td><td>".$resultado_merienda1."</td></tr>";
												$numero_filas_merienda++;
												$numero_filas_comidas++;
											}
										}

										$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
										if (db_num_rows($busca_comida)!=0) {
											$datos_comida=db_fetch_array($busca_comida);

											if ($datos_comida["cant_merienda2"]<4) {
												$color_fuente_linea="#000000";
												if (($alergias_ya_marcadas[$merienda2][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda2][$id_alumno]==1)) {
													$color_fuente_linea=$color_alerta;
												}
												$descripcion_comidas.="<tr class='tabla_merienda'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
												$resultado_merienda2=$resultados_comida[$datos_registro_comida["merienda2"]];
												if ($datos_registro_comida["cant_merienda2"]==3) { $resultado_merienda2=""; }
												$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_merienda2"]]."</td><td>".$resultado_merienda2."</td></tr>";
												$numero_filas_merienda++;
												$numero_filas_comidas++;
											}
										}

									} // Fin filtro no ficha tarde
								} // Fin merienda
							}

							// $texto_observaciones_linea="<br /> - ".$datos_registro_comida["observaciones_comida"];
						}
					}

					// ============================================================
					// NOTAS (siestas/observaciones) desde 12:00
					// ============================================================
					if ($MOSTRAR_NOTAS==true) {
                        
                        $texto_observaciones_linea="";

						// Otras observaciones comida
						if (strlen($descripcion_comidas)==0) {
							$texto_observaciones_linea="";
							$busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE dia=? AND (id_alumno=?) AND observaciones_comida<>\'\'', 'ss', array((string)(date("Y-m-d", $dia)), $id_alumno));

							while($incidencias=db_fetch_array($busca_incidencias)) {
								$texto_observaciones_linea.="<br /> - ".$incidencias["observaciones_comida"];
							}

							if (strlen($texto_observaciones_linea)!=0) {
								$descripcion_comidas.="<tr class='tabla_pie_comida'><td colspan=5><b>Observaciones:</b>".$texto_observaciones_linea."</td></tr>";
							}
						}

						$busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND area=0 ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));

						while($incidencias=db_fetch_array($busca_incidencias)) {
							$texto_observaciones_linea.="<br /> - ".$incidencias["nota"];
						}

						if (strlen($texto_observaciones_linea)!=0) {
							$descripcion_comidas.="<tr class='tabla_pie_comida'><td colspan=5><b>Observaciones:</b>".$texto_observaciones_linea."</td></tr>";
						}
					}

					// ============================================================
					// ALMUERZO (solo desde 12:00)
					// ============================================================
					if ($MOSTRAR_ALMUERZO==true) {

						$descripcion_almuerzo="";

						$busca_registro_almuerzo=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());

						if (db_num_rows($busca_registro_almuerzo)!=0) {
							$datos_registro_almuerzo=db_fetch_array($busca_registro_almuerzo);

							// Almuerzo
							if ($datos_registro_almuerzo["cant_almuerzo"]<4) {
								$descripcion_almuerzo.="<tr><td class='tabla_comidas' rowspan=[filas_comidas] style='max-width:100px!important;min-width:100px!important;' width='100'><img src='agenda/imagenes/PersonajeComedor.png' border=0></td><td class='tabla_almuerzo'><b>ALMUERZO</b></td><td class='tabla_almuerzo'>&nbsp;</td><td class='tabla_almuerzo'>".$cantidades_comida[$datos_registro_almuerzo["cant_almuerzo"]]."</td><td class='tabla_almuerzo'>".$resultados_comida[$datos_registro_almuerzo["almuerzo"]]."</td></tr>";
								$numero_filas_comidas++;
							}
						}

						if ($descripcion_almuerzo!="") {
							if (strlen($descripcion_comidas)!=0) {
								$descripcion_comidas="<tr>".$descripcion_comidas;
							}
							$descripcion_comidas=$descripcion_almuerzo.$descripcion_comidas;
							$numero_filas_almuerzo++;
						}
					}

					// Reemplazos
					$descripcion_comidas=str_replace("[filas_comidas]", $numero_filas_comidas, $descripcion_comidas);
					$descripcion_comidas=str_replace("[filas_comida]", $numero_filas_comida, $descripcion_comidas);
					$descripcion_comidas=str_replace("[filas_merienda]", $numero_filas_merienda, $descripcion_comidas);

					// ============================================================
					// MOSTRAR SALIDA (Bootstrap 3)
					// ============================================================
					if (trim($descripcion_comidas)=="") {
						?>
						<div class="alert alert-warning" style="margin-bottom:0;">
						  No hay información publicada todavía para hoy.
						</div>
						<?php
					} else {
						?>
						<div class="table-responsive">
						  <table class="table table-condensed table-striped" style="margin-bottom:0;">
							<thead>
							  <tr>
								<th style="width:120px;">Bloque</th>
								<th>Detalle</th>
								<th style="width:120px;">Cantidad</th>
								<th style="width:160px;">Resultado</th>
							  </tr>
							</thead>
							<tbody>
							  <?php echo $descripcion_comidas; ?>
							</tbody>
						  </table>
						</div>
						<?php
					}

				} else {
					?>
					<div class="alert alert-warning" style="margin-bottom:0;">
					  Hoy no consta entrada a comedor / menú especial.
					</div>
					<?php
				}

			} // fin else antes_12
			?>

				</div>
			  </div>
			</div>
			<?php

			$contador_hijos++;
		} // while alumnos
	} else {
		?>
		<div class="alert alert-warning">
		  No se han encontrado alumnos asociados a este usuario.
		</div>
		<?php
	}
?>
	  </div>
	</div>
<?php
} // fin $errores==""
?>
	  <!-- / FINAL CONTENIDO -->
	</section>
	<!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php
require("parte_inferior_padres.php");
?>