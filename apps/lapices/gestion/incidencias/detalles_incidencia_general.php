<?php  require("../../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php 

$id_incidencia=$_GET["id_incidencia"];

$busca_incidencia=db_query("SELECT * FROM incidencias_2017, incidencias_tipos_2017 WHERE incidencias_2017.tipo=incidencias_tipos_2017.id_tipo AND incidencias_2017.id_incidencia=$id_incidencia");

$datos_incidencia=db_fetch_array($busca_incidencia);

$nombre_tipo_actual=$datos_incidencia["nombre_tipo"];
$hora_incidencia=date("H:i",strtotime($datos_incidencia["hora_incidencia"]));
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$medidas_adoptadas1=$datos_incidencia["medidas_adoptadas1"];
$descripcion=$datos_incidencia["descripcion"];
$medidas_adoptadas=$datos_incidencia["medidas_adoptadas"];
$resuelta=$datos_incidencia["resuelta"];

$accion_eficaz=$datos_incidencia["accion_eficaz"];

$tipo_medida=$datos_incidencia["tipo_medida"];
$analisis_causas=$datos_incidencia["analisis_causas"];
$medidas_resultado_negativo=$datos_incidencia["medidas_resultado_negativo"];
$responsable_implantacion=$datos_incidencia["responsable_implantacion"];
$responsable_cierre=$datos_incidencia["responsable_cierre"];
$plazo_previsto=$datos_incidencia["plazo_previsto"];

$fecha="";
if ((strlen($datos_incidencia["fecha_incidencia"])!=0) and ($datos_incidencia["fecha_incidencia"]!='0000-00-00')) {
	$fecha=date("d/m/Y",strtotime($datos_incidencia["fecha_incidencia"]));
}

$fecha_cierre="";
if ((strlen($datos_incidencia["fecha_cierre"])!=0) and ($datos_incidencia["fecha_cierre"]!='0000-00-00')) {
	$fecha_cierre=date("d/m/Y",strtotime($datos_incidencia["fecha_cierre"]));
}

        // Busca nombre de la persona que notifica

        $busca_notificador=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["notificada_por"]);

        if (db_num_rows($busca_notificador)!=0) {
                $datos_notificador=db_fetch_array($busca_notificador);
                $nombre_notificador=$datos_notificador["Apellido1"]." ".$datos_notificador["Apellido2"].", ".$datos_notificador["Nombre"];
                } else {
                $nombre_notificador="Desconocido";
                }


        // Busca nombre de la persona que hace el seguimiento

        $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["seguimiento_por"]);
        $seguimiento_por=$datos_incidencia["seguimiento_por"];
        if (db_num_rows($busca_seguimiento)==0) {
                $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=$idUsuario");
                $seguimiento_por=$idUsuario;
                }
         $datos_seguimiento=db_fetch_array($busca_seguimiento);
         $nombre_seguimiento=$datos_seguimiento["Apellido1"]." ".$datos_seguimiento["Apellido2"].", ".$datos_seguimiento["Nombre"];

        // Busca nombre de la persona que ha provocado la incidencia

        $busca_incidenciado=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["id_usuario_incidencia"]);

        if (db_num_rows($busca_incidenciado)!=0) {
                $datos_incidenciado=db_fetch_array($busca_incidenciado);
                $nombre_incidenciado=$datos_incidenciado["Apellido1"]." ".$datos_incidenciado["Apellido2"].", ".$datos_incidenciado["Nombre"];
                $tipo_usuario_inci="";
                if ($datos_incidenciado["tipo_inscripcion"]==1) {
                        $tipo_usuario_inci=" (Alumno)";
                        }
                if ($datos_incidenciado["tipo_inscripcion"]==2) {
                        $tipo_usuario_inci=" (Padre)";
                        }
                if ($datos_incidenciado["tipo_inscripcion"]==4) {
                        $tipo_usuario_inci=" (Personal lápices)";
                        }
                } else {
                $nombre_incidenciado="Desconocido";
                $tipo_usuario_inci="";
                }
         
