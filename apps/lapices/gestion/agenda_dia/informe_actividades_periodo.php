<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

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
               
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
    <b>Aula: <?=$nombre_aula?> - Resumen de objetivos trabajados durante el <?=$trimestre_numeral?> trimestre: <?=date("d/m/Y",strtotime($fecha_ini))?> al <?=date("d/m/Y",strtotime($fecha_fin))?></font></b><br><br>
                                <?php 
                                $array_objetivos_area=array();
                                $contador_array_obj=0;
                                
                                $busca_objetivos=db_query("SELECT * FROM agenda_programacion WHERE id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."';") or die(db_error()."-1");

								while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
								
									// Objetivo principal
									
									$id_objetivo1=$cuenta_objetivos["objetivo"];
									
									// Busca area
									$busca_datos_objetivo1=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=$id_objetivo1");
                                    $datos_objetivos1=db_fetch_array($busca_datos_objetivo1);
                                    
									if (in_array($id_objetivo1, $array_objetivos_area)==false) {
											$array_objetivos_area[$contador_array_obj]=$id_objetivo1;
											$contador_array_obj++;
											}
										
									// Objetivos secundarios
									$objetivos_secundarios=$cuenta_objetivos["objetivos"];
									if (strlen($objetivos_secundarios)!=0) {
										$objetivos_secundarios=str_replace("[","",$objetivos_secundarios);
										$objetivos_secundarios=str_replace("]","",$objetivos_secundarios);
										$array_objetivos_sec=explode(",",$objetivos_secundarios);
										
										for ($contador_objetivos_sec=0;$contador_objetivos_sec<count($array_objetivos_sec);$contador_objetivos_sec++){
										// Busca area
										$busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
                                    	$datos_objetivos2=db_fetch_array($busca_datos_objetivo2);
                                    
												if (in_array($array_objetivos_sec[$contador_objetivos_sec], $array_objetivos_area)==false) {
													$array_objetivos_area[$contador_array_obj]=$array_objetivos_sec[$contador_objetivos_sec];
													$contador_array_obj++;
													}	
											}
										
										}
									
									
									}

                                if (db_num_rows($busca_objetivos)==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el mes seleccionado.</b></font>
                                <?php 
                                } else {
                                $num_objetivos_trabajados=count($array_objetivos_area);
                                for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                
                                $id_objetivo=$array_objetivos_area[($contador_objetivos-1)];
                                
                                $busca_objetivos=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=$id_objetivo");
                                
                                $datos_objetivos=db_fetch_array($busca_objetivos);
                                
                                 $busca_actividades=db_query("SELECT id_actividad,count(id_prog) AS num_actividades FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."' GROUP BY id_actividad ORDER BY id_actividad ") or die(db_error());
                                
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

$no_mostrar_link=false;

                                   ?>
                                </tr>
                                <tr>
                                   <td><font size=2><b><?=app_text_utf8($datos_objetivos["nombre_objetivo"])?></b><small> (Nº de veces que se trabaja)</small></font></td>
                                   <?php 
                                   // Busca actividades trabajadas

                                   $busca_actividades=db_query("SELECT id_actividad,count(id_prog) AS num_actividades FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."' GROUP BY id_actividad ORDER BY id_actividad ") or die(db_error());
                                   
                                   while($datos_actividades=db_fetch_array($busca_actividades)) {
                                   ?>
                                   <td align="center"><?=$datos_actividades["num_actividades"]?></td>
                                   <?php 
                                   }
                                   
                                   ?>
                                </tr>
                                </table><br>
                                <?php 
                                $busca_actividades=db_query("SELECT agenda_programacion.id_actividad,agenda_programacion_actividades.descripcion_actividad FROM agenda_programacion,agenda_programacion_actividades WHERE agenda_programacion.id_actividad=agenda_programacion_actividades.id_actividad AND (agenda_programacion.objetivo=".$id_objetivo." OR agenda_programacion.objetivos like '%[".$id_objetivo."]%') AND agenda_programacion.id_aula=".$id_aula." AND agenda_programacion.fecha>='".$fecha_ini."' AND agenda_programacion.fecha<='".$fecha_fin."' GROUP BY agenda_programacion.id_actividad ORDER BY agenda_programacion.id_actividad ") or die(db_error());
                                while($datos_actividades=db_fetch_array($busca_actividades)) {
                                        ?>
                                        <font size=2><b><?=$datos_actividades["id_actividad"];?>.</b> <?=$datos_actividades["descripcion_actividad"];?><br><br></font>
                                        <?php 
                                        }
                                        
                                // Actividades nuevas
                                $busca_actividades_nuevas=db_query("SELECT actividad FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."' AND id_actividad=0 GROUP BY actividad ORDER BY actividad ") or die(db_error()); 
                                $c_actividades=1;
                                
                                if (db_num_rows($busca_actividades_nuevas)) { ?><font size=2></font><b>Actividades nuevas introducidas por las educadoras.</b><br><br></font><?php  }
                                
                                while($actividades_nuevas=db_fetch_array($busca_actividades_nuevas)){                             
                                ?>
                                <font size=2><b><?=$c_actividades;?>.</b> <?=app_text_utf8($actividades_nuevas["actividad"]);?> <?php 
                                
                                if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
                                
                                // Comprueba si ya existe la actividad
                                
                                $busca_repetida=db_query("SELECT * FROM agenda_programacion_actividades WHERE descripcion_actividad='".$actividades_nuevas["actividad"]."' AND id_objetivo=".$datos_objetivos["id_objetivo"]);
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
