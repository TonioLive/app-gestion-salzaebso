<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];
$periodo=$_GET["trimestre"];
if (strlen($periodo)==0) {
        $periodo=1;
        }

$busca_datos_alumno=db_query_params('SELECT * FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=?;', 's', array($id_alumno)) or die(db_error()."-0");

$alumnos=db_fetch_array($busca_datos_alumno);

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
        
$busca_aula_alumno=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula)) or die(db_error()."-1");

$nombre_aula="";

while($aula_donde=db_fetch_array($busca_aula_alumno)) {
        $nombre_aula=$aula_donde["nombre"];
        }                               

// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=$id_alumno AND periodo=$periodo;") or die(db_error()."-2");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
    $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
    $observaciones_evaluacion_guardada1=$datos_estado_evaluacion_guardada["observaciones1"];
    $observaciones_evaluacion_guardada2=$datos_estado_evaluacion_guardada["observaciones2"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_comedor_estado_alumnos (id_curso,id_alumno,periodo) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($id_alumno), (string)($periodo))) or die(db_error()."-3");
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }  

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger"," text-warning"," text-primary"," text-muted");
$colores_niveles_general=array(""," panel-success"," panel-info"," panel-danger"," panel-warning"," panel-primary"," panel-default");

// Busca última modificación
$busca_ultima_moficiacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND periodo=$periodo AND id_alumno=".$id_alumno." AND fecha_modificacion is NOT Null ORDER BY fecha_modificacion DESC LIMIT 0,1") or die(db_error()) or die(db_error()."-6");
$modificada_por="";
if (db_num_rows($busca_ultima_moficiacion_guardada)!=0) {

        $ultima_modificacion_guardada=db_fetch_array($busca_ultima_moficiacion_guardada);
        
        $modificada_por="<h5>Modificada el día ".date("d/m/Y",strtotime($ultima_modificacion_guardada["fecha_modificacion"]))." a las ".date("H:i",strtotime($ultima_modificacion_guardada["fecha_modificacion"]))." por ".$ultima_modificacion_guardada["modificado_por"]."</h5>";

}

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
function sendAndLoad_NADA(){

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
  <div class="panel-heading"><a type="button" target='_blank' href="/pdf/evaluacion_comedor.php?id_alumno=<?=$id_alumno?>&trimestre=<?=$periodo?>" class="btn btn-primary pull-right">Vista previa</a> <h3>Evaluación comedor de <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></h3>
  <h4>Aula: <?=$nombre_aula?></h4>
  <h4>Periodo evaluado: Del <?=date("d/m/Y",strtotime($alumnos["fecha_matricula"]))?> al <?=date("d/m/Y",strtotime($alumnos["fecha_baja"]))?></h4><?=$modificada_por?></div>
  <div class="panel-body">
    <form method="post" action="" name="evaluacion">
    <input type="hidden" value="<?=$id_alumno?>" name="id_alumno"> 
                  <h3>Objetivos</h3>
                  
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">                          
                 <?php
                 
                 $busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_comedor WHERE id_objetivo_g=0 ORDER BY id_objetivo") or die(db_error()."-4");
                 
                 while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
                 ?>
                         <div class="panel <?=$colores_niveles_general[$objetivos_generales["nivel"]];?>">
                            <div class="panel-heading" role="tab" id="heading<?=$objetivos_generales["id_objetivo"];?>">
                                      <h4 class="panel-title"><?php  if ($objetivos_generales["id_objetivo"]==21) { ?><span class="label label-danger pull-right">Contiene objetivos específicos de nivel 3</span><?php  } ?>
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$objetivos_generales["id_objetivo"];?>" aria-expanded="true" aria-controls="collapse<?=$objetivos_generales["id_objetivo"];?>"><?=$objetivos_generales["id_objetivo"];?>. <?=$objetivos_generales["nombre_objetivo"];?></a>
                                      </h4>
                            </div>
                            <div id="collapse<?=$objetivos_generales["id_objetivo"];?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?=$objetivos_generales["id_objetivo"];?>">
                                      <div class="panel-body">
                                      <table class="table table-hover">
                                        <?php 
                                        $busca_objetivos_especificos=db_query_params('SELECT * FROM agenda_programacion_objetivos_comedor WHERE id_objetivo_g=? ORDER BY id_objetivo', 's', array($objetivos_generales["id_objetivo"])) or die(db_error()."-5");
                 
                                         while($objetivos_especificos=db_fetch_array($busca_objetivos_especificos)) {
                                         
                                                 // Busca envaluacion guardada, si no está la crea                                                                               
                                                        
                            $busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND periodo=$periodo AND id_alumno=".$id_alumno." AND id_objetivo=".$objetivos_especificos["id_objetivo"].";") or die(db_error()) or die(db_error()."-6");
                                                        
                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
                                                        $datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                            $id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                            $valor_calificacion=$datos_evaluacion_guardada["nota"];
                        } else {
                                $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_comedor_trimestrales (id_curso,id_aula,periodo,id_alumno,id_objetivo,nota) VALUES (?,?,?,?,?,\'\')', 'sssss', array((string)($id_curso_aula), (string)($id_aula), (string)($periodo), (string)($id_alumno), (string)($objetivos_especificos["id_objetivo"]))) or die(db_error()."-7");
                                $id_evaluacion_guardada=db_insert_id();
                                $valor_calificacion="";
                        }
                                         
                                         ?>
                                         <tr>
                                                 <td class="col-md-10<?=$colores_niveles[$objetivos_especificos["nivel"]];?>"><?=$objetivos_especificos["nombre_objetivo"];?></td>
                                                 <td class="col-md-2"><select name="evaluacion<?=$id_evaluacion_guardada?>" id="evaluacion<?=$id_evaluacion_guardada?>" style="font-size: 12px" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_comedor_trimestrales'},{desc:'campo',dato:'nota'},{desc:'valor',dato:document.getElementById('evaluacion<?=$id_evaluacion_guardada?>').value});">
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
                 <h4>Observaciones 1<sup>er</sup> Trimestre</h4>
                 <textarea class="form-control" rows="3" name="observaciones1" id="observaciones1" onblur="sendAndLoad({desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_comedor_estado_alumnos'},{desc:'campo',dato:'observaciones1'},{desc:'valor',dato:document.getElementById('observaciones1').value});"><?=utf8_decode($observaciones_evaluacion_guardada1)?></textarea>                 
                 <h4>Observaciones 2º Trimestre</h4>
                 <textarea class="form-control" rows="3" name="observaciones" id="observaciones" onblur="sendAndLoad({desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_comedor_estado_alumnos'},{desc:'campo',dato:'observaciones'},{desc:'valor',dato:document.getElementById('observaciones').value});"><?=utf8_decode($observaciones_evaluacion_guardada)?></textarea>
                 <h4>Observaciones 3<sup>er</sup> Trimestre</h4>
                 <textarea class="form-control" rows="3" name="observaciones2" id="observaciones2" onblur="sendAndLoad({desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_comedor_estado_alumnos'},{desc:'campo',dato:'observaciones2'},{desc:'valor',dato:document.getElementById('observaciones2').value});"><?=utf8_decode($observaciones_evaluacion_guardada2)?></textarea>                   
                 <h4>Estado de la evaluación</h4>
                 <select name="visibilidad" id="visibilidad" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_comedor_estado_alumnos'},{desc:'campo',dato:'estado'},{desc:'valor',dato:document.getElementById('visibilidad').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada==0) { print " selected"; } ?>>NO FINALIZADA</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada==1) { print " selected"; } ?>>FINALIZADA</option>
                                        </select>
                </div>                 
                <a type="button" target='_blank' href="/pdf/evaluacion_comedor.php?id_alumno=<?=$id_alumno?>&trimestre=1" class="btn btn-primary" style="margin-bottom: 5px;">Vista previa 1er Trimestre</a> <br>
				<a type="button" target='_blank' href="/pdf/evaluacion_comedor.php?id_alumno=<?=$id_alumno?>&trimestre=2" class="btn btn-primary" style="margin-bottom: 5px;">Vista previa 2º Trimestre</a> <br>
				<a type="button" target='_blank' href="/pdf/evaluacion_comedor.php?id_alumno=<?=$id_alumno?>&trimestre=3" class="btn btn-primary" style="margin-bottom: 5px;">Vista previa 3er. Trimestre</a> <br>
				<button type="button" onclick="window.opener.history.go(0);window.close();" class="btn btn-danger" style="margin-bottom: 5px;">Salir</button>
    </form>
</div>
</div>    
     
</body>
</html>
