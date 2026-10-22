<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Evaluación trimestral
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
		<li><a href="/app/documentos.php"><i class="fa fa-file-o"></i> Documentos y Evaluaciones</a></li>		
		<li class="active"><i class="fa fa-file-o"></i> Evaluación trimestral</li>		
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
if ((isMobile()) and ($comprobarMovil==true)) {
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alertaMoviles">
	<div class="row">
		<div class="col-xs-10">
			<img src="/app/imagenes/rotar_movil_blanco.png" width="48" border=0 class="pull-right">
			<strong>¿Estás usando un movil?</strong><br>Esta tabla se ve mejor con el movil en posición horizontal...	
		</div>
		<div class="col-xs-2">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		</div>
	</div>    
</div>
<?php	
}	
?> 
<?php 
           
if ($errores=="") {

    $id_alumno=$_GET["id_alumno"];

    // Comprueba si el padre es el padre…
    $id_alumno_cliente=$id_alumno;
    
    $consulta_datos_del_alumno=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno_cliente));
    if (db_num_rows($consulta_datos_del_alumno)!=0) {
            $datos_del_alumno=db_fetch_array($consulta_datos_del_alumno);
            $id_usuario_alumno=$datos_del_alumno["id_usuario"];
            }
                                                    
    $nombre_usuario_padre=$_SESSION['nombre_padre'];
    
    // Busca usuario
    $busca_usuario_padre=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario))) or die(db_error());
            
    if (db_num_rows($busca_usuario_padre)==0) {                
                    $id_usuario_padre=0;
                    } else {
                    $datos_usuario_padre=db_fetch_array($busca_usuario_padre);
                    $id_usuario_padre=$datos_usuario_padre["id_usuario"];
                    }
    
    $busca_hijo_de_padre=db_query_params('SELECT * FROM padres WHERE id_usuario=? AND padre_de=?', 'ss', array($id_usuario_padre, $id_usuario_alumno)) or die(db_error());
    
    if (db_num_rows($busca_hijo_de_padre)!=0) {
    
    $trimestre=$_GET["trimestre"];
    $id_aula=$_GET["id_aula"];
    
    $trimestre_numeral="er";
    
    if ($trimestre==2) {
            $trimestre_numeral="º";
            }
    
    // Busca si el trimestre es acceisble
    
    $busca_evaluaciones=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado WHERE trimestre=$trimestre AND id_aula=".$id_aula." AND estado=1");
    
    if (db_num_rows($busca_evaluaciones)==0) {
            die("<br /><br /><br /><br /><br /><p align=center><font face=Arial size=2>El informe de evaluación solicitado no está disponible</font></p>");
            }
    
    $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
    
    $nombre_teacher="";
    
    if (db_num_rows($busca_aulas)!=0){
            $datos_aula=db_fetch_array($busca_aulas);
    
            // Datos
            $nombre_aula=$datos_aula["nombre"];
            $id_centro_aula=$datos_aula["id_centro"];
            $id_curso_aula=$datos_aula["id_curso"];
            
            $evaluacion_por_edades=$datos_aula["evaluacion_por_edades"];
            $edad_ini_aula=$datos_aula["edad_minima_prog"];  
            $edad_fin_aula=$datos_aula["edad_maxima_prog"];        
                    
            $ciclo_aula=$datos_aula["ciclo"];
            if ($ciclo_aula==0) {
                    $ciclo_aula=1;
                    }
                    
            // Busca educadora
            $busca_actual=db_query_params('SELECT * FROM personal_historial WHERE id_aula=? AND fecha_inicio<=? AND fecha_fin>=?;', 'sss', array($id_aula, (string)(date("Y-m-d")), (string)(date("Y-m-d"))));
            
            $id_educadora=0;
            if (db_num_rows($busca_actual)!=0) {        
                    $datos_actual=db_fetch_array($busca_actual);
                    $id_educadora=$datos_actual["id_empleado"];
                    }
            
            
            // Busca nombre educadora
                    $busca_nombre_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_educadora));
                    
                    if (db_num_rows($busca_nombre_educadora)!=0) {
                            $datos_educadora=db_fetch_array($busca_nombre_educadora);
                            
                            $nombre_teacher=strtoupper($datos_educadora["Nombre"]);
                            }
                            
            // Busca datos curso
                    $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_aula)) or die(db_error());
                    
                    if (db_num_rows($busca_curso)!=0){
                        
                        $datos_curso=db_fetch_array($busca_curso);
    
                        $fecha_inicio_curso=$datos_curso["fecha_inicio"];
    
                        $fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
                        $fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];               
            
                        $mes_inicio_trimestre=date("n",strtotime($fecha_ini));  
                        $mes_fin_trimestre=date("n",strtotime($fecha_fin));                   
                    
                    }
            
            }
    
                                    // Busca objetivos trabajados
                                    $contador_areas=0;
                                    $contador_total=0;
                                    $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                    for ($c_areas=0;$c_areas<count($areas);$c_areas++) {
    
                                    $array_objetivos_area[$c_areas]=array();
                                    $contador_array_obj=0;
    
                                    if ($edad_ini_aula==0) {
                                                    // Bebes, solo lo trabajado
                                                    $busca_objetivos=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha>=? AND fecha<=? ORDER BY objetivo;', 'sss', array($id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
                                            } else {
                                                    // Resto, lo programado en el trimestre
                                                    // $busca_objetivos=mysql_query("SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre." ORDER BY id_objetivo;;") or die(mysql_error());
                                                    $consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND ((mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre.") OR (mes=0)) AND editorial=0 ORDER BY id_objetivo;";
                                                    // Comprueba si la programación debe ser EDEBE
                                                    $nueva_programacion_activa=true;
                                                    if ((strtotime($fecha_inicio_curso)<=strtotime("2018-08-31")) and (strtotime($fecha_inicio_curso)>=strtotime("2017-09-01")) and (($edad_ini_aula==1) or ($edad_ini_aula==2)) and ($nueva_programacion_activa==true)) {
                                                        
                                                        // 1 AÑO
                                                        
                                                        
                                                        // Trimestre 1
                                                        if ($trimestre==1) {
                                                            $filtro_meses_programacion="mes=1";	
                                                        }
                                                        
                                                        // Trimestre 2
                                                        if ($trimestre==2) {
                                                            $filtro_meses_programacion="mes=2 OR mes=3";	
                                                        }
                                                        
                                                        // Trimestre 3													
                                                        if ($trimestre==3) {
                                                            $filtro_meses_programacion="mes=4 OR mes=5";
                                                        }
                                                                                
                                                        if ($edad_ini_aula==2) {
                                                            
                                                            // 2 AÑOS
                                                            
                                                            if ($trimestre==1) {
                                                                $filtro_meses_programacion="mes=1 OR mes=2";
                                                            }
                                                            if ($trimestre==2) {
                                                                $filtro_meses_programacion="mes=3 OR mes=4";
                                                            }
                                                            if ($trimestre==3) {
                                                                $filtro_meses_programacion="mes=5 OR mes=6";
                                                            }								
                                                            
                                                        } 														
                                                                                                        
                                                        $consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>=$edad_ini_aula AND edad<=$edad_fin_aula AND (".$filtro_meses_programacion." OR mes=0) AND editorial=1 ORDER BY id_objetivo";
                                                    }
                                                    
                                                    if ((strtotime($fecha_inicio_curso)<=strtotime("2019-08-31")) and (strtotime($fecha_inicio_curso)>=strtotime("2018-09-01")) and ($edad_ini_aula<3)) {
                                                        $editorialProgramacion=2;
                                                        
                                                        if ($trimestre==3) {
                                                            $consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre>1 ORDER BY agenda_programacion_objetivos.id_objetivo";
                                                        } else {
                                                            $consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre=".$trimestre." ORDER BY agenda_programacion_objetivos.id_objetivo";
                                                        }
                                                    }
                                                    
                                                    $busca_objetivos=db_query($consulta_busca_objetivos) or die(db_error());  
                                            }
    
                                                                    while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
    
                                                                            // Objetivo principal
                                                                            if ($edad_ini_aula==0) {
                                                                                    // Bebes
                                                                                    $id_objetivo1=$cuenta_objetivos["objetivo"];
                                                                                    } else {
                                                                                    // Resto
                                                                                    $id_objetivo1=$cuenta_objetivos["id_objetivo"];
                                                                                    }
    
                                                                            // Busca area
                                                                            $busca_datos_objetivo1=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo1));
                                        $datos_objetivos1=db_fetch_array($busca_datos_objetivo1);
    
                                        if ($datos_objetivos1["id_area"]==($c_areas+1)) {
    
                                                                                    if (in_array($id_objetivo1, $array_objetivos_area[$c_areas])==false) {
                                                                                            $array_objetivos_area[$c_areas][$contador_array_obj]=$id_objetivo1;
                                                                                            $contador_array_obj++;
                                                                                            $contador_total++;
                                                                                            }
    
                                                                                    }
    
                                                                            // Objetivos secundarios
                                                                            $objetivos_secundarios=$cuenta_objetivos["objetivos"];
                                                                            if (strlen($objetivos_secundarios)!=0) {
                                                                                    $objetivos_secundarios=str_replace("[","",$objetivos_secundarios);
                                                                                    $objetivos_secundarios=str_replace("]","",$objetivos_secundarios);
                                                                                    $array_objetivos_sec=explode(",",$objetivos_secundarios);
    
                                                                                    for ($contador_objetivos_sec=0;$contador_objetivos_sec<count($array_objetivos_sec);$contador_objetivos_sec++){
                                                                                    // Busca area
                                                                                    $busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
                                                $datos_objetivos2=db_fetch_array($busca_datos_objetivo2);
    
                                                if ($datos_objetivos2["id_area"]==($c_areas+1)) {
                                                                                                    if (in_array($array_objetivos_sec[$contador_objetivos_sec], $array_objetivos_area[$c_areas])==false) {
                                                                                                            $array_objetivos_area[$c_areas][$contador_array_obj]=$array_objetivos_sec[$contador_objetivos_sec];
                                                                                                            $contador_array_obj++;
                                                                                                            $contador_total++;
                                                                                                            }
    
                                                                                                    }
                                                                                            }
    
                                                                                    }
    
    
                                                                            }
                                            }
    
    if ($contador_total!=0) {
    
    $busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? AND alumnos.id_alumno=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($id_aula, (string)(date("Y-m-t", strtotime($fecha_fin))), (string)(date("Y-m-01", strtotime($fecha_ini))), $id_alumno)) or die(db_error());
    
    if (db_num_rows($busca_alumnos)!=0) {
    
            while($listado_alumnos=db_fetch_array($busca_alumnos)){
    
                switch ($trimestre) {
                    case 1:
                        $grupo_actual=$listado_alumnos["agenda_grupo"];
                        break;
                    case 2:
                        $grupo_actual=$listado_alumnos["agenda_2_grupo"];
                           break;
                    case 3:
                        $grupo_actual=$listado_alumnos["agenda_3_grupo"];
                        break;
                    }
    
                            $busca_estado_evaluacion_guardada_alumno=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre;") or die(db_error());                
    
                            $datos_evaluacion_guardada_alumno=db_fetch_array($busca_estado_evaluacion_guardada_alumno);
    
                            echo("<h3>EVALUACION ".$trimestre."er. TRIMESTRE</h2>");    
                            echo("<h3>$nombre_aula<h2>");    
                            echo("<h4><b>NOMBRE:</b>   ".security_escape($listado_alumnos["Apellido1"])." ".security_escape($listado_alumnos["Apellido2"])." ".security_escape($listado_alumnos["Nombre"])."<br /><b>EDUCADORA:</b>   ".$nombre_teacher."</h3>");
                            
                            if (strlen($datos_evaluacion_guardada_alumno["observaciones"])!=0) {
                                echo("<p><b>Observaciones:</b> ".utf8_decode($datos_evaluacion_guardada_alumno["observaciones"])."</p>");
                            }                            

                            echo("<p class='text-center'><b>Leyenda:</b> <font color='#00cc00'>S</font> = SUPERADO <font color='#cc0000'>P</font> = EN PROGRESO  NT = NO TRABAJADO</p>");

                            echo('<div class="row" style="border-bottom:1px solid #ccc;"><div class="col-md-12"><span class="pull-right"><b>Progreso</b></span><b>Objetivo</b></div></div>');                                                                                           

                            // Limpia tabla temp
                            $limpia_tabla=db_query_params('DELETE FROM agenda_programacion_objetivos_temp WHERE id_alumno=?', 's', array($listado_alumnos["id_alumno"]));
                            
                            for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);

                                if ($edad_ini_aula==0) {
                                
                                    echo('<div class="row" style="border-bottom:1px solid #ccc;"><div class="col-md-12"><span class="pull-right">'.$calificacion.'</span>'.$areas[$c_areas].'</div></div>');                                                                                           

                                }

                                for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                    $id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];

                                    $busca_datos_objetivo=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
                                    $datos_objetivos=db_fetch_array($busca_datos_objetivo);
                            
                                    // Comprueba tipo de evaluación y procede según
                                    $mostrar_objetivo=true;
                                    if ($evaluacion_por_edades==1) {
                                        
                                        $edad_alumno_anyos=date("Y",strtotime($fecha_inicio_curso)) - date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));                            
                                        
                                        if ($edad_alumno_anyos==$datos_objetivos["edad"]) {
                                            $mostrar_objetivo=true;
                                        } else {
                                            $mostrar_objetivo=false;
                                        }
                                                                                                                        
                                    }
                            
                                    if ($mostrar_objetivo==true) {
    
                                               $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
    
                                               $num_actividades_programadas=0;
                                               $num_actividades_trabajadas=0;
                                               $num_actividades_superadas=0;
    
                                               // Busca actividades trabajadas por este niño
                                               if ($edad_ini_aula==0) {
                                                    // Bebes, solo las de su grupo
                                                    $busca_actividades=db_query("SELECT * FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND (id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."') AND edad=".($grupo_actual-1)." ORDER BY id_actividad ") or die(db_error());
                                               } else {
                                                    // Resto de edades, Todas
                                                    $busca_actividades=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
                                                    
                                                    $busca_actividades_curso=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_inicio_curso), (string)($fecha_fin))) or die(db_error()); 
                                               }
    
                                                $objetivo_no_programado = "";
                                                $objetivo_no_programado_curso = false;
                                                if (db_num_rows($busca_actividades)==0) {
                                                        $objetivo_no_programado = "No programado";
                                                }

                                                if ($edad_ini_aula!=0) {
                                                        if (db_num_rows($busca_actividades_curso)==0) {
                                                                $objetivo_no_programado_curso = true;
                                                        }
                                                }
    
                                               while($datos_actividades=db_fetch_array($busca_actividades)) {
    
                                                       // Busca evaluación
                                                       $busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_prog=? AND id_alumno=?', 'ss', array($datos_actividades["id_prog"], $listado_alumnos["id_alumno"]));
    
                                                       if (db_num_rows($busca_evaluacion)!=0) {
    
                                                            $datos_evaluacion=db_fetch_array($busca_evaluacion);
    
                                                            $num_actividades_trabajadas=$num_actividades_trabajadas+$datos_evaluacion["asistencia"];
                                                            $num_actividades_superadas=$num_actividades_superadas+$datos_evaluacion["superada"];
    
                                                            }
                                                    $num_actividades_programadas++;
                                                    }
                                                    $porcentaje_calificacion=0;
                                                    if ($num_actividades_programadas>0) {
                                                            $porcentaje_calificacion=round(($num_actividades_superadas/$num_actividades_programadas)*100,2);
                                                            }
    
                                                    if ($porcentaje_calificacion>=$porcentaje_autoevaluacion) {
                                                            $calificacion="<font color='#00cc00'>S</font>";
                                                            $superado=1;
                                                            } else {
                                                            $calificacion="<font color='#cc0000'>P</font>";
                                                            $superado=0;
                                                            }
                                                    // Inserta en tabla temp
                                                    if ($datos_objetivos["id_evaluacion"]!=0) {
                                                    
                                                             // Si son bebes, solo lo programado
                                                            $insertar=true;
                                                                   if (($edad_ini_aula==0) and ($objetivo_no_programado!="") and ($num_actividades_trabajadas==0)) {
                                                                     $insertar=false;
                                                                }
    
                                                                // Si no son bebes, es un objetivo independiente de edad, solo si programado 
                                                                if (($datos_objetivos["mes"]==0) and ($objetivo_no_programado_curso==true)) {
                                                                $insertar=false;
                                                                              }
    
                                                                   if ($insertar==true) {
                                                                      $inserta_fila=db_query_params('INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area) VALUES (?,?,?,?,?);', 'sssss', array((string)($id_objetivo), (string)($datos_objetivos["id_evaluacion"]), (string)($listado_alumnos["id_alumno"]), (string)($superado), (string)($c_areas)));
                                                                     }
                                                              }
                                                        } // Fin mostrar objetivo
                                                    }
    
                                               // Busca y muestra
                                               $busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados FROM agenda_programacion_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas GROUP BY id_evaluacion ORDER BY id_evaluacion ");
                                               while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
                                                    if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
                                                            $calificacion=" S";
                                                            } else {
                                                            $calificacion=" P";
                                                            }
                                                            // Busca datos envaluacion
                                                            $busca_evaluacion=db_query_params('SELECT * FROM agenda_evaluacion_items WHERE id_evaluacion=?', 's', array($datos_evaluaciones["id_evaluacion"])) or die(db_error());
                                                            $datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                            // Busca envaluacion guardada, si no está la crea
                                                            
                                                            $busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");
                                                            
                                                            if (db_num_rows($busca_evaluacion_guardada)!=0) {
                                                                    $datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                                                                    $id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                                                                    $valor_calificacion=$datos_evaluacion_guardada["nota"];
                                                                    
                                                                    if ($valor_calificacion==1) {
                                                                            $calificacion=" <font color='#00cc00'>S</font>";
                                                                            }
                                                                    if ($valor_calificacion==0) {
                                                                            $calificacion=" <font color='#cc0000'>P</font>";
                                                                            } 
                                                                    if ($valor_calificacion==2) {
                                                                            $calificacion=" NT";
                                                                            }
                                                                    
                                                                    }
    
                    // Comprueba si debe mostrarse (Si editorial==2 y trimestre=3 y calificacion=NT no se muestra)
                    
                    $mostrarEste=true;
                    
                    if (($editorialProgramacion==2) and ($trimestre==3) and ($calificacion==" NT")) {
                        $mostrarEste=false;	
                    }
                    
                    if ($calificacion==" NT") {
                        $mostrarEste=false;	
                    }
                    
                                    
                    if ($mostrarEste==true) { 
                            echo('<div class="row" style="border-bottom:1px solid #ccc;"><div class="col-md-12"><span class="pull-right">'.$calificacion.'</span>'.$datos_evaluacion["descripcion"].'<br>'.$datos_evaluacion["descripcion_esp"].'</div></div>');                                                                                                               
                            } // Fin mostrarEste
                    
                        } // Fin bucle
                    
                    }                
    
                }
            }
        }

    
    } else {
    // No es el padre de este niño
    ?>
    <p align="center"><br><br><font face="Arial" size="2">No tiene autorización para acceder a la información de este alumno</font><br><br></p>
    <?php 
    }

} // FIN if ($errores=="")

?>

      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>