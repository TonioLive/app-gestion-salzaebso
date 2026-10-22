<?php
$incrementoEscuela=0;

$sumarTicketGuarderiaEnEducacion=false;

$exportar=$_GET["exportar"];
$id_alumno=$_GET["id_alumno"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)==0) {
        $ejercicio=date("Y");
        }
		
if ($exportar!=1) {
	require("../configuracion/parte_superior_privado_ancho_bootstrap.php");
} else {
	require("../configuracion/config.php");
	require("../configuracion/autentifica_admin.php");
	
	$out = fopen('php://output', 'w');
	header("Content-Type:application/csv"); 
	header("Content-Disposition:attachment;filename=modelo233_".$ejercicio."_".$centro_factura.".csv");
	
}

function eliminar_tildes($cadena){
 
    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );
 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );
 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );
 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );
 
	/*
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );
    */
 
    return $cadena;
}

$listado_alumnos_errores=array();

$filtros="alumnos.id_centro<>14 AND year(facturas_emitidas.fecha_emision)=".$ejercicio;

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
$codigos_centros=array();

$busca_nombres_centros=db_query("SELECT * FROM centros");

while($datos_nombres_centros=db_fetch_array($busca_nombres_centros)) {

	$nombres_centros[$datos_nombres_centros["id_centro"]]=$datos_nombres_centros["nombre_centro"];
	$codigos_centros[$datos_nombres_centros["id_centro"]]=$datos_nombres_centros["codigo_centro"];
	
}

