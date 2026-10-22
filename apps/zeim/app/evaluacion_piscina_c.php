<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}

$trimestres=htmlspecialchars((string)app_value($_GET,'trimestres'),ENT_QUOTES,'Windows-1252');
$nombre_curso=htmlspecialchars((string)app_value($_GET,'nombre_curso'),ENT_QUOTES,'Windows-1252');
$periodo=(int)app_value($_GET,'periodo',1);
$trimestre=(int)app_value($_GET,'trimestre',1);

?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		VALORACIÓN CURSO NATACIÓN EDUCATIVA
		        <small>Curso: <?=$nombre_curso;?>, Perido: <?=$trimestres?></small>

      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php

$uid=$_GET["uid"];
	
if ($uid) {
    if (!is_string($uid)||!preg_match('/^\d{14}$/D',$uid)) family_forbidden();
	
	$numero_digitos_alumno=intval(substr($uid,12,1));
	$numero_digitos_codigo=intval(substr($uid,13,1));
	$id_evaluacion=intval(substr($uid,0,5));
	$id_evaluacion=substr($id_evaluacion,strlen($id_evaluacion)-$numero_digitos_codigo,$numero_digitos_codigo);
	$id_alumno=substr($uid,5,5);
	$id_alumno=substr($id_alumno,strlen($id_alumno)-$numero_digitos_alumno,$numero_digitos_alumno);
	$id_curso=intval(substr($uid,10,2));		
	
	$owned=app_rows('SELECT a.id_alumno FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE a.id_alumno=? AND p.id_usuario=? AND COALESCE(p.denegar_acceso_agenda,0)=0','ii',array((int)$id_alumno,$id_usuario));
    if (!$owned) family_forbidden();
    $id_alumno=(int)$id_alumno;$id_evaluacion=(int)$id_evaluacion;
	$origen_alumnos=0;	
	$mostrar_logo=1;
	
	// Valida el código
	$busca_codigo=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_estado=? AND id_curso=? AND id_alumno=?', 'sss', array($id_evaluacion, $id_curso, $id_alumno)) or die("El código introducido es incorrecto");
	if (db_num_rows($busca_codigo)==0) {
		echo("El código introducido es incorrecto");exit();
	} else {
	// Inroduce registro
	db_query_params('INSERT INTO registro_lectura_evaluaciones (id_alumno,REMOTE_ADDR,HTTP_ACCEPT_LANGUAGE,HTTP_User_Agent,HTTP_REFERER,uid) VALUES (?,?,?,?,?,?);', 'ssssss', array((string)($id_alumno), (string)($REMOTE_ADDR), (string)($HTTP_ACCEPT_LANGUAGE), (string)($HTTP_USER_AGENT), (string)($_SERVER["HTTP_REFERER"]), (string)($uid)));
	
	}
	
	} else {

	$lista_alumnos=$_GET["id_alumno"]; 
	$mostrar_logo=$_GET["logo"];

	// Comprueba si el padre es el padre…
	$id_alumno_cliente=$lista_alumnos;
	
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
	
	if (db_num_rows($busca_hijo_de_padre)==0) {
		exit("Error: No tienes permiso para ver la evaluación de este alumno.");
	}


	if (strlen($mostrar_logo)==0) {
		$mostrar_logo=1;
	}

	$origen_alumnos=0;
	if (substr($lista_alumnos,0,1)=="L") {
        $origen_alumnos=1;
        $lista_alumnos=str_replace("L","",$lista_alumnos);                
	}
        
	$lista_alumnos_array=explode(",",$lista_alumnos);
	if (count($lista_alumnos_array)==1) {
        $id_alumno=$lista_alumnos_array[0];
	}
        
	if (strlen($id_alumno)==0) {
        $id_alumno=$_GET["id_alumno"];
	}

}

if ((strlen($id_alumno)==0) and (strlen($lista_alumnos)==0)) {

echo("El código introducido es incorrecto");exit();

}

