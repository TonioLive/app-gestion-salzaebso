<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$lista_autoevaluacion=array();
$lista_evaluacion_alu=array();

$id_alumno=$_GET["id_alumno"];

$busca_datos_alumno=db_query_params('SELECT * FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=?;', 's', array($id_alumno)) or die(db_error());

$alumnos=db_fetch_array($busca_datos_alumno);
$id_usuario_alumno=$alumnos["id_usuario"];

$id_aula=$alumnos["aula"];
$id_curso_aula=$alumnos["id_curso"];

	// Fecha de incorporación
	$fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
	if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
	$fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
	if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
	$edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));
	if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
	if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }
$lista_grupos_alumno="";
        
$busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($id_alumno)) or die(db_error());

while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
	}
                                
if (strlen($lista_grupos_alumno)!=0) {
	$lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
	}         


// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=$id_alumno AND periodo=$periodo;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
	$datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $estado_evaluacion_guardada1=$datos_estado_evaluacion_guardada["estado_p1"];
    $estado_evaluacion_guardada2=$datos_estado_evaluacion_guardada["estado_p2"];
    $estado_evaluacion_guardada3=$datos_estado_evaluacion_guardada["estado_p3"]; 
    $estado_evaluacion_guardada4=$datos_estado_evaluacion_guardada["estado_p4"];   
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_estado_alumnos (id_curso,id_alumno,periodo) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($id_alumno), (string)($periodo)));
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    $estado_evaluacion_guardada1=0;
    $estado_evaluacion_guardada2=0;
    $estado_evaluacion_guardada3=0; 
    $estado_evaluacion_guardada4=0;          
    }  

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

// Busca evaluaciones anteriores
$busca_alumnos_usuario=db_query_params('SELECT *, agenda_programacion_evaluaciones_estado_alumnos.id_curso AS curso_evaluacion FROM alumnos, agenda_programacion_evaluaciones_estado_alumnos WHERE alumnos.id_alumno=agenda_programacion_evaluaciones_estado_alumnos.id_alumno AND alumnos.id_usuario=? AND alumnos.id_alumno<>? ORDER BY alumnos.fecha_matricula', 'ss', array($id_usuario_alumno, $id_alumno)) or die(db_error());

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Evaluación Alumno: <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/bootstrap-duallistbox.css" type="text/css">
<link rel="stylesheet" href="/src/chosen.css">

<style>
<?php 
foreach ($colores_subniveles as $indice => $color) {
?>

.badge-<?=$indice?> {
background-color: <?=$color?>;
}

<?php 
}

?>
</style>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/jquery.bootstrap-duallistbox.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $(".chosen-select").chosen()
});	
</script>
<script language="javascript">
<!--
self.focus();

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda){ celda.style.backgroundColor="#ffffff" }
    
var pagina_destino="/gestion/agenda_dia/guarda_dato_evaluacion.php";

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
</head>
<body>
<div class="container-fluid">
<div class="panel panel-default">
  <div class="panel-heading"><?php  
