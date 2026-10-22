<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

// Busca datos del aula
$consulta_datos_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_educadora);
?>
<div align="center"><center>
<form name="lita_horas" id="lista_horas">
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
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Turno</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Hora entrada</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Hora salida</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Horas trabajadas <font color="#ff0000">*</font></b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
            $total_horas_mes=0;
        $total_horas=0;
        $total_minutos=0;
        while($datos_asistencia=db_fetch_array($busca_asistencia)){

                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }
                
                $horas_diferencia=($diferencia_entrada/60)/60;

                // Restamos hora de comer, si han hecho más de 7 horas
                if ($horas_diferencia>=7) {
                        $horas_diferencia=$horas_diferencia-1;
                        }
                
                $total_horas_mes=$total_horas_mes+$horas_diferencia;
                
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
                        
                        
        $hora_entrada=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));    
        $hora_salida=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));
        
        if ($hora_salida=="01:00:00") {
                
                $hora_salida="";
                        }
                                         
        $hora_entrada="<input type='text' value='".$hora_entrada."' size=8 name='hora_entrada-".$datos_asistencia["asistencia_docentes"]."' id='hora_entrada-".$datos_asistencia["id_asistencia"]."'>";
        $hora_salida="<input type='text' value='".$hora_salida."' size=8 name='hora_salida-".$datos_asistencia["asistencia_docentes"]."' id='hora_salida-".$datos_asistencia["id_asistencia"]."'>";
                  
        ?>
        <tr>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_asistencia["fecha"]));?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_turno;?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$hora_entrada;?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$hora_salida;?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($horas_diferencia,2,",",".");?> Horas.</font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
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
        <td align="center" colspan=3><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">Total horas mes:</font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_horas_mes,2,",",".");?> Horas.</font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
        </tr>
        </table>
        <font color="#ff0000" size=1>* Cada día hay 1 hora para comer que no es horario trabajado.</font>
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
</form>
<br>
<input type="button" value="Recalcular" onclick="history.go(0);"> <input type="button" onclick="window.location.href='resumen_horas_exportar.php?id_aula=<?=$id_aula?>&ano=<?=$ano_sel?>&mes=<?=$mes_sel?>'" value="Crear Registro Diario de Jornada"> <input type="button" onclick="window.location.href='ver_calendario.php?id_docente=<?=$id_aula?>&dia=<?=$ano_sel?>/<?=$mes_sel?>/1'" value="Ver como calendario"> <input type="button" onclick="window.location.href='seleccion_fecha_horas.php'" value="Volver">
<br>
</center></div>
</div>
<script>
        
    function validateHhMm(inputField) {
        var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField);
        return isValid;
    }        
        
        $( document ).ready(function() {
        
                        $("#lista_horas input[type=text]").on("change",function(e) {                  
                                
                                if ((validateHhMm($(this).val()))||($(this).val()=='')) {
                                                        
                                        var href = "cambia_dato_horas.php?nombre_campo=" + $(this).attr("id") + "&valor_campo=" + $(this).val();
                
                                        var respuesta = $.ajax ({url: href}); 
                                        respuesta.always(function() {
                                                
                                                var textoRespuesta = respuesta.responseText;
                                                
                                                if( textoRespuesta.substr(0, 2) != "ok" ) {                                                        
                                                        alert(textoRespuesta);                                                
                                                }
                                                
                                        }); 
                                
                                } else {

                                        alert("No es un formato de hora correcto.");
                                        
                                }
                                                        
        
                        }); 
        
        
        });
        
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

