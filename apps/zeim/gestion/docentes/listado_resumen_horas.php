<?php  require("../../configuracion/parte_superior_privado_estrecho_bootstrap.php"); ?>
<?php 
// $id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

$fecha_control=$ano_sel."-".$mes_sel."-01";

?>
<div align="center"><center>        
<form name="lita_horas" id="lista_horas">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de empleados/as durante el mes de <?=$nombre_mes[$mes_sel];?> de <?=$ano_sel;?><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 

// Busca datos del aula
$consulta_empleados=db_query_params('SELECT * FROM usuarios WHERE (id_centro=? OR id_centro=0) ORDER BY Apellido1, Apellido2, Nombre;', 's', array($centro_actual)) or die(db_error());

if (db_num_rows($consulta_empleados)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table class="table table-bordered table-hover">
        <tr>
        <td align="center"><b>Empleada/o</b></td>
        <td align="center"><b>Horas realizadas</b></td>
        <td align="center"><b>&nbsp;</b></td>
        </tr>
        <?php         
            
                                           
        while($datos_empleado=db_fetch_array($consulta_empleados)){
              
		  $codigo=$datos_empleado["id_usuario"];
		    
		  // Comprueba si tiene contrato en vigor
		  $fecha_control=date("Y-m-d",strtotime("$ano_sel-$mes_sel-1"));
		  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());
		
		  if (db_num_rows($busca_contratos)!=0) {
		      		                                                
                $total_horas=0;
                        $total_minutos=0;                
                $total_horas_mes=0;
                
                $id_usuario=$datos_empleado["id_usuario"];
                
                        $busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND month(fecha)=? AND year(fecha)=? ORDER BY fecha,turno', 'sss', array($id_usuario, $mes_sel, $ano_sel));                        
                
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
                }                
                        
                if (($total_horas!=0) or ($total_minutos!=0)) {
              
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
                  
                }
                ?>
                <tr>
                <td align="center"><?=$datos_empleado["Apellido1"];?> <?=$datos_empleado["Apellido2"];?>, <?=$datos_empleado["Nombre"];?></td>
                <td align="right"><?=number_format($total_horas_mes,2,",",".");?> Horas.</td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="resumen_horas.php?id_aula=<?=$id_usuario?>&mes=<?=$mes_sel?>&ano=<?=$ano_sel?>"  class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ver detalles</a> <a href="resumen_horas_exportar.php?id_aula=<?=$id_usuario?>&mes=<?=$mes_sel?>&ano=<?=$ano_sel?>"  class="btn btn-sm btn-success" target="_blank"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Registro diario</a> <a href="/pdf/resumen_horas_exportar_pdf.php?id_aula=<?=$id_usuario?>&ano=<?=$ano_sel?>"  class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;</a></td>
                </tr>
                <?php 
                
            }
        }
       

        ?>
        </table>
        </td>
</tr>
<?php 
}
?>
</table>
</form>
<br>
<input type="button" class="btn btn-primary" onclick="window.location.href='seleccion_fecha_horas.php'" value="Volver">
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