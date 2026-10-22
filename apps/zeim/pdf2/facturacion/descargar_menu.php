<?php
// ----------- Datos ------------
ini_set("display_errors", 0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
require("../../configuracion/config.php"); 
require("../../menu/funciones_alergias.php");

$tokenOK=false;
$tokenDescarga = $_GET["tokendescarga"];
if (strlen($tokenDescarga)!=0) {
        // Borra tokens expirados
        $numeroDiasCaducidad = 1;
        $fecha_control=date("Y-m-d H:i:s",(time()-(86400*$numeroDiasCaducidad)));
        $borra_token=db_query_params('DELETE FROM tokens_descarga WHERE creacion<?', 's', array((string)($fecha_control)));

        // Busca token 
        $busca_token=db_query_params('SELECT * FROM tokens_descarga WHERE token=?', 's', array((string)($tokenDescarga)));
        if (db_num_rows($busca_token)!=0) {
                $datos_token=db_fetch_array($busca_token);
                $requestedChild=(int)app_value($_GET,'id_hijo',app_value($_POST,'id_hijo'));
                if(!app_rows('SELECT a.id_alumno FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE a.id_alumno=? AND p.id_usuario=? AND a.id_usuario=? AND COALESCE(p.denegar_acceso_agenda,0)=0','iii',array($requestedChild,(int)$datos_token['id_usuario'],(int)$datos_token['id_hijo'])))security_stop();
                $tokenOK=true;
                // Borra token usado
                $borra_token=db_query_params('DELETE FROM tokens_descarga WHERE token=?', 's', array((string)($tokenDescarga)));
        } else {
                ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="<?php  echo $meta_description ; ?>">
  <meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">  
  
  <link rel="shortcut icon" href="https://santpau.escuelas-infantiles.com/favicon.ico" type="image/vnd.microsoft.icon">
  <link rel="icon" href="https://santpau.escuelas-infantiles.com/favicon.ico" type="image/vnd.microsoft.icon">
    
  <title><?php  echo utf8_encode($titulo_web) ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/app/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/app/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/app/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-boxed TO GET A BOXED LAYOUT -->
<body class="hold-transition skin-blue layout-boxed sidebar-mini">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-12 col-xs-12 text-center"><img src="/app/imagenes/logo_redondo.jpg" border=0 width="100"></div>
  </div>
  <div class="row"> <!-- Principal -->
  
	<div class="panel panel-default">
	  <div class="panel-body text-center">

                <p align="center">
                        <h1>El código de descarga única no es válido. Ha expirado o ya ha sido usado</h1>
                        <h3>(El código de descarga solo puede usarse una vez y tiene una validaz de 24 horas desde el momento en que se solicita la descarga)</h3>
                        <h2>Vuelve y recarga la página donde has solicitado la descarga para generar un código nuevo.</h2>	
                </p>
	</div>
	</div>
</div>
<!-- Pie de página -->
<div class="row">
</div>
</div>
</body>
</html>
                <?php
                die();
        }
} else {
	die("No se ha enviado TOKEN de descarga.");
}

$id_alumno=$_GET["id_hijo"];
if (strlen($id_alumno)==0) {
		$id_alumno=$_POST["id_hijo"];
		}


$busca_datos_alumno=db_query_params('SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=? LIMIT 0,1', 's', array($id_alumno)) or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {

$datos_alumno=db_fetch_array($busca_datos_alumno);

$centro_actual=$datos_alumno["centro_asistencia"];
$id_padre=$id_usuario;
$id_usuario=$datos_alumno["id_usuario"];

$fechaInicioAlumno=$datos_alumno["fecha_matricula"];
$fechaFinAlumno=$datos_alumno["fecha_baja"];

if ($datos_alumno["sexo"]=="V") {
						$sufijo_hijo="o";
						} else {
						$sufijo_hijo="a";
						}

$nombre_de_pila_alumno=$datos_alumno["Nombre"];
$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
$comedor_alumno=$datos_alumno["comedor"];

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];

if (strlen($curso_seleccionado)==0) {

		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

		if (db_num_rows($busca_curso_actual)!=0) {
				$datos_curso_actual=db_fetch_array($busca_curso_actual);
				$id_curso_actual=$datos_curso_actual["id_curso"];
				} else {
				$id_curso_actual=0;
				}
		} else {
		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
		$id_curso_actual=$curso_seleccionado;
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		}
		
$inicio=$datos_curso_actual["fecha_inicio"];

if (strtotime($inicio)<strtotime(date("Y-m-d"))) {
		$inicio=date("Y-m-d");
		}

if (strtotime($inicio)>strtotime($datos_curso_actual["fecha_fin"])) {
		$inicio=$datos_curso_actual["fecha_fin"];
		}

$dia_sel_inicio=$_GET["dia"];
if (strlen($dia_sel_inicio)!=0) {
		$inicio=$dia_sel_inicio;
		}


if (strlen($inicio)!=0) {
		$anio_fecha=date("Y",strtotime($inicio));
		} else {
		$anio_fecha=date("Y");
		}

if (strlen($inicio)!=0) {
		$anio_fecha=date("Y",strtotime($inicio));
		} else {
		$anio_fecha=date("Y");
		}

if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
}

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
		$mes_anterior=12;
		$ano_anterior=$ano_anterior-1;
		}
