<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}

$formulario_name="redactar_mensaje";	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Nuevo mensaje
      </h1>
      <ol class="breadcrumb">
        <li><a href="/clientes"><i class="fa fa-home"></i> Inicio</a></li>
        <li><a href="/clientes/mensajeria.php"><i class="fa fa-envelope"></i> Mensajes</a></li>
        <li class="active">Nuevo Mensaje</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
 	<form name="<?=$formulario_name;?>" action="nuevo_mensaje1.php" method="POST">
		<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
		<input type="hidden" name="id_remitente" value="<?=$id_usuario?>">
          <div class="box box-primary">
            <div class="box-body">
              <div class="form-group">
                <select size=1 class="form-control" name="destinatario" id="destinatario">
					<?php 
					// Busca destinatarios (docentes de las aulas de sus hijos)
					$busca_datos_alumno=db_query_params('SELECT *,usuarios.Nombre AS nombre_hijo FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.") or die(db_error());

					$array_centros_alumno=array();

					if (db_num_rows($busca_datos_alumno)!=0) {
						while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
						
						if ($datos_alumno["sexo"]=="V") {
										$sufijo_hijo="o";
										} else {
										$sufijo_hijo="a";
										}
						
						// Finalmente muestra las cámaras
						
						// Datos de la escuela del niño
						$busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_alumno["id_centro"]));
						
						$datos_escuela=db_fetch_array($busca_datos_escuela);
						?>
						<option value="A<?=$datos_alumno["padre_de"];?>">Para: Monitor/a de <?=$datos_alumno["nombre_hijo"];?></option>
						<?php 							
						if ((strlen($array_centros_alumno[$datos_alumno["id_centro"]])!=1) and ($mostrar_mensajes_administracion==true)) {
						?>
						<option value="C<?=$datos_alumno["padre_de"];?>">Para: Coordinadora de <?=$datos_escuela["nombre_centro"];?></option>
						<?php 
						}
						
						$array_centros_alumno[$datos_alumno["id_centro"]]=1;

					 }   
					}
					?>
				</select>
              </div>
              <div class="form-group">
                <input class="form-control" placeholder="Asunto" name="asunto" id="asunto">
              </div>
              <div class="form-group">
                    <textarea id="compose-textarea" class="form-control" style="height: 300px" id="texto_mensaje" name="texto_mensaje" placeholder="Escribe aquí tu mensaje">
                    </textarea>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="button" class="btn btn-responsive btn-primary" onclick="enviar_mensaje();"><i class="fa fa-envelope-o"></i> Enviar</button>
              </div>
              <button type="button" onclick="window.location.href='mensajeria.php'" class="btn btn-responsive btn-default"><i class="fa fa-times"></i> Cancelar</button>
            </div>
            <!-- /.box-footer -->
          </div>
	</form>
	  
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

	function enviar_mensaje() {

		var errores=0;

		var mensajes_error="";                                    

		if (self.document.<?=$formulario_name;?>.asunto.value=="") {
				 mensajes_error=mensajes_error+"No has escrito el asunto del mensaje.\u000A";
				 errores=1;
				} 
		if (self.document.<?=$formulario_name;?>.texto_mensaje.value=="") {
				 mensajes_error=mensajes_error+"No has escrito el texto del mensaje.\u000A";
				 errores=1;
				}                           

		if (errores==0) {
				document.<?=$formulario_name;?>.submit();
				} else {
				alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
				}
    }

</script>

</body>
</html>	  
