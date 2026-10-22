<?php 

function conversor_segundos($seg_ini,$formato) {

        $horas = floor($seg_ini/3600);
         $minutos = floor(($seg_ini-($horas*3600))/60);
         $segundos = $seg_ini-($horas*3600)-($minutos*60);
         $minus="00".$minutos;         
         
         if ($formato==0) {                
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m:'.'s';
                 } else {        
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m';
                 }
         
         return $resultado;

        }

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_alumno.".jpg";

if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="nada.gif";
      }
      


        // Asistencia
                $ficha_tarde=0;
                
                $hora_entrada_m="";
                $hora_salida_m="";
                $hora_entrada_t="";
                $hora_salida_t="";
                

        $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$a AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY turno;");

        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                if ($datos_asistencia["turno"]==0) {
						$hora_entrada_m=date("H:i",strtotime($datos_asistencia["hora_entrada"]));
						$hora_salida_m=date("H:i",strtotime($datos_asistencia["hora_salida"]));
                        } else {
                        // Tarde
                        $ficha_tarde=1;
						$hora_entrada_t=date("H:i",strtotime($datos_asistencia["hora_entrada"]));
						$hora_salida_t=date("H:i",strtotime($datos_asistencia["hora_salida"]));
                        }
                 }      

?>
        <table width=100% border=0 cellpadding=0 cellspacing=0 align="center">
        <!-- encabezado -->
                <tr>
                <td colspan="2" align="center"><table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                <td><img src="imagenes/logo-lapices.png" border=0 align="left" ></td>
                <td align="center"><span class="texto_encabezado"><br />Mi agenda - <?=date("j",$fecha_time_stamp);?> de <?=$nombre_mes[date("n",$fecha_time_stamp)];?> de <?=date("Y",$fecha_time_stamp);?></span><br><span class="subtitulo_encabezado"><?=$nombre_completo_alumno;?></span></td>
                <td><img src="/fotos_usuarios/<?=$nombre_foto?>" border=1 align="right" height="120"></td>
                </tr></table></td>
                </tr>
                <!-- Horario de entrada y salida -->
                <tr>
                <td colspan="2"><table width=100% border=0 cellpaddin=2 cellspacing=2 width=100%>
                <tr class="encabezado_horario">
                	<td width=50%>Hora de Entrada</td>
                	<td width=50%>Hora de Salida</td>
                </tr>
                <tr class="filas_horario">
                <td><?=$hora_entrada_m?></td>
                <td><?=$hora_salida_m?></td>
                </tr>
                </table></td>
                </tr>
                <!-- Tablas -->
                <tr>                
                <!-- Programacion -->
                <td rowspan="2" valign="top">
<?php 
$coletilla_grupos="";

if (($edad_aula==0) and (strlen($grupo_alumno)!=0)) {
        // $coletilla_grupos=" AND (edad=".($grupo_alumno-1)." OR edad=0)";
        $coletilla_grupos=" AND (edad=".($grupo_alumno-1).")";
        }

// Programacion mañana
$busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE realizada=1 AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND turno=0 AND id_aula=$id_aula AND (objetivo<>0 OR actividad<>'')$coletilla_grupos ORDER BY orden_actividad");

// Programacion inglés mañana
$busca_programacion_ingles=db_query("SELECT * FROM agenda_programacion_ingles WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."' AND turno=0 AND id_aula=$id_aula AND (objetivo<>0 OR actividad<>'')$coletilla_grupos ORDER BY orden_actividad");

// Programacion Tarde
$busca_programacion_tarde=db_query("SELECT * FROM agenda_programacion WHERE realizada=1 AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND turno=1 AND id_aula=$id_aula AND (objetivo<>0 OR actividad<>'')$coletilla_grupos ORDER BY orden_actividad");

// Programacion inglés Tarde
$busca_programacion_ingles_tarde=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE fecha=? AND turno=1 AND id_aula=? AND (objetivo<>0 OR actividad<>\'\') ORDER BY orden_actividad', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), $id_aula));

$total_prog=db_num_rows($busca_programacion)+db_num_rows($busca_programacion_ingles)+db_num_rows($busca_programacion_tarde)+db_num_rows($busca_programacion_ingles_tarde)+1;