if ($mes==12) {
		$mes_siguiente=1;
		$ano_siguiente=$ano_siguiente+1;
		}

//Inicio es el día en el que empieza la primera semana del mes + 6 horas (por lo del cambio horario, mire usted)
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400+21600;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;

$mes_fin_curso=date("n",strtotime($datos_curso_actual["fecha_fin"]));
$anio_fin_curso=date("Y",strtotime($datos_curso_actual["fecha_fin"]));
$mes_ini_curso=date("n",strtotime($datos_curso_actual["fecha_inicio"]));
$anio_ini_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));

// Empezamos
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$alimentos_ya_marcados=array();
$alergias_ya_marcadas=array();

$platos_no_introducidos=array();
$platos_alaergias=array();
$hay_observaciones=false;

$mesnombre=$nombre_mes[$mes];
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$mes_s=1;
$dias_trabajados_mes=0;

$nombre_menu = "Calendario_Comedor_".$mesnombre."_".$anio_actual;

$tituloMenu ='<p align="center"><stron>Calendario comedor para '.$nombre_completo_alumno.' - <strong>'.$mesnombre.', '.$anio_actual.'</strong></strong></p>';
$leyendaAlmuerzo ='<br><p align="center">El almuerzo de todos los días consistirá en fruta de temporada para quienes comen sólido y de yogur o fruta triturada para los que coman triturado.</p><br>';

$tblMenu.='<table cellpadding="2" cellspacing="0" border="1"><tr bgcolor="#8F8F8F">';

for ($dia_s=1; $dia_s<=5; $dia_s=$dia_s+1)
{
	$diaActual = $dia_sem_largo[$dia_s];
	$tblMenu.='<td align="center"><strong>'.$diaActual.'</strong></td>';

}
$tblMenu.='</tr>';

