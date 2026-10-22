<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$nombres_centros=array();

$busca_centros=db_query("SELECT * FROM centros");
while($centros=db_fetch_array($busca_centros)){
	
	$nombres_centros[$centros["id_centro"]]=$centros["nombre_centro"];
	
}

$nombres_aulas=array();

$busca_aulas=db_query("SELECT * FROM centro_aulas");
while($aulas=db_fetch_array($busca_aulas)){
	
	$nombres_aulas[$aulas["id_aula"]]=$aulas["nombre"];
	
}

$nombre_archivo_csv="listado_alumnos_cursos_".date("d-m-Y");

header("Content-Type:text/csv");
header('Content-Disposition: attachment; filename="'.$nombre_archivo_csv.'.csv"');

echo("Código Alumno;Centro;Aula;Nombre;Apellido1;Apellido2;Fecha Nacimiento;Curso;Inicio en curso;Fin en curso;Teléfonos;E-mail 1;E-mail 2;E-mail 3;E-mail 4;E-mail 5;E-mail 6;E-mail 7;E-mail 8\n");

// Filas

$busca_listado_alumnos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND year(usuarios.fecha_nacimiento)>=2015 AND (alumnos.id_centro=6 OR alumnos.id_centro=7 OR alumnos.id_centro=8) GROUP BY alumnos.id_usuario ORDER BY usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre") or die(db_error());

$nombre_padre=array();
$email_padre=array();
$telefonos_padres=array();

while($listado_alumno=db_fetch_array($busca_listado_alumnos)) {

$fecha_inicio_curso_actual="";            
$fecha_fin_curso_actual=""; 
$nombre_centro_actual="";      
$nombre_aula_actual="";

$id_usuario_alumno=$listado_alumno["id_usu"];

                // Busca nombre del padre
                $nombre_padre[0]="";
                $email_padre[0]="";
                $nombre_padre[1]="";
                $email_padre[1]="";
                $nombre_padre[2]="";
                $email_padre[2]="";
                $nombre_padre[3]="";
                $email_padre[3]="";
                $telefonos_padres[0]="";
                $telefonos_padres[1]="";
                $telefonos_padres[2]="";
                $telefonos_padres[3]="";
                
                $consulta_relaciones=db_query("SELECT * FROM padres WHERE padre_de=".$id_usuario_alumno." ORDER BY parentesco") or die(db_error());

                $contador_padres=0;

                while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=".$relaciones["id_usuario"]);

                        $nombre_padre_act="";
                        if (db_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        $email_padre_act=$datos_padre["email"];
                                        $telefono_padre_act=$datos_padre["telefono1"]." ".$datos_padre["telefono2"];
                                        }
                        $nombre_padre[$contador_padres]=$nombre_padre_act;
                        $email_padre[$contador_padres]=$email_padre_act;
                        $telefonos_padres[$contador_padres]=$telefono_padre_act;
                        $contador_padres++;
                        }

                $telefono_contacto1=$listado_alumno["telefono_contacto1"];
                $telefono_contacto2=$listado_alumno["telefono_contacto2"];
                $telefonos=$telefono_contacto1." ".$telefono_contacto2;
            				 
				 $busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso<>1 ORDER BY fecha_inicio");
				 while($cursos=db_fetch_array($busca_cursos)) {
					 
				 	$busca_si_alumno=db_query("SELECT * FROM alumnos WHERE id_usuario=".$listado_alumno["id_usuario"]." AND id_curso=".$cursos["id_curso"]." ORDER BY fecha_matricula");
					
					if (db_num_rows($busca_si_alumno)!=0) {					 
						// Escribe fila
						echo($listado_alumno["id_usuario"].";");
						echo($nombres_centros[$listado_alumno["id_centro_ins"]].";");
						echo($nombres_aulas[$listado_alumno["aula"]].";");
						echo($listado_alumno["Nombre"].";");
						echo($listado_alumno["Apellido1"].";");
						echo($listado_alumno["Apellido2"].";");
						echo(date("d/m/Y",strtotime($listado_alumno["fecha_nacimiento"])).";");
						echo($cursos["nombre"].";"); 	
	                				 					
						$fecha_inicio_alumno="2100-01-01";
						$fecha_fin_alumno="2000-01-01";										
										
						while($si_alumno=db_fetch_array($busca_si_alumno)) {					
						
							if (strtotime($si_alumno["fecha_matricula"])<strtotime($fecha_inicio_alumno)) {
							$fecha_inicio_alumno=$si_alumno["fecha_matricula"];							
							}
						
							if (strtotime($si_alumno["fecha_baja"])>strtotime($fecha_fin_alumno)) {
							$fecha_fin_alumno=$si_alumno["fecha_baja"];								
							}
						 					
						echo(date("d/m/Y",strtotime($fecha_inicio_alumno)).";");
						echo(date("d/m/Y",strtotime($fecha_fin_alumno)).";");

						$listado_telefonos="";
						if ($listado_alumno["telefono1"]) {
							$listado_telefonos.=$listado_alumno["telefono1"].", ";
						}
						if ($listado_alumno["telefono2"]) {
							$listado_telefonos.=$listado_alumno["telefono2"].", ";
						}
						if ($listado_alumno["movil"]) {
							$listado_telefonos.=$listado_alumno["movil"].", ";
						}
						if (strlen($listado_telefonos)!=0) {
							$listado_telefonos=substr($listado_telefonos,0,strlen($listado_telefonos)-2);		
						}
						echo($listado_telefonos.";");						
						$emails=explode(",",$listado_alumno["email"]);	
						echo($emails[0].";");
						echo($emails[1].";");
						echo($email_padre[0].";");
						echo($email_padre[1].";");
						echo($email_padre[2].";");
						echo($email_padre[3].";");
						echo($email_padre[4].";");
						echo($email_padre[5].";");
						echo("\n");
					}
                							
				 }	
				 
		}			 
}

?>