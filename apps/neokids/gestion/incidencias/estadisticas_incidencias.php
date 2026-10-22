<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$ejercicio=$_GET["e"];

if (strlen($ejercicio)==0) {
        $ejercicio=date("Y");
        $mes_actual=date("n");
        if ($mes_actual<9) {
                $ejercicio--;
                }
        }
$texto_titulo="Estadísticas de incidencias - Curso $ejercicio - ".($ejercicio+1);
?>
<div align="center"><center>
<br><input type="button" onclick="window.location.href='menu_incidencias.php'" value="Salir" style="font-size: 10px"><br><br>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$texto_titulo;?><br><br></b></small></td>
  </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><a href="estadisticas_incidencias.php?e=<?=$ejercicio-1?>"><b><< Curso Anterior</b></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="estadisticas_incidencias.php?e=<?=$ejercicio+1?>"><b>Curso Siguiente >></b></a>
    </small></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <?php 

        $mes_actual=8;
        $ejercicio_actual=$ejercicio;
        for ($meses=1;$meses<=12;$meses++) {

        $mes_actual=$mes_actual+1;
        if ($mes_actual==13) {
                $mes_actual=1;
                $ejercicio_actual=$ejercicio_actual+1;
                }

        $busca_total_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND month(incidencias.fecha_incidencia)=$mes_actual AND year(incidencias.fecha_incidencia)=$ejercicio_actual");
        
        $busca_resueltas_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=1 AND month(incidencias.fecha_incidencia)=$mes_actual AND year(incidencias.fecha_incidencia)=$ejercicio_actual");
        
        $busca_en_proceso_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=0 AND incidencias.seguimiento_por<>0 AND month(incidencias.fecha_incidencia)=$mes_actual AND year(incidencias.fecha_incidencia)=$ejercicio_actual");
        
        $total_mes=db_num_rows($busca_total_mes);
        $total_mes_abs=$total_mes;
        $en_proceso_mes=db_num_rows($busca_en_proceso_mes);
        $en_proceso_mes_abs=$en_proceso_mes;
        $resueltas_mes=db_num_rows($busca_resueltas_mes);
        $resueltas_mes_abs=$resueltas_mes;
        $total_no_atendidas=$total_mes-$resueltas_mes-$en_proceso_mes;
        $total_no_atendidas_abs=$total_no_atendidas;
        if ($total_mes>0) {
                $por_en_proceso_mes=number_format(($en_proceso_mes/$total_mes)*100,2,".",",");
                $por_resueltas_mes=number_format(($resueltas_mes/$total_mes)*100,2,".",",");
                $por_no_atendidas=number_format(($total_no_atendidas/$total_mes)*100,2,".",",");
                } else {
                $por_en_proceso_mes="0,00";
                $por_resueltas_mes="0,00";
                $por_no_atendidas="0,00";
                }
        
        ?>
        <tr>
        <td align="left" colspan="4"><br><font size=2>Incidencias de <b><?=$nombre_mes[$mes_actual];?> de <?=$ejercicio_actual;?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nº de Incidencias</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>No atendidas</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>En proceso</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Resueltas</b></font></td>
        </tr>
        <tr bgcolor="#ffffff">
        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$total_mes;?></font></td>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total_no_atendidas;?> (<?=$por_no_atendidas?> %)</font></td>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$en_proceso_mes;?> (<?=$por_en_proceso_mes?> %)</font></td>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$resueltas_mes;?> (<?=$por_resueltas_mes?> %)</font></td>
        </tr>
        <?php 
        
        // Busca por tipo y ddesglose
        
        $busca_tipos=db_query("SELECT * FROM incidencias_tipos ORDER BY id_tipo");

        while($tipos=db_fetch_array($busca_tipos)){
        
        $busca_total_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual");

        $busca_resueltas_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=1 AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual");

        $busca_en_proceso_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=0 AND incidencias.seguimiento_por<>0 AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual");

        $total_mes=db_num_rows($busca_total_mes);
        $en_proceso_mes=db_num_rows($busca_en_proceso_mes);
        $resueltas_mes=db_num_rows($busca_resueltas_mes);
        $total_no_atendidas=$total_mes-$resueltas_mes-$en_proceso_mes;

        if ($total_mes>0) {
                $por_en_proceso_mes=number_format(($en_proceso_mes/$total_mes)*100,2,".",",");
                $por_resueltas_mes=number_format(($resueltas_mes/$total_mes)*100,2,".",",");
                $por_no_atendidas=number_format(($total_no_atendidas/$total_mes)*100,2,".",",");
                
                if ($total_mes_abs>0) {
                        $por_en_proceso_mes_abs=number_format(($en_proceso_mes/$total_mes_abs)*100,2,".",",");
                        $por_resueltas_mes_abs=number_format(($resueltas_mes/$total_mes_abs)*100,2,".",",");
                        $por_no_atendidas_abs=number_format(($total_no_atendidas/$total_mes_abs)*100,2,".",",");
                        } else {
                        $por_en_proceso_mes_abs="0,00";
                        $por_resueltas_mes_abs="0,00";
                        $por_no_atendidas_abs="0,00";
                        }
                
                } else {
                $por_en_proceso_mes="0,00";
                $por_resueltas_mes="0,00";
                $por_no_atendidas="0,00";
                $por_en_proceso_mes_abs="0,00";
                $por_resueltas_mes_abs="0,00";
                $por_no_atendidas_abs="0,00";
                }
        
        ?>
        <tr bgcolor="#ffffff">
        <td align="left"><font size=1 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<b><?=$tipos["nombre_tipo"]?></b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$total_mes;?></font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_no_atendidas;?> (<?=$por_no_atendidas?> % - <?=$por_no_atendidas_abs?> %)</font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$en_proceso_mes;?> (<?=$por_en_proceso_mes?> % - <?=$por_en_proceso_mes_abs?> %)</font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$resueltas_mes;?> (<?=$por_resueltas_mes?> % - <?=$por_resueltas_mes_abs?> %)</font></td>
        </tr>
        <?php 
        
                // Por categoria
                
                $busca_categorias=db_query("SELECT nombre_incidencia FROM incidencias WHERE incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual AND month(incidencias.fecha_incidencia)=$mes_actual GROUP BY nombre_incidencia ORDER BY nombre_incidencia");
                while($categorias=db_fetch_array($busca_categorias)){
                        $busca_total_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual AND incidencias.nombre_incidencia='".$categorias["nombre_incidencia"]."'");

                        $busca_resueltas_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=1 AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual AND incidencias.nombre_incidencia='".$categorias["nombre_incidencia"]."'");

                        $busca_en_proceso_mes=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=0 AND incidencias.seguimiento_por<>0 AND month(incidencias.fecha_incidencia)=$mes_actual AND incidencias.tipo=".$tipos["id_tipo"]." AND year(incidencias.fecha_incidencia)=$ejercicio_actual AND incidencias.nombre_incidencia='".$categorias["nombre_incidencia"]."'");

                        $total_mes=db_num_rows($busca_total_mes);
                        $en_proceso_mes=db_num_rows($busca_en_proceso_mes);
                        $resueltas_mes=db_num_rows($busca_resueltas_mes);
                        $total_no_atendidas=$total_mes-$resueltas_mes-$en_proceso_mes;

                        if ($total_mes>0) {
                                $por_en_proceso_mes=number_format(($en_proceso_mes/$total_mes)*100,2,".",",");
                                $por_resueltas_mes=number_format(($resueltas_mes/$total_mes)*100,2,".",",");
                                $por_no_atendidas=number_format(($total_no_atendidas/$total_mes)*100,2,".",",");

                                if ($total_mes_abs>0) {
                                        $por_en_proceso_mes_abs=number_format(($en_proceso_mes/$total_mes_abs)*100,2,".",",");
                                        $por_resueltas_mes_abs=number_format(($resueltas_mes/$total_mes_abs)*100,2,".",",");
                                        $por_no_atendidas_abs=number_format(($total_no_atendidas/$total_mes_abs)*100,2,".",",");
                                        } else {
                                        $por_en_proceso_mes_abs="0,00";
                                        $por_resueltas_mes_abs="0,00";
                                        $por_no_atendidas_abs="0,00";
                                        }

                                } else {
                                $por_en_proceso_mes="0,00";
                                $por_resueltas_mes="0,00";
                                $por_no_atendidas="0,00";
                                $por_en_proceso_mes_abs="0,00";
                                $por_resueltas_mes_abs="0,00";
                                $por_no_atendidas_abs="0,00";
                                }

                        if (strlen($categorias["nombre_incidencia"])!=0) {
                                $nombre_categoria=urldecode($categorias["nombre_incidencia"]);
                                } else {
                                $nombre_categoria="Indefinida";
                                }
                        ?>
                        <tr bgcolor="#ffffff">
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nombre_categoria;?></font></td>
                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$total_mes;?></font></td>
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_no_atendidas;?> (<?=$por_no_atendidas?> % - <?=$por_no_atendidas_abs?> %)</font></td>
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$en_proceso_mes;?> (<?=$por_en_proceso_mes?> % - <?=$por_en_proceso_mes_abs?> %)</font></td>
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$resueltas_mes;?> (<?=$por_resueltas_mes?> % - <?=$por_resueltas_mes_abs?> %)</font></td>
                        </tr>
                        <?php 
                        }
                }
        
        
        }
        ?>
        </table>
        </td>
</tr>
</table>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