$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{

  $tblMenu.="<tr>";

  $primerDiaSemana=true;
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {
	
	if ($dias<=5) {  // Solo dias entre semana
	if ($mes==date("n",$inicio_time_stamp)) {
	
		if ($primerDiaSemana==true) {
				if ($dias>1) {
					$anchoCeldas = $dias-1;
					if ($anchoCeldas>1) {
						$colspanCeldas=' colspan="'.$anchoCeldas.'"';
					}
					$tblMenu.='<td align="left" valign="top"'.$colspanCeldas.'> </td>'; 
				}
				$primerDiaSemana=false;       
		}

		// Comprueba si está marcado
		$busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
		if (db_num_rows($busca_dia_en_calendario)!=0) {
				$datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
				if ($datos_dia_en_calendario["estado"]==1) {
						$estado_boton=" checked";
						$fondo_celda_dia="#F17327";
						} else {
						$estado_boton="";
						$dias_trabajados_mes++;
						$dias_trabajados++;
						$fondo_celda_dia="#EFEFEF";
						}
				} else {
				// Si es domingo lo marca como festivo
				if (($dias==7) or ($dias==6)) {
						$estado_boton=" checked";
						$fondo_celda_dia="#F17327";
						} else {
						$estado_boton="";
						$dias_trabajados_mes++;
						$dias_trabajados++;
						$fondo_celda_dia="#EFEFEF";
						}
				}
				
		// Calcula menu apropiado para el niño
		
						// Edad actual del niño en años con decimales
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
										if ($edad_alumno_anos!=1) {
												$plural_anios="s";
												} else {
												$plural_anios="";
												}
										if ($edad_alumno_meses>1) {
												$plural_meses="es";
												} else {
												$plural_meses="";
												}

				// Comprueba si tiene un tipo de menu seleccionado
				$menu_actual_en_ficha=$datos_alumno["menu_actual"];
				$coletilla_error="";
				$tipo_seleccion_menu="";
				if ($menu_actual_en_ficha==-1) {
								if ($datos_aula["id_menu_predefinido"]!=0) {
										$tipo_seleccion_menu="Menú asignado en aula";
										$id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
										} else {
										$menu_actual_en_ficha=0;
										$coletilla_error=" (El aula no tiene menú asignado)";
										}
								}
				if ($menu_actual_en_ficha>0) {
						$tipo_seleccion_menu="Menú asignado en ficha".$coletilla_error;
						$id_menu_seleccionado=$menu_actual_en_ficha;
						} else {
						if ($menu_actual_en_ficha==0) {
						$tipo_seleccion_menu="Automático por edad".$coletilla_error;

						// Comprueba si es de regimen
						$regimen=$datos_alumno["comedor"];
						if ($regimen==3) {
								$filtro_regimen=" AND regimen=1";
								} else {
								$filtro_regimen=" AND regimen=0";
								}


						// Calcula el menú que le toca por edades
						// $busca_menu_apropiado=mysql_query("SELECT * FROM gestionguarderias.menu_menus WHERE  (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND edad_ini_menu<=$edad_alumno $filtro_regimen");
						$busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE gestionguarderias.menu_menus.id_menu=gestionguarderias.menu_calendario.id_menu AND gestionguarderias.menu_calendario.fecha='".date("Y-m-d",$inicio_time_stamp)."' AND (gestionguarderias.menu_menus.disponible_desde<='".date("Y-m-d",$inicio_time_stamp)."' AND gestionguarderias.menu_menus.disponible_hasta>='".date("Y-m-d",$inicio_time_stamp)."') AND gestionguarderias.menu_menus.edad_ini_menu<=$edad_alumno $filtro_regimen");
						// $busca_menu_apropiado=mysql_query("SELECT * FROM gestionguarderias.menu_menus WHERE edad_ini_menu<=$edad_alumno$filtro_regimen");
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
								if (($id_menu_seleccionado==0) and ($array_errores[$datos_alumno["id_alumno"]]!=1)) {
										if ($edad_alumno<$minimo_minimorum) {
												$motivo_error="La edad del niño no llega a la edad mínima (".round(($minimo_minimorum*12),0)." meses) admitida en los menús.<small><br><strong>Propuestas:</strong><br>1. Introducir un menú apropiado para la edad del niño.<br>2. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
												} else {
												$motivo_error="El cía ".date("d/m/Y",$inicio_time_stamp)." no se encuentra un menú apropiado para la edad del niño.<small><br><strong>Propuestas:</strong><br>1. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
												}

												$listado_errores=$listado_errores."<tr><td>No hay menú</font></td><td>".$motivo_error."</font></td></tr>";
												$array_errores[$datos_alumno["id_alumno"]]=1;
										}
							   }
						}

		$id_menu=$id_menu_seleccionado;
		
		// Comprueba si el niño asiste hoy
		if (($inicio_time_stamp<strtotime($fechaInicioAlumno)) or ($inicio_time_stamp>strtotime($fechaFinAlumno))) {
				$id_menu=0;
		}

		//  Busca nombre menú
		if ($id_menu!=0) {
				$busca_nombre_menu=db_query_params('SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=?', 's', array($id_menu));
				if (db_num_rows($busca_nombre_menu)!=0) {
						$datos_nombre_menu=db_fetch_array($busca_nombre_menu);
						$nombre_menu_seleccionado=$datos_nombre_menu["nombre_menu"];
						if (strlen($nombre_menu_seleccionado)!=0) {
								$nombre_menu_seleccionado=urldecode($nombre_menu_seleccionado);
								}
						} else {
						$nombre_menu_seleccionado="";
						}
				} else {
				$nombre_menu_seleccionado="";
				}

		// Inicio celda día
		$tblMenu.='<td align="left" valign="top" bgcolor="'.$fondo_celda_dia.'">';
		
		if ($mes==date("n",$inicio_time_stamp)) {
			$tblMenu.='<strong>';
		}
		if ($dias==7) {
			$tblMenu.='<font color="#800000">';    
		}
		if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y")) {
				$tblMenu.='<font color="#0000ff">';
		}
		$tblMenu.= (date("j",$inicio_time_stamp));
		if (($dias==7) or (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))) {
				$tblMenu.='</font>';
		}		
		if ($mes==date("n",$inicio_time_stamp)) {
			$tblMenu.='</strong>';
		}



			
		$tblMenu.='<br>';

		
		$busca_platos_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $inicio_time_stamp))));

		if (db_num_rows($busca_platos_dia)!=0) {

			$datos_platos_dia=db_fetch_array($busca_platos_dia);

			for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

			$codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];
			if ($codigo_plato!=0) {

				$consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

				if (db_num_rows($consulta_datos_plato)!=0) {
					$datos_plato=db_fetch_array($consulta_datos_plato);


					// Comprueba platos
					$busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE gestionguarderias.menu_ingredientes.id_alimento=gestionguarderias.menu_alimentos.id_alimento AND gestionguarderias.menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

					while($ingredientes=db_fetch_array($busca_ingredientes)) {
							for ($c=0;$c<count($array_alimentos);$c++) {
								if (($ingredientes[$array_alimentos[$c]]==1) and ($datos_alumno[$array_alimentos[$c]]==0)) {
									$hay_comida=false;
									if ($alimentos_ya_marcados[$codigo_plato]!=1) {
										$platos_no_introducidos[$c]=$platos_no_introducidos[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
										$alimentos_ya_marcados[$codigo_plato]=1;
									}
									$hay_observaciones=true;
								}
							}

							for ($c=0;$c<count($array_alergias);$c++) {
								if (($ingredientes[$array_alergias[$c]]==1) and ($datos_alumno[$array_alergias[$c]]==1)) {
									$nombre_alergia=$nombres_alergias[$c];
									$hay_comida=false;
									if ($alergias_ya_marcadas[$codigo_plato]!=1) {
									$platos_alaergias[$c]=$platos_alaergias[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
									$alergias_ya_marcadas[$codigo_plato]=1;
									}
									$hay_observaciones=true;
								}
							}
						}

					}

				}
			}
			// Edita el nuevo día
			$actualizar=false;
			$id_dia_menu=$datos_platos_dia["id_dia"];
			$comida1=$datos_platos_dia["comida1"];
			$comida2=$datos_platos_dia["comida2"];
			$comida3=$datos_platos_dia["comida3"];
			$comida4=$datos_platos_dia["comida4"];
			$comida5=$datos_platos_dia["comida5"];
			$postre1=$datos_platos_dia["postre1"];
			$merienda1=$datos_platos_dia["merienda1"];
			$merienda2=$datos_platos_dia["merienda2"];
			$cena=$datos_platos_dia["cena"];

			$color_alerta="#FF0000";
			
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
						$tblMenu.='<strong>Comida 1:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}												
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Comida 2:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Comida 3:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Comida 4:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Comida 5:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Postre:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Merienda 1:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}
			$busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
			if (db_num_rows($busca_comida)!=0) {
					$datos_comida=db_fetch_array($busca_comida);

					// Calcula contenido calorico plato
					$contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

					$listaAlergenosPlato = '';

					if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
					$listaAlergenosPlato = " Al. (".$contenidoCalorico['alergenos_lista'].")";
					}

					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
							$color_fuente_linea=$color_alerta;
							}
							$tblMenu.='<strong>Merienda 2:</strong> '.urldecode($datos_comida["nombre_plato"]).$listaAlergenosPlato.'<br>';
					}

			if (strlen($cena)!=0) {
				$tblMenu.='<strong>Cena:</strong> '.$cena.'<br>';
			}             
		} // Fin platos día		

		$tblMenu.='</td>';
		// Fin celda día
		} else {
			
			if ($mes<date("n",$inicio_time_stamp)) {
				$anchoCeldas = 5-$dias;
				if ($anchoCeldas>1) {
					$colspanCeldas=' colspan="'.$anchoCeldas.'"';
				}
				$tblMenu.='<td'.$colspanCeldas.'> </td>'; 
			}
		
		}
		} // Quita sabado y domingo
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }

  $tblMenu.='</tr>';

  if ($mes!=date("n",$inicio_time_stamp)) {
										   break;
										  }

}
$mes=$mes+1;
if ($mes==13) {
		$mes=1;
		}
