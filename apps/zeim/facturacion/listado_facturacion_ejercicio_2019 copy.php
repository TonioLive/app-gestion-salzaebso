<?php  require("../configuracion/parte_superior_privado_ancho_bootstrap.php"); ?>
<?php 
$id_alumno=$_GET["id_alumno"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)==0) {
        $ejercicio=date("Y");
        }
$filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;

if (strlen($centro_factura)!=0) {
        $filtros.=" AND alumnos.id_centro=".$centro_factura;
        }

if (strlen($id_alumno)!=0) {
		$filtros.=" AND usuarios.id_usuario=".$id_alumno;	
}		
		
$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

$columnas_tabla=12;
$columnas_centro=6;

// Ciclos aulas
$ciclos_aulas=array();

$busca_ciclos_aulas=db_query("SELECT * FROM centro_aulas;");

while($datos_ciclos_aulas=db_fetch_array($busca_ciclos_aulas)) {

	$ciclos_aulas[$datos_ciclos_aulas["id_aulas"]]=$datos_ciclos_aulas["ciclo"];
	
}

$nombres_centros=array();

$busca_nombres_centros=db_query("SELECT * FROM centros");

while($datos_nombres_centros=db_fetch_array($busca_nombres_centros)) {

	$nombres_centros[$datos_nombres_centros["id_centro"]]=$datos_nombres_centros["nombre_centro"];
	
}

?>
<br><br>
                        <table class="table">
                        <tr>
                           <td class="col-md-2"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio-1;?>'><b><< Ejercicio Anterior</b></a></td>
                           <td class="text-center col-md-8"><b>Listado de Cantidades Facturadas por CIF/NIF - <?=$ejercicio;?></b><br><br></td>
                           <td class="text-right col-md-2"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio+1;?>'><b>Ejercicio Siguiente >></b></a></td>
                        </tr>
						</table>
						<table class="table table-bordered table-estriped">
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura, alumnos.id_usuario AS id_usu_alu  FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr>
                          <td align="center" colspan=<?=$columnas_tabla?>><b>No se han encontrado facturas emitidas con los criterios seleccionados...</b></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr>
                          <td><b>NIF Madre</b></td>
                          <td><b>Nombre Madre</b></td>
                          <td><b>NIF Padre</b></td>
                          <td><b>Nombre Padre</b></td>						  
                          <td><b>Alumnos</b></td>
						  <td><b>Fecha Nac.</b></td>
                          <td><b>Escuela</b></td>
						   <?php						  
						  // Busca meses asistidos en el ejercicio
						  
						  for($c_meses=1;$c_meses<=12;$c_meses++) {
							  ?>
							  <td><b><?=$nombre_mes_corto[$c_meses]?></b></td>
							  <?php
						  }
						  
						  ?>
                          <td><b>Educación y Comedor</b></td>
						  <td><b>Pagado tickets</b></td>						  						  
						  <td><b>Subvenciones</b></td>
						  <td><b>Pagado padres</b></td>					  						  
                        </tr>
                        <?php 
                        }

                        $contador=0;
                        $contador1=0;

                        $total_facturado_dni=0;
                        $total_educacion_dni=0;
						
						$total_subvenciones_dni=0;
						$total_ticket_guarderia=0;

                        $id_cliente_actual="Inicial";
                        $texto_informar="";
						
