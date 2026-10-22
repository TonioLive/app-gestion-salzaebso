<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_incidencia=$_POST["id_incidencia"];
$seguimiento=$_POST["seguimiento"];
$id_seguimiento=$_POST["id_seguimiento"];

if ($id_seguimiento==0) {
	
	$inserta_seguimiento=db_query("INSERT INTO incidencias_seguimiento (id_usuario,id_incidencia,seguimiento) VALUES ('$idUsuario','$id_incidencia','".db_real_escape_string(app_text_utf8($seguimiento))."')");
	
	$id_seguimiento=db_insert_id();
	
?>
<div class="row" id="fila_seguimiento<?=$id_seguimiento?>" style="margin-bottom:5px;">
	<div class="col-md-10">
		<small><b><?=$nombre_completo?></b></small>
		<textarea class="form-control" id="nota_seguimiento<?=$id_seguimiento?>" name="nota_seguimiento<?=$id_seguimiento?>" rows=2 readonly><?=$seguimiento?></textarea>
	</div>
	<div class="col-md-2">
		<small>&nbsp;</small><br/>
		<button type="button" id="boton_eliminar_seguimiento<?=$id_seguimiento?>" onclick="eliminar_seguimiento(<?=$id_seguimiento?>);" class="btn btn-danger btn-sm" style="margin-bottom:5px;"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</button><br />
		<button type="button" id="boton_editar_seguimiento<?=$id_seguimiento?>" onclick="editar_seguimiento(<?=$id_seguimiento?>);" class="btn btn-primary btn-sm" style="margin-bottom:5px;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
 Editar</button>
	</div>
</div>
<?php
	
} else {

	$guarda_seguimiento=db_query("UPDATE incidencias_seguimiento SET seguimiento='".db_real_escape_string(app_text_utf8($seguimiento))."' WHERE id_seguimiento=$id_seguimiento");
	
}

?>