$formulario_name="formulario1";

$onload_script.="

    $('#fecha_incidencia').datepicker({
        language: 'es'
    });
    
    $('#fecha_cierre').datepicker({
        language: 'es'
    });    

";

?>
<div class="container text-center"><center>
<form method=POST action="detalles_incidencia_general1.php" name="<?=$formulario_name;?>">
<input type="hidden" name="id_incidencia" value="<?=$id_incidencia?>">
<input type="hidden" name="seguimiento_por" value="<?=$seguimiento_por?>">
<h2>Incidencia de <?=$nombre_tipo_actual;?></h2>
<table class="table table-bordered">
	<tr>
		<td align="right"><b>Código de incidencia:</b></td>
		<td><?=$id_incidencia?>-<?=date("Y",strtotime($fecha))?></td>
		<td align="right"><b>Escuela:</b></td>
		<td><?=$nombre_centro_actual;?></td>
	</tr>
	<tr>
		<td align="right"><b>Detectada por:</b></td>
		<td><?=$nombre_notificador?></td>
		<td align="right"><b>Fecha:</b></td>
		<td><input type="text" name="fecha_incidencia" id="fecha_incidencia" value="<?=$fecha;?>" required class="form-control"></td>
	</tr>
 	<tr>
		<td align="right"><b>Tipo de incidencia:</b></td>
		<td><select name="tipo" size=1 class="form-control">
        <?php 
        $busca_tipos=db_query("SELECT * FROM incidencias_tipos_2017 ORDER BY grupo,nombre_tipo");
		
		$grupo_actual="";
		
        while($tipos=db_fetch_array($busca_tipos)){
			
			if ($grupo_actual!=$tipos["grupo"]) {
				if ($grupo_actual!="") {
					?>
			</optgroup>
					<?php
				}
			?>
			<optgroup label="<?=$tipos["grupo"]?>">
			<?php
				$grupo_actual=$tipos["grupo"];
			}
		
        ?>
        <option value="<?=$tipos["id_tipo"];?>"<?php  if ($tipos["id_tipo"]==$datos_incidencia["tipo"]) { print " selected"; } ?>><?=$tipos["nombre_tipo"];?></option>
        <?php 
        }
        ?>
		</optgroup>
		</select></td>
		<td align="right"><b>Incidencia:</b></td>
		<td><input type="text" name="nombre_incidencia" size=60 readonly value="<?=urldecode($datos_incidencia["nombre_incidencia"])?>" class="form-control" onclick="intro_motivo()"></td>
	</tr>
	<tr>		
		<td align="right"><b>Alumno/Padre/Personal Lápices:</b></td>
		<td><?=$nombre_incidenciado;?><?=$tipo_usuario_inci;?></td>
		<td align="right"><b>Hora de notificación:</b></td>
		<td><?=$hora_incidencia;?></td>
	</tr>
	<tr>
		<td colspan="4"><b>Descripción de la incidencia:</b></td>
	</tr>	
	<tr>
		<td colspan="4"><textarea class="form-control" name="descripcion" rows=3><?=urldecode($descripcion);?></textarea></td>
	</tr>
	<tr>
		<td colspan="4"><b>Tipo de medida adoptada:</b></td>
	</tr>
	<tr>
		<td colspan="4"><input type="text" name="tipo_medida" readonly value="<?=urldecode($datos_incidencia["tipo_medida"])?>" class="form-control" onclick="intro_medida()"></td>
	</tr>	
	<tr>
		<td colspan="4"><b>Medidas adoptadas por la persona que notifica la incidencia:</b></td>
	</tr>	
	<tr>
		<td colspan="4"><textarea class="form-control" name="medidas_adoptadas1" rows=3><?=urldecode($medidas_adoptadas1);?></textarea></td>
	</tr>	
	<tr>
		<td colspan="4"><b>Analisis de causas:</b></td>
	</tr>	
	<tr>
		<td colspan="4"><textarea class="form-control" name="analisis_causas" rows=3><?=urldecode($analisis_causas);?></textarea></td>
	</tr>
	<tr>
		<td colspan="4"><b>Solución propuesta:</b></td>
	</tr>	
	<tr>
		<td colspan="4"><textarea class="form-control" name="medidas_adoptadas" rows=3><?=urldecode($medidas_adoptadas);?></textarea></td>
	</tr>
	<tr>
		<td align="right"><b>Responsable implantación:</b></td>
		<td><input type="text" class="form-control" value="<?=$responsable_implantacion?>" name="responsable_implantacion"></td>
		<td align="right"><b>Plazo previsto:</b></td>
		<td><input type="text" class="form-control" value="<?=$plazo_previsto?>" name="plazo_previsto"></td>
	</tr>
	<tr>
		<td colspan="4"><b>Seguimiento de la incidencia, verificaciópn de la eficacia y cierre:</b></td>
	</tr>
	<tr>
		<td colspan="4">
			<div id="seguimiento_incidencias">
			<?php
			$busca_seguimiento=db_query("SELECT incidencias_seguimiento.*,usuarios.Nombre,usuarios.Apellido1,usuarios.Apellido2 FROM incidencias_seguimiento,usuarios WHERE incidencias_seguimiento.id_usuario=usuarios.id_usuario AND incidencias_seguimiento.id_incidencia=$id_incidencia ORDER BY incidencias_seguimiento.fecha");
			
			while($seguimiento=db_fetch_array($busca_seguimiento)) {
				?>
			<div class="row" id="fila_seguimiento<?=$seguimiento["id_seguimiento"]?>" style="margin-bottom:5px;">
				<div class="col-md-10">
					<small><b><?=$seguimiento["Nombre"]?> <?=$seguimiento["Apellido1"]?> <?=$seguimiento["Apellido2"]?></b></small>
					<textarea class="form-control" id="nota_seguimiento<?=$seguimiento["id_seguimiento"]?>" name="nota_seguimiento<?=$seguimiento["id_seguimiento"]?>" rows=2 readonly><?=$seguimiento["seguimiento"]?></textarea>
				</div>
				<div class="col-md-2">
					<small>&nbsp;</small><br />
					<button type="button" id="boton_eliminar_seguimiento<?=$seguimiento["id_seguimiento"]?>" onclick="eliminar_seguimiento(<?=$seguimiento["id_seguimiento"]?>);" class="btn btn-danger btn-sm" style="margin-bottom:5px;"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</button><br />
					<button type="button" id="boton_editar_seguimiento<?=$seguimiento["id_seguimiento"]?>" onclick="editar_seguimiento(<?=$seguimiento["id_seguimiento"]?>);" class="btn btn-primary btn-sm" style="margin-bottom:5px;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
Editar</button>
				</div>
			</div>
				<?php
			}			
			?>	
			</div>
			<div class="row">
				<div class="col-md-10">
					<textarea class="form-control" id="nuevo_seguimiento" name="nuevo_seguimiento" rows=2></textarea>
				</div>
				<div class="col-md-2">
					<button type="button" id="boton_agregar_seguimiento" onclick="agregar_seguimiento();" class="btn btn-primary btn-sm">Añadir Nota Seguimiento</button>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="4"><b>En caso negativo indicar causas y que se va a hacer (desestimar, abrir otra incidencia,...)</b></td>
	</tr>
	<tr>
		<td colspan="4"><textarea class="form-control" name="medidas_resultado_negativo" rows=3><?=urldecode($medidas_resultado_negativo);?></textarea></td>
	</tr>		
	<tr>
		<td><b>Estado de la incidencia:</b></td>
		<td><input type="radio" name="resuelta"  value="0"<?php  if ($resuelta==0) { print " checked"; } ?>> Pendiente <input type="radio" name="resuelta" value="1"<?php  if ($resuelta==1) { print " checked"; } ?>> Cerrada</td>
		<td><b>¿Acción eficaz?</b></td>
		<td><input type="radio" name="accion_eficaz"  value="1"<?php  if ($accion_eficaz==1) { print " checked"; } ?>> Si <input type="radio" name="accion_eficaz" value="0"<?php  if ($accion_eficaz===0) { print " checked"; } ?>> No</td>
	</tr>	
	<tr>
		<td><b>Responsable del cierre:</b></td>
		<td><input type="text" class="form-control" value="<?=$responsable_cierre?>" name="responsable_cierre"></td>
		<td><b>Fecha cierre:</b></td>
		<td><input type="text" name="fecha_cierre" id="fecha_cierre" value="<?=$fecha_cierre;?>" required class="form-control"></td>
	</tr>
  </table>
  <br>
  <input type="button" onclick="enviar();" value="Guardar cambios" class="btn btn-primary"> <input type="button" onclick="window.location.href='incidencias_general.php?c=<?=$resuelta;?>'" value="Cancelar" class="btn btn-warning"><br><br><br>
  </form>
