<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$id_alumno=$_POST["id_alumno"];
$trimestre=$_POST["trimestre"];
$id_aula=$_POST["id_aula"];

$trimestre_numeral=$trimestre."er";

if ($trimestre==2) {
	$trimestre_numeral=$trimestre."º";
	}

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        $id_curso_aula=$datos_aula["id_curso"];
		
        $edad_ini_aula=$datos_aula["edad_minima_prog"];  
        $edad_fin_aula=$datos_aula["edad_maxima_prog"];        
		
		
        $ciclo_aula=$datos_aula["ciclo"];
        if ($ciclo_aula==0) {
        	$ciclo_aula=1;
        	}
        
        // Busca datos curso
		$busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso_aula) or die(db_error());
		
		if (db_num_rows($busca_curso)!=0){
        	$datos_curso=db_fetch_array($busca_curso);

        	$fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
			$fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];
        	
        	}
                
        }
        
// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_ingles_evaluaciones_estado WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre;") or die(db_error());
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
	$datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    } else {
    $guarda_evaluacion=db_query("INSERT INTO agenda_programacion_ingles_evaluaciones_estado (id_curso,id_aula,trimestre) VALUES ('$id_curso_aula','$id_aula','$trimestre')");
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }        
     
?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<script language="javascript">
<!--
self.focus();

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda){ celda.style.backgroundColor="#ffffff" }
    
var pagina_destino="guarda_dato_evaluacion.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);
    }
}
    
//-->
</script>


<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin">
                                <table class="table table-striped">
                                        <tr>
                                        <td><h3>Aula: <?=$nombre_aula?> - Evaluación del <?=$trimestre_numeral?> trimestre. Del  <?=date("d/m/Y",strtotime($fecha_ini))?> al <?=date("d/m/Y",strtotime($fecha_fin))?></h3></td>
                                        </tr>
<tr>
                                        <td><font size=2><b>Visibilidad del informe por parte de los padres: <select name="visibilidad" id="visibilidad" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_ingles_evaluaciones_estado'},{desc:'campo',dato:'estado'},{desc:'valor',dato:document.getElementById('visibilidad').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada==0) { print " selected"; } ?>>NO VISIBLE</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada==1) { print " selected"; } ?>>VISIBLE</option>
                                        </select></b></font></td>
                                        </tr>
                                </table><br />
                                <?php 
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $contador_total=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $array_objetivos_area[$c_areas]=array();
                                $contador_array_obj=0;
                                
								if ($id_curso_aula>=28) {
									$busca_objetivos=db_query("SELECT agenda_programacion_ingles_objetivos.* FROM agenda_programacion_ingles_objetivos,agenda_evaluacion_ingles_items WHERE agenda_programacion_ingles_objetivos.id_evaluacion=agenda_evaluacion_ingles_items.id_evaluacion AND agenda_programacion_ingles_objetivos.edad>=$edad_ini_aula AND agenda_programacion_ingles_objetivos.edad<=$edad_fin_aula AND agenda_programacion_ingles_objetivos.version=2 AND (agenda_evaluacion_ingles_items.trimestre=".$trimestre." OR agenda_evaluacion_ingles_items.trimestre=0) ORDER BY agenda_programacion_ingles_objetivos.id_objetivo") or die(db_error());
								} else {	
									$busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles WHERE id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."';") or die(db_error());
								}
                                                                while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
                                                                
                                                                        // Objetivo principal
                                                                        
                                                                        if ($id_curso_aula>=28) {
                                                                                $id_objetivo1=$cuenta_objetivos["id_objetivo"];
                                                                                } else {
                                                                                $id_objetivo1=$cuenta_objetivos["objetivo"];
                                                                                }

                                                                        // Busca area
                                                                        $busca_datos_objetivo1=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=$id_objetivo1");
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
                                

                                
                                $busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=$id_aula AND fecha_matricula<='".date("Y-m-t",strtotime($fecha_fin))."' AND fecha_baja>='".date("Y-m-01",strtotime($fecha_ini))."' ORDER BY Apellido1, Apellido2, Nombre") or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {
                                
                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

