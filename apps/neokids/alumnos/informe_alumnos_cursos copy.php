<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// escribe la parte superior del archivo
$fecha_informe=date("d")."-".date("m")."-".date("Y");
$nombre_archivo = "informes/Listado_alumnos_cursos_".$c2.$fecha_informe.".xls";
$Titulo_hoja="Alumnos $fecha_informe";
if (file_exists($nombre_archivo)) {
        unlink($nombre_archivo);
        }

$gestor = fopen($nombre_archivo, 'a');

fwrite($gestor, $contenido);

fwrite($gestor,"<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\"  xml:lang=\"es\" lang=\"es\">");
fwrite($gestor,"<head>");
fwrite($gestor,"<!--[if gte mso 9]><xml>");
fwrite($gestor,"<x:ExcelWorkbook>");
fwrite($gestor,"<x:ExcelWorksheets>");
fwrite($gestor,"<x:ExcelWorksheet>");
fwrite($gestor,"<x:Name>".$Titulo_hoja."</x:Name>");
fwrite($gestor,"<x:WorksheetOptions>");
fwrite($gestor,"<x:Print>");
fwrite($gestor,"<x:ValidPrinterInfo/>");
fwrite($gestor,"</x:Print>");
fwrite($gestor,"</x:WorksheetOptions>");
fwrite($gestor,"</x:ExcelWorksheet>");
fwrite($gestor,"</x:ExcelWorksheets>");
fwrite($gestor,"</x:ExcelWorkbook>");
fwrite($gestor,"</xml>");
fwrite($gestor,"<![endif]--> ");
fwrite($gestor,"<style>");
fwrite($gestor,"</head>");
fwrite($gestor,"<body>");
fwrite($gestor,"<table>");
fwrite($gestor,"<tr>");
fwrite($gestor,"<td><i>ID</i></td>");
fwrite($gestor,"<td><i>Nombre</i></td>");
fwrite($gestor,"<td><i>Apellido 1</i></td>");
fwrite($gestor,"<td><i>Apellido 2</i></td>");
fwrite($gestor,"<td><i>Fecha Nacimiento</i></td>");
	fwrite($gestor,"<td><i>Curso </i></td>");
	fwrite($gestor,"<td><i>Inicio en curso </i></td>");
	fwrite($gestor,"<td><i>Fin en curso</i></td>");
	fwrite($gestor,"<td><i>Grupos curso</i></td>");	
	
fwrite($gestor,"</tr>");

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


                // Busca provincia
                $id_provincia_sel=$listado_alumno["Provincia"];
                if (strlen($id_provincia_sel)==0) {
                        $id_provincia_sel=0;
                        }
                $busca_provincia=db_query_params('SELECT * FROM provincias WHERE id_provincia=?', 's', array($id_provincia_sel));
                if (db_num_rows($busca_provincia)!=0) {
                        $datos_provincia=db_fetch_array($busca_provincia);
                        $nombre_provincia=$datos_provincia["provincia"];
                        } else {
                        $nombre_provincia="";
                        }

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
					 
					// Escribe fila
    	            fwrite($gestor,"<tr>");
	                fwrite($gestor,"<td>".utf8_encode($listado_alumno["id_usuario"])."</td>");
                	fwrite($gestor,"<td>".utf8_encode($listado_alumno["Nombre"])."</td>");
            	    fwrite($gestor,"<td>".utf8_encode($listado_alumno["Apellido1"])."</td>");
        	        fwrite($gestor,"<td>".utf8_encode($listado_alumno["Apellido2"])."</td>");
    	            fwrite($gestor,"<td>".date("d/m/Y",strtotime($listado_alumno["fecha_nacimiento"]))."</td>");
	                fwrite($gestor,"<td>".utf8_encode($cursos["nombre"])."</td>"); 					 
					 
					// Busca si ha sido alumno
					$texto_celda="";
					
					$fecha_inicio_alumno="2100-01-01";
					$fecha_fin_alumno="2000-01-01";
					
					$busca_si_alumno=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=? ORDER BY fecha_matricula', 'ss', array($listado_alumno["id_usu"], $cursos["id_curso"]));
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
				 	fwrite($gestor,"<td><i>".$fecha_inicio_alumno."</i></td>");
				 	fwrite($gestor,"<td><i>".$fecha_fin_alumno."</i></td>");
				 	fwrite($gestor,"<td><i>".utf8_encode($lista_grupos_alumno)."</i></td>");	
				 	fwrite($gestor,"</tr>");					
				 }				 
}
// Parte inferior del archivo

fwrite($gestor,"</table>");
fwrite($gestor,"</body>");
fwrite($gestor,"</html>");

header("Location: ".$nombre_archivo);

 odbc_close($odbc);
?>