while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

	// Comprueba edad el més de emisión de la factura
	
	$procesar_factura=true;	
	
	// Anio actual menos fecha nacimiento
	$nacimiento=explode( '-', $datos_factura["fecha_nacimiento"]);
	$edad=date("Y",strtotime($datos_factura["fecha_emision"]))-$nacimiento[0];
	$mes_factura=date("n",strtotime($datos_factura["fecha_emision"]));

	// Todos los que tengan más de 3 años automáticamente descartados
	if ($edad>3) {		
		$procesar_factura=false;			
	}
	
	// Los que cumple 3 años en el ejercico procesado aplicamos la siguiente regla
	// Nacidos entre Enero y Agosto => Procesamos las facturas desde enero hasta agosto, las de septiembre a diciembre no se incluyen.
	// Nacidos Entre Septiembre y Diciembre => Procesamos las facturas de Enero al mes anterior al de nacimiento.
	
	if ($edad==3) {
		
		// Nacidos en agosto
		if (($nacimiento[1]<=8) and ($mes_factura>=8)) {
			$procesar_factura=false;
		}

		// Nacidos a partir de septiembre
		if (($nacimiento[1]>=9) and ($mes_factura>=$nacimiento[1])) {
			$procesar_factura=false;
		}
		
	}
	
	if ($procesar_factura==true) {
						
						// Busca pagado con tickets
						
						$total_tickets_factura=db_query_params('SELECT sum(importe_pago) AS total_pagado FROM pagos_a_cuenta WHERE id_factura=? AND forma_pago=5', 's', array($datos_factura["id_factura"]));
						
						if (db_num_rows($total_tickets_factura)!=0) {
						
							$datos_total_tickets_factura=db_fetch_array($total_tickets_factura);
						
							$total_ticket_guarderia=$total_ticket_guarderia+$datos_total_tickets_factura["total_pagado"];
							
						}


                        if ($id_cliente_actual!=$datos_factura["id_usuario"]) {
									
                        if ($id_cliente_actual!="Inicial") {

                            $color_letras="black";
						
                        if ($dni_mostrar!="Facturas sin CIF/NIF") {
                        $texto_informar="Informar a Dpto. de administración";
                        }
						
						$total_pagado_padres=($total_educacion_dni-$total_ticket_guarderia)+$total_subvenciones_dni;
						if ($total_pagado_padres<0) {
							
							$total_pagado_padres=0;
							
						}
						
						// Busca datos padres
						
						$busca_padres=db_query_params('SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario AND padre_de=? ORDER BY parentesco', 's', array($id_cliente_actual));
						
						$tiene_padres = false;
						
						$nombre_madre="";
						$dni_madre="";
						$nombre_padre="";
						$dni_padre="";
						
						while($padres=db_fetch_array($busca_padres)) {
							
							if ($padres["parentesco"]==1) {
							
								if ($padres["sexo"]=="H") {
									$nombre_madre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$dni_madre=$padres["DNI"];
									$tiene_padres = true;
								}
								
								if ($padres["sexo"]=="V") {
									$nombre_padre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$dni_padre=$padres["DNI"];
									$tiene_padres = true;
								}
							
							}						
							
						}
						
						if ($nombre_madre=="") {
							
							$dni_madre=$dni_padre;
							$nombre_madre=$nombre_padre;
							
						}
						
						$dni_madre=str_replace("-","",$dni_madre);
						$dni_madre=str_replace(".","",$dni_madre);
						$dni_madre=str_replace(",","",$dni_madre);
						$dni_madre=str_replace(" ","",$dni_madre);

						if ($dni_madre) {
							
							$dni_madreOld=$dni_madre;
							$nuevoDni=CalcularLetraNIF($dni_madre);
							
							if ($nuevoDni) {
								$dni_madre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						
						$dni_padre=str_replace("-","",$dni_padre);
						$dni_padre=str_replace(".","",$dni_padre);
						$dni_padre=str_replace(",","",$dni_padre);
						$dni_padre=str_replace(" ","",$dni_padre);
						
						if ($dni_padre) {
							
							$dni_padreOld=$dni_padre;
							$nuevoDni=CalcularLetraNIF($dni_padre);
							
							if ($nuevoDni) {
								$dni_padre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						
                        ?>
                        <tr>
                          <td><?=$dni_madre;?></td>
                          <td><?=mb_convert_case($nombre_madre, MB_CASE_UPPER);?></td>						
                          <td><?=$dni_padre;?></td>
                          <td><?=mb_convert_case($nombre_padre, MB_CASE_UPPER);?></td>				  
                          <td><?=mb_convert_case($alumno_actual, MB_CASE_UPPER);?></td>
						  <td><?=date("d/m/Y",strtotime($fecha_nacimiento_alumno))?></td>
                          <td><?=$nombre_escuela;?></td>
						  <?php						  
						  // Busca meses asistidos en el ejercicio
						  
						  
						  for($c_meses=1;$c_meses<=12;$c_meses++) {
							
							$fecha_ini_mes=date("Y-m-d",strtotime($ejercicio."-".$c_meses."-01"));
							$fecha_fin_mes=date("Y-m-t",strtotime($fecha_ini_mes));
							
							$asistencia_mes="N";
							
							$comprueba_inscripcion=db_query_params('SELECT * FROM alumnos LEFT JOIN centro_aulas ON alumnos.aula=centro_aulas.id_aula WHERE id_usuario=? AND fecha_matricula<=? AND fecha_baja>=?', 'sss', array($id_cliente_actual, (string)($fecha_fin_mes), (string)($fecha_ini_mes))) or die(db_error());
							
							if (db_num_rows($comprueba_inscripcion)!=0) {								
									
									$asistencia_mes="S";
									
									// Si es segundo ciclo, comprueba fecha de nacimiento
									$datos_inscripcion=db_fetch_array($comprueba_inscripcion);
									if (($datos_inscripcion["ciclo"]==2) and (date("j",strtotime($fecha_nacimiento_alumno))>=$c_meses)) {
									
										$asistencia_mes="N";
									
									}
									
							} 
						  ?>
							  <td><?=$asistencia_mes;?></td>						  
						  <?php
						  }
						  
						  ?>
                          <td class="text-right"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</td>
						  <td class="text-right"><?=number_format($total_ticket_guarderia, 2, ',', '.')?> €</td>						  
						  <td class="text-right success"><?=number_format($total_subvenciones_dni*-1, 2, ',', '.')?> €</td>						  						  						  
						  <td class="text-right success"><?=number_format($total_pagado_padres, 2, ',', '.')?> €</td>						  						  
                        </tr>
                        <?php 
                        }

                        $total_facturado_dni=0;
                        $total_educacion_dni=0;
						$total_ticket_guarderia=0;
						
						$desglose_alumno="";
						$desglose_alumno_sub="";
						
                        $dni_actual=$datos_factura["banco_dni_titular"];
                        $nombre_mostrar=$datos_factura["banco_titular"];
                        $id_usu_alumno_actual=$datos_factura["id_usu_alu"];
                        $alumno_actual=$datos_factura["Nombre"]." ".$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"];
                        $id_cliente_actual=$datos_factura["id_usuario"];
						
						$total_subvenciones_dni=0;
						
                        if ($dni_actual!="") {
                                $dni_mostrar=$dni_actual;
                                } else {
                                $dni_mostrar="Facturas sin CIF/NIF";
                                }
                        }


// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];


// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_iva=0;
$total_recargo=0;
$total_factura=0;


while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;

                // Consulta el codigo de cuenta

                $codigo_tarifa=$datos_albaranes["articulo"];
                $intentar_localizar=false;

                $codigo_cuenta=0;

                if ($codigo_tarifa==0) {

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="co_") {
                                $codigo_cuenta=2;
                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="he_") {
                                $codigo_cuenta=3;
                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,6)=="cod_e_") {

                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],6,(strlen($datos_albaranes["cod_facturacion"])-6));


                                $busca_servicio=db_query_params('SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_servicio_consumido=?;', 's', array($id_servicio_actual));

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);

                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }

                        if ((substr($datos_albaranes["cod_facturacion"],0,4)=="cod_") and (substr($datos_albaranes["cod_facturacion"],0,6)!="cod_e_")) {

                                $codigo_cuenta=2;

                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="au_") {

                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],3,(strlen($datos_albaranes["cod_facturacion"])-3));

                                $busca_servicio=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_servicio_contratado=?', 's', array($id_servicio_actual));

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);

                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }

                        } else {
                        // Busca en tarifa

                        $busca_servicio=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_servicio_contratado=?', 's', array($codigo_tarifa)) or die(db_error());

                        if (db_num_rows($busca_servicio)!=0) {
                                $datos_servicio=db_fetch_array($busca_servicio);

                                $codigo_tarifa=$datos_servicio["ID"];
                                $codigo_cuenta=$datos_servicio["desglosar_como"];

                                } else {
                                $intentar_localizar=true;
                                }

                        }

                 if ($intentar_localizar==true) {
                        // Intentando determinar mediante el concepto...

                        $palabras0=Array("chandal","uniforme","Material escolar","material escolar");

                        for ($c_pal=0;$c_pal<=count($palabras0);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras0[$c_pal]))) {
                                        $codigo_cuenta=0;
                                        break;
                                        }

                                }

                        $palabras1=Array("hora extra","salida","visita","gabinete","inscripción","inscripcion","Inscripción","Inscripcion");

                        for ($c_pal=0;$c_pal<=count($palabras1);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras1[$c_pal]))) {
                                        $codigo_cuenta=1;
                                        break;
                                        }

                                }

                        $palabras2=Array("comedor","comida","paella","Comedor","Toma mensual");

                        for ($c_pal=0;$c_pal<=count($palabras2);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras2[$c_pal]))) {
                                        $codigo_cuenta=2;
                                        break;
                                        }

                                }

                        $palabras3=Array("educacion","educación","Educación","Educación","Hora extra mensual");

                        for ($c_pal=0;$c_pal<=count($palabras3);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras3[$c_pal]))) {
                                        $codigo_cuenta=3;
                                        break;
                                        }

                                }

                        }

						if ((substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") or (substr($datos_albaranes["concepto"],0,13)=="Bono Infantil")) {
													$total_subvenciones_dni=$total_subvenciones_dni+$total_abs;
													$desglose_alumno_sub.=$datos_albaranes["concepto"]." ".number_format($total_abs,2,",",".")." €<br>";
													$codigo_cuenta=0;
													}                               
						if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
													$total_subvenciones_dni=$total_subvenciones_dni+$total_abs;                                		
													$desglose_alumno_sub.=$datos_albaranes["concepto"]." ".number_format($total_abs,2,",",".")." €<br>";													
													$codigo_cuenta=0;
													} 												
						
						if (substr($datos_albaranes["concepto"],0,22)=="Hora extra") {
								$codigo_cuenta=3;
							}
						
                        if (($codigo_cuenta==2) or ($codigo_cuenta==3)) {
                                $total_educacion_dni=$total_educacion_dni+$total_abs;
								
								$desglose_alumno.=$datos_albaranes["concepto"]." ".number_format($total_abs,2,",",".")." €<br>";
								
                                }
								
								
						$total_facturado_dni=$total_facturado_dni+$total_abs;
						
        }

		$fecha_nacimiento_alumno=$datos_factura["fecha_nacimiento"];
		$fecha_matricula=$datos_factura["fecha_matricula"];
		$fecha_baja=$datos_factura["fecha_baja"];
			
		$nombre_escuela=$nombres_centros[$datos_factura["centro_factura"]];
		
		$total_factura=round(($total_base+$total_iva+$total_recargo),2);

		// echo("Incluida: ".$datos_factura["fecha_emision"]."<br>");
		
		} else {
		
		// echo("Rechazada: ".$datos_factura["fecha_emision"]."<br>");
			
		} // Fin procesar factura

	}

                        $color_letras="black";

						$total_pagado_padres=($total_educacion_dni-$total_ticket_guarderia)+$total_subvenciones_dni;
						if ($total_pagado_padres<0) {
							
							$total_pagado_padres=0;
							
						}	

						// Busca datos padres
						
						$busca_padres=db_query_params('SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario AND padre_de=? ORDER BY parentesco', 's', array($id_cliente_actual)) or die(db_error());
						
						$tiene_padres = false;
						
						$nombre_madre="";
						$dni_madre="";
						$nombre_padre="";
						$dni_padre="";
						
						while($padres=db_fetch_array($busca_padres)) {
							
							if ($padres["parentesco"]==1) {
							
								if ($padres["sexo"]=="H") {
									$nombre_madre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$dni_madre=$padres["DNI"];
									$tiene_padres = true;
								}
								
								if ($padres["sexo"]=="V") {
									$nombre_padre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$dni_padre=$padres["DNI"];
									$tiene_padres = true;
								}
							
							}						
							
						}
	
						if ($nombre_madre=="") {
							
							$dni_madre=$dni_padre;
							$nombre_madre=$nombre_padre;
							
						}
	
						$dni_madre=str_replace("-","",$dni_madre);
						$dni_madre=str_replace(".","",$dni_madre);
						$dni_madre=str_replace(",","",$dni_madre);
						$dni_madre=str_replace(" ","",$dni_madre);

						if ($dni_madre) {
							
							$dni_madreOld=$dni_madre;
							$nuevoDni=CalcularLetraNIF($dni_madre);
							
							if ($nuevoDni) {
								$dni_madre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						
						$dni_padre=str_replace("-","",$dni_padre);
						$dni_padre=str_replace(".","",$dni_padre);
						$dni_padre=str_replace(",","",$dni_padre);
						$dni_padre=str_replace(" ","",$dni_padre);
						
						if ($dni_padre) {
							
							$dni_padreOld=$dni_padre;
							$nuevoDni=CalcularLetraNIF($dni_padre);
							
							if ($nuevoDni) {
								$dni_padre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						
                        ?>
                        <tr>
                          <td><?=$dni_madre;?></td>
                          <td><?=mb_convert_case($nombre_madre, MB_CASE_UPPER);?></td>						
                          <td><?=$dni_padre;?></td>
                          <td><?=mb_convert_case($nombre_padre, MB_CASE_UPPER);?></td>
                          <td><?=mb_convert_case($alumno_actual, MB_CASE_UPPER);?></td>
						  <td><?=date("d/m/Y",strtotime($fecha_nacimiento_alumno))?></td>
                          <td><?=$nombre_escuela;?></td>
						  <?php						  
						  // Busca meses asistidos en el ejercicio
						  
						  
						  for($c_meses=1;$c_meses<=12;$c_meses++) {
							
							$fecha_ini_mes=date("Y-m-d",strtotime($ejercicio."-".$c_meses."-01"));
							$fecha_fin_mes=date("Y-m-t",strtotime($fecha_ini_mes));
							
							$asistencia_mes="N";
							
							$comprueba_inscripcion=db_query_params('SELECT * FROM alumnos LEFT JOIN centro_aulas ON alumnos.aula=centro_aulas.id_aula WHERE id_usuario=? AND fecha_matricula<=? AND fecha_baja>=?', 'sss', array($id_cliente_actual, (string)($fecha_fin_mes), (string)($fecha_ini_mes))) or die(db_error());
							
							if (db_num_rows($comprueba_inscripcion)!=0) {								
									
									$asistencia_mes="S";
									
									// Si es segundo ciclo, comprueba fecha de nacimiento
									$datos_inscripcion=db_fetch_array($comprueba_inscripcion);
									if (($datos_inscripcion["ciclo"]==2) and (date("j",strtotime($fecha_nacimiento_alumno))>=$c_meses)) {
									
										$asistencia_mes="N";
									
									}
									
							}
													
						  ?>
							  <td><?=$asistencia_mes?></td>						  
						  <?php
						  }
						  
						  ?>
                          <td class="text-right"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</td>
						  <td class="text-right"><?=number_format($total_ticket_guarderia, 2, ',', '.')?> €</td>						  
						  <td class="text-right success"><?=number_format($total_subvenciones_dni*-1, 2, ',', '.')?> €</td>						  						  						  	
						  <td class="text-right success"><?=number_format($total_pagado_padres, 2, ',', '.')?> €</td>						  						  
                        </tr>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_facturacion.php';" style="font-size: 10px"><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php  require("../configuracion/parte_inferior.php"); ?>
