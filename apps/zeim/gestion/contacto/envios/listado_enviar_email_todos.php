<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_curso=$_GET["id_curso"];
$filtroCurso="";
if (strlen($id_curso)!=0) {
	$filtroCurso=" AND alumnos.id_curso=$id_curso";
	// $filtroCurso="(alumnos.id_curso=30 OR alumnos.id_curso=32)";
}

$ciclo=$_GET["ciclo"];

$filtroCiclo="";
if (strlen($ciclo)!=0) {

	if ($ciclo==1) {
		$filtroCiclo=' AND (centro_aulas.ciclo=1 OR centro_aulas.ciclo=0)';
	}
	if ($ciclo!=1) {
		$filtroCiclo = ' AND centro_aulas.ciclo='.$ciclo;
	}
}
		
$filtroCentro = '(alumnos.id_centro=10)';        		
		
$id_centro=$_GET["id_centro"];		
if (strlen($id_centro)==0) {
		$id_centro=$centro_actual;
        } else {
        
        $filtroCentro = 'alumnos.id_centro='.$id_centro;        
        
        }

$fechanac=$_GET["fechanac"];
$filtroedad="";
$nonatos=$_GET["incluirnonatos"];

if (strlen($fechanac)!=0) {
		
		if ($nonatos==1) {
			$filtroedad=" AND (year(usuarios.fecha_nacimiento)>=".$fechanac." OR usuarios.nonato=1)";
		} else {
			$filtroedad=" AND (year(usuarios.fecha_nacimiento)>=".$fechanac.")";	
		}
	
}

$fechafin=$_GET["fechafin"];
if (strlen($fechafin)!=0) {
		
	if ($nonatos==1) {
		$filtroedad=" AND (year(usuarios.fecha_nacimiento)<=".$fechafin." OR usuarios.nonato=1)";
	} else {
		$filtroedad=" AND (year(usuarios.fecha_nacimiento)<=".$fechafin.")";	
	}	
	
}	

$edadmax=$_GET["edadmax"];

if (strlen($edadmax)!=0) {
		
	$filtroedad=" AND centro_aulas.edad_maxima<=".$edadmax;	

}
		
$consultaAlumnos="SELECT *, usuarios.id_usuario AS id_usuario_alumno FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula".$filtroCiclo." AND $filtroCentro".$filtroCurso.$filtroedad." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre";		

$busca_alumnos=db_query($consultaAlumnos) or die(db_error());

$lista_emails_destino=array();

$contador_alumnos=0;

while($listado_alumnos=db_fetch_array($busca_alumnos)){

	$fila=$listado_alumnos["id_usuario"];
		
	// Array con todos los emails del alumno

	$listado_emails=str_replace(" ","",$listado_alumnos["email"]);

	$array_to = explode(",",$listado_emails);
	
	$lista_emails="";
        
	// Busca datos padres
	$busca_padres=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padre_de=?', 's', array($listado_alumnos["id_usuario_alumno"]));
	while($padres=db_fetch_array($busca_padres)) {
	
		$array_to[]=$padres["email"];
	
	}	
	
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

foreach($lista_emails_destino as $email) {
	echo($email."<br>");
}

?>