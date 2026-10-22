<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$centro_seleccionado_listado=$_GET["cen"];
$curso_seleccionado_listado=$_GET["cur"];

$coletilla="";

$busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$curso_seleccionado_listado);
if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $fecha_inicio_curso=$datos_curso["fecha_inicio"];
                                $fecha_fin_curso=$datos_curso["fecha_fin"];
                                }

// escribe la parte superior del archivo
$fecha_informe=date("d")."-".date("m")."-".date("Y");
$nombre_archivo = "informes/Informe_alumnos_".$c2.$fecha_informe.".xls";
$Titulo_hoja="Alumnos $fecha_informe";
if (file_exists($nombre_archivo)) {
        unlink($nombre_archivo);
        }

$gestor = fopen($nombre_archivo, 'a');

fwrite($gestor, $contenido);

fwrite($gestor,"<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">");
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
fwrite($gestor,"<!--table");
fwrite($gestor,".style20        {mso-number-format:0%; mso-style-name:Porcentual; mso-style-id:5;}");
fwrite($gestor,".xl26        {mso-style-parent:style20; mso-number-format:Percent; white-space:normal;}");
fwrite($gestor,"-->");
fwrite($gestor,"</style>");
fwrite($gestor,"</head>");
fwrite($gestor,"<body>");
fwrite($gestor,"<table style='border-collapse: collapse;table-layout:fixed;width:960pt'>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
fwrite($gestor,"<td width=67><i>ID Alumno</i></td>");
fwrite($gestor,"<td width=67><i>Nombre</i></td>");
fwrite($gestor,"<td width=323><i>Apellido 1</i></td>");
fwrite($gestor,"<td width=323><i>Apellido 2</i></td>");
fwrite($gestor,"<td width=120><i>Días asistidos</i></td>");
fwrite($gestor,"<td width=120><i>ültimo día asistido</i></td>");
fwrite($gestor,"<td width=150><i>Fecha Nacimiento</i></td>");
fwrite($gestor,"<td width=250><i>Domicilio</i></td>");
fwrite($gestor,"<td width=102><i>CP</i></td>");
fwrite($gestor,"<td width=121><i>Población</i></td>");
fwrite($gestor,"<td width=105><i>Provincia</i></td>");
fwrite($gestor,"<td width=105><i>Email</i></td>");
fwrite($gestor,"<td width=105><i>Teléfonos</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 2</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 2</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 2</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 2</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 3</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 3</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 3</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 4</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 4</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 4</i></td>");
fwrite($gestor,"<td width=120><i>Fecha inicio curso</i></td>");
fwrite($gestor,"<td width=120><i>Fecha baja curso</i></td>");
fwrite($gestor,"<td width=120><i>Escuela</i></td>");
fwrite($gestor,"<td width=120><i>Aula</i></td>");
fwrite($gestor,"<td width=120><i>Origen</i></td>");
fwrite($gestor,"<td width=120><i>Estado pagos</i></td>");
fwrite($gestor,"</tr>");

// Filas

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$curso_seleccionado_listado AND alumnos.id_centro=$centro_seleccionado_listado ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");
$nombre_padre=array();
$email_padre=array();
$telefonos_padres=array();

while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

				// Compruebo si es o ha sido alumno de sant-pau		
				$viene_de="";						
				$busca_sant_pau=db_query_params('SELECT * FROM escuelamunicipal.usuarios WHERE Nombre=? AND Apellido1=? AND Apellido2=? AND fecha_nacimiento=?;', 'ssss', array((string)($alumnos_inscritos["Nombre"]), (string)($alumnos_inscritos["Apellido1"]), (string)($alumnos_inscritos["Apellido2"]), (string)($alumnos_inscritos["fecha_nacimiento"]))) or die(db_error());
				if (db_num_rows($busca_sant_pau)!=0) {
					$viene_de="EI Sant Pau";
				}

                // Busca provincia
                $id_provincia_sel=$alumnos_inscritos["Provincia"];
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
                
                $consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=? ORDER BY parentesco', 's', array($alumnos_inscritos["id_usuario"])) or die(db_error());

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

                $consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($alumnos_inscritos["id_centro_ins"])) or die(db_error()."-2");
                $datos_centro=db_fetch_array($consulta_nombre_centro);

                // Busca datos del aula
                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($alumnos_inscritos["aula"]));
                $datos_aula=db_fetch_array($consulta_datos_aula);

                $telefono_contacto1=$alumnos_inscritos["telefono_contacto1"];
                $telefono_contacto2=$alumnos_inscritos["telefono_contacto2"];
                $telefonos=$telefono_contacto1." ".$telefono_contacto2;
                
                $busca_dias_asistidos=db_query_params('SELECT fecha FROM servicios_consumidos WHERE fecha>=? AND fecha<=? AND id_alumno=? GROUP BY fecha ORDER BY fecha DESC;', 'sss', array((string)($fecha_inicio_curso), (string)($fecha_fin_curso), $alumnos_inscritos["id_usu"])) or die(db_error());
                $num_dias_asistidos=db_num_rows($busca_dias_asistidos);
                if ($num_dias_asistidos!=0) {
                        $datos_dias_asistidos=db_fetch_array($busca_dias_asistidos);
                        $ultimo_dia_asistido=date("d/m/Y",strtotime($datos_dias_asistidos["fecha"]));
                        } else {
                        $ultimo_dia_asistido="-";
                        }
                
                $estado_pagos="";
                
                // Comprueba deudas
                
                $total_facturado_alumno=0;
                $total_abs_facturado_alumno=0;
                $total_abs_cobrado_alumno=0;
                $total_abs_abonos_alumno=0;
        
                // Comprueba deudas y pagos
        
                $consulta1=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_cliente=?', 's', array($alumnos_inscritos["id_alumno"]));
                
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
    
    		$estado_pagos="Debe: ".number_format($total_factura_alumno-$total_cobrado_alumno,2,",",".");
        
        } else {
        
	        $estado_pagos="Sin deudas";
        
        }
                
        // Fin facturas                
                
                // Fin Deudas
                
                // Escribe fila
                 fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["id_alumno"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Nombre"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Apellido1"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Apellido2"]."</td>");
                 fwrite($gestor,"<td>".$num_dias_asistidos."</td>");
                 fwrite($gestor,"<td>".$ultimo_dia_asistido."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($alumnos_inscritos["fecha_nacimiento"]))."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Direccion"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["cp"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Poblacion"]."</td>");
                 fwrite($gestor,"<td>".$nombre_provincia."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["email"]."</td>");                
                 fwrite($gestor,"<td>".$telefonos."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto1"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto2"]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[0]."</td>");
                 fwrite($gestor,"<td>".$email_padre[0]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[0]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[1]."</td>");
                 fwrite($gestor,"<td>".$email_padre[1]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[1]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[2]."</td>");
                 fwrite($gestor,"<td>".$email_padre[2]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[2]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[3]."</td>");
                 fwrite($gestor,"<td>".$email_padre[3]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[3]."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($alumnos_inscritos["fecha_matricula"]))."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($alumnos_inscritos["fecha_baja"]))."</td>");
                 fwrite($gestor,"<td>".$datos_centro["nombre_centro"]."</td>");
                 fwrite($gestor,"<td>".$datos_aula["nombre"]."</td>");
                 fwrite($gestor,"<td>".$viene_de."</td>");
                 fwrite($gestor,"<td>".$estado_pagos."</td>");
                 fwrite($gestor,"</tr>");

}
// Parte inferior del archivo

fwrite($gestor,"</table>");
fwrite($gestor,"</body>");
fwrite($gestor,"</html>");

header("Location: ".$nombre_archivo);

 odbc_close($odbc);
?>
