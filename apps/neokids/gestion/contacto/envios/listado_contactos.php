<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$nombresCentros=array();
$buscaCentros = db_query("SELECT * FROM centros ORDER BY nombre_centro DESC;");
while($centros=db_fetch_array($buscaCentros)) {

    $nombresCentros[$centros["id_centro"]]=$centros["nombre_centro"];

}

$id_curso=$_GET["id_curso"];
$id_trimestre=$_GET["id_trimestre"];
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

		if (strlen($id_trimestre)!=0) {
			// Filtro por trimestre
			// Busca curso
			$buscaCurso=db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE id_trimestre=?;', 's', array($id_trimestre));
			if (db_num_rows($buscaCurso)!=0) {
				$datosCurso=db_fetch_array($buscaCurso);

				$id_curso=$datosCurso["id_curso"];

				$fechaIniTrimestre=$datosCurso["fecha_inicio"];
				$fechaFinTrimestre=$datosCurso["fecha_fin"];
				
				$filtroCurso=" AND alumnos.id_curso=$id_curso AND alumnos.fecha_baja>'".$fechaIniTrimestre."' AND alumnos.fecha_matricula<='".$fechaFinTrimestre."'";

			}
		} else {
			// Filtro por curso
			if (strlen($id_curso)!=0) {
				if ($id_curso=='aa') {
					$filtroCurso=" AND alumnos.fecha_baja<'".date("Y-m-d")."'";
				} else {
					$filtroCurso=" AND alumnos.id_curso=$id_curso";
					// $filtroCurso="(alumnos.id_curso=30 OR alumnos.id_curso=32)";
				}
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
				
		$filtroCentro = '(alumnos.id_centro=20)';

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
		
		$contador_alumnos=0;
		
		while($listado_alumnos=db_fetch_array($busca_alumnos)){
		
        	// Comprueba si es antiguo alumno
			$tipoAlumno="";
			if (strlen($id_curso)!=0) {
				$busca_inscripcion_ant=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso<?', 'ss', array($listado_alumnos["id_usuario"], $id_curso)) or die(db_error());
				$tipoAlumno="Nuevo alumno/a";
				if (db_num_rows($busca_inscripcion_ant)!=0) {
					$tipoAlumno="Renueva";
				}
			}

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
				
					// Comprueba si est‡ en la lista negra
					$busca_lista=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
					
					if (db_num_rows($busca_lista)==0) {
						if (!in_array($to,$lista_emails_destino)) {
				
							$lista_emails_destino[]=$to;
							$num_emails_enviar++;
							if ($listado_alumnos["nonato"]!=1) {
								$array_edades[$to][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
							} else {
								$array_edades[$to][0]='';    
							}
							$array_edades[$to][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
							$array_edades[$to][2]=$listado_alumnos["fecha_matricula"];
							$array_edades[$to][3]=$listado_alumnos["fecha_baja"];	
							$array_edades[$to][4]=$listado_alumnos["id_usuario"];
							$array_edades[$to][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];	
							$array_edades[$to][6]=$tipoAlumno;			
						}			
					}
										
				}
			} 	
		
		}
		$data = array(
			['id_alumno','centro','email','fecha nacimiento','nombre alumno','fecha alta','fecha baja','observaciones']
		);
		foreach($lista_emails_destino as $email) {
			$data[] = [$array_edades[$email][4],$array_edades[$email][5],$email,$array_edades[$email][0],$array_edades[$email][1],$array_edades[$email][2],$array_edades[$email][3],$array_edades[$email][6]];
		}
	break;
	case 'contactos':

		$filtroCurso="";
		if (strlen($id_curso)!=0) {
			$filtroCurso=" AND contactos.id_curso=$id_curso";
		}
		
		$filtroCiclo="";
				
		$filtroCentro = '(contactos.id_centro=20)';

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
		
		$lista_emails_destino=array();
		
		$contador_alumnos=0;
		
		while($listado_alumnos=db_fetch_array($busca_alumnos)){
		
			$fila=$listado_alumnos["id_contacto"];
				
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
							if ($listado_alumnos["nonato"]!=1) {
								$array_edades[$to][0]=date('d/m/y',strtotime($listado_alumnos["fecha_nacimiento"]));
							} else {
								$array_edades[$to][0]='';    
							}
							$array_edades[$to][1]=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];
							$array_edades[$to][2]=$listado_alumnos["fecha_contacto"];
							$array_edades[$to][3]=$listado_alumnos["proceso_decision"];	
							$array_edades[$to][4]=$listado_alumnos["id_contacto"];
							$array_edades[$to][5]=$nombresCentros[$listado_alumnos["centro_alumno"]];	
							$array_edades[$to][6]='No';			
							if (intval($listado_alumnos["id_usuario"])>0) {
								$array_edades[$to][6]='Si';
							}
						}			
					}
										
				}
			} 	
		
		}
		$data = array(
			['id_contacto','centro','email','fecha nacimiento','nombre alumno','fecha contacto','estado','matriculado']
		);
		foreach($lista_emails_destino as $email) {
			$data[] = [$array_edades[$email][4],$array_edades[$email][5],$email,$array_edades[$email][0],$array_edades[$email][1],$array_edades[$email][2],$array_edades[$email][3],$array_edades[$email][6]];
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