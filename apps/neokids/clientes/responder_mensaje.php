<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}

    // Busca mensajes
	$tipos_mensajes=array("Enviado","Medicación","Recibido");
	$subtipos_mensajes=array(" a la monitora"," a la administración de Neokids");
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
    if (($mensajes["estado"]==0) and ($mensajes["tipo_nota"]==2)) { 
    	// Marca como leido
    	$marca_leido=db_query_params('UPDATE agenda_notas_padres SET estado=1 WHERE id_nota_padre=?', 's', array($id_mensaje));
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
        Responder <?=$nombres_tipos_mensajes[$mensajes["tipo_nota"]]?><small><?=$subtipo_nota?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/clientes"><i class="fa fa-home"></i> Inicio</a></li>
        <li><a href="/clientes/mensajeria.php"><i class="fa fa-envelope"></i> Mensajes</a></li>
        <li class="active">Responder Mensaje</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	<?php  
   if ($errores=="") {
    ?>
    
        <div class="col-md-12">
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
                <h3><?=$mensajes["asunto"];?></h3>
                <h5>De: <?=$nombre_remitente;?>                  
              </div>
              <!-- /.mailbox-read-info -->
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <?=$mensajes["texto_nota"];?>
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
			// Marca como leida
			if ($respuestas["respondido_por"]!=$id_usuario) {
				$marca_leida=db_query_params('UPDATE agenda_notas_padres_respuestas SET leida=1 WHERE id_respuesta=?', 's', array($respuestas["id_respuesta"]));
				}
			?>
				<div class="row">
					<div class="col-md-2"><?=date("d/m/Y",strtotime($respuestas["fecha_respuesta"]));?></div>
					<div class="col-md-2"><?=date("H:i:s",strtotime($respuestas["hora_respuesta"]));?></div>
					<div class="col-md-2"><?=$nombre_autor?></div>
					<div class="col-md-6"><?=$respuestas["texto_respuesta"]?></div>
				</div>
			<?php 
			}
			?>			  
              </div>
              <!-- /.mailbox-read-message -->
            </div>

 	<form name="formulario_respuesta" action="responder_mensaje1.php" method="POST">
 	<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
 	<input type="hidden" name="id_remitente" value="<?=$id_usuario?>">
	
              <div class="form-group">
                    <textarea id="compose-textarea" class="form-control" style="height: 300px" id="respuesta" name="respuesta" placeholder="Escribe aquí tu respuesta">
                    </textarea>
              </div>	
			  
 	</form>	 	
 		
			<?php 
			}
			?>		
			<!-- /.Respuestas -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="button" class="btn btn-responsive btn-default" onclick="if (document.formulario_respuesta.respuesta.value!='') { document.formulario_respuesta.submit(); } else { alert('No has escrito ningún texto en la respuesta.') }"><i class="fa fa-paper-plane"></i> Enviar respuesta</button>
              </div>
			  <?php  if (($mensajes["tipo_nota"]!=2) and ($mensajes["estado"]==0)) { ?>
				<button type="button" class="btn btn-responsive btn-default" onclick="if (confirm('Has seleccionado eliminar el mensaje. Esta operación no se puede deshacer.\u000A\u000A¿Continuar eliminando el mensaje?')) { window.location.href='eliminar_mensaje.php?id_mensaje=<?=$id_mensaje?>'; }"><i class="fa fa-trash-o"></i> Eliminar</button>
			  <?php  } ?>
              <button type="button" class="btn btn-responsive btn-default" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$id_mensaje;?>'"><i class="fa fa-times"></i> Cancelar</button>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /. box -->
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

 <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2014-<?=date("Y");?> <a href="http://www.neo-kids.es">Neokids</a>.</strong>
  </footer>

<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Page Script -->
<script>
  $(function () {
    //Add text editor
    $("#compose-textarea").wysihtml5();
  });
</script>

</body>
</html>	 