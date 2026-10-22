<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
    	<?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->    
    <?php  
                                   
    // Busca mensajes
	$tipos_mensajes=array("Enviado","Medicación","Recibido");
	$tipos_acciones_mensaje=array("Enviado","Introducida","Recibido");
	$nombres_tipos_mensajes=array("Mensaje Enviado","Instrucciones de Medicación","Mensaje Recibido");
	$nombres_tipos_mensajes=array("Mensaje","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");
	
	$id_mensaje=$_GET["id_mensaje"];
	
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
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
    $busca_remitente=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($mensajes["id_remitente"]));
    
    if (db_num_rows($busca_remitente)!=0) {
    	$datos_remitente=db_fetch_array($busca_remitente);
    	$nombre_remitente=$datos_remitente["Nombre"]." ".$datos_remitente["Apellido1"]." ".$datos_remitente["Apellido2"];
    	} else {
    	$nombre_remitente="-";
    	}    	
                  
    ?>
    <h3><?=$nombres_tipos_mensajes[$mensajes["tipo_nota"]]?></h3>
    <div align="center">     	
 	<table width=98%  class="tabla_datos" align="center">
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="left" colspan=2><b><?=$tipos_acciones_mensaje[$mensajes["tipo_nota"]]?> el día <?=date("d/m/Y",strtotime($mensajes["fecha"]));?> a las <?=date("H:i",strtotime($mensajes["hora"]));?></b>
 	<div align="right"><b>Estado:</b> <?=$estados_mensajes[$mensajes["estado"]];?></div></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right" width="73"><b>De:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=$nombre_remitente;?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Asunto:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=security_escape($mensajes["asunto"]);?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b><?=$nombres_textos_mensajes[$mensajes["tipo_nota"]]?></b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=security_html($mensajes["texto_nota"]);?></td>
 	</tr>
 	<?php 
 	if ($mensajes["tipo_nota"]==1) {
 	// Medicación
 	?>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Nombre del medicamento</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=$mensajes["medicamento"];?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Cantidad que se debe administrar</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=$mensajes["cantidad"];?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Hora a la que se debe administrar</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=date("H:i",strtotime($mensajes["hora_medicacion"]));?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Desde el día:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=date("d/m/Y",strtotime($mensajes["fecha_medicacion"]));?></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Hasta el día (incluido):</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left"><?=date("d/m/Y",strtotime($mensajes["fecha_fin_medicacion"]));?></td>
 	</tr>
 	<?php 
 	}
 	// Busca respuestas
 	$busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=? ORDER BY fecha_respuesta DESC, hora_respuesta DESC', 's', array($id_mensaje));
 	if (db_num_rows($busca_respuestas)!=0) {
 	?>
 	<tr><td colspan=2 align="right">
 	<table class="tabla_datos" width="100%">
 	<tr>
 	<th class="tabla_celda_encabezado celda_margen" colspan=4><b>Respuestas</b></th>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen"><b>Fecha</b></td>
 	<td class="tabla_celda_encabezado celda_margen"><b>Hora</b></td>
 	<td class="tabla_celda_encabezado celda_margen"><b>Autor</b></td>
 	<td class="tabla_celda_encabezado celda_margen"><b>Mensaje</b></td>
 	</tr>
 	<?php 
 	while($respuestas=db_fetch_array($busca_respuestas)) {
 	$busca_autor=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($respuestas["respondido_por"]));
 	if (db_num_rows($busca_autor)!=0) {
 		$datos_autor=db_fetch_array($busca_autor);
 		$nombre_autor=$datos_autor["Nombre"]." ".$datos_autor["Apellido1"]." ".$datos_autor["Apellido2"];
 		}
 	?>
 	<tr>
 	<td class="tabla_celda_datos celda_margen"><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></td>
 	<td class="tabla_celda_datos celda_margen"><?=date("H:i:s",strtotime($respuestas["hora_respuesta"]));?></td>
 	<td class="tabla_celda_datos celda_margen"><?=$nombre_autor?></td>
 	<td class="tabla_celda_datos celda_margen"><?=security_html($respuestas["texto_respuesta"])?></td>
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
 	<td align="right"><b>Respuesta:</b></td>
 	<td><textarea id="respuesta" name="respuesta" placeholder="Escribe aquí tu respuesta" style="font-size: 12px; font-family: Arial" cols="115" rows="8"></textarea></td>
 	</tr>
 	</table><br>
 	</form>	 	
 	<button onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$id_mensaje;?>'" class="tabla_celda_encabezado celda_margen"><i class="fa fa-backward"></i> Cancelar</button> <button onclick="if (document.formulario_respuesta.respuesta.value!='') { document.formulario_respuesta.submit(); } else { alert('No has escrito ningún texto en la respuesta.') }" class="tabla_celda_encabezado celda_margen"><i class="fa fa-paper-plane"></i> Enviar</button>
 	<br /><br />
    <?php 

    } else {
    ?>
    Error: El mensaje no se encuentra.
    <?php 
    }	
    	
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
