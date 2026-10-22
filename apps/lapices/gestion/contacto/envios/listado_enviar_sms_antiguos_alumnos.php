<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

// Calcula curso actual
$id_curso=$_GET["id_curso"];

if (strlen($id_curso)==0) {

	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

	if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso=$datos_curso_actual["id_curso"];
	} else {
		$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

		if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso=$datos_curso_actual["id_curso"];
		} else {
			$id_curso=0;
		}
	}
}	
	
$filtroCentro = '(alumnos.id_centro=6 OR alumnos.id_centro=7 OR alumnos.id_centro=8 OR alumnos.id_centro=13)';        		
		
$id_centro=$_GET["id_centro"];		
if (strlen($id_centro)==0) {
		$id_centro=$centro_actual;
        } else {
        
        $filtroCentro = 'alumnos.id_centro='.$id_centro;        
        
        }
        
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND $filtroCentro AND year(usuarios.fecha_nacimiento)>=2012 AND NOT EXISTS(SELECT id_usuario FROM alumnos WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$id_curso) ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre") or die(db_error());

$lista_emails_destino=array();
$datos_alumno_destino=array();

$num_sms_enviar=0;
$lista_sms_destino=array();

$contador_alumnos=0;

while($listado_alumnos=db_fetch_array($busca_alumnos)){

	$fila=$listado_alumnos["id_usuario"];
		
	// Array con todos los emails del alumno

	$listado_emails=str_replace(" ","",$listado_alumnos["email"]);

	$array_to = explode(",",$listado_emails);
	
	$lista_emails="";
        
	// Busca datos padres
	$busca_padres=db_query("SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padre_de=".$listado_alumnos["id_usuario_alumno"]);
	while($padres=db_fetch_array($busca_padres)) {
	
		$array_to[]=$padres["email"];
		
		// Móviles

		$t1=$padres["telefono1"];
		preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
		foreach($matches1[0] as $numero) {
			if (substr($numero,0,1)!="9") {
			
				if (!in_array($numero,$lista_sms_destino)) {
				
					$lista_sms_destino[]=$numero;
					$num_sms_enviar++;
				}
			
			}
		}
		
		$t2=$padres["telefono2"];
		preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t2, $matches2);
		foreach($matches2[0] as $numero) {
			if (substr($numero,0,1)!="9") {
			
				if (!in_array($numero,$lista_sms_destino)) {
				
					$lista_sms_destino[]=$numero;
					$num_sms_enviar++;
				
				}
			
			}
		}	
		$t3=$padres["movil"];
		preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t3, $matches3);
		foreach($matches3[0] as $numero) {
			if (substr($numero,0,1)!="9") {
			
				if (!in_array($numero,$lista_sms_destino)) {
				
					$lista_sms_destino[]=$numero;
					$num_sms_enviar++;
				
				}
			
			}
		}
	
	}	
	
	foreach ($array_to as $to) {        
        
		if (comprobar_email($to)!=0) {
		
			// Comprueba si está en la lista negra
			$busca_lista=db_query("SELECT * FROM no_recibir_mail WHERE email='".$to."'");
			
			if (db_num_rows($busca_lista)==0) {
				if (!in_array($to,$lista_emails_destino)) {
		
	    	        $lista_emails_destino[]=$to;
					$num_emails_enviar++;
					
				}			
			}    
		}
	} 	
		
	// Móviles
	
	$t1=$listado_alumnos["telefono1"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
	foreach($matches1[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			}
		
		}
	}
	
	$t2=$listado_alumnos["telefono2"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t2, $matches2);
	foreach($matches2[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}	
	$t3=$listado_alumnos["movil"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t3, $matches3);
	foreach($matches3[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}		
		

}
/*
foreach($lista_emails_destino as $email) {
	echo($email."<br>");
}
*/
foreach($lista_sms_destino as $sms) {
	echo($sms."<br>");
}

?>