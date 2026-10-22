
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
		
$filtroCentro = 'alumnos.id_centro=10';        		
		
$id_centro=$_GET["id_centro"];		
if (strlen($id_centro)==0) {
		$id_centro=$centro_actual;
        } else {
        
        $filtroCentro = 'alumnos.id_centro='.$id_centro;        
        
        }

$fechanac=$_GET["fechanac"];
$filtroedad="";
$fechasolo=$_GET["fechasolo"];

if (strlen($fechanac)!=0) {
		
		if ($fechasolo==1) {
			$filtroedad=" AND (year(usuarios.fecha_nacimiento)=".$fechanac.")";	
		} else {
			$filtroedad=" AND (year(usuarios.fecha_nacimiento)>=".$fechanac." OR usuarios.nonato=1)";
		}
	
}

$filtroedad=" AND year(usuarios.fecha_nacimiento)>=2010 AND year(usuarios.fecha_nacimiento)<=2018";

$consultaAlumnos="SELECT *, usuarios.id_usuario AS id_usuario_alumno, year(usuarios.fecha_nacimiento) AS anio_nacimiento FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula".$filtroCiclo." AND $filtroCentro".$filtroCurso.$filtroedad." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre";		

$busca_alumnos=db_query($consultaAlumnos) or die(db_error());

$lista_emails_destino=array();
$datos_alumno_destino=array();
$array_edades=array();

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
	$busca_padres=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padre_de=?', 's', array($listado_alumnos["id_usuario_alumno"]));
	while($padres=db_fetch_array($busca_padres)) {
	
		$array_to[]=$padres["email"];
		
		// Móviles

		$t1=$padres["telefono1"];
		preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
		foreach($matches1[0] as $numero) {
			if (substr($numero,0,1)!="9") {
			
				if (!in_array($numero,$lista_sms_destino)) {
				
					$lista_sms_destino[]=$numero;
					$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
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
					$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
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
					$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
					$num_sms_enviar++;
				
				}
			
			}
		}
	
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
		
	// Móviles
	
	$t1=$listado_alumnos["telefono1"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
	foreach($matches1[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
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
				$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
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
				$array_edades[$numero]=$listado_alumnos["anio_nacimiento"];
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
	echo($sms.";".$array_edades[$sms]."<br>");
}

?>