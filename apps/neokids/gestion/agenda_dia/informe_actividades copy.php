<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$fecha=$_GET["fecha"];

$cambio_dia=$_GET["c"];

if ($cambio_dia!=1) {
        $fecha_original=$fecha;
        } else {
        $fecha_original=$_GET["fecha_original"];
        }

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        }
        
$fecha_time_stamp=strtotime($fecha);
$fecha_original_time_stamp=strtotime($fecha_original);

$mes_original=date("n",$fecha_original_time_stamp);
$ano_original=date("Y",$fecha_original_time_stamp);

$inicio_mes_original=$ano_original."-".$mes_original."-1";

$inicio_mes_original_timestamp=strtotime($inicio_mes_original);

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("Y-m-d",strtotime(date("Y",$fecha_time_stamp)."-".$mes."-1"));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }
        
?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>
</head>

<body>
<table width="100%" border="0">
  <tr>
    <td align="left">
    <input type="button" value="< Mes anterior" onclick="window.location.href='informe_actividades.php?fecha_original=<?=$fecha_original?>&c=1&id_aula=<?=$id_aula?>&fecha=<?=$ano_anterior;?>-<?=$mes_anterior;?>-1'">
    </td>
    <td align="right">
    <input type="button" value="Mes Siguiente >" onclick="window.location.href='informe_actividades.php?fecha_original=<?=$fecha_original?>&c=1&id_aula=<?=$id_aula?>&fecha=<?=$ano_siguiente;?>-<?=$mes_siguiente;?>-1'">
    </td>
