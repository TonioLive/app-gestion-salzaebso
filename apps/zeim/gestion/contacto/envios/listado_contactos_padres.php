<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$nombresCentros=array();
$buscaCentros = db_query("SELECT * FROM centros ORDER BY nombre_centro DESC;");
while($centros=db_fetch_array($buscaCentros)) {

    $nombresCentros[$centros["id_centro"]]=$centros["nombre_centro"];

}

$id_curso=$_GET["id_curso"];
$ciclo=$_GET["ciclo"];
$id_centro=$_GET["id_centro"];		
$fechanac=$_GET["fechanac"];
$nonatos=$_GET["incluirnonatos"];
$fechafin=$_GET["fechafin"];


$origendatos = $_GET['origendatos'];
if (strlen($origendatos)==0) {
	$origendatos = 'alumnos';
}

switch ($origendatos) {
	case 'alumnos':

		$filtroCurso="";
		if (strlen($id_curso)!=0) {
			if ($id_curso=='aa') {
				$filtroCurso=" AND alumnos.fecha_baja<'".date("Y-m-d")."'";
			} else {
				$filtroCurso=" AND alumnos.id_curso=$id_curso";
				// $filtroCurso="(alumnos.id_curso=30 OR alumnos.id_curso=32)";
			}
		}
		
		$filtroCiclo="";
		if (strlen($ciclo)!=0) {
		
			if ($ciclo==1) {
				$filtroCiclo=' AND (centro_aulas.ciclo=1 OR centro_aulas.ciclo=0)';
			}
			if ($ciclo!=1) {
				$filtroCiclo = ' AND centro_aulas.ciclo='.$ciclo;
			}
		}
				
		$filtroCentro = '(alumnos.id_centro=6 OR alumnos.id_centro=7 OR alumnos.id_centro=8 OR alumnos.id_centro=13)';        		
				
		if (strlen($id_centro)==0) {
				$id_centro=$centro_actual;
				} else {
				
				$filtroCentro = 'alumnos.id_centro='.$id_centro;        
				
				}
		
		$filtroedad="";
		
		if (strlen($fechanac)!=0) {
				
				if ($nonatos==1) {
					$filtroedad=" AND (year(usuarios.fecha_nacimiento)>=".$fechanac." OR usuarios.nonato=1)";
				} else {
					$filtroedad=" AND (year(usuarios.fecha_nacimiento)>=".$fechanac.")";	
				}
			
		}
		
		if (strlen($fechafin)!=0) {
				
			if ($nonatos==1) {
				$filtroedad.=" AND (year(usuarios.fecha_nacimiento)<=".$fechafin." OR usuarios.nonato=1)";
			} else {
				$filtroedad.=" AND (year(usuarios.fecha_nacimiento)<=".$fechafin.")";	
			}	
			
		}	

		$consultaAlumnos="SELECT *, usuarios.id_usuario AS id_usuario_alumno, year(usuarios.fecha_nacimiento) AS anio_nacimiento, alumnos.fecha_matricula,alumnos.fecha_baja, alumnos.id_centro AS centro_alumno FROM usuarios,alumnos,centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula".$filtroCiclo." AND $filtroCentro".$filtroCurso.$filtroedad." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre";		
				
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
				
				// M?viles

				$t1=$padres["telefono1"];
				preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
				foreach($matches1[0] as $numero) {
					if (substr($numero,0,1)!="9") {
					
						if (!in_array($numero,$lista_sms_destino)) {
						
							$lista_sms_destino[]=$numero;
							if ($listado_alumnos["nonato"]!=1) {
								$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
							} else {
								$array_edades[$numero][0]='';    
							}
							$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
							$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
							$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
							$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
							$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];
							$array_edades[$numero][6]=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
							$array_edades[$numero][7]=$padres["email"];
							$array_edades[$numero][8]=$padres["DNI"];
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
							if ($listado_alumnos["nonato"]!=1) {
								$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
							} else {
								$array_edades[$numero][0]='';    
							}
							$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
							$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
							$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
							$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
							$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];
							$array_edades[$numero][6]=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
							$array_edades[$numero][7]=$padres["email"];
							$array_edades[$numero][8]=$padres["DNI"];
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
							if ($listado_alumnos["nonato"]!=1) {
								$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
							} else {
								$array_edades[$numero][0]='';    
							}
							$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
							$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
							$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
							$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
							$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];
							$array_edades[$numero][6]=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
							$array_edades[$numero][7]=$padres["email"];
							$array_edades[$numero][8]=$padres["DNI"];
							$num_sms_enviar++;
						
						}
					
					}
				}
			
			}	
			
			foreach ($array_to as $to) {        
				
				if (comprobar_email($to)!=0) {
				
					// Comprueba si est? en la lista negra
					$busca_lista=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
					
					if (db_num_rows($busca_lista)==0) {
						if (!in_array($to,$lista_emails_destino)) {
				
							$lista_emails_destino[]=$to;
							$num_emails_enviar++;
							
						}			
					}    
				}
			} 	
				
			// M?viles
			
			$t1=$listado_alumnos["telefono1"];
			preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
			foreach($matches1[0] as $numero) {
				if (substr($numero,0,1)!="9") {
				
					if (!in_array($numero,$lista_sms_destino)) {
					
						$lista_sms_destino[]=$numero;
						if ($listado_alumnos["nonato"]!=1) {
							$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
						} else {
							$array_edades[$numero][0]='';    
						}
						$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
						$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
						$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
						$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
						$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];						
                        $array_edades[$numero][6]="";
                        $array_edades[$numero][7]=$listado_alumnos["email"];                                                
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
						if ($listado_alumnos["nonato"]!=1) {
							$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
						} else {
							$array_edades[$numero][0]='';    
						}
						$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
						$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
						$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
						$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
						$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];
                        $array_edades[$numero][6]="";
                        $array_edades[$numero][7]=$listado_alumnos["email"];                         					
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
						if ($listado_alumnos["nonato"]!=1) {
							$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
						} else {
							$array_edades[$numero][0]='';    
						}
						$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
						$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
						$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
						$array_edades[$numero][4]=$listado_alumnos["id_usuario"];
						$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];	
                        $array_edades[$numero][6]="";
                        $array_edades[$numero][7]=$listado_alumnos["email"];                         		
						$num_sms_enviar++;
					
					}
				
				}
			}		
				

		}
		
		$data = array(
			['id_alumno','centro','tel?fono','a?o nacimiento','nombre alumno','nombre padre/madre','DNI Padre/madre','email','fecha alta','fecha baja']
		);
		foreach($lista_sms_destino as $sms) {
			$data[] = [$array_edades[$sms][4],$array_edades[$sms][5],$sms,$array_edades[$sms][0],$array_edades[$sms][1],$array_edades[$sms][6],$array_edades[$sms][8],$array_edades[$sms][7],$array_edades[$sms][2],$array_edades[$sms][3]];
		}

	break;
	case 'contactos':

		$filtroCurso="";
		if (strlen($id_curso)!=0) {
			$filtroCurso=" AND contactos.id_curso=$id_curso";
		}
		
		$filtroCiclo="";
				
		$filtroCentro = '(contactos.id_centro=6 OR contactos.id_centro=7 OR contactos.id_centro=8 OR contactos.id_centro=13)';        		
				
		if (strlen($id_centro)==0) {
				$id_centro=$centro_actual;
				} else {
				
				$filtroCentro = 'contactos.id_centro='.$id_centro;        
				
				}
		
		$filtroedad="";
		
		if (strlen($fechanac)!=0) {
				
				if ($nonatos==1) {
					$filtroedad=" AND (year(contactos.fecha_nacimiento)>=".$fechanac." OR contactos.nonato=1)";
				} else {
					$filtroedad=" AND (year(contactos.fecha_nacimiento)>=".$fechanac.")";	
				}
			
		}
		
		if (strlen($fechafin)!=0) {
				
			if ($nonatos==1) {
				$filtroedad.=" AND (year(contactos.fecha_nacimiento)<=".$fechafin." OR contactos.nonato=1)";
			} else {
				$filtroedad.=" AND (year(contactos.fecha_nacimiento)<=".$fechafin.")";	
			}	
			
		}	

		$consultaAlumnos="SELECT *, contactos.id_contacto AS id_usuario_alumno, year(contactos.fecha_nacimiento) AS anio_nacimiento, contactos.id_centro AS centro_alumno FROM contactos WHERE id_contacto>0".$filtroCiclo." AND $filtroCentro".$filtroCurso.$filtroedad." ORDER BY Apellido1, Apellido2, Nombre";		
		
		$busca_alumnos=db_query($consultaAlumnos) or die(db_error());
		
		$busca_alumnos=db_query($consultaAlumnos) or die(db_error());
		
		$lista_emails_destino=array();
		$datos_alumno_destino=array();
		$array_edades=array();

		$num_sms_enviar=0;
		$lista_sms_destino=array();

		$contador_alumnos=0;

		while($listado_alumnos=db_fetch_array($busca_alumnos)){

			$fila=$listado_alumnos["id_usuario"];
								
			// M?viles
			
			$t1=$listado_alumnos["telefono"];
			preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
			foreach($matches1[0] as $numero) {
				if (substr($numero,0,1)!="9") {
				
					if (!in_array($numero,$lista_sms_destino)) {
					
						$lista_sms_destino[]=$numero;
						if ($listado_alumnos["nonato"]!=1) {
							$array_edades[$numero][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
						} else {
							$array_edades[$numero][0]='';    
						}
						$array_edades[$numero][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
						$array_edades[$numero][2]=$listado_alumnos["fecha_matricula"];
						$array_edades[$numero][3]=$listado_alumnos["fecha_baja"];	
						$array_edades[$numero][4]=$listado_alumnos["id_contacto"];
						$array_edades[$numero][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];
						$array_edades[$numero][6]='No';			
						if (intval($listado_alumnos["id_usuario"])>0) {
							$array_edades[$numero][6]='Si';
						}	
						$array_edades[$numero][7]='';			
						$array_edades[$numero][8]=$listado_alumnos["email"];			
						$num_sms_enviar++;
					}
				
				}
			}		
		}

		$data = array(
			['id_contacto','centro','email','fecha nacimiento','nombre alumno','nombre padre/madre','DNI Padre/madre','email','fecha contacto','estado','matriculado']
		);
		foreach($lista_sms_destino as $sms) {
			$data[] = [$array_edades[$sms][4],$array_edades[$sms][5],$sms,$array_edades[$sms][0],$array_edades[$sms][1],$array_edades[$sms][7],'',$array_edades[$sms][8],$array_edades[$sms][2],$array_edades[$sms][3],$array_edades[$sms][6]];
		}
	break;		
}


header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="listado_contactos_'.date("y-m-d-his").'.csv"');

$fp = fopen('php://output', 'wb');
foreach ($data as $fields) {
    fputcsv($fp, $fields,";");
}
fclose($fp);

?>