?>
<table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td width=100>&nbsp;</td>
                <td class='tabla_actividades_encabezado' width=100>¿Cuándo?</td>
                <td class='tabla_actividades_encabezado'>¿Qué y cómo?</td>
                </tr>
                <tr>
                <td rowspan="<?=$total_prog?>" class="tabla_actividades" width=100 valign="top"><img src="<?=$dominio_actual?>/webcam/agenda/imagenes/PersonajeActividades.png" border=0 width=100><br /></td>
<?php 
// Programacion mañana                                     
if (db_num_rows($busca_programacion)!=0) {
                       
                    while($programacion=db_fetch_array($busca_programacion)) {
                                                $area_actual_objetivo=$programacion["area"];
                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }
                
                    $programacion_dia="<tr class='tabla_actividades_filas'><td>Mañana</td><td>".$descripcion_actividad_actual;
                     
                    $objetivos_dia="";   
                                        // Busca nombre objetivo
                                        if ($programacion["objetivo"]!=0) {
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$programacion["objetivo"]." AND id_area=".$area_actual_objetivo);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                        }
                                                }  
                        
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]." AND id_area=".$area_actual_objetivo);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                                        if ($objetivos_dia!="") {
                                                                $programacion_dia.="<br /><b>Objetivos trabajados:</b><br>".$objetivos_dia;
                                                                }
                                       echo($programacion_dia."</td>");
                                }

                            }
                                        
// Programacion inglés mañana                                     
if (db_num_rows($busca_programacion_ingles)!=0) {
                       
                    while($programacion=db_fetch_array($busca_programacion_ingles)) {

                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }
                                        $programacion_dia="<tr class='tabla_actividades_filas'><td>Morning</td><td>".$descripcion_actividad_actual;
                       
                                        // Busca nombre objetivo
                                        if ($programacion["objetivo"]!=0) {
                                        $programacion_dia.="<br /><b>Main aims:</b><br>"; 
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$programacion["objetivo"]);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                $programacion_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                }  
                    }
                        
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $programacion_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                       echo($programacion_dia."</td>");
                                }
                        }

// Programacion Tarde                                   
if (db_num_rows($busca_programacion_tarde)!=0) {
                       
                    while($programacion=db_fetch_array($busca_programacion_tarde)) {
                                                $area_actual_objetivo=$programacion["area"];
                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }

                                        $programacion_dia="<tr class='tabla_actividades_filas'><td>Tarde</td><td>".$descripcion_actividad_actual;
                                        
                                        // Busca nombre objetivo
                                        $objetivos_dia="";  
                                        if ($programacion["objetivo"]!=0) {
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$programacion["objetivo"]." AND id_area=".$area_actual_objetivo);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                }  
                            }    
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]." AND id_area=".$area_actual_objetivo);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                                        if ($objetivos_dia!="") {
                                                                $programacion_dia.="<br /><b>Objetivos trabajados:</b><br>".$objetivos_dia;
                                                                }
                                       echo($programacion_dia."</td>");
                                }
                        }

// Programacion inglés Tarde                                    
if (db_num_rows($busca_programacion_ingles_tarde)!=0) {
                                               
                    while($programacion=db_fetch_array($busca_programacion_ingles_tarde)) {

                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }
                                    
                                        $programacion_dia="<tr class='tabla_actividades_filas'><td>Afternoon</td><td>".$descripcion_actividad_actual; 
                       
                                        // Busca nombre objetivo
                                        if ($programacion["objetivo"]!=0) {
                                        $programacion_dia.="<br /><b>Main aims:</b><br>"; 
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$programacion["objetivo"]);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                $programacion_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                } 
                                        } 
                        
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $programacion_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                       echo($programacion_dia."</td>");
                                }
                        }