</tr>
</table>
<table width="100%" border="0">
  <tr>
    <td align="left">
    <b>Aula: <?=$nombre_aula?> - Resumen de objetivos trabajados durante el mes de <?=$nombre_mes[date("n",strtotime($fecha))]?> de <?=date("Y",strtotime($fecha))?></font></b><br><br>
                                <?php 
                                // Busca objetivos trabajados
                                
                                $busca_objetivos=db_query("SELECT * FROM agenda_programacion,agenda_programacion_objetivos WHERE agenda_programacion.objetivo=agenda_programacion_objetivos.id_objetivo AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY agenda_programacion_objetivos.id_objetivo ORDER BY agenda_programacion_objetivos.nombre_objetivo") or die(db_error());

                                if (db_num_rows($busca_objetivos)==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el mes seleccionado.</b></font>
                                <?php 
                                } else {
                                while($datos_objetivos=db_fetch_array($busca_objetivos)) {
                                
                                 $busca_actividades=db_query("SELECT id_actividad,count(id_prog) AS num_actividades FROM agenda_programacion WHERE objetivo=".$datos_objetivos["id_objetivo"]." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY id_actividad ORDER BY id_actividad ") or die(db_error());
                                
                                ?>
                                <table width=100% border=1 style="border-collapse: collapse" bordercolor="#000000">
                                <tr>
                                   <td width=45%><font size=2><b>&nbsp;</b></font></td>
                                   <td align="center" colspan="<?=db_num_rows($busca_actividades);?>"><font size=2><b>Actividad</b></font></td>
                                </tr>
                                <tr>
                                   <td><font size=2><b>Objetivo</b></font></td>
                                   <?php 
                                   // Busca actividades trabajadas

                                   while($datos_actividades=db_fetch_array($busca_actividades)) {
                                   if ($datos_actividades["id_actividad"]==0) {
                                           ?>
                                           <td align="center">Nuevas</td>
                                           <?php 
                                           } else {
                                           ?>
                                           <td align="center"><?=$datos_actividades["id_actividad"]?></td>
                                           <?php                                            
                                           }
                                   }

// Comprueba si se ha recuperado para este mes

$busca_objetivo_mes=db_query("SELECT * FROM agenda_programacion_objetivos_recuperados WHERE aula=$id_aula AND id_objetivo=".$datos_objetivos["id_objetivo"]." AND mes=".date("n",strtotime($fecha_original))." AND anyo=".date("Y",strtotime($fecha_original)));
$no_mostrar_link=false;
if (db_num_rows($busca_objetivo_mes)!=0) { $no_mostrar_link=true; }

                                   ?>
                                </tr>
                                <tr>
                                   <td><font size=2><b><?=$datos_objetivos["nombre_objetivo"]?></b><small> (Nº de veces que se trabaja)</small><?php  if (($inicio_mes_time_stamp<$inicio_mes_original_timestamp) and ($no_mostrar_link==false)) { ?><br><a href="recuperar_objetivo.php?id_objetivo=<?=$datos_objetivos["id_objetivo"];?>&fecha=<?=$fecha;?>&fecha_original=<?=$fecha_original;?>&id_aula=<?=$id_aula;?>">Volver a trabajar en <?=$nombre_mes[date("n",$inicio_mes_original_timestamp)]?> de <?=date("Y",$inicio_mes_original_timestamp)?></a><?php  } ?></font></td>
                                   <?php 
                                   // Busca actividades trabajadas

                                   $busca_actividades=db_query("SELECT id_actividad,count(id_prog) AS num_actividades FROM agenda_programacion WHERE objetivo=".$datos_objetivos["id_objetivo"]." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY id_actividad ORDER BY id_actividad ") or die(db_error());
                                   
                                   while($datos_actividades=db_fetch_array($busca_actividades)) {
                                   ?>
                                   <td align="center"><?=$datos_actividades["num_actividades"]?></td>
                                   <?php 
                                   }
                                   
                                   ?>
                                </tr>
                                </table><br>
                                <?php 
                                $busca_actividades=db_query("SELECT agenda_programacion.id_actividad,agenda_programacion_actividades.descripcion_actividad FROM agenda_programacion,agenda_programacion_actividades WHERE agenda_programacion.id_actividad=agenda_programacion_actividades.id_actividad AND agenda_programacion.objetivo=".$datos_objetivos["id_objetivo"]." AND agenda_programacion.id_aula=".$id_aula." AND month(agenda_programacion.fecha)=".date("n",strtotime($fecha))." AND year(agenda_programacion.fecha)=".date("Y",strtotime($fecha))." GROUP BY agenda_programacion.id_actividad ORDER BY agenda_programacion.id_actividad ") or die(db_error());
                                while($datos_actividades=db_fetch_array($busca_actividades)) {
                                        ?>
                                        <font size=2><b><?=$datos_actividades["id_actividad"];?>.</b> <?=$datos_actividades["descripcion_actividad"];?><br><br></font>
                                        <?php 
                                        }
                                        
                                // Actividades nuevas
                                $busca_actividades_nuevas=db_query("SELECT actividad FROM agenda_programacion WHERE objetivo=".$datos_objetivos["id_objetivo"]." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." AND id_actividad=0 GROUP BY actividad ORDER BY actividad ") or die(db_error()); 
                                $c_actividades=1;
                                
                                if (db_num_rows($busca_actividades_nuevas)) { ?><font size=2></font><b>Actividades nuevas introducidas por las educadoras.</b><br><br></font><?php  }
                                
                                while($actividades_nuevas=db_fetch_array($busca_actividades_nuevas)){                             
                                ?>
                                <font size=2><b><?=$c_actividades;?>.</b> <?=$actividades_nuevas["actividad"];?> <?php 
                                
                                if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
                                
                                // Comprueba si ya existe la actividad
                                
                                $busca_repetida=db_query_params('SELECT * FROM agenda_programacion_actividades WHERE descripcion_actividad=? AND id_objetivo=?', 'ss', array((string)($actividades_nuevas["actividad"]), $datos_objetivos["id_objetivo"]));
                                if (db_num_rows($busca_repetida)==0) {
                                ?>(<a href="agregar_actividad.php?actividad=<?=$actividades_nuevas["actividad"];?>&objetivo=<?=$datos_objetivos["id_objetivo"];?>">Agregar a las actividades de este objetivo</a>)<?php 
                                	} else {
                                	$datos_repetidas=db_fetch_array($busca_repetida);
								?>(<a href="quitar_actividad.php?actividad=<?=$datos_repetidas["id_actividad"];?>&objetivo=<?=$datos_objetivos["id_objetivo"];?>">Eliminar de las actividades de este objetivo</a>)<?php 
                                	}
                                } ?><br><br></font>
                                <?php 
                                $c_actividades++;
                                }
                                
                                }
                                
                                }
                                ?>
                                
                                <p align="center"><!--<input type="button" onclick="window.close()" style="font-size: 12px" value="Cerrar">--></p>
</td>
  </tr>
</table>
</body>
</html>
<?php 
db_close();
?>