if (db_num_rows($busca_alumnos_usuario)!=0) {
?>
<button onclick="self.location.href='importar_evaluacion.php?id_alumno=<?=$id_alumno?>';" class="btn btn-success pull-right">Importar resultados de evaluaciones cursos anteriores</button>
<?php
}
?><h3>Evaluación del alumno <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></h3>
  <h4>Grupos: <?=$lista_grupos_alumno?></h4></div>
  <div class="panel-body">
    <form method="post" action="" name="evaluacion">
    <input type="hidden" value="<?=$id_alumno?>" name="id_alumno"> 
  		<h3>Objetivos Generales</h3>
  		
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">  			
 		<?php
 		
 		$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
 		
 		while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
 		?>
 			<div class="panel <?=$colores_niveles_general[$objetivos_generales["nivel"]];?>">
    			<div class="panel-heading" role="tab" id="heading<?=$objetivos_generales["id_objetivo"];?>">
      				<h4 class="panel-title"><?php  if ($objetivos_generales["id_objetivo"]==20) { ?><span class="label label-danger pull-right">Contiene objetivos específicos de nivel 3</span><?php  } ?>
        			<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$objetivos_generales["id_objetivo"];?>" aria-expanded="true" aria-controls="collapse<?=$objetivos_generales["id_objetivo"];?>"><?=$objetivos_generales["id_objetivo"];?>. <?=$objetivos_generales["nombre_objetivo"];?></a>
      				</h4>
    			</div>
    			<div id="collapse<?=$objetivos_generales["id_objetivo"];?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?=$objetivos_generales["id_objetivo"];?>">
      				<div class="panel-body">
      				<h5>Objetivos Específicos</h5>
      				<table class="table table-hover">
					<?php 
					$busca_objetivos_especificos=db_query_params('SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=? ORDER BY id_objetivo', 's', array($objetivos_generales["id_objetivo"]));
 		
			 		while($objetivos_especificos=db_fetch_array($busca_objetivos_especificos)) {
			 		
			 			// Busca envaluacion guardada, si no está la crea                                                                               
                                                        
                    	$busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND periodo=$periodo AND id_alumno=".$id_alumno." AND id_objetivo=".$objetivos_especificos["id_objetivo"].";") or die(db_error());
                                                        
                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
							$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                            $id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                            $valor_calificacion=$datos_evaluacion_guardada["nota"];
                        } else {
                        	$guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_trimestrales (id_curso,id_aula,periodo,id_alumno,id_objetivo,nota) VALUES (?,?,?,?,?,\'\')', 'sssss', array((string)($id_curso_aula), (string)($id_aula), (string)($periodo), (string)($id_alumno), (string)($objetivos_especificos["id_objetivo"])));
                        	$id_evaluacion_guardada=db_insert_id();
                        	$valor_calificacion="";
                        }
			 		
			 		// Muestra subniveles
			 		$lista_subniveles=explode(",",$objetivos_especificos["autoevaluacion"]);
			 		
			 		$subniveles="";
			 		
			 		foreach ($lista_subniveles as $indice => $subnivel) {
			 		
			 			if ($subnivel=="1") {
			 			
			 				$subniveles.=" <span class='badge badge-".($indice+1)."'>".$nombres_subniveles[($indice+1)]."</span>";	
			 				$lista_autoevaluacion[$indice][]=$objetivos_especificos["id_objetivo"];	
			 				
				 			$lista_evaluacion_alu[$indice][]=$valor_calificacion;		 				
			 			
			 			}
			 		
			 		}
			 		
			 		?>
			 		<tr>
			 			<td class="col-md-10<?=$colores_niveles[$objetivos_especificos["nivel"]];?>"><?=$subniveles." ".$objetivos_especificos["nombre_objetivo"];?></td>
			 			<td class="col-md-2"><select name="evaluacion<?=$id_evaluacion_guardada?>" id="evaluacion<?=$id_evaluacion_guardada?>" style="font-size: 12px" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_trimestrales'},{desc:'campo',dato:'nota'},{desc:'valor',dato:document.getElementById('evaluacion<?=$id_evaluacion_guardada?>').value});">
                                        				<option style="color: #00cc00" value="1"<?php  if ($valor_calificacion==1) { print " selected"; } ?>>Superada</option>
                                        				<option style="color: #cc0000" value="2"<?php  if ($valor_calificacion==2) { print " selected"; } ?>>Progresa</option>
														<option style="color: #000000" value="0"<?php  if ($valor_calificacion==0) { print " selected"; } ?>>No trabajada</option>                                     				
                                        				</select></td>
			 		</tr>
			 		<?php 
			 		}
					?>
					</table>
      				</div>
    			</div>
  			</div>
 		<?php
 		}
 		
 		?>
 		<h4>Observaciones</h4>
 		<textarea class="form-control" rows="3" name="observaciones" id="observaciones" onblur="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'observaciones'},{desc:'valor',dato:document.getElementById('observaciones').value});"><?=utf8_decode($observaciones_evaluacion_guardada)?></textarea>
 		<?php 
 		// Comprueba niveles máximos
 		
 		$nivel_maximo_superado=-1;
 		$nivel_maximo_trabajado=-1;
 		
 		for ($niveles=0;$niveles<=8;$niveles++) {
 		
 			$total_objetivos_para_superado=count($lista_autoevaluacion[$niveles]);
 			$total_superados=0;
 			$total_trabajado=0; 			 		 		
 			
			if (is_array($lista_evaluacion_alu[$niveles])) {
			 			
 				foreach ($lista_evaluacion_alu[$niveles] as $valor_evauacion) { 			 				 
 			 			
 					if ($valor_evauacion=="1") {
 						$total_superados++;
 					}
 					if ($valor_evauacion=="2") {
 						$total_trabajado++;
 					}
 			
 				}
 			
 			}
 			
 			if (($total_superados==$total_objetivos_para_superado) and ($total_superados>0)) {
 			
 				$nivel_maximo_superado=$niveles;
 				$nivel_maximo_trabajado=$niveles;	
 			
 			} else {
 			
 				if (($total_trabajado>0) or ($total_superados>0)) {
 				
 					$nivel_maximo_trabajado=$niveles;
 				
 				}
 			
 			}
 			
 		
 		}
 		
 		if (($nivel_maximo_superado==-1) and ($nivel_maximo_trabajado>2)) {
 		
 			if ($nivel_maximo_trabajado<6) {
 				$nivel_maximo_superado=2;
 				} else {
 				$nivel_maximo_superado=5;
 				}
 		
 		} 		 		
 		
 		// Guarda dato
 		$guarda_niveles=db_query_params('UPDATE alumnos SET nivel_maximo_superado=?, nivel_maximo_trabajado=? WHERE id_alumno=?', 'sss', array((string)($nivel_maximo_superado + 1), (string)($nivel_maximo_trabajado + 1), $id_alumno));
 		
 		?>
 		<h4>Nivel máximo superado: <span class='badge badge-<?=($nivel_maximo_superado+1);?>'><?=$nombres_subniveles[($nivel_maximo_superado+1)];?></span></h4>
 		<h4>Nivel máximo trabajado: <span class='badge badge-<?=($nivel_maximo_trabajado+1);?>'><?=$nombres_subniveles[($nivel_maximo_trabajado+1)];?></span></h4>
 		<h4>Estado de la evaluación</h4>
 		 <div class="row">
 			<div class="col-md-3">Periodo 1</div>
 			<div class="col-md-3">Periodo 2</div>
 			<div class="col-md-3">Periodo 3</div>
 			<div class="col-md-3">Periodo 4</div>
 		</div>
 		<div class="row">
 			<div class="col-md-3"><select name="visibilidad1" id="visibilidad1" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'estado_p1'},{desc:'valor',dato:document.getElementById('visibilidad1').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada1==0) { print " selected"; } ?>>NO FINALIZADA</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada1==1) { print " selected"; } ?>>FINALIZADA</option>
                                        </select></div>
 			<div class="col-md-3"><select name="visibilidad2" id="visibilidad2" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'estado_p2'},{desc:'valor',dato:document.getElementById('visibilidad2').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada2==0) { print " selected"; } ?>>NO FINALIZADA</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada2==1) { print " selected"; } ?>>FINALIZADA</option>
                                        </select></div>
 			<div class="col-md-3"><select name="visibilidad3" id="visibilidad3" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'estado_p3'},{desc:'valor',dato:document.getElementById('visibilidad3').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada3==0) { print " selected"; } ?>>NO FINALIZADA</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada3==1) { print " selected"; } ?>>FINALIZADA</option>
                                        </select></div>
 			<div class="col-md-3"><select name="visibilidad4" id="visibilidad4" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'estado_p4'},{desc:'valor',dato:document.getElementById('visibilidad4').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada4==0) { print " selected"; } ?>>NO FINALIZADA</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada4==1) { print " selected"; } ?>>FINALIZADA</option>
                                        </select></div>
 		</div>
		</div> 		
		<button type="button" onclick="window.opener.history.go(0);window.close();" class="btn btn-danger">Salir</button>
    </form>
</div>
</div>        
</body>
</html>