// Busca envaluacion alumno guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
	$datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones_ingles"];
    } else {
    $guarda_evaluacion=db_query("INSERT INTO agenda_programacion_evaluaciones_estado_alumnos (id_curso,id_alumno,trimestre) VALUES ('$id_curso_aula','".$listado_alumnos["id_alumno"]."','$trimestre')");
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }

                                ?>
                                <table class="table table-striped table-hover">
                                        <tr class="info">
                                        <td colspan=3><h4><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></h3></td>
                                        </tr>
                                        <tr>
                                        <td colspan=3><h5>Observaciones</h5>
 		<textarea class="form-control" rows="3" name="observaciones<?=$id_estado_evaluacion_guardada?>" id="observaciones<?=$id_estado_evaluacion_guardada?>" onblur="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'observaciones_ingles'},{desc:'valor',dato:document.getElementById('observaciones<?=$id_estado_evaluacion_guardada?>').value});"><?=app_text_utf8($observaciones_evaluacion_guardada)?></textarea></td>
                                        </tr>                                        
                                        <?php 
                                        // Limpia tabla temp
                                        $limpia_tabla=db_query("DELETE FROM agenda_programacion_ingles_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]);
                                        
                                        for ($c_areas=0;$c_areas<count($areas);$c_areas++) {
                                        
                                        $num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);
                                        
                                        for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                           $id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];
                                           
                                           $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=$id_objetivo");
                                           $datos_objetivos=db_fetch_array($busca_datos_objetivo);
                                           
                                           $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
                                        
                                           $num_actividades_programadas=0;
                                           $num_actividades_trabajadas=0;
                                           $num_actividades_superadas=0;

                                           // Busca actividades trabajadas
                                           $busca_actividades=db_query("SELECT * FROM agenda_programacion_ingles WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND (id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."') ORDER BY id_actividad ") or die(db_error());

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
                                                        $superado=1;
                                                        } else {
                                                        $calificacion="<font color='#cc0000'>P</font>";
                                                        $superado=0;
                                                        }
                                                // Inserta en tabla temp
                                                $inserta_fila=db_query("INSERT INTO agenda_programacion_ingles_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area) VALUES ('$id_objetivo','".$datos_objetivos["id_evaluacion"]."','".$listado_alumnos["id_alumno"]."','$superado','$c_areas');");
                                                }
                                                
                                           // Busca y muestra
                                           $busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados FROM agenda_programacion_ingles_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas AND id_evaluacion<>0 GROUP BY id_evaluacion ORDER BY id_evaluacion ");
                                           while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
                                                if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
                                                        $calificacion=" <font color='#00cc00'>S</font>";
                                                        $valor_calificacion=1;
                                                        } else {
                                                        $calificacion=" <font color='#cc0000'>P</font>";
                                                        $valor_calificacion=0;
                                                        }
                                                        // Busca datos envaluacion
                                                        $busca_evaluacion=db_query("SELECT * FROM agenda_evaluacion_ingles_items WHERE id_evaluacion=".$datos_evaluaciones["id_evaluacion"]) or die(db_error());
                                                        $datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                        
                                                        // Busca envaluacion guardada, si no está la crea
                                                        
                                                        $busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_ingles_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");
                                                        
                                                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
                                                        	$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                                                        	$id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                                                        	$valor_calificacion=$datos_evaluacion_guardada["nota"];
                                                        	} else {
                                                        	$guarda_evaluacion=db_query("INSERT INTO agenda_programacion_ingles_evaluaciones_trimestrales (id_curso,id_aula,trimestre,id_alumno,id_evaluacion,nota) VALUES ('$id_curso_aula','$id_aula','$trimestre','".$listado_alumnos["id_alumno"]."','".$datos_evaluaciones["id_evaluacion"]."','$valor_calificacion')");
                                                        	$id_evaluacion_guardada=db_insert_id();
                                                        	}

                                                        ?>
                                                        <tr>
                                        				<td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_evaluacion["descripcion"]?></font></td>
                                        				<td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$calificacion?></font></td>
                                        				<td align="center"><select name="evaluacion<?=$id_evaluacion_guardada?>" id="evaluacion<?=$id_evaluacion_guardada?>" style="font-size: 10px" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_ingles_evaluaciones_trimestrales'},{desc:'campo',dato:'nota'},{desc:'valor',dato:document.getElementById('evaluacion<?=$id_evaluacion_guardada?>').value});">
                                        				<option style="color: #00cc00" value="1"<?php  if ($valor_calificacion==1) { print " selected"; } ?>>S</option>
                                        				<option style="color: #cc0000" value="0"<?php  if ($valor_calificacion==0) { print " selected"; } ?>>P</option>
                                        				<option style="color: #0000cc" value="2"<?php  if ($valor_calificacion==2) { print " selected"; } ?>>NT</option>
                                        				</select></td>
                                        				</tr>
                                                        <?php 
                                                }
                                                }
                                        ?>
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
