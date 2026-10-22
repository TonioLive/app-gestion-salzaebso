<?php  require("../../configuracion/parte_superior_docente.php"); ?>
    <div align="center">
    <?php 
$c_array=0;
$array_no_leidos=array();    
    
// Busca mensajes pendientes en bandeja de entrada

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

$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");

$consulta_mensajes="";

while($alumnos=db_fetch_array($busca_alumnos)) {
	$consulta_mensajes.=" OR agenda_notas_padres.id_alumno=".$alumnos["id_usuario"];
	}
$hay_mensajes_no_leidos=false;
if ($consulta_mensajes!="") {

	$consulta_mensajes="(".substr($consulta_mensajes,4,strlen($consulta_mensajes)-4).")";
	
	}
	
	$busca_no_leidos=db_query("(SELECT id_nota FROM agenda_notas_padres WHERE $consulta_mensajes AND tipo_nota<>2 AND estado=0 AND destino=1 AND (id_centro_alumno=$centro_actual OR id_centro_alumno=0)) UNION (SELECT agenda_notas_padres.id_nota FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND $consulta_mensajes AND agenda_notas_padres.tipo_nota<>2 AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0 AND agenda_notas_padres.destino=1 AND (agenda_notas_padres.id_centro_alumno=$centro_actual OR agenda_notas_padres.id_centro_alumno=0)) AS tabla1"); 
    
                          
    // Busca mensajes
	$tipos_mensajes=array("Recibido","Medicación","Enviado");
	$subtipos_mensajes=array(" para la educadora"," para la coordinadora");
	$estados_mensajes=array("No leido","Leido","Cerrado");
	
    $busca_mensajes=db_query("SELECT * FROM ((SELECT id_nota_padre AS id_mensaje FROM agenda_notas_padres WHERE $consulta_mensajes AND tipo_nota<>2 AND estado=0 AND destino=1 AND (id_centro_alumno=$centro_actual OR id_centro_alumno=0)) UNION (SELECT agenda_notas_padres.id_nota_padre AS id_mensaje  FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND $consulta_mensajes AND agenda_notas_padres.tipo_nota<>2 AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0 AND agenda_notas_padres.destino=1 AND (id_centro_alumno=$centro_actual OR id_centro_alumno=0))) AS tabla1 GROUP BY id_mensaje") or die(db_error());
                  
    ?>
    <br>
    <div align=left><font size=3><b>Mensajes Nuevos</b></font></div>
    <br>
 	<table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
 	<tr bgcolor="#efefef">
 	<td><font size=2><b>Tipo</b></font></td>
 	<td><font size=2><b>De</b></font></td>
 	<td><font size=2><b>Asunto</b></font></td>
 	<td><font size=2><b>Fecha</b></font></td>
 	<td><font size=2><b>Estado</b></font></td>
 	<td><font size=2><b>&nbsp;</b></font></td>
 	</tr>
    <?php 
    if (db_num_rows($busca_mensajes)==0) {
    	?>
   	<tr bgcolor="#ffffff">
 	<td align="center" colspan=6><font size=2><b>No hay mensajes</b></font></td>
 	</tr>
    	<?php 
    	}
    while($lista_mensajes=db_fetch_array($busca_mensajes)) {
    
    $busca_datos_mensaje=db_query("SELECT * FROM agenda_notas_padres WHERE id_nota_padre=".$lista_mensajes["id_mensaje"]);
    
    $mensajes=db_fetch_array($busca_datos_mensaje);
    
    // Busca remitente
    $busca_remitente=db_query("SELECT * FROM usuarios WHERE id_usuario=".$mensajes["id_remitente"]);
    
    if (db_num_rows($busca_remitente)!=0) {
    	$datos_remitente=db_fetch_array($busca_remitente);
    	$nombre_remitente=$datos_remitente["Nombre"]." ".$datos_remitente["Apellido1"]." ".$datos_remitente["Apellido2"];
    	} else {
    	$nombre_remitente="-";
    	}
    	
    if ($mensajes["tipo_nota"]!=2) {
    	$subtipo_nota=$subtipos_mensajes[$mensajes["destino"]];
    	} else {
    	$subtipo_nota="";
    	}
    
    // Busca respuestas no leidas
    
    $id_alumno=$mensajes["id_alumno"];
    
    // Datos alumno
    $busca_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_alumno");  
    
    $datos_alumno=db_fetch_array($busca_alumno);     
    
    $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];        
    
    // Aula actual
    
    $busca_aula_actual=db_query("SELECT centro_aulas.nombre AS nombre_aula FROM alumnos, cursos_academicos, centro_aulas WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.aula=centro_aulas.id_aula AND cursos_academicos.fecha_inicio<='".date("Y-m-d")."' AND cursos_academicos.fecha_fin>='".date("Y-m-d")."' AND alumnos.id_usuario=$id_alumno ORDER BY cursos_academicos.fecha_inicio LIMIT 0,1") or die(db_error());
    
    if (db_num_rows($busca_aula_actual)!=0) {
    	$datos_aula_actual=db_fetch_array($busca_aula_actual);
    	$nombre_aula_actual="<br /><b>Aula:</b> ".$datos_aula_actual["nombre_aula"];
    	} else {
    	$nombre_aula_actual="";
    	}
    
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE padre_de=$id_alumno");
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR respondido_por=".$padres["id_usuario"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	}
    	
    $busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=".$mensajes["id_nota_padre"]." AND ($filtro_padres)") or die(db_error());
    
    if (db_num_rows($busca_respuestas)!=0) {
    	if (db_num_rows($busca_respuestas)) {
    	$coletilla_leido="";
    	if (db_num_rows($busca_respuestas)>1) {
    		$coletilla_leido="s";
    		}
    		}
    	$estado_mensaje="<font color='#f00'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido</font>";
    	} else {
    	$estado_mensaje=$estados_mensajes[$mensajes["estado"]];
    	}
     
    ?>
 	<tr bgcolor="#ffffff">
 	<td><font size=2><?=$tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?></font></td>
 	<td><font size=2><?=$nombre_remitente?><br /><b>Sobre su hijo/a:</b> <?=$nombre_completo_alumno.$nombre_aula_actual?></font></td>
 	<td><font size=2><?=$mensajes["asunto"];?></b></font></td>
 	<td><font size=1><?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></font></td>
 	<td><font size=2><?=$estado_mensaje;?></b></font></td>
 	<td align="center"><font size=2><input type="button" style="font-size: 10px" value="Abrir" onclick="window.location.href='ver_mensaje.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$mensajes["id_nota_padre"]?>'"></b></font></td>
 	</tr>
    <?php 
    	}
    ?></table></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>