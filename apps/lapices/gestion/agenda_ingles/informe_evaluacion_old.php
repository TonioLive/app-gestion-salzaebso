<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$fecha=$_GET["fecha"];

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
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
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin">
                                <?php 
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {
                                ?>
<table width="100%" border="0">
  <tr>
    <td align="left">
    <font size=2>
    <b>Aula: <?=$nombre_aula?> - Evaluación mensual <?=$nombre_mes[date("n",strtotime($fecha))]?> de <?=date("Y",strtotime($fecha))?><br><br>
    <?=$areas[$c_areas]?></b><br><br></font>
                                <?php 
                                $busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles,agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles.objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND agenda_programacion_ingles_objetivos.id_area=".($c_areas+1)." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY agenda_programacion_ingles_objetivos.id_objetivo ORDER BY agenda_programacion_ingles_objetivos.nombre_objetivo") or die(db_error());

                                if (db_num_rows($busca_objetivos)==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el mes seleccionado.</b></font>
                                <?php 
                                } else {
                                
                                $num_objetivos_trabajados=db_num_rows($busca_objetivos);
                                
                               ?>
                                <table width=100% border=1 style="border-collapse: collapse" bordercolor="#000000">
                                <tr>
                                   <td rowspan="3"><font size=2><b>Alumno</b></font></td>
                                   <td colspan="<?=$num_objetivos_trabajados*4?>"><font size=2><b>Objetivos trabajados durante el mes</b></font></td>
                                </tr>
                                <tr>
                                <?php 
                                for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                ?>
                                   <td align="center" colspan=4><font size=2><b><?=$contador_objetivos?></b></font></td>
                                <?php 
                                }
                                ?>
                                </tr>
                               <tr>
                                <?php 
                                for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                ?>
                                   <td align="center"><font size=2><b>Prog.</b></font></td>
                                   <td align="center"><font size=2><b>Trab.</b></font></td>
                                   <td align="center"><font size=2><b>Sup.</b></font></td>
                                   <td align="center"><font size=2><b>Calif.</b></font></td>
                                <?php 
                                }
                                ?>
                                </tr>
                                <?php 
                                $busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=$id_aula AND fecha_matricula<='".date("Y-m-01",strtotime($fecha))."' AND fecha_baja>='".date("Y-m-t",strtotime($fecha))."' ORDER BY Apellido1, Apellido2, Nombre") or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {
                                
                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

                                        ?>
                                        <tr valign="top">
                                        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></font></td>
                                        <?php 
                                        $busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles,agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles.objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND agenda_programacion_ingles_objetivos.id_area=".($c_areas+1)." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY agenda_programacion_ingles_objetivos.id_objetivo ORDER BY agenda_programacion_ingles_objetivos.nombre_objetivo") or die(db_error());
                                        
                                        while($datos_objetivos=db_fetch_array($busca_objetivos)) {
                                        
                                           $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
                                        
                                           $num_actividades_programadas=0;
                                           $num_actividades_trabajadas=0;
                                           $num_actividades_superadas=0;
                                        
                                           // Busca actividades trabajadas
                                           $busca_actividades=db_query("SELECT * FROM agenda_programacion_ingles WHERE objetivo=".$datos_objetivos["id_objetivo"]." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." ORDER BY id_actividad ") or die(db_error());

                                           while($datos_actividades=db_fetch_array($busca_actividades)) {
                                                   // Busca evaluación
                                                   $busca_evaluacion=db_query("SELECT * FROM agenda_programacion_ingles_evaluacion_alumnos WHERE id_prog=".$datos_actividades["id_prog"]." AND id_alumno=".$listado_alumnos["id_alumno"]);
                                           
                                                   if (db_num_rows($busca_evaluacion)!=0) {
                                           
                                                        $datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                
                                                        $num_actividades_trabajadas=$num_actividades_trabajadas+$datos_evaluacion["asistencia"];
                                                        $num_actividades_superadas=$num_actividades_superadas+$datos_evaluacion["superada"];

                                                        }
                                                $num_actividades_programadas++;
                                                }
                                                $porcentaje_calificacion=0;
                                                if ($num_actividades_programadas>0) {
                                                        $porcentaje_calificacion=round(($num_actividades_superadas/$num_actividades_programadas)*100,2);
                                                        }

                                                if ($porcentaje_calificacion>=$porcentaje_autoevaluacion) {
                                                        $calificacion="<font color='#00cc00'>S</font>";
                                                        } else {
                                                        $calificacion="<font color='#cc0000'>P</font>";
                                                        }
                                                        
                                                ?>
                                                   <td align="center"><font size=2><?=$num_actividades_programadas?></font></td>
                                                   <td align="center"><font size=2><?=$num_actividades_trabajadas?></font></td>
                                                   <td align="center"><font size=2><?=$num_actividades_superadas?></font></td>
                                                   <td align="center"><font size=2><?=$calificacion?> (<?=number_format($porcentaje_calificacion,2,",",".")?> %)</font></td>
                                                <?php 
                                                }
                                        }
                                        }
                                }
                                ?>
                                </table>
                                <br><b>Objetivos trabajados:</b><br><br>
                                <?php 
                                $busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles,agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles.objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND agenda_programacion_ingles_objetivos.id_area=".($c_areas+1)." AND id_aula=".$id_aula." AND month(fecha)=".date("n",strtotime($fecha))." AND year(fecha)=".date("Y",strtotime($fecha))." GROUP BY agenda_programacion_ingles_objetivos.id_objetivo ORDER BY agenda_programacion_ingles_objetivos.nombre_objetivo") or die(db_error());
                                
                                $contador_objetivos=1;
                                while($datos_objetivos=db_fetch_array($busca_objetivos)) {
                                        ?>
                                        <b><?=$contador_objetivos?>.</b> <?=$datos_objetivos["nombre_objetivo"]?><br>
                                        <?php 
                                        $contador_objetivos++;
                                        }
                                ?>
                                <p align="center">
                                </font>
</td>
  </tr>
</table>
<?php 
$contador_areas++;
if ($contador_areas<count($areas)) {
?>
<br class="saltopagina" />
<?php 
}
                                }
                                ?>
</body>
</html>
<?php 
db_close();
?>