// Fin programación
?></table>                                
                </td>
                <!--  Fin programación -->           
                <!-- Higiene y salud -->                
                <td valign=middle width=50%><?php 
                
                // Seguimiento
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=1 OR area=2) AND tipo_nota<>14  AND tipo_nota<>4 ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=db_num_rows($busca_incidencias);
                
                // Busca incidencias no atendidas
                $busca_incidencias_no=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=1 OR area=2) AND tipo_nota=1  AND avisar<>0 AND realizada=0', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=$num_filas_seguimiento-db_num_rows($busca_incidencias_no);
                
                if ($siesta1!=0) {
                        $num_filas_seguimiento++;
                        }
                if ($siesta2!=0) {
                        $num_filas_seguimiento++;
                        }
                ?>
                <table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td colspan=2 width=100>&nbsp;</td>


                </tr>
                <tr>
                <td rowspan="<?=$num_filas_seguimiento?>" class="tabla_higiene" width=100><img src="imagenes/PersonajeHigieneySalud.png" border=0 width=100><br /></td>
                <?php 
                $cuenta_filas=0;
                while($incidencias=db_fetch_array($busca_incidencias)) {
                $mostrar_fila=true;        
                if (($incidencias["tipo_nota"]==1) and ($incidencias["avisar"]!=0) and ($incidencias["realizada"]==0)) {
                                $mostrar_fila=false;
                                }
        if ($mostrar_fila==true) { 
                if ($cuenta_filas>0) {
                        ?>
                        <tr>
                        <?php 
                        }               
        ?>
        <td class="tabla_higiene_filas"><?=$tipos_notas_inci[$incidencias["tipo_nota"]]?></td>
        <!-- <td class="tabla_higiene_filas"><?=date("H:i",strtotime($incidencias["hora"]))?></td>-->
        <td colspan=2 class="tabla_higiene_filas"><?php  if ($incidencias["tipo_nota"]!=3) { echo(date("H:i",strtotime($incidencias["hora"])).": "); } ?><?=security_html($incidencias["nota"])?></td>
                        </tr>
        <?php 
                        $cuenta_filas++;   
                        }                
                }
        
                if ($siesta1!=0) {
                        $num_siesta="";
                        if ($siesta2!=0) {

                                }
                        ?>
                        <tr>             
                        <td class="tabla_higiene_filas"><?=$num_siesta?>Siesta</td>
                        <td class="tabla_higiene_filas" colspan=2>Ha dormido <?=conversor_segundos($siesta1*60,1);?></td>
                </tr>
                <?php 
                        $cuenta_filas++; 
                        }
                if ($siesta2!=0) {
                        $num_siesta="";
                        if ($siesta1!=0) {

                                }
                        ?>
                        <tr>             
                        <td class="tabla_higiene_filas"><?=$num_siesta?>Siesta</td>
                        <td class="tabla_higiene_filas" colspan=2>Ha dormido <?=conversor_segundos($siesta2*60,1);?></td>
                </tr>
                <?php 
                        $cuenta_filas++; 
                        }
        if ($cuenta_filas==0) {
                ?>
                <td class="tabla_higiene_filas" colspan=3>&nbsp;</td>
                <?php 
                }        
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (tipo_nota=14  OR tipo_nota=4) ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));        
        if (db_num_rows($busca_incidencias)!=0) {
        ?>
        <tr class='tabla_pie_higiene'><td colspan=4><b>Recordatorios y recomendaciones:</b>
        <?php 
        while($incidencias=db_fetch_array($busca_incidencias)) {
                        echo("<br /> - ".security_html($incidencias["nota"]));
        }
        ?>
        </td></tr>
        <?php 
                }        
                ?></table></td>
                </tr>
                <tr>
                <!-- Notas y recordatorios -->
                <td valign=bottom><?php 
                
                // Seguimiento
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=3) ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=db_num_rows($busca_incidencias);
                ?>
                <table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td rowspan="<?=$num_filas_seguimiento?>" class="tabla_notas" width=100><img src="imagenes/PersonajeNotas.png" border=0 width=100><br /></td>
                <?php 
                $cuenta_filas=0;
                while($incidencias=db_fetch_array($busca_incidencias)) {
                if ($cuenta_filas>0) {
                        ?>
                        <tr>
                        <?php 
                        }
        ?>
        <td class="tabla_notas_filas"><?=$tipos_notas_inci[$incidencias["tipo_nota"]]?></td>
        <td class="tabla_notas_filas"><?=security_html($incidencias["nota"])?></td>
                        </tr>
        <?php 
                $cuenta_filas++;            
                }
                
                if ($cuenta_filas==0) {
                ?>
                <td class="tabla_notas_filas" colspan=2>&nbsp;</td>
                <?php 
                }
                        
                ?>
                </tr>
                </table></td>
                </tr>
                </table>
