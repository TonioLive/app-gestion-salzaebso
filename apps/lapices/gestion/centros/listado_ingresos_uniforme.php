<?php  require("../../configuracion/parte_superior_privado_ancho_bootstrap.php"); ?>
<style>

.modal.modal-wide .modal-dialog {
  width: 80%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}

</style>
<?php 

// Comprobar permisos de acceso
if (!in_array($idUsuario,$usuariosPermitidos)) {
	exit("No tienes permiso para acceder a esta página");
}

$mostrar_estadisticas=false;

$num_columnas_tabla=7;
if ($_GET["mostrar_estadisticas"]==1) {
        $mostrar_estadisticas=true;
        $num_columnas_tabla++;
        } 

$total_ninos=0;
$total_ninos=0;

$id_centro=$centro_actual;

$fecha_seleccionada=$_GET["fecha_seleccionada"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=date("U");
        }


// Primer y último día del mes
$inicioMes = date("Y-m-01",$fecha_seleccionada); 
$finMes = date("Y-m-t",$fecha_seleccionada);


$url_retorno="/index.php";
        
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }

        // Busca el curso actual

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_seleccionada)."' AND fecha_fin>='".date("Y-m-d",$fecha_seleccionada)."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombreCurso=$datos_curso_actual["nombre"];
                }

        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><big><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b></big><br><br>
                        <table class="table table-bordered table-striped">
                        <tr valign="top">
                                <td colspan="5" align="center" bgcolor="#efefef"><big><b>Ingresos por aulas durante el curso <?=$nombreCurso?></b></big></td>
                        </tr>
                         <?php 
                         
                         if ($id_curso_actual==0) {
                         ?>
                         <tr>
                         	<td colspan="11" class="text-center" bgcolor="#fff">
                         		<h2>No hay clases en el mes seleccionado</h2>
                         	</td>
                         </tr>
                         <?php
                         } else {
                         
                         $totalIngresosAula=0;
                         $totalIngresosHoraExtra=0;
                         $totalIngresosComedor=0;
                         $totalIngresosPiscina=0;
                         $totalIngresosTotal=0; 
                         $totalSubvenciones=0;

                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo<3 AND id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$aulas["id_curso"]);
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                $fecha_fin_curso=$datos_curso["fecha_fin"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                $fecha_fin_curso=date();
                                }

                        // Analiza el aula
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$aulas["id_aula"].";") or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);
                                
                        // Calcula ingresos por uniformes
                                
                        //Servicio alumno
                        $busca_inscripcion=db_query("SELECT * FROM alumnos, usuarios, servicios_contratados, tarifa WHERE alumnos.id_usuario=usuarios.id_usuario AND servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=alumnos.id_alumno AND tarifa.CATEGORIA=8 AND alumnos.aula=".$aulas["id_aula"].";") or die(db_error()."-3");                                
						// Calcula ingresos por uniformes						
						$ingresosAula=0;
												
						while($datosInscripcion=db_fetch_array($busca_inscripcion)){
						
							$total_linea=$datosInscripcion["cantidad"]*$datosInscripcion["PRECIO_VENTA"];
        					$total_dto=$total_linea*round(($datosInscripcion["descuento"]/100),2);
        					$base_linea=$total_linea-$total_dto;
							$ingresosAula+=$base_linea;
							
							$desgloseAula.="<tr><td>".$datosInscripcion["ARTICULO"]." - ".$datosInscripcion["Nombre"]." ".$datosInscripcion["Apellido1"]." ".$datosInscripcion["Apellido2"]."</td><td class='text-right'>".number_format($base_linea,2,",",".")." €</td></tr>";							
							
						}

                        // Servicio aula
                        
                        $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=".$aulas["id_aula"]." AND tarifa.CATEGORIA=8;") or die(db_error()."-4");

                        while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $consulta_ocupacion_total=db_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$aulas["id_aula"]) or die(db_error()."-1");
                        while($datos_ocupacion_total=db_fetch_array($consulta_ocupacion_total)) {
                                $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=".$datos_ocupacion_total["id_alumno"]);
                                        if (db_num_rows($busca_si_activado)==0) {
                                                
												$total_linea=$datos_inscripcion["cantidad"]*$datos_inscripcion["PRECIO_VENTA"];
					        					$total_dto=$total_linea*round(($datos_inscripcion["descuento"]/100),2);
        										$base_linea=$total_linea-$total_dto;
        										
												$ingresosAula+=$base_linea;

												$desgloseAula.="<tr><td>".$datos_inscripcion["ARTICULO"]." - ".$datos_ocupacion_total["Nombre"]." ".$datos_ocupacion_total["Apellido1"]." ".$datos_ocupacion_total["Apellido2"]."</td><td class='text-right'>".number_format($base_linea,2,",",".")." €</td></tr>";

                                                }
                                        }
                                }                                                        
                        


                        // Fin servicios
                        $desgloses[$aulas["id_aula"]] = "<table class='table table-bordered table-striped'>".$desgloseAula."</table>";
                        $nombresAulas[$aulas["id_aula"]] = $aulas["nombre"];
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=4 align="left" bgcolor="#efefef"><big>Curso: <?=$nombre_curso_academico;?></big></td>
                                <td class="text-right" bgcolor="#efefef"><button type="button" onclick="selecciona_fecha();" title="Selecciona la fecha en el calendario" class="btn btn-default btn-sm"><i class="fa fa-calendar" aria-hidden="true"></i> Seleccionar fecha</button></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><b>Nombre</b></td>
                                <td align="center" bgcolor="#efefef"><b>Capacidad</b></td>
                                <td align="center" bgcolor="#efefef"><b>Ocupación</b></td>
                                <td align="center" bgcolor="#efefef"><b>Ingresos por venta de Uniformes</b></td>                           
                                <td align="center" bgcolor="#efefef"><b>&nbsp;</b></td>
                        </tr>
                        <?php 
                        $curso_actual=$nombre_curso_academico;
                        }
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><?=$aulas["nombre"];?></td>
                                <td align="center" bgcolor="#ffffff"><?=$aulas["capacidad"];?></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(5,<?=$aulas["id_aula"]?>);'><?=$ocupacion_actual;?></a></td>
                                <td align="right" bgcolor="#ffffff"><?=number_format($ingresosAula,2,",",".")?> €</td>
                                <td align="center" bgcolor="#ffffff">
                                	<button type="button" onclick="mostrar_desglose(<?=$aulas["id_aula"]?>)" class="btn btn-success btn-sm"><i class="fa fa-file-text-o" aria-hidden="true"></i></button> 
                                	<a target="_blank" href="listado_alumnos.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula" class="btn btn-primary btn-sm"><i class="fa fa-user" aria-hidden="true"></i></a>
                                </td>
                        </tr>
                         <?php 
	                         $totalIngresosAula+=$ingresosAula;                       
                         }
                        ?>
                        <tr>
                        	<td colspan="3"><b>Total centro:</b></td>
                        	<td align="right" bgcolor="#ffffff"><?=number_format($totalIngresosAula,2,",",".")?> €</td>                       
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        }
                        ?>
                        </table><br><br>
                </td>
        </tr>
</table><br><br>

<!-- Modal -->

<?php
	
foreach($desgloses as $key=>$value) {

?>
<div class="modal modal-wide fade" id="desglose<?=$key?>" tabindex="-1" role="dialog" aria-labelledby="desglose<?=$key?>Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="desglose<?=$key?>Label">Desglose ingresos aula <?=$nombresAulas[$key];?></h4>
      </div>
      <div class="modal-body text-center">
			<?=$value?>
      </div>    
    </div>
  </div>
</div>
<?php

}

?>

<script language="Javascript">

function mostrar_desglose(id_aula) {

	$('#desglose'+id_aula).modal('show');

}

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_seleccionada);?>;
        dia_presel=<?=date("d",$fecha_seleccionada);?>;
        ano_presel=<?=date("Y",$fecha_seleccionada);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
        window.open('/herramientas/calendario_mes1.php?dia='+fecha_preseleccionada+'&pagina_destino=/gestion/centros/listado_ingresos_uniforme.php','calendario','toolbar=no,resizable=no,width=300,height=216');
        }
</script>
<?php 
} else {
?>
<br><br>Error: <a href="/index.php">Volver a la página de inicio</a></br></br>
<?php 
}
 require("../../configuracion/parte_inferior.php"); ?>
