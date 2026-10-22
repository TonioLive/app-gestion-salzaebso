<?php  require("../../configuracion/parte_superior_docente.php"); ?>
    <div align="center">
    <?php 
    $id_alumno=$_GET["id_alumno"];
      
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE padre_de=?', 's', array($id_alumno));
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR respondido_por=".$padres["id_usuario"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	}
     
    // Datos alumno
    $busca_alumno=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_alumno));  
    
    $datos_alumno=db_fetch_array($busca_alumno);     
    
    $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];        
                                  
    // Busca mensajes
    
if (($nivel_acceso==3) or ($nivel_acceso==4)) {
	// Coordinadora
	$filtro_coordinadora="";
	} else {
	// Educadora
	$filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
	}    
    
	$tipos_mensajes=array("Recibido","Medicación","Enviado");
	$subtipos_mensajes=array(" para la educadora"," para la coordinadora");
	$estados_mensajes=array("<font color='#ff0000'>No leido</font>","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=$id_alumno AND (id_centro_alumno=$centro_actual OR id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC, hora DESC");              
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=$id_alumno AND tipo_nota<>2 AND estado=0 AND (id_centro_alumno=$centro_actual OR id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC"); 
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
    	if (db_num_rows($busca_no_leidos)) {
    	$coletilla_leido="";
    	if (db_num_rows($busca_no_leidos)>1) {
    		$coletilla_leido="s";
    		}
    		}
    	$texto_no_leidos=" (<font color='#f00'>".db_num_rows($busca_no_leidos)." no leido$coletilla_leido</font>)";
    	}
                  
    ?>
    <br>
    <div align=left><font size=3><b>Mensajes de <?=$nombre_completo_alumno?><?=$texto_no_leidos?></b></font></div>
    <div align=right><font size=3><b><input type="button" value="Nuevo mensaje" style="font-size: 10px" onclick="window.location.href='nuevo_mensaje.php?id_alumno=<?=$id_alumno?>'"></b></font></div><br>
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
    while($mensajes=db_fetch_array($busca_mensajes)) {
    
    // Busca remitente
    $busca_remitente=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($mensajes["id_remitente"]));
    
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
 	<td><font size=2><?=$nombre_remitente?></font></td>
 	<td><font size=2><?=security_escape($mensajes["asunto"]);?></b></font></td>
 	<td><font size=1><?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></font></td>
 	<td><font size=2><?=$estado_mensaje;?></b></font></td>
 	<td align="center"><font size=2><input type="button" style="font-size: 10px" value="Abrir" onclick="window.location.href='ver_mensaje.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$mensajes["id_nota_padre"]?>'"></b></font></td>
 	</tr>
    <?php 
    	}
    ?></table></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
