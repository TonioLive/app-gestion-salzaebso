<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_plantilla=$_POST["id_plantilla"];
$sesion_editada=$_POST["sesion_editada"];
$programacion=$_POST["duallistbox_ActividadesProgramadas"];

$volver=$_POST["volver"];
$pagina_activa=$_POST["pagina_activa"];

if ($programacion) {

	$programacion=array_unique($programacion);

}

$sesiones_copiar=$_POST["sesiones_copiar"];
$borrar=$_POST["borrar"];

$lista_sesiones_creadas=array();

if ($borrar==1) {

if (count($sesiones_copiar)!=0) {
	
	foreach ($sesiones_copiar as $sesion_copiar) {
		
		$borra_programacion=db_query("DELETE FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_copiar") or die(db_error());
		
		}
	
	}
}

foreach ($programacion as $fila){ 

	// Inserta
	$lista_sesiones_creadas[]=$fila;
	$comprueba_si_esta=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_editada AND id_actividad=$fila") or die(db_error());
	if (db_num_rows($comprueba_si_esta)==0) {
		$inserta=db_query("INSERT INTO agenda_programacion_plantillas_actividades (id_plantilla,numero_sesion,id_actividad) VALUES ('$id_plantilla','$sesion_editada','$fila');");
	}
	
	
	if (count($sesiones_copiar)!=0) {
	
		foreach ($sesiones_copiar as $sesion_copiar) {
		
			// Comprueba si está
			$busca_1=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_copiar AND id_actividad=$fila");
			
			if (db_num_rows($busca_1)==0) {
				$inserta=db_query("INSERT INTO agenda_programacion_plantillas_actividades (id_plantilla,numero_sesion,id_actividad) VALUES ('$id_plantilla','$sesion_copiar','$fila');");
			}					
		
			}
	
	}

}

// Borra las que deban ser borradas
$busca_todas=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesion_editada") or die(db_error());
while($fila_esta=db_fetch_array($busca_todas)) {

	// Comprueba si está en el array
	if (!in_array($fila_esta['id_actividad'],$lista_sesiones_creadas)) {
		// Borra actividad actual	
		$borra_programacion=db_query("DELETE FROM agenda_programacion_plantillas_actividades WHERE id_prog=".$fila_esta['id_prog']) or die(db_error());	
		
	}	
	
}

if ($nueva_act==1) {

	// Introduce nueva actividad
	$inserta_actividad=db_query("INSERT INTO agenda_programacion_actividades_piscina (id_objetivo, descripcion_actividad) VALUES ('".$_POST["objetivo_nueva"]."','".$_POST["actividad_nueva"]."')");
	
	header("Location: editar_plantillas.php?sesion_editada=$sesion_editada&id_plantilla=$id_plantilla");

}

if ($volver==1) {

	header("Location: editar_plantillas.php?pagina_activa=$pagina_activa&sesion_editada=$sesion_editada&id_plantilla=$id_plantilla");	
	
} else {

	header("Location: gestion_plantillas.php?id_plantilla=$id_plantilla");	
	
}
?>