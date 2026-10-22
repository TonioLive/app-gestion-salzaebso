<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prog=$_GET["id_prog"];

$busca_datos_programacion=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE id_prog=?', 's', array($id_prog));

$datos_programacion=db_fetch_array($busca_datos_programacion);

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_programacion["id_aula"]));

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        }
        
$busca_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$datos_programacion["objetivo"]);

if (db_num_rows($busca_objetivo)!=0){
        $datos_objetivos=db_fetch_array($busca_objetivo);

        // Datos
        $nombre_objetivo=$datos_objetivos["nombre_objetivo"];
        $edad_objetivo=$datos_objetivos["edad"];
        $area_objetivo=$datos_objetivos["id_area"];
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
    <b>Objetivo: <?=$nombre_objetivo?><br><br>Aula: <?=$nombre_aula?> - Día <?=date("d/m/Y",strtotime($datos_programacion["fecha"]))?>, Tiempo de trabajo <?=$datos_programacion["tiempo_trabajo"]?></font></b><br><br>
    <a href="asignar_actividad.php?id_prog=<?=$id_prog?>&id_actividad=0">Crear actividad nueva</a><br><hr noshade color="#000000" size="1">
                                <?php 
                                // Busca actividades de este objetivo
                                $busca_actividades=db_query("SELECT * FROM agenda_programacion_ingles_actividades,agenda_programacion_ingles_tipos_actividades WHERE agenda_programacion_ingles_actividades.tipo_actividad=agenda_programacion_ingles_tipos_actividades.id_tipo_actividad AND ((id_objetivo=".$datos_programacion["objetivo"].") OR (id_objetivo=0 AND edad=$edad_objetivo AND area=$area_objetivo)) ORDER BY tipo_actividad") or die(db_error());

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
                                
                                $busca_programadas=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE id_aula=? AND id_actividad=? ORDER BY fecha;', 'ss', array($datos_programacion["id_aula"], $datos_activiades["id_actividad"]));

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
