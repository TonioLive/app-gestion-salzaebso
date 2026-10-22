<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$nombre_archivo_csv="listado_alumnos_cursos_".date("d-m-Y");

header("Content-Type:text/csv");
header('Content-Disposition: attachment; filename="'.$nombre_archivo_csv.'.csv"');

echo("Código Alumno;Nombre;Apellido1;Apellido2;Fecha Nacimiento;Curso;Inicio en curso;Fin en curso;Grupos curso;Teléfonos;E-mail 1;E-mail 2;E-mail 3;E-mail 4;Estado pagos\n");

// Filas

$busca_listado_alumnos=db_query_params('SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_centro=? GROUP BY alumnos.id_usuario ORDER BY usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre', 's', array($centro_actual)) or die(db_error());

$nombre_padre=array();
$email_padre=array();
$telefonos_padres=array();

while($listado_alumno=db_fetch_array($busca_listado_alumnos)) {

$fecha_inicio_curso_actual="";            
$fecha_fin_curso_actual=""; 
$nombre_centro_actual="";      
$nombre_aula_actual="";

$id_usuario_alumno=$listado_alumno["id_usu"];

// Comprueba deudas
                
                $total_facturado_alumno=0;
                $total_abs_facturado_alumno=0;
                $total_abs_cobrado_alumno=0;
                $total_abs_abonos_alumno=0;
        
                // Comprueba deudas y pagos
        
                $consulta1=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_cliente=?', 's', array($listado_alumno["id_alumno"]));
                
                $consulta_albaranes_cliente = db_query_params($consulta1[0], $consulta1[1], $consulta1[2]) or die (db_error());

				while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

					// Mira si los albaranes de la factura y calcula los totales
					$id_factura=$datos_factura["id_factura"];
					
					// Calcula totales

					// Busca albaranes
					$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

					$total_dto=0;
					$total_base=0;
					$total_iva=0;
					$total_recargo=0;

					$total_factura_alumno=0;
					$total_cobrado_alumno=0;

					while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

					        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
					        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
					        $base_linea=$total_linea-$total_dto;
					        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
					        $total_abs=$base_linea+$iva_linea;

					        $total_base=$total_base+$base_linea;
					        $total_iva=$total_iva+$iva_linea;
                        
        				}
        				
        			$total_facturado_alumno=$total_base+$total_iva+$total_recargo;
        			
					// Comprueba pagos a cuenta
        			$total_a_cuenta=0;

        			$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));
        
        			if ((db_num_rows($busca_pagos)==0) and (($total_base+$total_iva+$total_recargo)<0)) {
           				$total_abs_abonos_alumno=$total_abs_abonos_alumno+round(($total_base+$total_iva+$total_recargo),2);
        				}
        
        			while ($pagos=db_fetch_array($busca_pagos)) {
                		$total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                		}
                
       				$total_a_cuenta=round($total_a_cuenta,2);
       				$total_factura_sel=round(($total_base+$total_iva+$total_recargo),2);

        			if ($total_a_cuenta<$total_factura_sel) {
                		$morosidad=true;
                		} else {
                		$morosidad=false;
                		}

                	$total_cobrado=$total_cobrado+round($total_a_cuenta,2);
                	$total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);

                	$total_factura_alumno=$total_factura_alumno+round($total_a_cuenta,2);
                	$total_cobrado_alumno=$total_cobrado_alumno+round(($total_base+$total_iva+$total_recargo),2);;

                	$total_abs_facturado_alumno=$total_abs_facturado_alumno+round(($total_base+$total_iva+$total_recargo),2);
                	$total_abs_cobrado_alumno=$total_abs_cobrado_alumno+round($total_a_cuenta,2);

                	$contador=$contador+1;
        	}
        
        if ($total_factura_alumno-$total_cobrado_alumno!=0) {
    
    		$estado_pagos="Debe: ".number_format($total_factura_alumno-$total_cobrado_alumno,2,",",".")." €";
        
        } else {
        
	        $estado_pagos="Sin deudas";
        
        }
                
        // Fin facturas

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
                
                $consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=? ORDER BY parentesco', 's', array($id_usuario_alumno)) or die(db_error());

                $contador_padres=0;

                while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($relaciones["id_usuario"]));

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
					 
				 	$busca_si_alumno=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=? ORDER BY fecha_matricula', 'ss', array($listado_alumno["id_usuario"], $cursos["id_curso"]));
					
					if (db_num_rows($busca_si_alumno)!=0) {					 
						// Escribe fila
						echo($listado_alumno["id_usuario"].";");
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
						
							// Busca grupos		
							$lista_grupos_alumno="";
        
							$busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($si_alumno["id_alumno"])) or die(db_error());

							while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                			$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
                			}            						 	
						}
						if (strlen($lista_grupos_alumno)!=0) {
	                		$lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);		
						}  					
						echo(date("d/m/Y",strtotime($fecha_inicio_alumno)).";");
						echo(date("d/m/Y",strtotime($fecha_fin_alumno)).";");
						echo($lista_grupos_alumno.";");
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
						echo($emails[2].";");
						echo($emails[3].";");
						echo($estado_pagos.";");
						echo("\n");
					}
                							
				 }				 
}

?>
