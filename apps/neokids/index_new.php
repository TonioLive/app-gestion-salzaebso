<?php

include("configuracion/config.php");

$dia_sel=$_GET["dia"];
if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}


// Cumpleaños Hoy


$inicio_semana=$fecha_time_stamp-((date("N",($fecha_time_stamp))-1)*86400);
$fecha_inicio_semana=$inicio_semana;
$fin_semana=$inicio_semana+(7*86400);

$filtro_cumples="";
for($c_semana=0;$c_semana<7;$c_semana++){
	$filtro_cumples.="(day(usuarios.fecha_nacimiento)=".date("j",$inicio_semana)." AND month(usuarios.fecha_nacimiento)=".date("n",$inicio_semana).") OR ";
	$inicio_semana=$inicio_semana+86400;	
}

if ($filtro_cumples!="") {
	$filtro_cumples="(".substr($filtro_cumples,0,strlen($filtro_cumples)-4).")";
	}

$texto_cumples="";
$consulta_cumples="SELECT *,centro_aulas.nombre AS nombre_aula FROM usuarios, alumnos, centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' AND $filtro_cumples AND alumnos.id_centro=".$centro_actual." ORDER BY month(usuarios.fecha_nacimiento), day(usuarios.fecha_nacimiento)";

$busca_cumples=db_query($consulta_cumples);

