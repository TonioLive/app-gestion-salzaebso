<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

// Busca datos del aula
$consulta_datos_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_educadora);
?>
<SCRIPT LANGUAGE=javascript>
<!--

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
function ver_detalles(fila) {
        window.open('detalles_horas_extra.php?id_aula=<?=$id_aula;?>&id_alumno='+fila+'&mes=<?=$mes_sel;?>&ano=<?=$ano_sel;?>','servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Resumen de horas realizadas por <i><?=$datos_aula["Apellido1"];?> <?=$datos_aula["Apellido2"];?>, <?=$datos_aula["Nombre"];?></i> durante el mes de <?=$nombre_mes[$mes_sel];?> de <?=$ano_sel;?><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 

$busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND month(fecha)=? AND year(fecha)=? ORDER BY fecha,turno', 'sss', array($id_aula, $mes_sel, $ano_sel));

if (db_num_rows($busca_asistencia)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Turno</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Hora entrada</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Hora salida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Horas realizadas</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        $total_horas=0;
        $total_minutos=0;
        while($datos_asistencia=db_fetch_array($busca_asistencia)){

                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }
                
                $horas_diferencia=($diferencia_entrada/60)/60;
                if ($horas_diferencia<round((($diferencia_entrada/60)/60),0)) {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0)-1;
                        } else {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0);
                        }

                $minutos_diferencia=$horas_diferencia-$horas_diferencia1;
                $minutos_diferencia=round(($minutos_diferencia*60),0);
                
                $total_horas=intval($total_horas)+intval($horas_diferencia1);
                $total_minutos=intval($total_minutos)+intval($minutos_diferencia);
                
                $horas_diferencia1="0".$horas_diferencia1;
                $minutos_diferencia="0".$minutos_diferencia;
                
                $horas_diferencia1=substr($horas_diferencia1,strlen($horas_diferencia1)-2,2);
                $minutos_diferencia=substr($minutos_diferencia,strlen($minutos_diferencia)-2,2);
                
                if ($diferencia_entrada!=0) {
                        $diferencia_horas=$horas_diferencia1.":".$minutos_diferencia;
                        } else {
                        $diferencia_horas="00:00";
                        }
                        
                if ($datos_asistencia["turno"]==0) {
                        $nombre_turno="Mañana";
                        } else {
                        $nombre_turno="Tarde";
                        }
        ?>
        <tr>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_asistencia["fecha"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_turno;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$diferencia_horas;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
        </tr>
        <?php 
        }
        
        // Totales

        $total_minutos=$total_minutos/60;
        if ($total_minutos<round($total_minutos,0)) {
                        $total_minutos1=round($total_minutos,0)-1;
                        } else {
                        $total_minutos1=round($total_minutos,0);
                        }

        $minutos_restantes=round((($total_minutos-$total_minutos1)*60),0);
        
        $total_horas=$total_horas+$total_minutos1;
        
        $total_horas="0".$total_horas;
        $minutos_restantes="0".$minutos_restantes;

        $total_horas=substr($total_horas,strlen($total_horas)-2,2);
        $minutos_restantes=substr($minutos_restantes,strlen($minutos_restantes)-2,2);

        ?>
        <tr>
        <td align="center" colspan=3><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>">Total horas mes:</font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$total_horas.":".$minutos_restantes;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
        </tr>
        </form>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>La educadora todavía no ha realizado ninguna hora durante le mes seleccionado.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.location.href='ver_calendario.php?id_docente=<?=$id_aula?>&dia=<?=$ano_sel?>/<?=$mes_sel?>/1'" value="Ver como calendario" style="font-size: 10px"> <input type="button" onclick="window.location.href='seleccion_fecha_horas.php'" value="Volver" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

