<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

// Busca datos del aula
$nombre_aula = "de todas las aulas del centro";

$mostrarTodos = false;

if ($id_aula!=0) {
	
	$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
	$datos_aula=db_fetch_array($consulta_datos_aula);

	$nombre_aula = "del Aula " . $datos_aula["nombre"];
	
	$mostrarTodos = true;
}

$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$centro_actual) or die(db_error());
$datos_centro=db_fetch_array($busca_datos_centro);

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,scrollbars=yes,resizable=no,width=600,height=500');
        }
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,scrollbars=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
function ver_detalles(fila) {
        window.open('detalles_horas_extra.php?id_aula=<?=$id_aula;?>&id_alumno='+fila+'&mes=<?=$mes_sel;?>&ano=<?=$ano_sel;?>','servicios','toolbar=yes,resizable=yes,scrollbars=yes,width=800,height="+screen.height+"');
        }
//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Resumen de horas extra consumidas por los de alumnos <i><?=$nombre_aula;?></i> durante el mes de <?=$nombre_mes[$mes_sel];?> de <?=$ano_sel;?><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 
// $busca_alumnos=mysql_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND usuarios.tipo_inscripcion=1 AND usuarios.Apellido1 like '".$inicial."%'");

if ($id_aula!=0) {

	$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND aula=$id_aula ORDER BY Apellido1, Apellido2, Nombre");
	
} else {
	
	$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND fecha_matricula<='".date("Y-m-t",strtotime($ano_sel."-".$mes_sel."-01"))."' AND fecha_baja>='".$ano_sel."-".$mes_sel."-01' ORDER BY Apellido1, Apellido2, Nombre");
	
}

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">Para editar un alumno, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Horas extra Consumidas</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Horas a facturar</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
// Comprueba si es horario de media jornada