if ($exportar!=1) {
?>
<br><br>
                        <table class="table">
                        <tr>
                           <td class="col-md-2"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio-1;?>'><b><< Ejercicio Anterior</b></a></td>
                           <td class="text-center col-md-8"><b>Listado de Cantidades Facturadas por CIF/NIF - <?=$ejercicio;?></b><br><br></td>
                           <td class="text-right col-md-2"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio+1;?>'><b>Ejercicio Siguiente >></b></a></td>
                        </tr>
						</table>
						<a href="<?=$_SERVER["PHP_SELF"]?>?centro_factura=<?=$centro_factura?>&ejercicio=<?=$ejercicio?>&id_alumno=<?=$id_alumno?>&exportar=1" class="btn btn-success btn-md"><i class="fa fa-share-square-o" aria-hidden="true"></i> Exportar CSV para agencia tributaria</a><br><br>
						<table class="table table-bordered table-estriped">
                        <?php
}						
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura, alumnos.id_usuario AS id_usu_alu  FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY usuarios.id_usuario,usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
							if ($exportar!=1) { 
                        ?>
                        <tr>
                          <td align="center" colspan=<?=$columnas_tabla?>><b>No se han encontrado facturas emitidas con los criterios seleccionados...</b></td>
                        </tr>
                        <?php
							}
                        } else {
							if ($exportar!=1) {
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
						  <td><b>Observaciones</b></td>				  						  
                        </tr>
                        <?php
						  }
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
						
						$total_tickets_factura=db_query("SELECT sum(importe_pago) AS total_pagado FROM pagos_a_cuenta WHERE id_factura=".$datos_factura["id_factura"]." AND forma_pago=5");
						
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
						
						$busca_padres=db_query("SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario WHERE padre_de=".$id_cliente_actual." ORDER BY parentesco");
						
						$tiene_padres = false;
						
						$nombre_madre="";
						$dni_madre="";
						$nombre_padre="";
						$dni_padre="";
						$nombre_solo_madre="";
						$apellido1_madre="";
						$apellido2_madre="";
						$nombre_solo_padre="";
						$apellido1_padre="";
						$apellido2_padre="";												

// Guardamos hasta 2 progenitores (parentesco = 1)
$p1 = null;
$p2 = null;

while ($row = db_fetch_array($busca_padres)) {

    if ((int)$row["parentesco"] != 1) {
        continue;
    }

    if ($p1 === null) {
        $p1 = $row;
    } elseif ($p2 === null) {
        $p2 = $row;
    } else {
        // Si hubiera más de 2, ignoramos el resto (ajustable)
        break;
    }
}

$tiene_padres = ($p1 !== null);

// Helper inline: asignación a variables según rol
// (lo haremos como bloques para no usar funciones)

// 1 progenitor -> siempre a _madre
if ($p1 !== null && $p2 === null) {

    $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
    $nombre_solo_madre = $p1["Nombre"];
    $apellido1_madre   = $p1["Apellido1"];
    $apellido2_madre   = $p1["Apellido2"];
    $dni_madre         = $p1["DNI"];

}

// 2 progenitores
if ($p1 !== null && $p2 !== null) {

    $sexo1 = isset($p1["sexo"]) ? $p1["sexo"] : "";
    $sexo2 = isset($p2["sexo"]) ? $p2["sexo"] : "";

    // Si son sexos opuestos (H/V), H -> madre, V -> padre
    if ($sexo1 !== "" && $sexo2 !== "" && $sexo1 != $sexo2) {

        if ($sexo1 == "H") {
            // p1 madre, p2 padre
            $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
            $nombre_solo_madre = $p1["Nombre"];
            $apellido1_madre   = $p1["Apellido1"];
            $apellido2_madre   = $p1["Apellido2"];
            $dni_madre         = $p1["DNI"];

            $nombre_padre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
            $nombre_solo_padre = $p2["Nombre"];
            $apellido1_padre   = $p2["Apellido1"];
            $apellido2_padre   = $p2["Apellido2"];
            $dni_padre         = $p2["DNI"];

        } else {
            // p2 madre (H), p1 padre (V)
            $nombre_madre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
            $nombre_solo_madre = $p2["Nombre"];
            $apellido1_madre   = $p2["Apellido1"];
            $apellido2_madre   = $p2["Apellido2"];
            $dni_madre         = $p2["DNI"];

            $nombre_padre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
            $nombre_solo_padre = $p1["Nombre"];
            $apellido1_padre   = $p1["Apellido1"];
            $apellido2_padre   = $p1["Apellido2"];
            $dni_padre         = $p1["DNI"];
        }

    } else {
        // Mismo sexo (H/H o V/V) o sexo desconocido: por orden (p1->madre, p2->padre)

        $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
        $nombre_solo_madre = $p1["Nombre"];
        $apellido1_madre   = $p1["Apellido1"];
        $apellido2_madre   = $p1["Apellido2"];
        $dni_madre         = $p1["DNI"];

        $nombre_padre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
        $nombre_solo_padre = $p2["Nombre"];
        $apellido1_padre   = $p2["Apellido1"];
        $apellido2_padre   = $p2["Apellido2"];
        $dni_padre         = $p2["DNI"];
    }
}

						/*
						while($padres=mysql_fetch_array($busca_padres)) {
							
							if ($padres["parentesco"]==1) {
							
								if ($padres["sexo"]=="H") {
									$nombre_madre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$nombre_solo_madre=$padres["Nombre"];
									$apellido1_madre=$padres["Apellido1"];
									$apellido2_madre=$padres["Apellido2"];
									$dni_madre=$padres["DNI"];
									$tiene_padres = true;
								}
								
								if ($padres["sexo"]=="V") {
									$nombre_padre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$nombre_solo_padre=$padres["Nombre"];
									$apellido1_padre=$padres["Apellido1"];
									$apellido2_padre=$padres["Apellido2"];
									$dni_padre=$padres["DNI"];
									$tiene_padres = true;
								}
							
							}						
							
						}
						
						if ($nombre_madre=="") {
							
							$dni_madre=$dni_padre;
							$nombre_madre=$nombre_padre;
							$nombre_solo_madre=$nombre_solo_padre;
							$apellido1_madre=$apellido1_padre;
							$apellido2_madre=$apellido2_padre;
							
						}
						*/ 

						$dni_madre=str_replace("-","",$dni_madre);
						$dni_madre=str_replace(".","",$dni_madre);
						$dni_madre=str_replace(",","",$dni_madre);
						$dni_madre=str_replace(" ","",$dni_madre);
						
						$dni_madreOld='';

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
						
						$dni_padreOld='';
						
						if ($dni_padre) {
							
							$dni_padreOld=$dni_padre;
							$nuevoDni=CalcularLetraNIF($dni_padre);
							
							if ($nuevoDni) {
								$dni_padre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						
						// Quita acentos
						$nombre_madre=eliminar_tildes($nombre_madre);
						$nombre_solo_madre=eliminar_tildes($nombre_solo_madre);
						$apellido1_madre=eliminar_tildes($apellido1_madre);
						$apellido2_madre=eliminar_tildes($apellido2_madre);
						
						$nombre_padre=eliminar_tildes($nombre_padre);						
						$nombre_solo_padre=eliminar_tildes($nombre_solo_padre);
						$apellido1_padre=eliminar_tildes($apellido1_padre);
						$apellido2_padre=eliminar_tildes($apellido2_padre);	
						
						if ($apellido2_madre==$apellido2_padre) {
							$nombre_madre=$nombre_solo_madre." ".$apellido1_madre;
							$nombre_padre=$nombre_solo_padre." ".$apellido1_padre;	
						}
						
							
						$alumno_actual=eliminar_tildes($alumno_actual);				
						$nombre_alumno_actual=eliminar_tildes($nombre_alumno_actual);
						$apellido1_alumno_actual=eliminar_tildes($apellido1_alumno_actual);
						$apellido2_alumno_actual=eliminar_tildes($apellido2_alumno_actual);											
						
						if ($exportar!=1) {
							// Mostrar en pantalla																																					
							$color_madre="";
							if (comprobar_nif($dni_madre)!='OK') {
								$color_madre=" class='danger'";	
							}
							$color_padre="";
							if (comprobar_nif($dni_padre)!='OK') {
								$color_padre=" class='danger'";	
							}
								
							$observaciones="";
							if (($color_madre==" class='danger'") and ($color_padre==" class='danger'")) {
								
								$observaciones="- No se enviará. Ni el padre ni la madre tienen DNI o NIE. <a target='_blank' href='/alumnos/nuevo_alumno2.php?u=".$id_cliente_actual."'>Corregir</a><br>";
								
							} else {
								
								if (($color_madre==" class='danger'") and ($color_padre!=" class='danger'")) {
									
									$observaciones="- La madre no tiene DNI o NIE. Pero el padre si... ¿Debemos enviarlo solo con los datos de padre? <a target='_blank' href='/alumnos/nuevo_alumno2.php?u=".$id_cliente_actual."'>Corregir</a><br>";
										
								}
								
								if ($color_padre==" class='danger'") {
									
									$observaciones="- El padre no tiene DNI o NIE.Se enviará solo con los datos de la madre. <a target='_blank' href='/alumnos/nuevo_alumno2.php?u=".$id_cliente_actual."'>Corregir</a><br>";
										
								}		
								
							}
							
							if (($color_madre!="") or ($color_padre!="")) {
								
								$listado_alumnos_errores[$id_cliente_actual] = array(
									'escuela' => $nombre_escuela,
									'nombre_alumno' => $alumno_actual,
									'dni_madre' => $dni_madreOld,
									'nombe_madre' => $nombre_madre,
									'dni_padre' => $dni_padreOld,
									'nombre_padre' => $nombre_padre,
									'observaciones' => $observaciones
								);
								
							}
							
							$color_pagado=" success";
							$color_subvenciones=" success";
							if ($total_educacion_dni==0) {
								$color_pagado=" danger";
								$observaciones.="- Los padres no han pagado nada";	
															
								if ($total_subvenciones_dni!=0) {								
									$observaciones.=" pero han recibido subvención.";	
								} else {
									$observaciones.=" ni han recibido subvención.";	
								}							
								
								$observaciones.=".<br>";
							}							
							
							
                        ?>
                        <tr>	                      
                          <td<?=$color_madre?>><?=$dni_madre;?></td>
                          <td<?=$color_madre?>><?=mb_convert_case($nombre_madre, MB_CASE_UPPER);?></td>						
                          <td<?=$color_padre?>><?=$dni_padre;?></td>
                          <td<?=$color_padre?>><?=mb_convert_case($nombre_padre, MB_CASE_UPPER);?></td>				  
                          <td><?=mb_convert_case($alumno_actual, MB_CASE_UPPER);?></td>
						  <td><?=date("d/m/Y",strtotime($fecha_nacimiento_alumno))?></td>
                          <td><?=$nombre_escuela;?></td>
						  <?php	
						} else {
							// Exportar CSV
							
							$incluir_fila=true;
							
							$dni_madre_ok=true;
							if (comprobar_nif($dni_madre)!='OK') {
								$dni_madre_ok=false;	
							}
							$dni_padre_ok=true;
							if (comprobar_nif($dni_padre)!='OK') {
								$dni_padre_ok=false;	
							}
								
							$observaciones="";
							if (($dni_madre_ok==false) and ($dni_padre_ok==false)) {
								
								$incluir_fila=false;
								
							} else {
								
								if (($dni_madre_ok==false) and ($dni_padre_ok==true)) {
									
									// La madre no tiene DNI o NIE, enviamos datos del padre
									$dni_madre=$dni_padre;
									$dni_padre="";	
									$nombre_solo_madre=$nombre_solo_padre;
									$apellido1_madre=$apellido1_padre;
									$apellido2_madre=$apellido2_padre;	
									$nombre_solo_padre="";
									$apellido1_padre="";
									$apellido2_padre="";
								}
								
								if ($dni_padre_ok==false) {
									
									// El padre no tiene DNI o NIE.Se enviará solo con los datos de la madre
									$dni_padre="";
									$nombre_solo_padre="";
									$apellido1_padre="";
									$apellido2_padre="";									
										
								}		
								
							}
							
							if ($total_educacion_dni==0) {
								
								// Los padres no han pagado nada	
															
								if ($total_subvenciones_dni!=0) {								
									// pero han recibido subvención	
								} else {
									// ni han recibido subvención.";
									$incluir_fila=false;	
								}							

							}							

							// Tipo de Registro	Modelo 	Ejercicio	Identificador de Registro
							$arrayFilaActual=array(2,233,$ejercicio,$id_cliente_actual+$incrementoEscuela);
							
							$arrayFilaActual[]=$dni_madre;							
							$arrayFilaActual[]=mb_convert_case($apellido1_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_solo_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=$dni_padre;							
							$arrayFilaActual[]=mb_convert_case($apellido1_padre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_padre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_solo_padre, MB_CASE_UPPER);							
							$arrayFilaActual[]='';					// NIF Menor							
							$arrayFilaActual[]=mb_convert_case($apellido1_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=date("Ymd",strtotime($fecha_nacimiento_alumno));
														
						}
						  // Busca meses asistidos en el ejercicio
						  
						  $num_meses_asistidos=0;
						  for($c_meses=1;$c_meses<=12;$c_meses++) {
							  
							$dias_asistencia=0;
							$motivo_no="";
							
							$fecha_ini_mes=date("Y-m-d",strtotime($ejercicio."-".$c_meses."-01"));
							$fecha_fin_mes=date("Y-m-t",strtotime($fecha_ini_mes));
							
							$asistencia_mes="N";
							
							$comprueba_inscripcion=db_query("SELECT * FROM alumnos LEFT JOIN centro_aulas ON alumnos.aula=centro_aulas.id_aula WHERE id_usuario=$id_cliente_actual AND fecha_matricula<='$fecha_fin_mes' AND fecha_baja>='$fecha_ini_mes'") or die(db_error());
							
							if (db_num_rows($comprueba_inscripcion)!=0) {								
									
									$datos_inscripcion=db_fetch_array($comprueba_inscripcion);
									
									$tiempo_asistencia=strtotime($datos_inscripcion['fecha_baja'])-strtotime($datos_inscripcion['fecha_matricula']);
									
									$dias_asistencia=ceil($tiempo_asistencia/86400);
																	
									$asistencia_mes="S";
									
									// Si es segundo ciclo, comprueba fecha de nacimiento
									
									if (($datos_inscripcion["ciclo"]==2) and (date("n",strtotime($fecha_nacimiento_alumno))<$c_meses)) {
									
										$asistencia_mes="N";
										$motivo_no="> 3 años.";
									
									} else {
										
										// Comprueba si es custodia y si el niño tiene más de 3 años en el mes de diciembre
										if (($c_meses==12) or ($c_meses==7)) {
												
											$edadAhora = $ejercicio-date("Y",strtotime($fecha_nacimiento_alumno));
											$mes_nacimiento=date("n",strtotime($fecha_nacimiento_alumno));
											if ($mes_nacimiento>$c_meses) {
												$edadAhora--;
											}	
											if ($edadAhora>=3) {
												$asistencia_mes="N";
												$motivo_no="> 3 años.";
											}	
											
										}
										
									}

									if ($dias_asistencia==0) {
										$asistencia_mes="N";
										$motivo_no="Mes incompleto";
									}

							}
							
							if ($asistencia_mes=="S") {
								
								$num_meses_asistidos++;
								
							}							
							
								if ($exportar!=1) {
									// Mostrar en pantalla
						  ?>
							  <td><?=$asistencia_mes;?><?="<br>".$dias_asistencia."<br><small>".$motivo_no."</small>";?></td>						  						  
						  <?php
								} else {
									// Exportar CSV
									$arrayFilaActual[]=$asistencia_mes;
									
								}
						  }
						  
								if ($exportar!=1) {
									// Mostrar en pantalla
						  
						  if ($num_meses_asistidos==0) {
								$observaciones.="- No ha asistido ningún mes completo.<br>";
								
								// No ha asistido ningún mes completo, no se incluye
								$incluir_fila=false;	  
						  }
						  
						  ?>
                          <td class="text-right"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</td>
						  <td class="text-right"><?=number_format($total_ticket_guarderia, 2, ',', '.')?> €</td>						  
						  <td class="text-right<?=$color_subvenciones;?>"><?=number_format($total_subvenciones_dni*-1, 2, ',', '.')?> €</td>						  						  						  
						  <td class="text-right<?=$color_pagado;?>"><?=number_format($total_pagado_padres, 2, ',', '.')?> €</td>
						  <td>
							  <small><small>
							  <?=$observaciones?>
							  <br>
							  <b>Cobrado Educación</b><br>
							  <?=$desglose_alumno?>
							  <br><br>
							  <b>Subvenciones</b><br>
							  <?=$desglose_alumno_sub?>
							  </small></small>
						  </td>				  
                        </tr>
                        <?php
								} else {
								
									// No ha asistido ningún mes completo, no se incluye
									if ($num_meses_asistidos==0) {
										$incluir_fila=false;	
									}

									// Exportar CSV

									// Sumo ticket guardería a educación si procede
									if ($sumarTicketGuarderiaEnEducacion==true) {
										$totalEducacionFila = $total_educacion_dni+$total_ticket_guarderia;									
									} else {
										$totalEducacionFila = $total_educacion_dni;	
									}

									if (($total_subvenciones_dni*-1)>$totalEducacionFila) {
										$total_subvenciones_dni=($totalEducacionFila*-1);
									}									
									$arrayFilaActual[]=round($totalEducacionFila,2);
									$arrayFilaActual[]=round(($total_subvenciones_dni*-1),2);									
									$arrayFilaActual[]=$codigo_escuela;
									$arrayFilaActual[]="10";
									$arrayFilaActual[]="";
									$arrayFilaActual[]="";
																		
									// Si todo OK se incluye en el CSV
									if ($incluir_fila==true) {

										fputcsv($out, $arrayFilaActual, ";");	
									
									}
								}
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
						$nombre_alumno_actual=$datos_factura["Nombre"];
						$apellido1_alumno_actual=$datos_factura["Apellido1"];
						$apellido2_alumno_actual=$datos_factura["Apellido2"];
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
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (odbc_errormsg());

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
                $intentar_localizar=true;

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


                                $busca_servicio=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_servicio_consumido=$id_servicio_actual;");

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

                                $busca_servicio=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_servicio_contratado=$id_servicio_actual");

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

                        $busca_servicio=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_servicio_contratado=".$codigo_tarifa) or die(db_error());

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
					
                        $palabras0=Array("chandal","uniforme","material escolar","Babero","sabana");

                        for ($c_pal=0;$c_pal<=count($palabras0);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras0[$c_pal]))) {
                                        $codigo_cuenta=0;
                                        break;
                                        }

                                }

                        $palabras1=Array("salida","visita","gabinete");

                        for ($c_pal=0;$c_pal<=count($palabras1);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras1[$c_pal]))) {
                                        $codigo_cuenta=1;
                                        break;
                                        }

                                }

                        $palabras2=Array("comedor","comida","paella","toma mensual");

                        for ($c_pal=0;$c_pal<=count($palabras2);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);
								
                                if (is_numeric(strpos($concepto_min,$palabras2[$c_pal]))) {
                                        $codigo_cuenta=2;
                                        break;
                                        }

                                }

						$palabras3=Array("educacion","educación","hora extra mensual","inscripción","inscripcion","Gestion y Comunicacion");

                        for ($c_pal=0;$c_pal<=count($palabras3);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras3[$c_pal]))) {
                                        $codigo_cuenta=3;
                                        break;
                                        }

                                }

                        }																								

						if ((substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") or (substr($datos_albaranes["concepto"],0,13)=="Bono Infantil") or (substr($datos_albaranes["concepto"],0,24)=="Subvencion Bono Infantil")) {
													$total_subvenciones_dni=$total_subvenciones_dni+$total_abs;
													$desglose_alumno_sub.=$datos_albaranes["concepto"]." ".number_format($total_abs,2,",",".")." €<br>";
													$codigo_cuenta=0;
													}                               
						if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor") or (substr($datos_albaranes["concepto"],0,14)=="CHEQUE ESCOLAR")) {
													$total_subvenciones_dni=$total_subvenciones_dni+$total_abs;                                		
													$desglose_alumno_sub.=$datos_albaranes["concepto"]." ".number_format($total_abs,2,",",".")." €<br>";													
													$codigo_cuenta=0;
													} 	

						if ((substr($datos_albaranes["concepto"],0,13)=="bono infantil") or (substr($datos_albaranes["concepto"],0,14)=="Bono Educación")) {
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
		$codigo_escuela=$codigos_centros[$datos_factura["centro_factura"]];
		
		
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
						
						$busca_padres=db_query("SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario WHERE padre_de=".$id_cliente_actual." ORDER BY parentesco") or die(db_error());
												
						$tiene_padres = false;
						
						$nombre_madre="";
						$dni_madre="";
						$nombre_padre="";
						$dni_padre="";
						$nombre_solo_madre="";
						$apellido1_madre="";
						$apellido2_madre="";
						$nombre_solo_padre="";
						$apellido1_padre="";
						$apellido2_padre="";						

// Guardamos hasta 2 progenitores (parentesco = 1)
$p1 = null;
$p2 = null;

while ($row = db_fetch_array($busca_padres)) {

    if ((int)$row["parentesco"] != 1) {
        continue;
    }

    if ($p1 === null) {
        $p1 = $row;
    } elseif ($p2 === null) {
        $p2 = $row;
    } else {
        // Si hubiera más de 2, ignoramos el resto (ajustable)
        break;
    }
}

$tiene_padres = ($p1 !== null);

// Helper inline: asignación a variables según rol
// (lo haremos como bloques para no usar funciones)

// 1 progenitor -> siempre a _madre
if ($p1 !== null && $p2 === null) {

    $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
    $nombre_solo_madre = $p1["Nombre"];
    $apellido1_madre   = $p1["Apellido1"];
    $apellido2_madre   = $p1["Apellido2"];
    $dni_madre         = $p1["DNI"];

}

// 2 progenitores
if ($p1 !== null && $p2 !== null) {

    $sexo1 = isset($p1["sexo"]) ? $p1["sexo"] : "";
    $sexo2 = isset($p2["sexo"]) ? $p2["sexo"] : "";

    // Si son sexos opuestos (H/V), H -> madre, V -> padre
    if ($sexo1 !== "" && $sexo2 !== "" && $sexo1 != $sexo2) {

        if ($sexo1 == "H") {
            // p1 madre, p2 padre
            $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
            $nombre_solo_madre = $p1["Nombre"];
            $apellido1_madre   = $p1["Apellido1"];
            $apellido2_madre   = $p1["Apellido2"];
            $dni_madre         = $p1["DNI"];

            $nombre_padre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
            $nombre_solo_padre = $p2["Nombre"];
            $apellido1_padre   = $p2["Apellido1"];
            $apellido2_padre   = $p2["Apellido2"];
            $dni_padre         = $p2["DNI"];

        } else {
            // p2 madre (H), p1 padre (V)
            $nombre_madre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
            $nombre_solo_madre = $p2["Nombre"];
            $apellido1_madre   = $p2["Apellido1"];
            $apellido2_madre   = $p2["Apellido2"];
            $dni_madre         = $p2["DNI"];

            $nombre_padre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
            $nombre_solo_padre = $p1["Nombre"];
            $apellido1_padre   = $p1["Apellido1"];
            $apellido2_padre   = $p1["Apellido2"];
            $dni_padre         = $p1["DNI"];
        }

    } else {
        // Mismo sexo (H/H o V/V) o sexo desconocido: por orden (p1->madre, p2->padre)

        $nombre_madre      = $p1["Nombre"] . " " . $p1["Apellido1"] . " " . $p1["Apellido2"];
        $nombre_solo_madre = $p1["Nombre"];
        $apellido1_madre   = $p1["Apellido1"];
        $apellido2_madre   = $p1["Apellido2"];
        $dni_madre         = $p1["DNI"];

        $nombre_padre      = $p2["Nombre"] . " " . $p2["Apellido1"] . " " . $p2["Apellido2"];
        $nombre_solo_padre = $p2["Nombre"];
        $apellido1_padre   = $p2["Apellido1"];
        $apellido2_padre   = $p2["Apellido2"];
        $dni_padre         = $p2["DNI"];
    }
}

						/*
						$tiene_padres = false;
						
						$nombre_madre="";
						$dni_madre="";
						$nombre_padre="";
						$dni_padre="";
						
						while($padres=mysql_fetch_array($busca_padres)) {
							
							if ($padres["parentesco"]==1) {
							
								if ($padres["sexo"]=="H") {
									$nombre_madre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$nombre_solo_madre=$padres["Nombre"];
									$apellido1_madre=$padres["Apellido1"];
									$apellido2_madre=$padres["Apellido2"];
									$dni_madre=$padres["DNI"];
									$tiene_padres = true;
								}
								
								if ($padres["sexo"]=="V") {
									$nombre_padre=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
									$nombre_solo_padre=$padres["Nombre"];
									$apellido1_padre=$padres["Apellido1"];
									$apellido2_padre=$padres["Apellido2"];
									$dni_padre=$padres["DNI"];
									$tiene_padres = true;
								}
							
							}						
							
						}
						
						if ($nombre_madre=="") {
							
							$dni_madre=$dni_padre;
							$nombre_madre=$nombre_padre;
							$nombre_solo_madre=$nombre_solo_padre;
							$apellido1_madre=$apellido1_padre;
							$apellido2_madre=$apellido2_padre;
							
						}
						*/

						$dni_madre=str_replace("-","",$dni_madre);
						$dni_madre=str_replace(".","",$dni_madre);
						$dni_madre=str_replace(",","",$dni_madre);
						$dni_madre=str_replace(" ","",$dni_madre);

						$dni_madreOld='';

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
						
						$dni_padreOld = '';
						
						if ($dni_padre) {
							
							$dni_padreOld=$dni_padre;
							$nuevoDni=CalcularLetraNIF($dni_padre);
							
							if ($nuevoDni) {
								$dni_padre=$nuevoDni[0].$nuevoDni[1];
							}
							
						}
						// Quita acentos
						$nombre_madre=eliminar_tildes($nombre_madre);
						$nombre_solo_madre=eliminar_tildes($nombre_solo_madre);
						$apellido1_madre=eliminar_tildes($apellido1_madre);
						$apellido2_madre=eliminar_tildes($apellido2_madre);
						
						$nombre_padre=eliminar_tildes($nombre_padre);						
						$nombre_solo_padre=eliminar_tildes($nombre_solo_padre);
						$apellido1_padre=eliminar_tildes($apellido1_padre);
						$apellido2_padre=eliminar_tildes($apellido2_padre);	
							
						$alumno_actual=eliminar_tildes($alumno_actual);				
						$nombre_alumno_actual=eliminar_tildes($nombre_alumno_actual);
						$apellido1_alumno_actual=eliminar_tildes($apellido1_alumno_actual);
						$apellido2_alumno_actual=eliminar_tildes($apellido2_alumno_actual);											
						
						if ($exportar!=1) {
							// Mostrar en pantalla																																					
							$color_madre="";
							if (comprobar_nif($dni_madre)!='OK') {
								$color_madre=" class='danger'";	
							}
							$color_padre="";
							if (comprobar_nif($dni_padre)!='OK') {
								$color_padre=" class='danger'";	
							}
								
							$observaciones="";
							if (($color_madre==" class='danger'") and ($color_padre==" class='danger'")) {
								
								$observaciones="- No se enviará. Ni el padre ni la madre tienen DNI o NIE.<br>";
								
							} else {
								
								if (($color_madre==" class='danger'") and ($color_padre!=" class='danger'")) {
									
									$observaciones="- La madre no tiene DNI o NIE. Pero el padre si... ¿Debemos enviarlo solo con los datos de padre?<br>";
										
								}
								
								if ($color_padre==" class='danger'") {
									
									$observaciones="- El padre no tiene DNI o NIE.Se enviará solo con los datos de la madre.<br>";
										
								}		
								
							}
							
							if (($color_madre!="") or ($color_padre!="")) {
								
								$listado_alumnos_errores[$id_cliente_actual] = array(
									'escuela' => $nombre_escuela,
									'nombre_alumno' => $alumno_actual,
									'dni_madre' => $dni_madreOld,
									'nombe_madre' => $nombre_madre,
									'dni_padre' => $dni_padreOld,
									'nombre_padre' => $nombre_padre,
									'observaciones' => $observaciones
								);
								
							}							
							
							$color_pagado=" success";
							$color_subvenciones=" success";
							if ($total_educacion_dni==0) {
								$color_pagado=" danger";
								$observaciones.="- Los padres no han pagado nada";	
															
								if ($total_subvenciones_dni!=0) {								
									$observaciones.=" pero han recibido subvención.";	
								} else {
									$observaciones.=" ni han recibido subvención.";	
								}							
								
								$observaciones.=".<br>";
							}							
							
							
                        ?>
                        <tr>	                      
                          <td<?=$color_madre?>><?=$dni_madre;?></td>
                          <td<?=$color_madre?>><?=mb_convert_case($nombre_madre, MB_CASE_UPPER);?></td>						
                          <td<?=$color_padre?>><?=$dni_padre;?></td>
                          <td<?=$color_padre?>><?=mb_convert_case($nombre_padre, MB_CASE_UPPER);?></td>				  
                          <td><?=mb_convert_case($alumno_actual, MB_CASE_UPPER);?></td>
						  <td><?=date("d/m/Y",strtotime($fecha_nacimiento_alumno))?></td>
                          <td><?=$nombre_escuela;?></td>
						  <?php	
						} else {
							// Exportar CSV
							
							$incluir_fila=true;
							
							$dni_madre_ok=true;
							if (comprobar_nif($dni_madre)!='OK') {
								$dni_madre_ok=false;	
							}
							$dni_padre_ok=true;
							if (comprobar_nif($dni_padre)!='OK') {
								$dni_padre_ok=false;	
							}
								
							$observaciones="";
							if (($dni_madre_ok==false) and ($dni_padre_ok==false)) {
								
								$incluir_fila=false;
								
							} else {
								
								if (($dni_madre_ok==false) and ($dni_padre_ok==true)) {
									
									// La madre no tiene DNI o NIE, enviamos datos del padre
									$dni_madre=$dni_padre;
									$dni_padre="";	
									$nombre_solo_madre=$nombre_solo_padre;
									$apellido1_madre=$apellido1_padre;
									$apellido2_madre=$apellido2_padre;	
									$nombre_solo_padre="";
									$apellido1_padre="";
									$apellido2_padre="";
								}
								
								if ($dni_padre_ok==false) {
									
									// El padre no tiene DNI o NIE.Se enviará solo con los datos de la madre
									$dni_padre="";
									$nombre_solo_padre="";
									$apellido1_padre="";
									$apellido2_padre="";									
										
								}		
								
							}
							
							if ($total_educacion_dni==0) {
								
								// Los padres no han pagado nada	
															
								if ($total_subvenciones_dni!=0) {								
									// pero han recibido subvención	
								} else {
									// ni han recibido subvención.";
									$incluir_fila=false;	
								}							

							}							

							// Tipo de Registro	Modelo 	Ejercicio	Identificador de Registro
							$arrayFilaActual=array(2,233,$ejercicio,$id_cliente_actual+$incrementoEscuela);
							
							$arrayFilaActual[]=$dni_madre;							
							$arrayFilaActual[]=mb_convert_case($apellido1_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_solo_madre, MB_CASE_UPPER);
							$arrayFilaActual[]=$dni_padre;							
							$arrayFilaActual[]=mb_convert_case($apellido1_padre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_padre, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_solo_padre, MB_CASE_UPPER);							
							$arrayFilaActual[]='';					// NIF Menor							
							$arrayFilaActual[]=mb_convert_case($apellido1_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($apellido2_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=mb_convert_case($nombre_alumno_actual, MB_CASE_UPPER);
							$arrayFilaActual[]=date("Ymd",strtotime($fecha_nacimiento_alumno));
														
						}
						  // Busca meses asistidos en el ejercicio
						  
						  $num_meses_asistidos=0;
						  for($c_meses=1;$c_meses<=12;$c_meses++) {
							  
							$dias_asistencia=0;
							$motivo_no="";
							
							$fecha_ini_mes=date("Y-m-d",strtotime($ejercicio."-".$c_meses."-01"));
							$fecha_fin_mes=date("Y-m-t",strtotime($fecha_ini_mes));
							
							$asistencia_mes="N";
							
							$comprueba_inscripcion=db_query("SELECT * FROM alumnos LEFT JOIN centro_aulas ON alumnos.aula=centro_aulas.id_aula WHERE id_usuario=$id_cliente_actual AND fecha_matricula<='$fecha_fin_mes' AND fecha_baja>='$fecha_ini_mes'") or die(db_error());
							
							if (db_num_rows($comprueba_inscripcion)!=0) {								
									
									$datos_inscripcion=db_fetch_array($comprueba_inscripcion);
									
									$tiempo_asistencia=strtotime($datos_inscripcion['fecha_baja'])-strtotime($datos_inscripcion['fecha_matricula']);
									
									$dias_asistencia=ceil($tiempo_asistencia/86400);
																	
									$asistencia_mes="S";
									
									// Si es segundo ciclo, comprueba fecha de nacimiento
									
									if (($datos_inscripcion["ciclo"]==2) and (date("n",strtotime($fecha_nacimiento_alumno))<$c_meses)) {
									
										$asistencia_mes="N";
										$motivo_no="> 3 años.";
									
									} else {
										
										// Comprueba si es custodia y si el niño tiene más de 3 años en el mes de diciembre
										if (($c_meses==12) or ($c_meses==7)) {
												
											$edadAhora = $ejercicio-date("Y",strtotime($fecha_nacimiento_alumno));
											$mes_nacimiento=date("n",strtotime($fecha_nacimiento_alumno));
											if ($mes_nacimiento>$c_meses) {
												$edadAhora--;
											}	
											if ($edadAhora>=3) {
												$asistencia_mes="N";
												$motivo_no="> 3 años.";
											}	
											
										}
										
									}

									if ($dias_asistencia==0) {
										$asistencia_mes="N";
										$motivo_no="Mes incompleto";
									}

							}
							
							if ($asistencia_mes=="S") {
								
								$num_meses_asistidos++;
								
							}							
							 
								if ($exportar!=1) {
									// Mostrar en pantalla
						  ?>
							  <td><?=$asistencia_mes;?><?="<br>".$dias_asistencia."<br><small>".$motivo_no."</small>";?></td>						  
						  <?php
								} else {
									// Exportar CSV
									$arrayFilaActual[]=$asistencia_mes;
									
								}
						  }
						  
						  if ($num_meses_asistidos==0) {
								$observaciones.="- No ha asistido ningún mes completo.<br>";
								
								// No ha asistido ningún mes completo, no se incluye
								$incluir_fila=false;	  
						  }						  
						  
								if ($exportar!=1) {
									// Mostrar en pantalla						 
						  
						  ?>
                          <td class="text-right"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</td>
						  <td class="text-right"><?=number_format($total_ticket_guarderia, 2, ',', '.')?> €</td>						  
						  <td class="text-right<?=$color_subvenciones;?>"><?=number_format($total_subvenciones_dni*-1, 2, ',', '.')?> €</td>						  						  						  
						  <td class="text-right<?=$color_pagado;?>"><?=number_format($total_pagado_padres, 2, ',', '.')?> €</td>
						  <td>
							  <small><small>
							  <?=$observaciones?>
							  <br>
							  <b>Cobrado Educación</b><br>
							  <?=$desglose_alumno?>
							  <br><br>
							  <b>Subvenciones</b><br>
							  <?=$desglose_alumno_sub?>
							  </small></small>
						  </td>				  
                        </tr>
                        <?php 
								} else {
									// Exportar CSV

									// Sumo ticket guardería a educación si procede
									if ($sumarTicketGuarderiaEnEducacion==true) {
										$totalEducacionFila = $total_educacion_dni+$total_ticket_guarderia;									
									} else {
										$totalEducacionFila = $total_educacion_dni;	
									}									

									if (($total_subvenciones_dni*-1)>$totalEducacionFila) {
										$total_subvenciones_dni=($totalEducacionFila*-1);
									}	
									$arrayFilaActual[]=round($totalEducacionFila,2);
									$arrayFilaActual[]=round(($total_subvenciones_dni*-1),2);									
									$arrayFilaActual[]=$codigo_escuela;
									$arrayFilaActual[]="10";
									$arrayFilaActual[]="";
									$arrayFilaActual[]="";
									
									// Si todo OK se incluye en el CSV
									if ($incluir_fila==true) {

										fputcsv($out, $arrayFilaActual, ";");	
									
									}
								}	
								if ($exportar!=1) {
									// Mostrar en pantalla	
									
									$muestra_errores="<table class='table table-bordered'>";
									
									foreach($listado_alumnos_errores as $idAluError => $datosAluError) {
									
									$muestra_errores.= "<tr>
										<td>".$idAluError."</td>
										<td>".$datosAluError['escuela']."</td>
										<td>".$datosAluError['nombre_alumno']."</td>
										<td>".$datosAluError['dni_madre']."</td>
										<td>".$datosAluError['nombe_madre']."</td>
										<td>".$datosAluError['dni_padre']."</td>
										<td>".$datosAluError['nombre_padre']."</td>
										<td>".$datosAluError['observaciones']."</td>
									</tr>";														
										
									}
									
									$muestra_errores.="</table>";
																
						?>
                         </table>
                         <?=$muestra_errores?>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_facturacion.php';" style="font-size: 10px"><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php require("../configuracion/parte_inferior.php"); 
								} else {
									// Cierra CSV
									
									fclose($out);
									
								}
?>
