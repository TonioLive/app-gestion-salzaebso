<?php  require("../../configuracion/parte_superior_docente.php"); ?>
    <div align="center">
    <?php 
    $id_alumno=$_GET["id_alumno"];
    $id_mensaje=$_GET["id_mensaje"];
      
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
	$tipos_mensajes=array("Recibido","Medicación","Enviado");
	$subtipos_mensajes=array(" para la educadora"," para la coordinadora");
	$tipos_acciones_mensaje=array("Recibido","Introducida","Enviado");
	$nombres_tipos_mensajes=array("Mensaje Recibido","Instrucciones de Medicación","Mensaje Enviado");
	$nombres_tipos_mensajes=array("Mensaje recibido","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");
	
    $busca_mensajes=db_query_params('SELECT * FROM agenda_notas_padres WHERE id_alumno=? AND id_nota_padre=?', 'ss', array($id_alumno, $id_mensaje));              
    
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
 	<td bgcolor="#ffffff" align="left"><font size=2><?=security_escape($mensajes["asunto"]);?></font></td>
 	</tr>
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b><?=$nombres_textos_mensajes[$mensajes["tipo_nota"]]?></b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><?=security_html($mensajes["texto_nota"]);?></font></td>
 	</tr>
 	<?php 
 	if ($mensajes["tipo_nota"]==1) {
 	// Medicación
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
 	$busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=? ORDER BY fecha_respuesta DESC, hora_respuesta DESC', 's', array($id_mensaje));
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
 	$busca_autor=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($respuestas["respondido_por"]));
 	if (db_num_rows($busca_autor)!=0) {
 		$datos_autor=db_fetch_array($busca_autor);
 		$nombre_autor=$datos_autor["Nombre"]." ".$datos_autor["Apellido1"]." ".$datos_autor["Apellido2"];
 		}
 	?>
 	<tr bgcolor="#efefef">
 	<td><font size=2><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></font></td>
 	<td><font size=2><?=date("d/m/Y",strtotime($respuestas["hora_respuesta"]));?></font></td>
 	<td><font size=2><?=$nombre_autor?></font></td>
 	<td><font size=2><?=security_html($respuestas["texto_respuesta"])?></font></td>
 	</tr>
 	<?php 
 	}
 	?>
 	</table><br>
 	</td></tr>
 	<?php 
 	}
 	?>
 	<form name="formulario_respuesta" action="responder_mensaje1.php" method="POST">
 	<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
 	<input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
 	<tr bgcolor="#efefef" valign="top">
 	<td align="right"><font size=2><b>Respuesta:</b></font></td>
 	<td><textarea id="respuesta" name="respuesta" placeholder="Escribe aquí tu respuesta" style="font-size: 12px; font-family: Arial" cols="115" rows="8"></textarea></td>
 	</tr>
 	</table><br>
 	<input type="button" value="Cancelar" onclick="window.location.href='ver_mensaje.php?id_alumno=<?=$id_alumno?>&id_mensaje=<?=$id_mensaje;?>'" style="font-size: 10px"> <input type="button" onclick="if (document.formulario_respuesta.respuesta.value!='') { document.formulario_respuesta.submit(); } else { alert('No has escrito ningún texto en la respuesta.') }" value="Enviar" style="font-size: 10px">
 	</form>	
 	<br>
    <?php 

    } else {
    ?>
    Error: El mensaje no se encuentra.
    <?php 
    }	
    
    ?></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