</center></div>
<script>

function eliminar_seguimiento(id_seguimiento) {

	if (confirm("¿Quieres eliminar la nota de seguimiento seleccioanda?")) {
		$.ajax({
			
			type: "POST",
			url: "/gestion/incidencias/eliminar_seguimiento.php",
			data:{
			  id_seguimiento: id_seguimiento,
			  id_incidencia: <?=$id_incidencia?>
			},
			cache: false,
			success : function(html){
				$("#fila_seguimiento"+id_seguimiento).html('');
			},
			error:function(){
				// alert("error");
			}
			
		});
	}
	
}

function editar_seguimiento(id_seguimiento) {

	if ($("#nota_seguimiento"+id_seguimiento).prop('readonly') == true) {
		$("#nota_seguimiento"+id_seguimiento).prop('readonly',false);
		$("#boton_editar_seguimiento"+id_seguimiento).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar');
	} else {
		guardar_seguimiento(id_seguimiento);
	}

}

function guardar_seguimiento(id_seguimiento) {

	if ($("#nota_seguimiento"+id_seguimiento).val()=="") {
		alert("No has escrito ningún texto en la nota de seguimiento");
	} else {
		$.ajax({
			
			type: "POST",
			url: "/gestion/incidencias/editar_seguimiento.php",
			data:{
			  id_seguimiento: id_seguimiento,
			  id_incidencia: <?=$id_incidencia?>,
			  seguimiento: $("#nota_seguimiento"+id_seguimiento).val()
			},
			cache: false,
			success : function(html){
				$("#nota_seguimiento"+id_seguimiento).prop('readonly',true);
				$("#boton_editar_seguimiento"+id_seguimiento).html('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar');
			},
			error:function(){
				// alert("error");
			}
			
		});
	}		
	
}

function agregar_seguimiento() {
	
	if ($("#nuevo_seguimiento").val()=="") {
		alert("No has escrito ningún texto en la nota de seguimiento");
	} else {
		$.ajax({
			
			type: "POST",
			url: "/gestion/incidencias/editar_seguimiento.php",
			data:{
			  id_seguimiento: 0,
			  id_incidencia: <?=$id_incidencia?>,
			  seguimiento: $("#nuevo_seguimiento").val()
			},
			cache: false,
			success : function(html){
				$("#seguimiento_incidencias").append(html);
				seguimiento: $("#nuevo_seguimiento").val('');
			},
			error:function(){
				// alert("error");
			}
			
		});
	}
	
}

function enviar() {
                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.descripcion.value=="") { mensajes_error=mensajes_error+"Debes describir la incidencia.\u000A";errores=1; }

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
        
function intro_motivo() {
        window.open('listado_tipos_incidencias.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
		
function intro_medida() {
        window.open('listado_tipos_medidas.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
		
//-->
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

