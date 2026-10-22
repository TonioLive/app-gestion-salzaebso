<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";
                  
                  
    // Busca mensajes
	$tipos_mensajes=array("Enviado","Medicación","Recibido");
	$tipos_acciones_mensaje=array("Enviado","Introducida","Recibido");
	$nombres_tipos_mensajes=array("Mensaje Enviado","Instrucciones de Medicación","Mensaje Recibido");
	$nombres_tipos_mensajes=array("Mensaje","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");
	
	$id_mensaje=$_GET["id_mensaje"];
	
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR id_alumno=".$padres["padre_de"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	} 	
    
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) AND id_nota_padre=$id_mensaje");              
    
    if (db_num_rows($busca_mensajes)!=0) {    
    
    $mensajes=db_fetch_array($busca_mensajes);
    
    if (($mensajes["estado"]==0) and ($mensajes["tipo_nota"]==0)) {
    	$estado_boton_editar="";
    	} else {
    	$estado_boton_editar=" disabled";
    	}
    	
    // Busca remitente
    $busca_remitente=db_query("SELECT * FROM usuarios WHERE id_usuario=".$mensajes["id_remitente"]);
    
    if (db_num_rows($busca_remitente)!=0) {
    	$datos_remitente=db_fetch_array($busca_remitente);
    	$nombre_remitente=$datos_remitente["Nombre"]." ".$datos_remitente["Apellido1"]." ".$datos_remitente["Apellido2"];
    	} else {
    	$nombre_remitente="-";
    	}    	
                  
    ?>
 	<div align="left"><font size=3><b>&nbsp;&nbsp;<?=$nombres_tipos_mensajes[$mensajes["tipo_nota"]]?></b></font></div><br>
 	<table width=98%  class="tabla_datos" align="center">
 	<tr>
 	<td class="tabla_celda_encabezado" align="left" colspan=2><font size=2><b><?=$tipos_acciones_mensaje[$mensajes["tipo_nota"]]?> el día <?=date("d/m/Y",strtotime($mensajes["fecha"]));?> a las <?=date("H:i",strtotime($mensajes["hora"]));?></b></font>
 	<div align="right"><font size=2><b>Estado:</b> <?=$estados_mensajes[$mensajes["estado"]];?></font></div></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right" width="73"><font size=2><b>De:</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=$nombre_remitente;?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Asunto:</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=$mensajes["asunto"];?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b><?=$nombres_textos_mensajes[$mensajes["tipo_nota"]]?></b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=$mensajes["texto_nota"];?></font></td>
 	</tr>
 	<?php 
 	if ($mensajes["tipo_nota"]==1) {
 	// Medicación
 	?>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Nombre del medicamento</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=$mensajes["medicamento"];?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Cantidad que se debe administrar</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=$mensajes["cantidad"];?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Hora a la que se debe administrar</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=date("H:i",strtotime($mensajes["hora_medicacion"]));?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Desde el día:</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=date("d/m/Y",strtotime($mensajes["fecha_medicacion"]));?></font></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado" align="right"><font size=2><b>Hasta el día (incluido):</b></font></td>
 	<td class="tabla_celda_datos" align="left"><font size=2><?=date("d/m/Y",strtotime($mensajes["fecha_fin_medicacion"]));?></font></td>
 	</tr>
 	<?php 
 	}
 	// Busca respuestas
 	$busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=$id_mensaje ORDER BY fecha_respuesta DESC, hora_respuesta DESC");
 	if (db_num_rows($busca_respuestas)!=0) {
 	?>
 	<tr><td colspan=2 align="right">
 	<table cellpadding=2 cellspacing=1 border=0 width=100%>
 	<tr>
 	<th class="tabla_celda_encabezado" colspan=4><font size=3><b>Respuestas</b></font></th>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado"><font size=2><b>Fecha</b></font></td>
 	<td class="tabla_celda_encabezado"><font size=2><b>Hora</b></font></td>
 	<td class="tabla_celda_encabezado"><font size=2><b>Autor</b></font></td>
 	<td class="tabla_celda_encabezado"><font size=2><b>Mensaje</b></font></td>
 	</tr>
 	<?php 
 	while($respuestas=db_fetch_array($busca_respuestas)) {
 	$busca_autor=db_query("SELECT * FROM usuarios WHERE id_usuario=".$respuestas["respondido_por"]);
 	if (db_num_rows($busca_autor)!=0) {
 		$datos_autor=db_fetch_array($busca_autor);
 		$nombre_autor=$datos_autor["Nombre"]." ".$datos_autor["Apellido1"]." ".$datos_autor["Apellido2"];
 		}
 	?>
 	<tr>
 	<td class="tabla_celda_datos"><font size=2><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></font></td>
 	<td class="tabla_celda_datos"><font size=2><?=date("H:i:s",strtotime($respuestas["hora_respuesta"]));?></font></td>
 	<td class="tabla_celda_datos"><font size=2><?=$nombre_autor?></font></td>
 	<td class="tabla_celda_datos"><font size=2><?=$respuestas["texto_respuesta"]?></font></td>
 	</tr>
 	<?php 
 	}
 	?>
 	</table></td></tr>
 	<?php 
 	}
 	?>
 	<form name="formulario_respuesta" action="responder_mensaje1.php" method="POST">
 	<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
 	<input type="hidden" name="id_remitente" value="<?=$id_usuario?>">
 	<tr bgcolor="#efefef" valign="top">
 	<td align="right"><font size=2><b>Respuesta:</b></font></td>
 	<td><textarea id="respuesta" name="respuesta" placeholder="Escribe aquí tu respuesta" style="font-size: 12px; font-family: Arial" cols="115" rows="8"></textarea></td>
 	</tr>
 	</table><br>
 	<input type="button" value="Cancelar" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$id_mensaje;?>'" class="tabla_celda_encabezado"> <input type="button" onclick="if (document.formulario_respuesta.respuesta.value!='') { document.formulario_respuesta.submit(); } else { alert('No has escrito ningún texto en la respuesta.') }" value="Enviar" class="tabla_celda_encabezado">
 	</form>	
 	<br>
    <?php 

    } else {
    ?>
    Error: El mensaje no se encuentra.
    <?php 
    }	
    	
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
