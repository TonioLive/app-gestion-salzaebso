<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$id_alumno=$_POST["id_alumno"];

$id_aula=$_POST["id_aula"];

        $dia_i=$_POST["dia_i"];
        $mes_i=$_POST["mes_i"];
        $ano_i=$_POST["ano_i"];
        $dia_f=$_POST["dia_f"];
        $mes_f=$_POST["mes_f"];
        $ano_f=$_POST["ano_f"];
        if ($dia_i=="--" || $mes_i=="--" || $ano_i=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de inicio del periodo.";
                } else {
                        if (!checkdate(intval($mes_i),intval($dia_i),intval($ano_i))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de inicio del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }
        if ($dia_f=="--" || $mes_f=="--" || $ano_f=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de finalización del periodo.";
                } else {
                        if (!checkdate(intval($mes_f),intval($dia_f),intval($ano_f))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de finalización del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }

$fecha_ini=$ano_i."-".$mes_i."-".$dia_i;
$fecha_fin=$ano_f."-".$mes_f."-".$dia_f;

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

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
</head>

<body>
                                <?php 
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $contador_total=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $array_objetivos_area[$c_areas]=array();
                                $contador_array_obj=0;
                                
                                $busca_objetivos=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE id_aula=? AND fecha>=? AND fecha<=?;', 'sss', array($id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());

                                                                while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
                                                                
                                                                        // Objetivo principal
                                                                        
                                                                        $id_objetivo1=$cuenta_objetivos["objetivo"];
                                                                        
                                                                        // Busca area
                                                                        $busca_datos_objetivo1=db_query_params('SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=?', 's', array($id_objetivo1));
                                    $datos_objetivos1=db_fetch_array($busca_datos_objetivo1);
                                    
                                    if ($datos_objetivos1["id_area"]==($c_areas+1)) {

                                                                                if (in_array($id_objetivo1, $array_objetivos_area[$c_areas])==false) {
                                                                                        $array_objetivos_area[$c_areas][$contador_array_obj]=$id_objetivo1;
                                                                                        $contador_array_obj++;
                                                                                        $contador_total++;
                                                                                        }

                                                                                }
                                                                                
                                                                        // Objetivos secundarios
                                                                        $objetivos_secundarios=$cuenta_objetivos["objetivos"];
                                                                        if (strlen($objetivos_secundarios)!=0) {
                                                                                $objetivos_secundarios=str_replace("[","",$objetivos_secundarios);
                                                                                $objetivos_secundarios=str_replace("]","",$objetivos_secundarios);
                                                                                $array_objetivos_sec=explode(",",$objetivos_secundarios);
                                                                                
                                                                                for ($contador_objetivos_sec=0;$contador_objetivos_sec<count($array_objetivos_sec);$contador_objetivos_sec++){
                                                                                // Busca area
                                                                                $busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
                                            $datos_objetivos2=db_fetch_array($busca_datos_objetivo2);

                                            if ($datos_objetivos2["id_area"]==($c_areas+1)) {
                                                                                                if (in_array($array_objetivos_sec[$contador_objetivos_sec], $array_objetivos_area[$c_areas])==false) {
                                                                                                        $array_objetivos_area[$c_areas][$contador_array_obj]=$array_objetivos_sec[$contador_objetivos_sec];
                                                                                                        $contador_array_obj++;
                                                                                                        $contador_total++;
                                                                                                        }

                                                                                                }
                                                                                        }
                                                                                
                                                                                }
                                                                        
                                                                        
                                                                        }
                                        }
                                        
                                if ($contador_total==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el periodo seleccionado.</b></font>
                                <?php 
                                } else {
                                

                                
                                $busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($id_aula, (string)(date("Y-m-t", strtotime($fecha_fin))), (string)(date("Y-m-01", strtotime($fecha_ini))))) or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {
                                
                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

                                ?>
                                <table width=100% border=1 style="page-break-after: always; border-collapse: collapse" bordercolor="#000000" >
                                        <tr>
                                        <td><font size=2><b>Aula: <?=$nombre_aula?> - Evaluación periodo del  <?=date("d/m/Y",strtotime($fecha_ini))?> al <?=date("d/m/Y",strtotime($fecha_fin))?></b></font></td>
                                        </tr>
                                        <tr valign="top">
                                        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></font></td>
                                        </tr>
                                        <tr>
                                        <td><font size=2 face="<?= $fuente_general; ?>">
                                        <?php 
                                        
                                        for ($c_areas=0;$c_areas<count($areas);$c_areas++) {
                                        
                                        $num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);
                                        
                                        for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                           $id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];
                                           
                                           $busca_datos_objetivo=db_query_params('SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
                                           $datos_objetivos=db_fetch_array($busca_datos_objetivo);
                                           
                                           $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
                                        
                                           $num_actividades_programadas=0;
                                           $num_actividades_trabajadas=0;
                                           $num_actividades_superadas=0;

                                           // Busca actividades trabajadas
                                           $busca_actividades=db_query_params('SELECT * FROM agenda_programacion_ingles WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());

                                           while($datos_actividades=db_fetch_array($busca_actividades)) {

                                                   // Busca evaluación
                                                   $busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_ingles_evaluacion_alumnos WHERE id_prog=? AND id_alumno=?', 'ss', array($datos_actividades["id_prog"], $listado_alumnos["id_alumno"]));
                                           
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
                                                        $calificacion=" <font color='#00cc00'>S</font>";
                                                        } else {
                                                        $calificacion=" <font color='#cc0000'>P</font>";
                                                        }
                                                ?>
                                                <?=$datos_objetivos["nombre_objetivo"].$calificacion;?><br>
                                                <?php 
                                                }

                                                }
                                        ?>
                                        </td></tr>
                                        </table><br class="saltopagina" />
                                        <?php 
                                        }
                                        }
                                }
                                ?>
                                </font>

</body>
</html>
<?php 
db_close();
?>
