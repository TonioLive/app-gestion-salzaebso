<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// if (($idUsuario!=166) and ($idUsuario!=938)) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];

$id_alumno=$_GET["id_alumno"];
$id_curso=$_GET["id_curso"];

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));	
$datos_curso_actual=db_fetch_array($busca_curso_actual);

if (strtotime($datos_curso_actual["fecha_inicio"])>time()) {

		// Procedemos a borrar
	
		// Borra facturas
		$busca_detalles_factura=db_query_params('SELECT * FROM facturas_emitidas WHERE id_cliente=?', 's', array($id_alumno)) or die(db_error());
		while($lista_detalles_factura=db_fetch_array($busca_detalles_factura)) {
	
			db_query_params('DELETE FROM detalles_factura WHERE id_factura=?', 's', array($lista_detalles_factura["id_factura"])) or die(db_error());			
			
			// echo("DELETE FROM detalles_factura WHERE id_factura=".$lista_detalles_factura["id_factura"]."<br>");
				
			}
	
		db_query_params('DELETE FROM facturas_emitidas WHERE id_cliente=?', 's', array($id_alumno)) or die(db_error());
		// echo("DELETE FROM facturas_emitidas WHERE id_cliente=".$id_alumno."<br>");

		// Borra servicios contratados
		db_query_params('DELETE FROM servicios_contratados WHERE id_alumno=?', 's', array($id_alumno)) or die(db_error());
		// echo("DELETE FROM servicios_contratados WHERE id_alumno=".$id_alumno."<br>");
		
		// Borra de los grupos
		db_query_params('DELETE FROM aulas_alumnos_grupos WHERE id_curso=? AND id_alumno=?', 'ss', array($id_curso, $id_alumno)) or die(db_error());
		// echo("DELETE FROM aulas_alumnos_grupos WHERE id_curso=$id_curso AND id_alumno=".$id_alumno."<br>");
	
		// Borra ficha de alumno
		db_query_params('DELETE FROM alumnos WHERE id_curso=? AND id_alumno=?', 'ss', array($id_curso, $id_alumno)) or die(db_error());
		// echo("DELETE FROM alumnos WHERE id_curso=$id_curso AND id_alumno=".$id_alumno."<br>");
	
}

// header("Location: detalles_grupo.php?id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&fecha_limite=$fecha_limite");

?>
<script>
window.parent.location.href="index.php?curso_seleccionado=<?=$id_curso?>";
</script>