if ($listado_alumnos["tipo_mensualidad"]!=1) {
$hora_entrada_centro_man=$datos_centro["hora_entrada1"];
$hora_salida_centro_man=$datos_centro["hora_salida2"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada1"];
$hora_salida_centro_tar=$datos_centro["hora_salida2"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=round(((strtotime($hora_entrada_centro_tar)-strtotime($hora_salida_centro_man))/3600),2);
if ($horas_comida<0) {
        $horas_comida=0;
        }
} else {
$hora_entrada_centro_man=$datos_centro["hora_entrada1_mj"];
$hora_salida_centro_man=$datos_centro["hora_salida1_mj"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada2_mj"];
$hora_salida_centro_tar=$datos_centro["hora_salida2_mj"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=0;
}

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
        
		$id_aula= $listado_alumnos["aula"];
        // Consulta servicios asistencia durante el mes seleccionado
        $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND month(fecha)=$mes_sel AND year(fecha)=$ano_sel;");
        $total_horas_extra=0;
        $total_horas_extra_fact=0;
        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                $horas_extra_hoy=0;
                if ($datos_asistencia["turno"]==0) {
                        $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                        $hora_salida_centro=strtotime($hora_salida_centro_man);
                        $hora_salida_alt=$hora_salida_centro_man;
                        } else {
                        $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                        $hora_salida_centro=strtotime($hora_salida_centro_tar);
                        $hora_salida_alt=$hora_salida_centro_tar;
                        }

                $hora_entrada11=$datos_asistencia["hora_entrada"];
                if ($hora_entrada11=="") {
                        $hora_entrada11=intval($hora_entrada_centro);
                        } else {
                        $hora_entrada11=intval(strtotime($datos_asistencia["hora_entrada"]));
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-$hora_entrada11;

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }
                        
                $hora_salida_analizada=$datos_asistencia["hora_salida"];
                if (($hora_salida_analizada=='00:00:00') or (strlen($hora_salida_analizada)==0)) {
                        if (($datos_asistencia["hora_entrada_c"]!="") and ($datos_asistencia["turno"]==0)) {
                                $hora_salida_analizada=$datos_asistencia["hora_entrada_c"];
                                } else {
                                $hora_salida_analizada=$hora_salida_alt;
                                }
                        }

                $diferencia_salida=intval(strtotime($hora_salida_analizada))-intval($hora_salida_centro);
                
                /*
                $hora_salida11=$datos_asistencia["hora_salida"];
                if ($hora_salida11=="") {
                        $hora_salida11=intval($hora_salida_centro);
                        } else {
                        $hora_salida11=intval(strtotime($datos_asistencia["hora_salida"]));
                        }

                $diferencia_salida=$hora_salida11-intval($hora_salida_centro);
                */
                
                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }

                $diferencia_entrada1=$diferencia_entrada;
                $$diferencia_salida1=$diferencia_salida;
                
                // Si se queda a comer no se cobran horas
                        if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$datos_asistencia["id_alumno"]." AND id_servicio=".$datos_asistencia["id_servicio"]." AND fecha='".date("Y-m-d",strtotime($datos_asistencia["fecha"]))."' AND turno=0;");

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                // Si el niño ha venido a comer y ha entrado por la mañana, no le cobro horas extra
                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                }
                                        }
                                }
                                
                if ($diferencia_entrada1<$diferencia_entrada) {
                        $diferencia_entrada=$diferencia_entrada1;
                        }
                if ($$diferencia_salida1<$diferencia_salida) {
                        $diferencia_salida=$$diferencia_salida1;
                        }
                        
                // Busca bonos horas extra contratados durante todo el año
                $busca_bonos1=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=".$listado_alumnos["id_alumno"]." AND tarifa.TIPO_SERVICIO=1 AND periodicidad=1 AND (fecha_inicio<='".$ano_sel."-".$mes_sel."-1') AND (fecha_fin>='".$ano_sel."-".$mes_sel."-1')") or die(db_error());
                $numero_bonos1=0;
                while($bonos1=db_fetch_array($busca_bonos1)) {
                        $numero_bonos1=$numero_bonos1+$bonos1["cantidad"];
                        }
                // Busca bonos horas extra contratados para este mes
                $busca_bonos2=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=".$listado_alumnos["id_alumno"]." AND tarifa.TIPO_SERVICIO=1 AND periodicidad=2 AND  (month(fecha_inicio)=$mes_sel) AND (year(fecha_inicio)=$ano_sel)") or die(db_error());
                $numero_bonos2=0;
                while($bonos2=db_fetch_array($busca_bonos2)) {
                        $numero_bonos2=$numero_bonos2+$bonos2["cantidad"];
                        }

                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);
                $horas_extra_hoy_fact=$horas_extra_hoy-$numero_bonos1-$numero_bonos2;

                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                
                if ($datos_asistencia["no_facturar"]==1) {
                        $horas_extra_hoy_fact=0;
                        }
                
                if ($horas_extra_hoy_fact>0) {
                        $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                        }
                }
				
				$total_horas_extra_numero = $total_horas_extra;
				
                $horas_extra1=round($total_horas_extra,0);
                if ($horas_extra1>$total_horas_extra) {
                        $horas_extra1=$horas_extra1-1;
                        }
                $minutos_extra1="00".(($total_horas_extra-$horas_extra1)*60);
                $minutos_extra1=substr($minutos_extra1,strlen($minutos_extra1)-2,2);
                $horas_extra1="00".$horas_extra1;
                $horas_extra1=substr($horas_extra1,strlen($horas_extra1)-2,2);
                $total_horas_extra=$horas_extra1.":".$minutos_extra1;

        // Busca horas extra individuales
        $busca_bonos3=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=".$listado_alumnos["id_alumno"]." AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND  (month(fecha_inicio)=$mes_sel) AND (year(fecha_inicio)=$ano_sel)") or die(db_error());
        $numero_bonos3=0;
        while($bonos3=db_fetch_array($busca_bonos3)) {
                        $numero_bonos3=$numero_bonos3+$bonos3["cantidad"];
                        }
        $total_horas_extra_fact=$total_horas_extra_fact-$numero_bonos3;
        if ($total_horas_extra_fact<0) {
                $total_horas_extra_fact=0;
                }

        $horas_extra2=round($total_horas_extra_fact,0);
        if ($horas_extra2>$total_horas_extra_fact) {
                        $horas_extra2=$horas_extra2-1;
                        }
        $minutos_extra2="00".(($total_horas_extra_fact-$horas_extra2)*60);
        $minutos_extra2=substr($minutos_extra2,strlen($minutos_extra2)-2,2);
        $horas_extra2="00".$horas_extra2;
        $horas_extra2=substr($horas_extra2,strlen($horas_extra2)-2,2);
        $total_horas_extra_fact=$horas_extra2.":".$minutos_extra2;

		if (($total_horas_extra_numero>0) or ($mostrarTodos==true)) {
		
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:ver_detalles(<?=$listado_alumnos["id_alumno"];?>)"><?=$total_horas_extra;?></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:ver_detalles(<?=$listado_alumnos["id_alumno"];?>)"><?=$total_horas_extra_fact;?></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a> <a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>" title="Editar datos del alumno"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
			}
        }
        ?>
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
<input type="button" onclick="window.location.href='seleccion_aula_horas.php'" value="Volver" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

