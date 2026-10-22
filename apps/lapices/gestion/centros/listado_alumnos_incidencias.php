<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
$fecha=$_GET["fecha"];

if (strlen($fecha)==0) {

        $fecha=time();

}


// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if ($curso_seleccionado) {

	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");

	if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso_actual=$datos_curso_actual["id_curso"];
			$fecha=strtotime($datos_curso_actual["fecha_inicio"]);
		} 		
		
	}	

$fecha_inicio_periodo=date("Y-m-01",$fecha);
$fecha_fin_periodo=date("Y-m-t",$fecha);

$mes_mostrado=date("n",$fecha);
$anio_mostrado=date("Y",$fecha);

$mes_anterior=$mes_mostrado-1;
$ano_anterior=$anio_mostrado;
$mes_siguiente=$mes_mostrado+1;
$ano_siguiente=$anio_mostrado;
if ($mes_mostrado==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes_mostrado==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }


// Curso
$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".$fecha_inicio_periodo."' AND fecha_fin>='".$fecha_fin_periodo."'");

if (db_num_rows($busca_curso_actual)!=0) {
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d",$fecha_inicio_periodo)."' ORDER BY fecha_inicio");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        }

if ($id_curso_actual!=0) {

$curso_del_aula=$datos_curso_actual["id_curso"];

$fecha_limite_presentacion=$datos_curso_actual["fecha_limite_presentacion"];

$fecha_de_inicio_curso=$datos_curso_actual["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval($fecha_inicio_periodo)) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }
        
$contador_alumnos=0;
?>
<SCRIPT LANGUAGE=javascript>
<!--

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
        
//-->
</SCRIPT>
<div align="center"><center>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=2><small><b>Listado de alumnos del <i>Curso <?=$datos_curso_actual["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i><br />Durante el mes de <?=$nombre_mes[$mes_mostrado]?> de <?=$anio_mostrado?></b></small></td>
  </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
   <td><a href='<?=$_SERVER["PHP_SELF"];?>?fecha=<?=strtotime($ano_anterior."-".$mes_anterior."-1");?>'><< Mes anterior</a></td>
   <td align="right"><a href="<?=$_SERVER["PHP_SELF"];?>?fecha=<?=strtotime($ano_siguiente."-".$mes_siguiente."-1");?>">Mes siguiente >></a></td>
  </tr>
<?php 
$orden_en_aula=1;
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND alumnos.id_curso=$id_curso_actual ORDER BY alumnos.aula,Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {

?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nº</b></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Telefono</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Dias asistidos*</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Dias comedor*</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo educacion</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Obser.</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Inicio</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fin</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        
        $suma_numeros_orden=0;
        $contador_nuevos=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        // Comprueba si es antiguo alumno
        $busca_inscripcion_ant=db_query("SELECT * FROM alumnos WHERE id_usuario=".$listado_alumnos["id_usuario"]." AND id_curso<$curso_del_aula") or die(db_error());

        if (db_num_rows($busca_inscripcion_ant)!=0) {
                $obs="<font color='blue'>Antiguo</font>";
                } else {
                $obs="<font color='red'>Nuevo</font>";
                $contador_nuevos++;
                }
        

        // Comprueba si se le han incluido los servicios
        $id_inscripcion=$listado_alumnos["id_alumno"];
        $busca_inscripcion=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=1") or die(db_error());

         $busca_inscripcion_facturada=db_query("SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.concepto='Inscripción de curso 2012-2013'");

        if ((db_num_rows($busca_inscripcion)==0) and (db_num_rows($busca_inscripcion_facturada)==0)) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                }
        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                }
        $busca_cuotas=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=2 AND fecha_fin>='$fecha_fin_periodo'") or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                }
         if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }
        if ($no_inscripcion==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=1") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                }
                        }
                }
                
        if ($no_cuotas==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=2 AND fecha_fin>='$fecha_fin_periodo'") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_cuotas=false;
                                }
                        }
                }

        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($listado_alumnos["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#45A9F6";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }
                
        if ($fecha_baja==$listado_alumnos["fecha_matricula"]) {
                $color_fuente_linea="#ffacac";
                }
                                // Tipos de comedor
                                $tipo_de_comedor[0]="Desconocido";
                                $tipo_de_comedor[1]="Si";
                                $tipo_de_comedor[2]="No";
                                $tipo_de_comedor[3]="Régimen";

                                // Tipos de menu
                                $tipo_de_menu[0]="Desconocido";
                                $tipo_de_menu[1]="Pequeños";
                                $tipo_de_menu[2]="Mayores";
                                $tipo_de_menu[3]="Colegial";
                                $tipo_de_menu[4]="Toma";
                                $tipo_de_menu[5]="Bebe";
                                
        // Dias asistidos
        $filtros_asistencia="year(servicios_consumidos.fecha)=".date("Y",$fecha);
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".date("n",$fecha);
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        
        $busca_dias_comedor=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND (tipo_menu=1 OR tipo_menu=3) AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_comedor=db_num_rows($busca_dias_comedor);
        
        // Busca si este mes ha sido facturado
        $fecha_servicio_buscar=$nombre_mes[$mes_mostrado]." de ".$anio_mostrado;
        $filtro_servicio_buscar="((detalles_factura.concepto like '%Mensualidad educación%') OR (detalles_factura.concepto like '%Educacion Media Jornada%') OR (detalles_factura.concepto like '%Educación Media Jornada%'))";
        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=".$listado_alumnos["id_alumno"]." AND $filtro_servicio_buscar AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
        $consulta_factura = db_query($consulta2) or die (db_error());

        $no_facturado = db_num_rows ($consulta_factura);

        if (($total_dias_asistidos!=0) and ($no_facturado==0)) {
        
        $contador_alumnos++;
        ?>
        <tr onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'#ffffff')">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$contador_alumnos;?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><a target="edicion_alumnos" href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php  if ($listado_alumnos["sexo"]=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nacionalidad"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono1"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$tipo_de_comedor[$listado_alumnos["comedor"]]?><?php  if ($listado_alumnos["comedor"]==1) { print " (".$tipo_de_menu[$listado_alumnos["tipo_menu_comedor"]].")"; } ?></font></td>
        <td align=center><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href="#" onclick="window.open('/herramientas/calendario_asistencia_mes.php?dia=<?=time();?>&id_alumno=<?=$listado_alumnos["id_usuario"];?>','calendario','toolbar=no,resizable=no,width=300,height=316');"><?=$total_dias_asistidos?></a></font></td>
        <td align=center><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href="#" onclick="window.open('/herramientas/calendario_asistencia_mes.php?dia=<?=time();?>&id_alumno=<?=$listado_alumnos["id_usuario"];?>','calendario','toolbar=no,resizable=no,width=300,height=316');"><?=$total_dias_comedor?></a></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php if ($listado_alumnos["tipo_mensualidad"]==1) { print "Media jornada"; } else { print "Jornada Completa"; } ?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$obs;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));?> <?=date("d/m/Y",strtotime($listado_alumnos["fecha_inscripcion"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));?></font></td>
        <?php 
        
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
        <?php 
                } else {
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>">
        <?php 

        $datos_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$listado_alumnos["aula"]);

        $datos_listado_aulas=db_fetch_array($datos_aulas);
        echo($datos_listado_aulas["nombre"]);

        ?>
        </font></td>
        <?php 
                }
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a> <a href="/gestion/calendario/calendario_asistencia.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="calendario_asistencia" title="Editar calendario docente del alumno"><img src="calendario.gif" border=0></a> <a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>" title="Editar datos del alumno"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        $orden_en_aula++;
                }
        }

        ?>
        <tr bgcolor="#efefef">
        <td colspan=9><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Total alumnos nuevos: <?=$contador_nuevos?></b></font></td>
        <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        </form>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en este aula.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
</center></div>
</div>
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
