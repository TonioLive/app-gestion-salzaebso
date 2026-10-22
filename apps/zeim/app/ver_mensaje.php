<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}

    // Busca mensajes
	$tipos_mensajes=array("Enviado","Medicación","Recibido");
	$subtipos_mensajes=array(" a la monitora"," a la dirección");
	$tipos_acciones_mensaje=array("Enviado","Introducida","Recibido");
	$nombres_tipos_mensajes=array("Mensaje Enviado","Instrucciones de Medicación","Mensaje Recibido");
	$nombres_tipos_mensajes=array("Mensaje","Instrucciones","Mensaje recibido");
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
    
    if (($mensajes["estado"]==0) and ($mensajes["tipo_nota"]!=2)) {
    	$estado_boton_editar="";
    	} else {
    	$estado_boton_editar=" disabled";
    	}


	$pos = strpos($mensajes["leido_por"], "[".$id_usuario."]");
			
	// Marca como leida si es nota recibida
    if (($pos === false) and (($mensajes["tipo_nota"]==2))) { 
    	// Marca como leido
		$marca_mensaje=db_query_params('UPDATE agenda_notas_padres SET estado=1, leido_por=CONCAT(leido_por,?) WHERE id_nota_padre=?', 'ss', array('[' . (string)($id_usuario) . ']', $id_mensaje));
    	}
    	
    // Busca remitente
    $busca_remitente=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($mensajes["id_remitente"]));
    
    if (db_num_rows($busca_remitente)!=0) {
    	$datos_remitente=db_fetch_array($busca_remitente);
    	$nombre_remitente=$datos_remitente["Nombre"];
    	} else {
    	$nombre_remitente="-";
    	}
    
    
    if ($mensajes["tipo_nota"]!=2) {
    	$subtipo_nota=$subtipos_mensajes[$mensajes["destino"]];
    	} else {
    	$subtipo_nota="";
    	}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$nombres_tipos_mensajes[$mensajes["tipo_nota"]]?><small><?=$subtipo_nota?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
        <li><a href="/app/mensajeria.php"><i class="fa fa-envelope"></i> Mensajes</a></li>
        <li class="active">Leer Mensaje</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	<?php  
   if ($errores=="") {
    ?>
    
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$tipos_acciones_mensaje[$mensajes["tipo_nota"]]?> el día <?=date("d/m/Y",strtotime($mensajes["fecha"]));?> a las <?=date("H:i",strtotime($mensajes["hora"]));?></h3>
              <div class="pull-right">
			  		<b>Estado:</b> <?=$estados_mensajes[$mensajes["estado"]];?>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-read-info">
                <h3><?=security_escape($mensajes["asunto"]);?></h3>
                <h5>De: <?=$nombre_remitente;?>                  
              </div>
              <!-- /.mailbox-read-info -->
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <?=security_html($mensajes["texto_nota"]);?>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.box-body -->
            <?php
			if ($mensajes["tipo_nota"]==1) {
			// Medicación
			?>
			<div class="box-body no-padding">
				<div class="mailbox-read-info">
					<h4>Nombre del medicamento</h4>
					<?=$mensajes["medicamento"];?>
				</div>
				<div class="mailbox-read-info">
					<h4>>Cantidad que se debe administrar</h4>
					<?=$mensajes["cantidad"];?>
				</div>
				<div class="mailbox-read-info">
					<h4>>Hora a la que se debe administrar</h4>
					<?=date("H:i",strtotime($mensajes["hora_medicacion"]));?>
				</div>
				<div class="mailbox-read-info">
					<h4>Desde el día:</h4>
					<?=date("d/m/Y",strtotime($mensajes["fecha_medicacion"]));?>
				</div>
				<div class="mailbox-read-info">
					<h4>Hasta el día (incluido):</h4>
					<?=date("d/m/Y",strtotime($mensajes["fecha_fin_medicacion"]));?>
				</div>
			</div>
			<?php
			}
			?>
			<!-- /.medicación -->
			
			<?php 
			// Busca respuestas
			$busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=? ORDER BY fecha_respuesta DESC, hora_respuesta DESC', 's', array($id_mensaje));
			$hay_respuestas=false;
			if ($mensajes["tipo_nota"]==2) {
				$hay_respuestas=true;
				}
			if (db_num_rows($busca_respuestas)!=0) {
			$hay_respuestas=true;
			?>			
            <div class="box-header with-border">
              <h3 class="box-title">Respuestas</h3>
            </div>
			
            <div class="box-body no-padding">
              <div class="mailbox-read-info">
				<div class="row">
					<div class="col-md-2"><b>Fecha</b></div>
					<div class="col-md-2"><b>Hora</b></div>
					<div class="col-md-2"><b>De</b></div>
					<div class="col-md-6"><b>Mensaje</b></div>
				</div>
              </div>
              <div class="mailbox-read-message">
			<?php 
			while($respuestas=db_fetch_array($busca_respuestas)) {
			$busca_autor=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($respuestas["respondido_por"]));
			if (db_num_rows($busca_autor)!=0) {
				$datos_autor=db_fetch_array($busca_autor);
				$nombre_autor=$datos_autor["Nombre"];
				}
			// Marca como leida si la ha enviado un docente
			if ($respuestas["direccion"]==1) {	
				$pos = strpos($respuestas["leido_por"], "[".$id_usuario."]");
			
				if ($pos === false) { 
					// Marca la respuesta como leida
					$marca_leida=db_query_params('UPDATE agenda_notas_padres_respuestas SET leida=1, leido_por=CONCAT(leido_por,?) WHERE id_respuesta=?', 'ss', array('[' . (string)($id_usuario) . ']', $respuestas["id_respuesta"]));
					}
				/*
				if ($respuestas["respondido_por"]!=$id_usuario) {
					$marca_leida=mysql_query("UPDATE agenda_notas_padres_respuestas SET leida=1 WHERE id_respuesta=".$respuestas["id_respuesta"]);
					}
				*/
			}
			?>
				<div class="row">
					<div class="col-md-2"><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></div>
					<div class="col-md-2"><?=date("H:i:s",strtotime($respuestas["hora_respuesta"]));?></div>
					<div class="col-md-2"><?=$nombre_autor?></div>
					<div class="col-md-6"><?=security_html($respuestas["texto_respuesta"])?></div>
				</div>
			<?php 
			}
			?>			  
              </div>
              <!-- /.mailbox-read-message -->
            </div>

			<?php 
			}
			?>		
			<!-- /.Respuestas -->
            <div class="box-footer">
              <div class="pull-right">
                <?php  if (($mensajes["destino"]==$id_usuario) OR ($hay_respuestas==true)) { ?><button type="button" class="btn btn-responsive btn-default" onclick="window.location.href='responder_mensaje.php?id_mensaje=<?=$id_mensaje?>'"><i class="fa fa-reply"></i> Responder</button><?php  } ?>
              </div>
			  <?php  if (($mensajes["tipo_nota"]!=2) and ($mensajes["estado"]==0)) { ?>
				<button type="button" class="btn btn-responsive btn-default" onclick="if (confirm('Has seleccionado eliminar el mensaje. Esta operación no se puede deshacer.\u000A\u000A¿Continuar eliminando el mensaje?')) { window.location.href='eliminar_mensaje.php?id_mensaje=<?=$id_mensaje?>'; }"><i class="fa fa-trash-o"></i> Eliminar</button>
			  <?php  } ?>
              <button type="button" class="btn btn-responsive btn-default" onclick="window.location.href='mensajeria.php'"><i class="fa fa-backward"></i> Volver</button>
            </div>
            <!-- /.box-footer -->
          </div> 
    
	<?php                   

		} // Errores
    } else {
    ?>
    Error: El mensaje no se encuentra.
    <?php 
    }	
    	
    ?>  

      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>