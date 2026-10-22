<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_curso=$_GET["id_curso"];
$filtroCurso="";

if (strlen($id_curso)!=0) {
	$filtroCurso=" AND alumnos.id_curso=$id_curso";	
}

$ciclo=$_GET["ciclo"];

$filtroCiclo="";
if (strlen($ciclo)!=0) {

	if ($ciclo==1) {
		$filtroCiclo=' AND (centro_aulas.ciclo=1 OR centro_aulas.ciclo=0)';
	}
	if ($ciclo!=1) {
		$filtroCiclo = 'AND centro_aulas.ciclo='.$ciclo;
	}
}

$filtroCentro = 'alumnos.id_centro=20';        		
		
$id_centro=$_GET["id_centro"];		
if (strlen($id_centro)==0) {
		
	$id_centro=$centro_actual;

} else {
        
	$filtroCentro = 'alumnos.id_centro='.$id_centro;        
        
}

$fechanac=$_GET["fechanac"];
$filtroedad="";
if (strlen($fechanac)!=0) {
		
	$filtroedad=" AND year(usuarios.fecha_nacimiento)>=".$fechanac;
	
}

$fechafin=$_GET["fechafin"];
if (strlen($fechafin)!=0) {
		
	$filtroedad=" AND year(usuarios.fecha_nacimiento)<=".$fechafin;
	
}

// $filtroedad=" AND year(usuarios.fecha_nacimiento)>=2011 AND year(usuarios.fecha_nacimiento)<=2014";
// $filtroedad=" AND year(usuarios.fecha_nacimiento)>=2015 AND year(usuarios.fecha_nacimiento)<=2018";


$fecha_desde=$_GET["fecha"];
$filtrofecha="";
if (strlen($fecha_desde)!=0) {
	$filtrofecha=" AND fecha_baja>='".$fecha_desde."'";		
}
  
$filtroServicio = $_GET["filtro_servicio"];

$consultaAlumnos="SELECT * FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula".$filtroCiclo." AND ".$filtroCentro.$filtroCurso.$filtroedad.$filtrofecha." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre";		

$busca_alumnos=db_query($consultaAlumnos) or die(db_error());

$lista_emails_destino=array();

$contador_alumnos=0;

while($listado_alumnos=db_fetch_array($busca_alumnos)){

	$fila=$listado_alumnos["id_usuario"];
		
	$procesar=true;
	if ($filtroServicio!="") {

		// Comprueba servicio contratado
		$buscaServicio=db_query("SELECT id_servicio_contratado FROM servicios_contratados WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_servicio=$filtroServicio AND cantidad>0 LIMIT 1");

		if (db_num_rows($buscaServicio)==0) {
			$procesar=false;
		}/* else {
			$datosServicio=mysql_fetch_array($buscaServicio);
			echo($listado_alumnos["id_usuario"]." ".$listado_alumnos["id_alumno"]." ".$datosServicio["id_servicio_contratado"]."<br>");
		} */

	}

	if ($procesar==true) {
		// Array con todos los emails del alumno

		$listado_emails=str_replace(" ","",$listado_alumnos["email"]);

		$array_to = explode(",",$listado_emails);
		
		$lista_emails="";
			
		foreach ($array_to as $to) {        
			
			if (comprobar_email($to)!=0) {
			
				// Comprueba si está en la lista negra
				$busca_lista=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
				
				if (db_num_rows($busca_lista)==0) {
					if (!in_array($to,$lista_emails_destino)) {
			
						$lista_emails_destino[]=$to;
						$num_emails_enviar++;
					
					}			
				}
										
			}
		} 
	}	

}
$contador_emails=0;
foreach($lista_emails_destino as $email) {
	$contador_emails++;
	echo($email."<br>");
}
?>