if (db_num_rows($busca_cumples)!=0) {

        $encabezado_cumples="<a class='btn btn-primary btn-xs' href='".$_SERVER["PHP_SELF"]."?dia=".($fecha_inicio_semana-(86400*7))."'><i class='fa fa-caret-left' aria-hidden='true'></i> Semana anterior</a> <a class='btn btn-primary btn-xs pull-right' href='".$_SERVER["PHP_SELF"]."?dia=".($fin_semana+86400)."'>Semana siguiente <i class='fa fa-caret-right' aria-hidden='true'></i></a><b>Cumpleaños del ".date("d/m/Y",$fecha_inicio_semana)." al ".date("d/m/Y",$fin_semana)."</b><br />";
		$texto_cumples="";
		$cumple_actual="";
        while($listado_cumples=db_fetch_array($busca_cumples)) {
        
        	if ($cumple_actual!=date("d/m",strtotime($listado_cumples["fecha_nacimiento"]))) {
        		$cumple_actual=date("d/m",strtotime($listado_cumples["fecha_nacimiento"]));
        		
        		$dia_semana=date("w",strtotime(date("Y").date("-m-d",strtotime($listado_cumples["fecha_nacimiento"]))));
        		if ($dia_semana==0) {
        			$dia_semana=7;
        		}
        		
        		$texto_cumples.='
				<tr class="warning">
					<td colspan="4"><b>'.$dia_sem_largo[$dia_semana].' '.date("j",strtotime($listado_cumples["fecha_nacimiento"])).' de '.$nombre_mes[date("n",strtotime($listado_cumples["fecha_nacimiento"]))].'</b></td>		
				</tr>        		
        		<tr class="active">
					<td><b>Nombre</b></td>
					<td><b>Cumple</b></td>	
					<td><b>Grupo</b></td>
					<td><b>&nbsp;</b></td>				
				</tr>
				';
        	}
        
            $nacimiento=explode( '-', $listado_cumples["fecha_nacimiento"]);
            $edad_cumple=date("Y",$fecha_time_stamp)-$nacimiento[0];
            $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y",$fecha_time_stamp));
            $hoy=strtotime(date("Y/m/d",$fecha_time_stamp));
            if (($cumple-$hoy)>0) {
                                $edad_cumple=$edad_cumple-1;
                                }
           		if ($hoy<$cumple) {
           			$edad_cumple=$edad_cumple+1;
           			} 
           		$coletilla_edad="";                
                if ($edad_cumple>1) {
                        $coletilla_edad="s";
                        }                                   
                        
                $lista_grupos_alumno="";

                $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($listado_cumples["id_alumno"])) or die(db_error());

                $total_grupos=db_num_rows($busca_alumnos_grupo);
                $contador_grupos=0;
                while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                        $contador_grupos++;
                        
                        $separador_grupos="<br />";
                                                
                        $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).$separador_grupos;
                        }

				// busca si hay registo
				$busca_registro_cumple=db_query_params('SELECT * FROM alumnos_cumples_confirmados WHERE id_alumno=? AND fecha_cumple=?;', 'ss', array($listado_cumples["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp))));

				if (db_num_rows($busca_registro_cumple)==0) {
					$intro_registro_cumple=db_query_params('INSERT INTO alumnos_cumples_confirmados (id_alumno,fecha_cumple) VALUES (?,?);', 'ss', array((string)($listado_cumples["id_alumno"]), (string)(date("Y-m-d", $fecha_time_stamp))));		
					$id_registro_cumple=db_insert_id();
					$estado_registro_cumple=0;								
				} else {
					$datos_registro_cumple=db_fetch_array($busca_registro_cumple);
					$id_registro_cumple=$datos_registro_cumple["id_cumple"];
					$estado_registro_cumple=$datos_registro_cumple["confirmado"];
				}
				
				if ($estado_registro_cumple==0) {
                	$estado_actual_cumple="";
                } else {
                	$estado_actual_cumple=" checked";
                }				

                $texto_cumples.="<tr><td>".$listado_cumples["Nombre"]." ".$listado_cumples["Apellido1"]." ".$listado_cumples["Apellido2"]."</td><td>".$edad_cumple." año$coletilla_edad</td><td>".$lista_grupos_alumno."</td><td><input data-toggle='toggle' id='comfirmacion-cumple-".$id_registro_cumple."' value='".$id_registro_cumple."' data-size='mini' data-onstyle='success' data-offstyle='warning' data-on='Celebrado' data-off='Pendiente' type='checkbox'".$estado_actual_cumple."></td></tr>";
                }

        }
        





// Faltas Recuperables



?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php  echo $titulo_web ; ?></title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="/dashboard/assets/css/bootstrap.css" />
		<link rel="stylesheet" href="/dashboard/assets/css/font-awesome.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="/dashboard/assets/css/ace-fonts.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="/dashboard/assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="/dashboard/assets/css/ace-part2.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="/dashboard/assets/css/ace-ie.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="/dashboard/assets/js/ace-extra.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="/dashboard/assets/js/html5shiv.js"></script>
		<script src="/dashboard/assets/js/respond.js"></script>
		<![endif]-->
	</head>

	<body class="no-skin">
		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<!-- #section:basics/sidebar.mobile.toggle -->
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<!-- /section:basics/sidebar.mobile.toggle -->
				<div class="navbar-header pull-left">
					<!-- #section:basics/navbar.layout.brand -->
					<a href="#" class="navbar-brand">
						<small>
							<i class="fa fa-leaf"></i>
							La Piscina de Neokids
						</small>
					</a>

					<!-- /section:basics/navbar.layout.brand -->

					<!-- #section:basics/navbar.toggle -->

					<!-- /section:basics/navbar.toggle -->
				</div>

				<!-- #section:basics/navbar.dropdown -->
				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">						

						<!-- #section:basics/navbar.user_menu -->
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<big><big><big><i class="fa fa-user"></i></big></big></big>
								<span class="user-info">
									<small>Hola,</small>
									<?=$nombre;?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

								<li>
									<a href="/gestion/usuarios/datos_usuario.php">
										<i class="ace-icon fa fa-user"></i>
										Mis datos
									</a>
								</li>

								<li class="divider"></li>

								<li>
									<a href="/configuracion/cerrar_sesion.php">
										<i class="ace-icon fa fa-power-off"></i>
										Salir
									</a>
								</li>
							</ul>
						</li>

						<!-- /section:basics/navbar.user_menu -->
					</ul>
				</div>

				<!-- /section:basics/navbar.dropdown -->
			</div><!-- /.navbar-container -->
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<!-- #section:basics/sidebar -->
			<div id="sidebar" class="sidebar                  responsive">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>

				<ul class="nav nav-list">
					<li class="active">
						<a href="index.html">
							<i class="menu-icon fa fa-tachometer"></i>
							<span class="menu-text"> Área de trabajo </span>
						</a>

						<b class="arrow"></b>
					</li>

					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-graduation-cap"></i>
							<span class="menu-text">
								Alumnos
							</span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
						
							<li class="">
								<a href="/contactos/listado_contactos.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Contactos
								</a>

								<b class="arrow"></b>
							</li>
						
							<li class="">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-caret-right"></i>

									Gestión alumnos
									<b class="arrow fa fa-angle-down"></b>
								</a>

								<b class="arrow"></b>

								<ul class="submenu">
									<li class="">
										<a href="/alumnos/nuevo_alumno.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Inscribir alumno
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/alumnos/busqueda_alumno.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Buscar alumno
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/alumnos/informe_alumnos_cursos.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Todos los alumnos
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/alumnos/listado_alumnos_lapices.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Alumnos Lápices
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/alumnos/seleccion_listados.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Listados alumnos
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/pdf/cartas.php?id_escuela=<?=$centro_actual?>" target="_new">
											<i class="menu-icon fa fa-caret-right"></i>
											Cartas Webcam
										</a>

										<b class="arrow"></b>
									</li>
								</ul>
							</li>


							<li class="">
								<a href="/lista_espera/listas_espera.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Lista de espera
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-caret-right"></i>

									Gestión de listados
									<b class="arrow fa fa-angle-down"></b>
								</a>

								<b class="arrow"></b>

								<ul class="submenu">
									<li class="">
										<a href="/gestion/listados/listado_motivos.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Motivos de cancelación
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="#" class="dropdown-toggle">
											<i class="menu-icon fa fa-caret-right"></i>

											Profesiones
											<b class="arrow fa fa-angle-down"></b>
										</a>

										<b class="arrow"></b>

										<ul class="submenu">
											<li class="">
												<a href="/gestion/listados/listado_profesiones.php">
													<i class="menu-icon fa fa-caret-right"></i>
													Profesiones
												</a>

												<b class="arrow"></b>
											</li>

											<li class="">
												<a href="/gestion/listados/informe_profesiones.php">
													<i class="menu-icon fa fa-caret-right"></i>
													Informe profesiones
												</a>

												<b class="arrow"></b>
											</li>
											<li class="">
												<a href="/gestion/listados/informe_profesiones_completo.php">
													<i class="menu-icon fa fa-caret-right"></i>
													Listado profesiones
												</a>

												<b class="arrow"></b>
											</li>											
										</ul>
									</li>
								</ul>
							</li>

							<li class="">
								<a href="/contactos/mapa.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Mapa alumnos
								</a>

								<b class="arrow"></b>
							</li>

						</ul>
					</li>

					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-money"></i>
							<span class="menu-text"> Facturación </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
						
							<li class="">
								<a href="/facturacion/gestion_familias.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Servicios
								</a>

								<b class="arrow"></b>
							</li>						
						
							<li class="">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-caret-right"></i>

									Facturación
									<b class="arrow fa fa-angle-down"></b>
								</a>

								<b class="arrow"></b>
								
								<ul class="submenu">								
								
									<li class="">
										<a href="/facturacion/facturar_servicios.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Emitir facturas
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/facturacion_vista_previa/facturar_servicios.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Vista previa facturas
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/facturacion/seleccion_facturas.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Consultar facturación
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/facturacion/resumen_impagos.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Gestión impagos
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/facturacion/resumen_alumnos.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Listado facturación
										</a>

										<b class="arrow"></b>
									</li>

									<li class="">
										<a href="/gestion_gastos/costes.php">
											<i class="menu-icon fa fa-caret-right"></i>
											Gestión de gastos
										</a>

										<b class="arrow"></b>
									</li>
								</ul>
							</li>						
						
							<li class="">
								<a href="/caja/listado_caja.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Caja
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/grupos_pruebas/ocupacion_valorada.php" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Informe ocupación valorado
								</a>

								<b class="arrow"></b>
							</li>
						</ul>
					</li>

					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-pencil-square-o"></i>
							<span class="menu-text"> Renovaciones </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="">
								<a href="/aulas/calendario_control_renovaciones.php" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Calendario de renovaciones
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/pdf/recibos_hoy.php" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Imprimir recibos de hoy
								</a>

								<b class="arrow"></b>
							</li>

						</ul>
					</li>
					
					
					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-life-ring"></i>
							<span class="menu-text"> Material </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="">
								<a href="/gestion/material/gestion_material.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Reservas
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/material/control_entregas.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Entregas de material
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/material/ventas_material.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Resumen
								</a>

								<b class="arrow"></b>
							</li>
							
							<li class="">
								<a href="/gestion/material/ventas_material_viejosynuevos.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Resumen Niños/niñas
								</a>

								<b class="arrow"></b>
							</li>							

							<li class="">
								<a href="/gestion/material/ventas_material_comparado.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Ventas material por curso
								</a>

								<b class="arrow"></b>
							</li>

						</ul>
					</li>					

					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-calendar"></i>
							<span class="menu-text"> Grupos </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="">
								<a href="/gestion/centros/gestion_aulas.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Listado alumnos por niveles
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/grupos_pruebas/" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Horario Grupos
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/grupos_lapices_nuevo/" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Horario Grupos Guarderías
								</a>

								<b class="arrow"></b>
							</li>
							
							<li class="">
								<a href="/gestion/grupos_externos/" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Horario Grupos Externos
								</a>

								<b class="arrow"></b>
							</li>							

							<li class="">
								<a href="/aulas/calendario_control.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Asistencia
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/grupos_pruebas/ocupacion.php" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Informe ocupación
								</a>

								<b class="arrow"></b>
							</li>
							
							<li class="">
								<a href="/herramientas/enviar_mensaje.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Mensaje padres
								</a>

								<b class="arrow"></b>
							</li>	
							
							<li class="">
									<i class="menu-icon fa fa-caret-right"></i>
									Mensaje interno
								</a>

								<b class="arrow"></b>
							</li>														

						</ul>
					</li>	
					
					<li class="">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-users"></i>
							<span class="menu-text"> Personal </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="">
								<a href="/gestion/docentes/listado_docentes.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Gestión de personal
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/calendario/calendario_laboral.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Calendario Laboral
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="/gestion/calendario/calendario_ausencias.php">
									<i class="menu-icon fa fa-caret-right"></i>
									Incidencias personal
								</a>

								<b class="arrow"></b>
							</li>
							
							<li class="">
								<a href="/gestion/docentes/seleccion_fecha_horas.php" target="_blank">
									<i class="menu-icon fa fa-caret-right"></i>
									Control del personal
								</a>

								<b class="arrow"></b>
							</li>							

						</ul>
					</li>					

				</ul><!-- /.nav-list -->

				<!-- #section:basics/sidebar.layout.minimize -->
				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

				<!-- /section:basics/sidebar.layout.minimize -->
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<!-- /section:basics/sidebar -->
			<div class="main-content">
				<div class="main-content-inner">
					<!-- #section:basics/content.breadcrumbs -->
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="#">Inicio</a>
							</li>
							<li class="active"><?=$titulo_web?></li>
						</ul><!-- /.breadcrumb -->

						<!-- #section:basics/content.searchbox -->
						<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Buscar ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
							</form>
						</div><!-- /.nav-search -->

						<!-- /section:basics/content.searchbox -->
					</div>

					<!-- /section:basics/content.breadcrumbs -->
					<div class="page-content">

						<div class="page-header">
							<h1>
								<?=$titulo_web?>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							

						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<div class="footer">
				<div class="footer-inner">
					<!-- #section:basics/footer -->
					<div class="footer-content">

					</div>

					<!-- /section:basics/footer -->
				</div>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='/dashboard/assets/js/jquery.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='/dashboard/assets/js/jquery1x.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='/dashboard/assets/js/jquery.mobile.custom.js'>"+"<"+"/script>");
		</script>
		<script src="/dashboard/assets/js/bootstrap.js"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="/dashboard/assets/js/excanvas.js"></script>
		<![endif]-->
		<script src="/dashboard/assets/js/jquery-ui.custom.js"></script>
		<script src="/dashboard/assets/js/jquery.ui.touch-punch.js"></script>
		<script src="/dashboard/assets/js/jquery.easypiechart.js"></script>
		<script src="/dashboard/assets/js/jquery.sparkline.js"></script>
		<script src="/dashboard/assets/js/flot/jquery.flot.js"></script>
		<script src="/dashboard/assets/js/flot/jquery.flot.pie.js"></script>
		<script src="/dashboard/assets/js/flot/jquery.flot.resize.js"></script>

		<!-- ace scripts -->
		<script src="/dashboard/assets/js/ace/elements.scroller.js"></script>
		<script src="/dashboard/assets/js/ace/elements.colorpicker.js"></script>
		<script src="/dashboard/assets/js/ace/elements.fileinput.js"></script>
		<script src="/dashboard/assets/js/ace/elements.typeahead.js"></script>
		<script src="/dashboard/assets/js/ace/elements.wysiwyg.js"></script>
		<script src="/dashboard/assets/js/ace/elements.spinner.js"></script>
		<script src="/dashboard/assets/js/ace/elements.treeview.js"></script>
		<script src="/dashboard/assets/js/ace/elements.wizard.js"></script>
		<script src="/dashboard/assets/js/ace/elements.aside.js"></script>
		<script src="/dashboard/assets/js/ace/ace.js"></script>
		<script src="/dashboard/assets/js/ace/ace.ajax-content.js"></script>
		<script src="/dashboard/assets/js/ace/ace.touch-drag.js"></script>
		<script src="/dashboard/assets/js/ace/ace.sidebar.js"></script>
		<script src="/dashboard/assets/js/ace/ace.sidebar-scroll-1.js"></script>
		<script src="/dashboard/assets/js/ace/ace.submenu-hover.js"></script>
		<script src="/dashboard/assets/js/ace/ace.widget-box.js"></script>
		<script src="/dashboard/assets/js/ace/ace.settings.js"></script>
		<script src="/dashboard/assets/js/ace/ace.settings-rtl.js"></script>
		<script src="/dashboard/assets/js/ace/ace.settings-skin.js"></script>
		<script src="/dashboard/assets/js/ace/ace.widget-on-reload.js"></script>
		<script src="/dashboard/assets/js/ace/ace.searchbox-autocomplete.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
				$('.easy-pie-chart.percentage').each(function(){
					var $box = $(this).closest('.infobox');
					var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
					var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
					var size = parseInt($(this).data('size')) || 50;
					$(this).easyPieChart({
						barColor: barColor,
						trackColor: trackColor,
						scaleColor: false,
						lineCap: 'butt',
						lineWidth: parseInt(size/10),
						animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
						size: size
					});
				})
			
				$('.sparkline').each(function(){
					var $box = $(this).closest('.infobox');
					var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
					$(this).sparkline('html',
									 {
										tagValuesAttribute:'data-values',
										type: 'bar',
										barColor: barColor ,
										chartRangeMin:$(this).data('min') || 0
									 });
				});
			
			
			  //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
			  //but sometimes it brings up errors with normal resize event handlers
			  $.resize.throttleWindow = false;
			
			  var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
			  var data = [
				{ label: "social networks",  data: 38.7, color: "#68BC31"},
				{ label: "search engines",  data: 24.5, color: "#2091CF"},
				{ label: "ad campaigns",  data: 8.2, color: "#AF4E96"},
				{ label: "direct traffic",  data: 18.6, color: "#DA5430"},
				{ label: "other",  data: 10, color: "#FEE074"}
			  ]
			  function drawPieChart(placeholder, data, position) {
			 	  $.plot(placeholder, data, {
					series: {
						pie: {
							show: true,
							tilt:0.8,
							highlight: {
								opacity: 0.25
							},
							stroke: {
								color: '#fff',
								width: 2
							},
							startAngle: 2
						}
					},
					legend: {
						show: true,
						position: position || "ne", 
						labelBoxBorderColor: null,
						margin:[-30,15]
					}
					,
					grid: {
						hoverable: true,
						clickable: true
					}
				 })
			 }
			 drawPieChart(placeholder, data);
			
			 /**
			 we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
			 so that's not needed actually.
			 */
			 placeholder.data('chart', data);
			 placeholder.data('draw', drawPieChart);
			
			
			  //pie chart tooltip example
			  var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
			  var previousPoint = null;
			
			  placeholder.on('plothover', function (event, pos, item) {
				if(item) {
					if (previousPoint != item.seriesIndex) {
						previousPoint = item.seriesIndex;
						var tip = item.series['label'] + " : " + item.series['percent']+'%';
						$tooltip.show().children(0).text(tip);
					}
					$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
				} else {
					$tooltip.hide();
					previousPoint = null;
				}
				
			 });
			
				/////////////////////////////////////
				$(document).one('ajaxloadstart.page', function(e) {
					$tooltip.remove();
				});
			
			
			
			
				var d1 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.5) {
					d1.push([i, Math.sin(i)]);
				}
			
				var d2 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.5) {
					d2.push([i, Math.cos(i)]);
				}
			
				var d3 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.2) {
					d3.push([i, Math.tan(i)]);
				}
				
			
				var sales_charts = $('#sales-charts').css({'width':'100%' , 'height':'220px'});
				$.plot("#sales-charts", [
					{ label: "Domains", data: d1 },
					{ label: "Hosting", data: d2 },
					{ label: "Services", data: d3 }
				], {
					hoverable: true,
					shadowSize: 0,
					series: {
						lines: { show: true },
						points: { show: true }
					},
					xaxis: {
						tickLength: 0
					},
					yaxis: {
						ticks: 10,
						min: -2,
						max: 2,
						tickDecimals: 3
					},
					grid: {
						backgroundColor: { colors: [ "#fff", "#fff" ] },
						borderWidth: 1,
						borderColor:'#555'
					}
				});
			
			
				$('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('.tab-content')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
			
			
				$('.dialogs,.comments').ace_scroll({
					size: 300
			    });
				
				
				//Android's default browser somehow is confused when tapping on label which will lead to dragging the task
				//so disable dragging when clicking on label
				var agent = navigator.userAgent.toLowerCase();
				if("ontouchstart" in document && /applewebkit/.test(agent) && /android/.test(agent))
				  $('#tasks').on('touchstart', function(e){
					var li = $(e.target).closest('#tasks li');
					if(li.length == 0)return;
					var label = li.find('label.inline').get(0);
					if(label == e.target || $.contains(label, e.target)) e.stopImmediatePropagation() ;
				});
			
				$('#tasks').sortable({
					opacity:0.8,
					revert:true,
					forceHelperSize:true,
					placeholder: 'draggable-placeholder',
					forcePlaceholderSize:true,
					tolerance:'pointer',
					stop: function( event, ui ) {
						//just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
						$(ui.item).css('z-index', 'auto');
					}
					}
				);
				$('#tasks').disableSelection();
				$('#tasks input:checkbox').removeAttr('checked').on('click', function(){
					if(this.checked) $(this).closest('li').addClass('selected');
					else $(this).closest('li').removeClass('selected');
				});
			
			
				//show the dropdowns on top or bottom depending on window height and menu position
				$('#task-tab .dropdown-hover').on('mouseenter', function(e) {
					var offset = $(this).offset();
			
					var $w = $(window)
					if (offset.top > $w.scrollTop() + $w.innerHeight() - 100) 
						$(this).addClass('dropup');
					else $(this).removeClass('dropup');
				});
			
			})
		</script>

		<!-- the following scripts are used in demo only for onpage help and you don't need them -->
		<link rel="stylesheet" href="/dashboard/assets/css/ace.onpage-help.css" />
		<link rel="stylesheet" href="/dashboard/docs/assets/js/themes/sunburst.css" />

		<script type="text/javascript"> ace.vars['base'] = '..'; </script>
		<script src="/dashboard/assets/js/ace/elements.onpage-help.js"></script>
		<script src="/dashboard/assets/js/ace/ace.onpage-help.js"></script>
		<script src="/dashboard/docs/assets/js/rainbow.js"></script>
		<script src="/dashboard/docs/assets/js/language/generic.js"></script>
		<script src="/dashboard/docs/assets/js/language/html.js"></script>
		<script src="/dashboard/docs/assets/js/language/css.js"></script>
		<script src="/dashboard/docs/assets/js/language/javascript.js"></script>
	</body>
</html>