$anio_actual=date("Y",$inicio_time_stamp);
$inicio_time_stamp=$inicio_time_stamp-(86400*7);

$anio_actual_1=date("Y",$inicio_time_stamp);
$mes_actual_1=date("n",$inicio_time_stamp);

$tblMenu.='</table>';


// Alergenos
/*
$tblMenu1.='<p align="center"><strong>Alergenos:</strong><br>';

foreach ($arrayKeysAlergias as $keyAlg => $numAlg) {
		$tblMenu1.='<img src="https://catering.escuelas-infantiles.com/img/iconos/'.$keyAlg.'.png"> '.$numAlg.'. '.ucfirst($nombres_alergias[$numAlg-1]).' ';
		if ($numAlg==6) {
			$tblMenu1.=  "<br>";
		}
}

$tblMenu1.='</p>';
*/
$tblObservaciones = "";

if ($hay_observaciones==true) {
	$tblObservaciones.='<table border=1 cellpadding=2 cellspacing=0>';
	$tblObservaciones.='<tr valign="top">';
	$tblObservaciones.='<td colspan=2 bgcolor="#efefef"><font size=2><strong>Observaciones:</strong></td>';
	$tblObservaciones.='</tr>';

	$encabezado=false;
	for ($c=0;$c<count($array_alimentos);$c++) {
		if (strlen($platos_no_introducidos[$c])!=0) {
			$platos_no_introducidos[$c]=urldecode(substr($platos_no_introducidos[$c],0,strlen($platos_no_introducidos[$c])-1));
			if ($encabezado==false) {
		
				$tblObservaciones.='<tr valign="top">';
				$tblObservaciones.='<td colspan=2 bgcolor="#efefef"><font size=2><strong>Alimentos todavía no introducidos.</strong><br>';
				$tblObservaciones.='Debido a la presencia de algún ingrediente no introducido todavía en la alimentación de '.ucwords(strtolower($nombre_de_pila_alumno)).', los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>';
				$tblObservaciones.='</tr>';
			
				$encabezado=true;
			}
		
			$tblObservaciones.='<tr valign="top">';
			$tblObservaciones.='<td><font size=2><strong>'.$nombres_alimentos[$c].'</strong></font></td>';
			$tblObservaciones.='<td><font size=2>'.$platos_no_introducidos[$c].'</font></td>';
			$tblObservaciones.='</tr>';
				
		}
	}
			
	$encabezado=false;
	for ($c=0;$c<count($array_alergias);$c++) {
		if (strlen($platos_alaergias[$c])!=0) {
			$platos_alaergias[$c]=urldecode(substr($platos_alaergias[$c],0,strlen($platos_alaergias[$c])-1));
			if ($encabezado==false) {
				
				$tblObservaciones.='<tr valign="top">';
				$tblObservaciones.='<td colspan=2 bgcolor="#efefef"><font size=2><strong>Alergias</strong><br>';
				$tblObservaciones.='Debido a la presencia de algún ingrediente al que '.ucwords(strtolower($nombre_de_pila_alumno)).' tiene alergia, los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>';
				$tblObservaciones.='</tr>';
			
				$encabezado=true;
			}

			$tblObservaciones.='<tr valign="top">';
			$tblObservaciones.='<td><font size=2><strong>'.$nombres_alergias[$c].'</strong></font></td>';
			$tblObservaciones.='<td><font size=2>'.$platos_alaergias[$c].'</font></td>';
			$tblObservaciones.='</tr>';
		}
	}
	$tblObservaciones.='</table>';

}


} else {
	die();
}

