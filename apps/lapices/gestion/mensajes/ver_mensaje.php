<?php  require("../../configuracion/parte_superior_docente.php"); ?>
    <div align="center">
    <?php 
    $id_alumno=$_GET["id_alumno"];
    $id_mensaje=$_GET["id_mensaje"];
      
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE padre_de=$id_alumno");
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR respondido_por=".$padres["id_usuario"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	}
     
    // Datos alumno
    $busca_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_alumno");  
    
    $datos_alumno=db_fetch_array($busca_alumno);     
    
    $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];    
                  
                  
    // Busca mensajes
	$tipos_mensajes=array("Recibido","Medicación","Enviado");
	$subtipos_mensajes=array(" para la educadora"," para la coordinadora");
	$tipos_acciones_mensaje=array("Recibido","Introducida","Enviado");
	$nombres_tipos_mensajes=array("Mensaje Recibido","Instrucciones de Medicación","Mensaje Enviado");
	$nombres_tipos_mensajes=array("Mensaje recibido","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");
	
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=$id_alumno AND id_nota_padre=$id_mensaje");              
    
    if (db_num_rows($busca_mensajes)!=0) {    
   
    $mensajes=db_fetch_array($busca_mensajes);
    
    if (($mensajes["estado"]==0) and ($mensajes["tipo_nota"]==2)) {
    	$estado_boton_editar="";
    	} else {
    	$estado_boton_editar=" disabled";
    	}
    if (($mensajes["estado"]==0) and ($mensajes["tipo_nota"]!=2)) { 
    	// Marca como leido
    	$estado_boton_marcar_leido="";
    	} else {
    	$estado_boton_marcar_leido=" disabled";
    	}
    	
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
          
    $busca_respuestas_no_leidas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=".$mensajes["id_nota_padre"]." AND ($filtro_padres)") or die(db_error());
    
    if (db_num_rows($busca_respuestas_no_leidas)!=0) {      
    	$estado_boton_marcar_leido="";
    	}
                  
    ?>
    <br>
 	<div align="left"><font size=3><b><?=$nombres_tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?></b></font></div><br>
 	<table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
 	<tr>
 	<td bgcolor="#efefef" align="left" colspan=2><font size=2><b><?=$tipos_acciones_mensaje[$mensajes["tipo_nota"]]?> el día <?=date("d/m/Y",strtotime($mensajes["fecha"]));?> a las <?=date("H:i",strtotime($mensajes["hora"]));?></b></font>
 	<div align="right"><font size=2><b>Estado:</b> <?=$estados_mensajes[$mensajes["estado"]];?></font></div></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right" width="73"><font size=2><b>De:</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=$nombre_remitente;?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Asunto:</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=$mensajes["asunto"];?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b><?=$nombres_textos_mensajes[$mensajes["tipo_nota"]]?></b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=$mensajes["texto_nota"];?></font></td>
 	</tr>
 	<?php 
 	if ($mensajes["tipo_nota"]==1) {
 	// Medicación
	if ($mensajes["medicacion_agenda"]==0) {
 		$estado_boton_medicacion="";
 		} else {
 		$estado_boton_medicacion=" disabled";
 		?>
 		<tr><td bgcolor='#efefef' align='CENTER' colspan=2><font color="#f00"><b>Instrucciones de medicación ya introducidas en la agenda</b></font></td></tr>
 		<?php 
 		}
 	?>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Nombre del medicamento</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=$mensajes["medicamento"];?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Cantidad que se debe administrar</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=$mensajes["cantidad"];?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Hora a la que se debe administrar</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=date("H:i",strtotime($mensajes["hora_medicacion"]));?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Desde el día:</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=date("d/m/Y",strtotime($mensajes["fecha_medicacion"]));?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Hasta el día (incluido):</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=date("d/m/Y",strtotime($mensajes["fecha_fin_medicacion"]));?></font></td>
 	</tr>
 	<?php 
 	}
 	// Busca respuestas
 	$busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=$id_mensaje ORDER BY fecha_respuesta DESC, hora_respuesta DESC");
 	$hay_respuestas=false;
 	if ($mensajes["tipo_nota"]!=2) {
 		$hay_respuestas=true;
 		}
 	if (db_num_rows($busca_respuestas)!=0) {
 	$hay_respuestas=true;
 	?>
 	<tr><td colspan=2 align="right">
 	<table cellpadding=2 cellspacing=1 border=0 width=100%>
 	<tr>
 	<th bgcolor="#efefef" colspan=4><font size=3><b>Respuestas</b></font></th>
 	</tr>
 	<tr bgcolor="#cdcdcd">
 	<td><font size=2><b>Fecha</b></font></td>
 	<td><font size=2><b>Hora</b></font></td>
 	<td><font size=2><b>De</b></font></td>
 	<td><font size=2><b>Mensaje</b></font></td>
 	</tr>
 	<?php 
 	while($respuestas=db_fetch_array($busca_respuestas)) {
 	$busca_autor=db_query("SELECT * FROM usuarios WHERE id_usuario=".$respuestas["respondido_por"]);
 	if (db_num_rows($busca_autor)!=0) {
 		$datos_autor=db_fetch_array($busca_autor);
 		$nombre_autor=$datos_autor["Nombre"]." ".$datos_autor["Apellido1"]." ".$datos_autor["Apellido2"];
 		}
 	?>
 	<tr bgcolor="#efefef">
 	<td><font size=2><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></font></td>
 	<td><font size=2><?=date("H:i:s",strtotime($respuestas["hora_respuesta"]));?></font></td>
 	<td><font size=2><?=$nombre_autor?></font></td>
 	<td><font size=2><?=$respuestas["texto_respuesta"]?></font></td>
 	</tr>
 	<?php 
 	}
 	?>
 	</table><br>
 	</td></tr>
 	<?php 
 	}
 	?>
 	</table>
 	<br><?php  if ($hay_respuestas==true) { ?><input type="button" onclick="window.location.href='responder_mensaje.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$id_mensaje?>'" value="Responder"> <?php  } ?> <input type="button" onclick="if (confirm('Has seleccionado marcar el mensaje y todas sus respuestas como leidas. Esta operación no se puede deshacer.\u000A\u000A¿Continuar?')) { window.location.href='marcar_leido.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$id_mensaje?>'; }" value="Marcar como leido"<?=$estado_boton_marcar_leido?>> <input type="button" onclick="if (confirm('Has seleccionado eliminar el mensaje antes de que sea leido por el destinatario. Esta operación no se puede deshacer.\u000A\u000A¿Continuar eliminando el mensaje?')) { window.location.href='eliminar_mensaje.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$id_mensaje?>'; }" value="Eliminar"<?=$estado_boton_editar?>> <?php  if ($mensajes["tipo_nota"]==1) { ?><input<?=$estado_boton_medicacion?> type="button" value="Incluir medicación en agenda" onclick="incluir_agenda();"> <?php  } ?><input type="button" onclick="window.location.href='mensajeria.php?id_alumno=<?=$id_alumno?>&'" value="Volver">
 <script language="Javascript">
 
 function incluir_agenda() {
 
 	if (confirm("¿Incluir las instrucciones de medicación en la agenda del alumno?")) {
 		window.location.href='incluir_en_agenda.php?id_alumno=<?=$id_alumno;?>&id_mensaje=<?=$id_mensaje;?>';
 		}
 
 	}
 
 </script>
    <?php 

    } else {
    ?>
    Error: El mensaje no se encuentra.
    <?php 
    }	

    ?></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
