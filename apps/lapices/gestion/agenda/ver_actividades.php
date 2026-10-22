<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prog=$_GET["id_prog"];

$busca_datos_programacion=db_query("SELECT * FROM agenda_programacion WHERE id_prog=$id_prog");

$datos_programacion=db_fetch_array($busca_datos_programacion);

$fecha_objetivo=$datos_programacion["fecha"];

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_programacion["id_aula"]);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
		$edad_aula=$datos_aula["edad_minima_prog"];
		
        }
        
$busca_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$datos_programacion["objetivo"]);

if (db_num_rows($busca_objetivo)!=0){
        $datos_objetivos=db_fetch_array($busca_objetivo);

        // Datos
        $nombre_objetivo=$datos_objetivos["nombre_objetivo"];
        $edad_objetivo=$datos_objetivos["edad"];
        $area_objetivo=$datos_objetivos["id_area"];
		$unidad_objetivo=$datos_objetivos["unidad"];
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
<table width="100%" border="0">
  <tr>
    <td align="left">
    <b>Objetivo: <?=$nombre_objetivo?><br><br>Aula: <?=$nombre_aula?> - Día <?=date("d/m/Y",strtotime($datos_programacion["fecha"]))?>, Tiempo de trabajo <?=$datos_programacion["tiempo_trabajo"]?></font></b><br><br>
    <a href="asignar_actividad.php?id_prog=<?=$id_prog?>&id_actividad=0">Crear actividad nueva</a><br><hr noshade color="#000000" size="1">
                                <?php 
                                // Busca actividades de este objetivo
								$consulta_actividades_objetivo="SELECT * FROM agenda_programacion_actividades,agenda_programacion_tipos_actividades WHERE agenda_programacion_actividades.tipo_actividad=agenda_programacion_tipos_actividades.id_tipo_actividad AND editorial=0 AND ((id_objetivo=".$datos_programacion["objetivo"].") OR (id_objetivo=0 AND edad=$edad_objetivo AND area=$area_objetivo)) ORDER BY tipo_actividad";
								
								// Comprueba si es EDEBE...
								/*
								$nueva_programacion_activa=true;
								if ((strtotime($fecha_objetivo)<=strtotime("2019-08-31")) and (strtotime($fecha_objetivo)>=strtotime("2017-09-01")) and (($edad_aula==1) or ($edad_aula==2)) and ($nueva_programacion_activa==true)) {
							
									$editorialProgramacion=1;
									if (strtotime($fecha_objetivo)>=strtotime("2018-09-01")) {
											$editorialProgramacion=2;
									}
							
									$consulta_actividades_objetivo="SELECT * FROM agenda_programacion_actividades,agenda_programacion_tipos_actividades WHERE agenda_programacion_actividades.tipo_actividad=agenda_programacion_tipos_actividades.id_tipo_actividad AND edad=$edad_objetivo AND ((unidad=".$unidad_objetivo." AND editorial=$editorialProgramacion) OR (id_objetivo=0 AND area=$area_objetivo)) ORDER BY tipo_actividad";
								
								}								
								*/
                                $busca_actividades=db_query($consulta_actividades_objetivo) or die(db_error());

                                if (db_num_rows($busca_actividades)==0) {
                                ?>
                                <b>No se han encontrado actividades para este objetivo.</b></font>
                                <?php 
                                }

                                $tipo_actual="";

                                while($datos_activiades=db_fetch_array($busca_actividades)) {
                                
                                if ($tipo_actual!=$datos_activiades["nombre_tipo_actividad"]) {
                                        if ($tipo_actual!="") {
                                        ?>
                                        </blockquote>
                                        <?php 
                                        }
                                        ?>
                                        <font size=4><b><?=$datos_activiades["nombre_tipo_actividad"];?></b></font>
                                        <blockquote>
                                        <?php 
                                        $tipo_actual=$datos_activiades["nombre_tipo_actividad"];
                                        }

                                // Buscar cuando se programó
                                
                                $busca_programadas=db_query("SELECT * FROM agenda_programacion WHERE id_aula=".$datos_programacion["id_aula"]." AND id_actividad=".$datos_activiades["id_actividad"]." ORDER BY fecha;");

                                $ya_programadas="";
                                $contador_prog=0;
                                while($programadas=db_fetch_array($busca_programadas)) {
                                        $ya_programadas.=$programadas["fecha"].", ";
                                        $contador_prog++;
                                        }

                                if ($contador_prog>1) {
                                        $ya_programadas="<br><font color='#ff0000' size=1>(Programada los días ".substr($ya_programadas,0,strlen($ya_programadas)-2).")</font>";
                                        }
                                if ($contador_prog==1) {
                                        $ya_programadas="<br><font color='#ff0000' size=1>(Programa el día ".substr($ya_programadas,0,strlen($ya_programadas)-2).")</font>";
                                        }
                                
                                ?>
                                · <a href="asignar_actividad.php?id_prog=<?=$id_prog?>&id_actividad=<?=$datos_activiades["id_actividad"]?>"><?=$datos_activiades["descripcion_actividad"]?></a><?=$ya_programadas?><br><hr noshade color="#000000" size="1">
                                <?php 
                                }
                                ?>
                                </blockquote>
                                <p align="center"><input type="button" onclick="window.close()" style="font-size: 12px" value="Cerrar"></p>
</td>
  </tr>
</table>
</body>
</html>
<?php 
db_close();
?>