// DEBUG
// exit("<html><body>".$tblMenu."</body></html>"); // Solo para debug
// DEBUG

// ---------Fin datos-----------

$limite_inferior=240;
$posicion_inicial=64;

require_once('../config/lang/spa.php');
require_once('../tcpdf.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		/*
		// Logo
		$image_file = K_PATH_IMAGES.'logo_factura.jpg';
		$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 12);
		// Title
		$this->Cell(0, 15, 'FECHA: '.date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])), 0, false, 'R', 0, '', 0, false, 'M', 'M');
		*/
	}

	// Page footer
	public function Footer() {
		$x_leyenda=10;
		$y_leyenda=195;
		$this->Image('leyenda_alergenos.png',$x_leyenda,$y_leyenda,200);

		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 6);
		// Page number
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}

// create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('ZEIM Escuelas Infantiles');
$pdf->SetTitle('Menú');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font

// add a page
$pdf->AddPage();

// Logotipo

// Tabla Menu
$pdf->setPageMark();
$x_logo=10;
$y_logo=5;
$pdf->Image('logo_menu'.$centro_actual.'.png',$x_logo,$y_logo,50);
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML(utf8_encode($tituloMenu), true, false, false, false, '');
$pdf->SetFont('helvetica', '', 8);
$pdf->writeHTML($leyendaAlmuerzo, true, false, false, false, '');

$pdf->SetFont('helvetica', '', 6);
$pdf->writeHTML(utf8_encode($tblMenu), true, false, false, false, '');
// $pdf->writeHTML(utf8_encode($tblMenu1), true, false, false, false, ''); // Sustituido por imagen leyenda

if (strlen($tblObservaciones)!=0) {
	$pdf->AddPage();
	$pdf->setPageMark();
	$x_logo=10;
	$y_logo=5;
	$pdf->Image('logo_menu'.$centro_actual.'.png',$x_logo,$y_logo,50);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->writeHTML($tblObservaciones, true, false, false, false, '');

}

// Fin Tabla Menu                

//Close and output PDF document
$pdf->Output("$nombre_menu.pdf", 'I');

//============================================================+
// END OF FILE                                                
//============================================================+