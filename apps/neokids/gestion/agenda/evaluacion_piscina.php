<?php  require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

$lista_alumnos=$_GET["lista_alumnos"]; 
$mostrar_logo=$_GET["logo"];

$origen_alumnos=0;
if (substr($lista_alumnos,0,1)=="L") {
        $origen_alumnos=1;
        $lista_alumnos=str_replace("L","",$lista_alumnos);                
}
        
$lista_alumnos_array=explode(",",$lista_alumnos);
if (count($lista_alumnos_array)==1) {
        $id_alumno=$lista_alumnos_array[0];
}
        
if (strlen($id_alumno)==0) {
        $id_alumno=$_GET["id_alumno"];
}

$base_datos_alumnos="";
$campo_periodo="periodo";
$valor_campo_periodo=$periodo;
$tabla_grupos_alumnos="aulas_alumnos_grupos";
if ($origen_alumnos==1) {
        $base_datos_alumnos="gestionguarderias.";
        $campo_periodo="trimestre";
        $valor_campo_periodo=$trimestre;
        $tabla_grupos_alumnos="aulas_alumnos_lapices_grupos";
}

// Busca objetivos generales
$nombre_objetivos_generales=array();
$niveles_objetivos_generales=array();
$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
                 
while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
        $nombre_objetivos_generales[$objetivos_generales["id_objetivo"]]=$objetivos_generales["nombre_objetivo"];
        $niveles_objetivos_generales[$objetivos_generales["id_objetivo"]]=$objetivos_generales["nivel"];
}

error_reporting(7);

if ($id_alumno) {
        // Uno solo
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=".$id_alumno.";") or die(db_error()."-1");
} else {             
        // Muchos   
        $filtro_alumnos="";
        foreach ($lista_alumnos_array as $valor) {
            $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";                
        }
        if (strlen($filtro_alumnos)!=0) {
                $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-2");                           
}

$contador_alumnos=0;
$total_alumno=db_num_rows($busca_datos_alumno);

if (db_num_rows($busca_datos_alumno)!=0) {

        while($listado_alumnos=db_fetch_array($busca_datos_alumno)){
                
                if ($origen_alumnos==0) {
                        // $anotacion_nivel=" en el nivel ".$nivel_actual;
                }
                
                $id_alumno=$listado_alumnos["id_alumno"];
                                
                // Busca envaluacion guardada
                                                                
                $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=$id_alumno AND $campo_periodo=$valor_campo_periodo;") or die(db_error());                

                $datos_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
                
                if ($datos_evaluacion_guardada["estado"]==1) {                
        
                        if ($contador_alumnos>0) {
                                $pdf->ezNewPage();
                        }                
                        
                        $contador_alumnos++;                        
                        
                        $id_aula=$listado_alumnos["aula"];
                        $id_curso_aula=$listado_alumnos["id_curso"];
                        $nivel_actual=$listado_alumnos["nivel_actual"];


                        // Fecha de incorporación
                        $fecha_incorporacion=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));
                        if (strtotime($listado_alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                                $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]))."</span>";
            }
                        $fecha_baja=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));
                        if (strtotime($listado_alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                    $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))."</span>";
                }
                        if ($listado_alumnos["nonato"]==1) {
                    $edad_alumno="";
                                $fecha_nac_alumno="No ha nacido";
                } else {
                                $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                        $edad_alumno=$edad_alumno-1;
                }
                                if ($edad_alumno<2) {
                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos["fecha_nacimiento"];
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
                                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                }
                
                        $lista_grupos_alumno="";
        
                        $busca_alumnos_grupo=db_query("SELECT * FROM ".$base_datos_alumnos."usuarios,$tabla_grupos_alumnos,aulas_grupos,".$base_datos_alumnos."alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $tabla_grupos_alumnos.id_alumno=alumnos.id_alumno AND $tabla_grupos_alumnos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=".$id_alumno) or die(db_error());
                        $total_grupos=db_num_rows($busca_alumnos_grupo);
                        $contador_grupos=0;
                        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                                $lista_grupos_alumno.=" los ".$dia_sem_largo_plural[$grupos_donde["dia"]];
                                $contador_grupos++;
                                if ($contador_grupos<$total_grupos) {
                                        if ($contador_grupos<($total_grupos-1)) {
                                                $lista_grupos_alumno.=", ";                                        
                                        } else {
                                                $lista_grupos_alumno.=" y ";
                                        }
                                }
                        }
                                       

                        $fechas_inicio_fin=$nombre_mes[date("n",strtotime($listado_alumnos["fecha_matricula"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_matricula"]))." a ".$nombre_mes[date("n",strtotime($listado_alumnos["fecha_baja"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_baja"]));

                        // Busca objetivos trabajados
                        $consulta_objetivos_trabajados=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_evaluaciones_trimestrales.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo WHERE agenda_programacion_evaluaciones_trimestrales.$campo_periodo=$valor_campo_periodo AND agenda_programacion_evaluaciones_trimestrales.id_alumno=".$id_alumno." ORDER BY agenda_programacion_objetivos_piscina.id_objetivo_g, agenda_programacion_evaluaciones_trimestrales.id_objetivo;") or die(db_error());                


?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Evaluación Alumno: <?=$listado_alumnos["Nombre"]?> <?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"];?></title>

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
                <div class="panel-heading">
                        <h3>Evaluación del alumno <?=$listado_alumnos["Nombre"]?> <?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"];?></h3>
                        <h4>Periodo evaluado: Del <?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]))?> al <?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))?></h4>
                </div>
                <div class="panel-body">
<?php 
                        $objetivo_general_actual=0;

                        while($busca_objetivos_trabajados=db_fetch_array($consulta_objetivos_trabajados)) {

                                if ($busca_objetivos_trabajados["nota"]!=0) { // Solo los trabajados
                
                                        if ($busca_objetivos_trabajados["nota"]==1) {
                                                $calificacion="SUPERADO";
                                       } else {
                                                   $calificacion="TRABAJADO";
                                      } 
                                                                                               
                                          if ($objetivo_general_actual!=$busca_objetivos_trabajados["id_objetivo_g"]) {
                                                  $objetivo_general_actual=$busca_objetivos_trabajados["id_objetivo_g"];
                                                  echo("<h4 class='".$colores_niveles[$niveles_objetivos_generales[$objetivo_general_actual]]."'>".$nombre_objetivos_generales[$objetivo_general_actual]."</h4>");
                                                  $posicion_actual=$posicion_actual-14;
                                        }

                                        echo("<span class='".$colores_niveles[$busca_objetivos_trabajados["nivel"]]."'><span class='badge'><b>".$calificacion."</b></span> ".$busca_objetivos_trabajados["nombre_objetivo"]."<br /></span>");


                                } // Fin if nota!=0
                        } // Fin bucle
                } // Fin if evaluacion guardada estado=1
        }        // Fin bucle alumnos
}        // Fin if hay alumnos

?>
                </div>
        </div>
</div>

</body>
</html>
