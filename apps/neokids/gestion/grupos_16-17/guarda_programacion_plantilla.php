<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_plantilla=$_POST["id_plantilla"];
$sesion_editada=$_POST["sesion_editada"];
$programacion=$_POST["duallistbox_ActividadesProgramadas"];

if ($programacion) {

	$programacion=array_unique($programacion);

}

$sesiones_copiar=$_POST["sesiones_copiar"];
$borrar=$_POST["borrar"];

// Borra programacion actual
$borra_programacion=db_query("DELETE FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_editada") or die(db_error());

if ($borrar==1) {

if (count($sesiones_copiar)!=0) {
	
	foreach ($sesiones_copiar as $sesion_copiar) {
		
		$borra_programacion=db_query("DELETE FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_copiar") or die(db_error());
		
		}
	
	}
}

foreach ($programacion as $fila){ 

	// Inserta
	$inserta=db_query_params('INSERT INTO agenda_programacion_plantillas_actividades (id_plantilla,numero_sesion,id_actividad) VALUES (?,?,?);', 'sss', array((string)($id_plantilla), (string)($sesion_editada), (string)($fila)));
	
	
	if (count($sesiones_copiar)!=0) {
	
		foreach ($sesiones_copiar as $sesion_copiar) {
		
			// Comprueba si está
			$busca_1=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_copiar AND id_actividad=$fila");
			
			if (db_num_rows($busca_1)==0) {
				$inserta=db_query_params('INSERT INTO agenda_programacion_plantillas_actividades (id_plantilla,numero_sesion,id_actividad) VALUES (?,?,?);', 'sss', array((string)($id_plantilla), (string)($sesion_copiar), (string)($fila)));
			}					
		
			}
	
	}

}

if ($nueva_act==1) {

	// Introduce nueva actividad
	$inserta_actividad=db_query_params('INSERT INTO agenda_programacion_actividades_piscina (id_objetivo, descripcion_actividad) VALUES (?,?)', 'ss', array((string)($_POST["objetivo_nueva"]), (string)($_POST["actividad_nueva"])));
	
	header("Location: editar_plantillas.php?sesion_editada=$sesion_editada&id_plantilla=$id_plantilla");

}

header("Location: gestion_plantillas.php?id_plantilla=$id_plantilla");

?>