$base_datos_alumnos="";
$campo_periodo="periodo";
$valor_campo_periodo=$periodo;
$tabla_grupos_alumnos="aulas_alumnos_grupos";
if ($origen_alumnos==1) {
        $base_datos_alumnos="gestionguarderias.";
        $campo_periodo="trimestre";
        $valor_campo_periodo=$trimestre;
        $tabla_grupos_alumnos="aulas_alumnos_lapices_grupos";
}

// Busca objetivos generales
$nombre_objetivos_generales=array();
$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
                 
while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
        $nombre_objetivos_generales[$objetivos_generales["id_objetivo"]]=$objetivos_generales["nombre_objetivo"];
}

// error_reporting(7);

if ($id_alumno) {
        // Uno solo
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=".$id_alumno.";") or die(db_error()."-1");
} else {             
        // Muchos   
        $filtro_alumnos="";
        foreach ($lista_alumnos_array as $valor) {
            $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";                
        }
        if (strlen($filtro_alumnos)!=0) {
                $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-2");                           
}

$contador_alumnos=0;
$total_alumno=db_num_rows($busca_datos_alumno);

if (db_num_rows($busca_datos_alumno)!=0) {

        while($listado_alumnos=db_fetch_array($busca_datos_alumno)){
                
                $nombre_alumno=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
                
                if ($origen_alumnos==0) {
                        // $anotacion_nivel=" en el nivel ".$nivel_actual;
                }
                
                $id_alumno=$listado_alumnos["id_alumno"];
                                
                // Busca envaluacion guardada
                                                                
                $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=$id_alumno AND $campo_periodo=$valor_campo_periodo;") or die(db_error());                

                $datos_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
                
                if ((($datos_evaluacion_guardada["estado_t1"]+$datos_evaluacion_guardada["estado_t2"]+$datos_evaluacion_guardada["estado_t3"])>0) or (($datos_evaluacion_guardada["estado_p1"]+$datos_evaluacion_guardada["estado_p2"]+$datos_evaluacion_guardada["estado_p3"]+$datos_evaluacion_guardada["estado_p4"])>0)) {
        
                        if ($contador_alumnos>0) {
                                $pdf->ezNewPage();
                        }                
                        
                        $contador_alumnos++;                        
                        
                        $id_aula=$listado_alumnos["aula"];
                        $id_curso_aula=$listado_alumnos["id_curso"];
                        $nivel_actual=$listado_alumnos["nivel_actual"];


                        // Fecha de incorporación
                        $fecha_incorporacion=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));
                        if (strtotime($listado_alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                                $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]))."</span>";
            }
                        $fecha_baja=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));
                        if (strtotime($listado_alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                    $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))."</span>";
                }
                        if ($listado_alumnos["nonato"]==1) {
                    $edad_alumno="";
                                $fecha_nac_alumno="No ha nacido";
                } else {
                                $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                        $edad_alumno=$edad_alumno-1;
                }
                                if ($edad_alumno<2) {
                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos["fecha_nacimiento"];
                                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                        // 1.1. Cacula edad con decimales
                                        $mes_d_l=date("m",strtotime($fecha_l));
                                        $mes_d_n=date("m");
                                        $dif_meses=$mes_d_n-$mes_d_l;

                                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                                        $edad_alumno=$edad_d_l." meses";
                    } else {
                        $edad_alumno=$edad_alumno." años";
                    }
                                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                }
                
                        $lista_grupos_alumno="";
        
                        $busca_alumnos_grupo=db_query("SELECT * FROM ".$base_datos_alumnos."usuarios,$tabla_grupos_alumnos,aulas_grupos,".$base_datos_alumnos."alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $tabla_grupos_alumnos.id_alumno=alumnos.id_alumno AND $tabla_grupos_alumnos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=".$id_alumno) or die(db_error());
                        $total_grupos=db_num_rows($busca_alumnos_grupo);
                        $contador_grupos=0;
                        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                                $lista_grupos_alumno.=" los ".$dia_sem_largo_plural[$grupos_donde["dia"]];
                                $contador_grupos++;
                                if ($contador_grupos<$total_grupos) {
                                        if ($contador_grupos<($total_grupos-1)) {
                                                $lista_grupos_alumno.=", ";                                        
                                        } else {
                                                $lista_grupos_alumno.=" y ";
                                        }
                                }
                        }
                                       

                        $fechas_inicio_fin=$nombre_mes[date("n",strtotime($listado_alumnos["fecha_matricula"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_matricula"]))." a ".$nombre_mes[date("n",strtotime($listado_alumnos["fecha_baja"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_baja"]));

                        // Busca objetivos trabajados
                        $consulta_objetivos_trabajados=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_evaluaciones_trimestrales.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo WHERE agenda_programacion_evaluaciones_trimestrales.$campo_periodo=$valor_campo_periodo AND agenda_programacion_evaluaciones_trimestrales.id_alumno=".$id_alumno." ORDER BY agenda_programacion_objetivos_piscina.id_objetivo_g, agenda_programacion_evaluaciones_trimestrales.id_objetivo;") or die(db_error());                

                        ?>
                        <div><p style="align:justify;">
                        <?php
                        echo("<big><b>".security_escape($listado_alumnos["Nombre"])." ".security_escape($listado_alumnos["Apellido1"])." ".security_escape($listado_alumnos["Apellido2"])."</b> ha asistido al curso de natación educativa en La piscina de Neokids, ".$lista_grupos_alumno.". A lo largo de este período ha demostrado una gran capacidad de trabajo. A fecha de hoy (".date("d/m/Y").") su evolución ha sido positiva,  acorde a su edad y a su nivel, hasta superar los objetivos establecidos y que le destacamos a continuación con la letra S. Además,  durante este tiempo ha avanzado en otros muchos y que le señalamos con la letra P.</big>");
                        ?>
                        </p>
                        <table class="table table-hover table-stripped">
						<?php
						
                        $posicion_actual=$posicion_actual-68;
                                        
                        $pagina_actual=1;
                        $objetivo_general_actual=0;

						$contador_mostrados=0;

                        while($busca_objetivos_trabajados=db_fetch_array($consulta_objetivos_trabajados)) {

                                if ($busca_objetivos_trabajados["nota"]!=0) { // Solo los trabajados
                
                                        if ($busca_objetivos_trabajados["nota"]==1) {
                                                $calificacion=" S";
                                       } else {
                                                   $calificacion=" P";
                                      } 
                                                                                               
                                          if ($objetivo_general_actual!=$busca_objetivos_trabajados["id_objetivo_g"]) {
                                                  $objetivo_general_actual=$busca_objetivos_trabajados["id_objetivo_g"];
                                                  echo("<tr><td><big><b>".$nombre_objetivos_generales[$objetivo_general_actual]."</b></big></td></tr>");
		                                                  
                                        }

                                        echo("<tr><td><li>".$busca_objetivos_trabajados["nombre_objetivo"]." <span class='pull-right'>".$calificacion."</span></li></td></tr>");
										$contador_mostrados++;
										

                                } // Fin if nota!=0
                        } // Fin bucle                        

						?>
						</table>
						<?php
                        echo("<br><br><div class='text-center'><big><b>COMENTARIOS DE LA MONITORA</b></big></div><br>");                       

                        if (strlen($datos_evaluacion_guardada["observaciones"])!=0) {
                        	echo("<p style='text-align:justify'><big>".utf8_decode($datos_evaluacion_guardada["observaciones"])."</big><p>");
                        }

                        echo("<big>Desde Neokids le animamos a seguir practicando la natación educativa y le agradecemos la confianza depositada en nosotros todo este tiempo.</big><br><br>


Valencia, ".date("j")." de ".$nombre_mes[date("n")]." de ".date("Y"));

                } // Fin if evaluacion guardada estado=1
        }        // Fin bucle alumnos
}        // Fin if hay alumnos


?>
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php

require("parte_inferior_padres.php");
	
?>