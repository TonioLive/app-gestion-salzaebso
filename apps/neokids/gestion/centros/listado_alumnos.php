<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);
$nombre_aula=$datos_aula["nombre_aula"];
$ciclo_aula=$datos_aula["ciclo"];

$curso_del_aula=$datos_aula["id_curso"];

$fecha_limite_presentacion=$datos_aula["fecha_limite_presentacion"];


$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

?>
<SCRIPT LANGUAGE=javascript>
<!--

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
function presupuestos() {
        window.open('/pdf/presupuesto_inscritos_aula.php?id_aula=<?=$id_aula?>','pdf','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }


function ver_carpeta(id_usuario) {
        
        window.open('ver_carpeta_alumno.php?id_alumno='+id_usuario,'carpetas','width=550,height=450toolbar=yes,scrollbars=yes,resizables=yes');
        
        }

//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de alumnos del Aula <?=$nombre_aula;?> <i>Curso <?=$datos_aula["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 
$orden_en_aula=1;
$busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND alumnos.aula=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($centro_actual, $id_aula));

if (db_num_rows($busca_alumnos)!=0) {

$busca_orden_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula=? AND orden_en_aula<>0', 's', array($id_aula));
$con_num_orden=db_num_rows($busca_orden_en_aula);
$busca_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula=?', 's', array($id_aula));
$total_alumnos_aula=db_num_rows($busca_en_aula);


?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=7><font size=1 face="<?= $fuente_general; ?>">Para editar un alumno, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)<br>
        * Días asistidos al aula y al comedor durante el més actual (<?=$nombre_mes[date("n")];?> de <?=date("Y");?>)</font></td>
        <td colspan=8 align=center valign=middle><?php 
        if (($curso_del_aula==10) and ($ciclo_aula==1)) {
        ?>
        <font size=2 face="<?= $fuente_general; ?>"><a href="/pdf/anexoIVa09-10.php?id_aula=<?=$id_aula?>" target="_new">Imprimir modelo de factura informativa para el curso 2009-2010.</a></font>
        <?php 
        } else {
        ?><font size=1 face="<?= $fuente_general; ?>"><a href="/pdf/presupuesto_inscritos_aula.php?id_aula=<?=$id_aula?>&m=1" target="_new">Imprimir presupuesto de educación y subvención con modelo de la consellería para todos los alumnos.</a><br>
        <a href="/pdf/presupuesto_paso1.php?id_aula=<?=$id_aula?>&m=2" target="_new">Imprimir presupuesto de educación y subvención con modelo de la consellería para todos los alumnos (DETALLADO).</a></font><?php 
        }
        ?></td>
        </tr>
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
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Grupos</b></font></td>
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
        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=1', 's', array($id_inscripcion)) or die(db_error());

         $busca_inscripcion_facturada=db_query("SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.concepto='Inscripción de curso 2012-2013'");

        if ((db_num_rows($busca_inscripcion)==0) and (db_num_rows($busca_inscripcion_facturada)==0)) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                }
        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                }
        $busca_cuotas=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=2', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                }
         if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }
        if ($no_inscripcion==true) {
                $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=1', 's', array($id_aula)) or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion["id_servicio_contratado"], $id_inscripcion));
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                }
                        }
                }
                
        if ($no_cuotas==true) {
                $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=2', 's', array($id_aula)) or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion["id_servicio_contratado"], $id_inscripcion));
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
        $filtros_asistencia="year(servicios_consumidos.fecha)=".date("Y");
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".date("n");
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        
        $busca_dias_comedor=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND (tipo_menu=1 OR tipo_menu=3) AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_comedor=db_num_rows($busca_dias_comedor);
        
        $lista_grupos_alumno="";

        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_usuario=?', 's', array($listado_alumnos["id_usuario"])) or die(db_error());

        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
                }

        ?>
        <tr onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'#ffffff')">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$orden_en_aula;?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><a target="edicion_alumnos" href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <?php 
        if ((($no_inscripcion==false) and ($no_cuotas==false)) or ($curso_iniciado==false)) {
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php  if ($listado_alumnos["sexo"]=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nacionalidad"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono1"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$tipo_de_comedor[$listado_alumnos["comedor"]]?><?php  if ($listado_alumnos["comedor"]==1) { print " (".$tipo_de_menu[$listado_alumnos["tipo_menu_comedor"]].")"; } ?></font></td>
        <td align=center><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href="#" onclick="window.open('/herramientas/calendario_asistencia_mes.php?dia=<?=time();?>&id_alumno=<?=$listado_alumnos["id_usuario"];?>','calendario','toolbar=no,resizable=no,width=300,height=316');"><?=$total_dias_asistidos?></a></font></td>
        <td align=center><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href="#" onclick="window.open('/herramientas/calendario_asistencia_mes.php?dia=<?=time();?>&id_alumno=<?=$listado_alumnos["id_usuario"];?>','calendario','toolbar=no,resizable=no,width=300,height=316');"><?=$total_dias_comedor?></a></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$lista_grupos_alumno;?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$obs;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));?> <?=date("d/m/Y",strtotime($listado_alumnos["fecha_inscripcion"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));?></font></td>
        <?php 
        } else {
        $mensaje11="";
        if ($no_inscripcion==true) {
                $mensaje11=$mensaje11."Inscripción en el curso";
                }
        if ($no_cuotas==true) {
                if ($mensaje11!="") {
                        $mensaje11=$mensaje11." y ";
                        }
                $mensaje11=$mensaje11."Servicio de educación";
                }
        $mensaje11="Falta asigar al niño los servicios de: ".$mensaje11.".";
        ?>
        <td colspan=11 align="center"><font color="red" size=1><?=$mensaje11?></font></td>
        <?php 
        }
        
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
        <?php 
                } else {
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><select name="cambio_aula<?=$listado_alumnos["id_alumno"];?>" onchange="cambio_aula(<?=$listado_alumnos["id_alumno"];?>);" style="font-size: 10px">
        <option value=""></option>
        <?php 
        if ($nivel_acceso==4) {
        $listado_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? AND id_aula<>? AND id_curso=?', 'sss', array($centro_actual, $id_aula, $datos_aula["id_curso"]));
        } else {
        $listado_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? AND tipo=0 AND id_aula<>? AND id_curso=?', 'sss', array($centro_actual, $id_aula, $datos_aula["id_curso"]));
        }

        while($datos_listado_aulas=db_fetch_array($listado_aulas)){
        $capacidad_alumnos_list=db_query_params('SELECT * FROM alumnos WHERE aula=?', 's', array($datos_listado_aulas["id_aula"]));
        ?>
        <option value="<?=$datos_listado_aulas["id_aula"]?>"><?=$datos_listado_aulas["nombre"]?></option>
        <?php 
        }
        ?>
        </select></font></td>
        <?php 
                }
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a> <a href="/gestion/calendario/calendario_asistencia.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="calendario_asistencia" title="Editar calendario docente del alumno"><img src="calendario.gif" border=0></a>  <a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>" title="Editar datos del alumno"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="/pdf/portada_informe.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="_new" title="Imprimir portada informe Psicopedagógico"><img src="fotos.gif" border=0></a> <a href="/pdf/presupuesto_inscrito.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>&m=1" target="_new" title="Imprimir presupuesto educación"><img src="presup.gif" border=0></a> <a href="/pdf/presupuesto_inscrito.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>&m=2" target="_new" title="Imprimir presupuesto educación detallado"><img src="presup.gif" border=0> Detallado</a> <a href="/menu/ver_menu_mes.php?id_hijo=<?=$listado_alumnos["id_alumno"];?>" target="_new" title="Imprimir menú"><img src="menu.gif" border=0></a> <a title="Examinar carpetas de informes" href="javascript:ver_carpeta(<?=$listado_alumnos["id_usuario"]?>);"><img src="carpetas.gif" border=0></a></font></td>
        </tr>
        <?php 
        $orden_en_aula++;
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
<input type="button" onclick="window.location.href='gestion_aulas.php?curso_seleccionado=<?=$curso_del_aula;?>'" value="Volver al listado de aulas" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
