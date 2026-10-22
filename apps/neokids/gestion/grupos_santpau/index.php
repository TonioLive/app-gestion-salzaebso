<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$error_al_quitar=$_GET["error_al_quitar"];

$total_grupos=0;
$total_grupos_monitor=0;

$tipo_piscina=1;

$filtro_monitores=$_GET["monitor"];
if (strlen($filtro_monitores)==0) {
        $filtro_monitores=0;
        }

$nombres_centros=array();
$nombres_aulas=array();

$fecha_time_stamp=$_GET["fecha"];
if (strlen($fecha_time_stamp)==0) {
        $fecha_time_stamp=time();
        }

// Inicio y fin de semana
$diasemana=date("w",$fecha_time_stamp);
$dia_inicio_semana=$fecha_time_stamp-(($diasemana-1)*86400);

$estado_aulas=array();

$dias_maximo=6;

$mes=date("n",$fecha_time_stamp);
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

$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-01");
$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-01");

// busca curso actual
// Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM escuelamunicipal.cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }

        $curso_seleccionado_nk=$_GET["curso_seleccionado_nk"];
        if (strlen($curso_seleccionado_nk)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual_nk=db_fetch_array($busca_curso_actual);
                $id_curso_actual_neokids=$datos_curso_actual_nk["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual_nk=db_fetch_array($busca_curso_actual);
                        $id_curso_actual_neokids=$datos_curso_actual_nk["id_curso"];
                        } else {
                        $id_curso_actual_neokids=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado_nk");
        $id_curso_actual_neokids=$curso_seleccionado;
        $datos_curso_actual_nk=db_fetch_array($busca_curso_actual);
        }

$num_dias_asisten_externos=$datos_curso_actual_nk["num_dias_asisten_externos"];

// busca grupos

$busca_grupos=db_query_params('SELECT * FROM aulas_grupos WHERE id_curso=? ORDER BY dia,hora_inicio', 's', array($id_curso_actual_neokids));

$lista_cuadros_grupos0="";
$lista_cuadros_grupos1="";
$lista_cuadros_grupos2="";
$lista_cuadros_grupos3="";

$lista_li_grupos0="";
$lista_li_grupos1="";
$lista_li_grupos2="";
$lista_li_grupos3="";

while($grupos=db_fetch_array($busca_grupos)) {
        $lista_cuadros_grupos0.="#grupo_0_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos1.="#grupo_1_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos2.="#grupo_2_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos3.="#grupo_3_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos4.="#grupo_4_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos5.="#grupo_5_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos6.="#grupo_6_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos7.="#grupo_7_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos8.="#grupo_8_".$grupos["id_grupo"].", ";

        $lista_li_grupos0.="#grupo_0_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos1.="#grupo_1_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos2.="#grupo_2_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos3.="#grupo_3_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos4.="#grupo_4_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos5.="#grupo_5_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos6.="#grupo_6_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos7.="#grupo_7_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos8.="#grupo_8_".$grupos["id_grupo"]." li, ";
        }

if (strlen($lista_cuadros_grupos1)!=0) {
        $lista_cuadros_grupos0=substr($lista_cuadros_grupos0,0,strlen($lista_cuadros_grupos0)-2);
        $lista_cuadros_grupos1=substr($lista_cuadros_grupos1,0,strlen($lista_cuadros_grupos1)-2);
        $lista_cuadros_grupos2=substr($lista_cuadros_grupos2,0,strlen($lista_cuadros_grupos2)-2);
        $lista_cuadros_grupos3=substr($lista_cuadros_grupos3,0,strlen($lista_cuadros_grupos3)-2);
        $lista_cuadros_grupos4=substr($lista_cuadros_grupos4,0,strlen($lista_cuadros_grupos4)-2);
        $lista_cuadros_grupos5=substr($lista_cuadros_grupos5,0,strlen($lista_cuadros_grupos5)-2);
        $lista_cuadros_grupos6=substr($lista_cuadros_grupos6,0,strlen($lista_cuadros_grupos6)-2);
        $lista_cuadros_grupos7=substr($lista_cuadros_grupos7,0,strlen($lista_cuadros_grupos7)-2);
        $lista_cuadros_grupos8=substr($lista_cuadros_grupos8,0,strlen($lista_cuadros_grupos8)-2);

        $lista_li_grupos0=substr($lista_li_grupos0,0,strlen($lista_li_grupos0)-2);
        $lista_li_grupos1=substr($lista_li_grupos1,0,strlen($lista_li_grupos1)-2);
        $lista_li_grupos2=substr($lista_li_grupos2,0,strlen($lista_li_grupos2)-2);
        $lista_li_grupos3=substr($lista_li_grupos3,0,strlen($lista_li_grupos3)-2);
        $lista_li_grupos4=substr($lista_li_grupos4,0,strlen($lista_li_grupos4)-2);
        $lista_li_grupos5=substr($lista_li_grupos5,0,strlen($lista_li_grupos5)-2);
        $lista_li_grupos6=substr($lista_li_grupos6,0,strlen($lista_li_grupos6)-2);
        $lista_li_grupos7=substr($lista_li_grupos7,0,strlen($lista_li_grupos7)-2);
        $lista_li_grupos8=substr($lista_li_grupos8,0,strlen($lista_li_grupos8)-2);
        }

$contador_ocupacion=0;
$contador_no_asignados=0;

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Horario Grupos</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
<?php 

$busca_centros=db_query("SELECT * FROM escuelamunicipal.centros WHERE piscina=$tipo_piscina ORDER BY id_centro") or die(db_error());


while($lista_centros=db_fetch_array($busca_centros)) {

$busca_aulas=db_query_params('SELECT * FROM escuelamunicipal.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual, $lista_centros["id_centro"]));

while($lista_aulas=db_fetch_array($busca_aulas)) {


$busca_alumnos=db_query_params('SELECT * FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND escuelamunicipal.alumnos.aula=? AND escuelamunicipal.alumnos.fecha_matricula<=? AND escuelamunicipal.alumnos.fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($lista_aulas["id_aula"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

$total_alumnos_nuevos=0;

if (db_num_rows($busca_alumnos)!=0) {


while($alumnos=db_fetch_array($busca_alumnos)) {

        // Comprueba si está asignado

        $busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_santpau_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual_neokids));

if (db_num_rows($busca_grupo_alumno)!=0) {

        $contador_ocupacion=$contador_ocupacion+$num_dias_asisten_externos;

        } else {

        $contador_no_asignados=$contador_no_asignados+$num_dias_asisten_externos;

        }

        if (db_num_rows($busca_grupo_alumno)<$num_dias_asisten_externos) {
        $total_alumnos_nuevos++;
        }
}

$estado_aulas[$lista_aulas["id_aula"]]=$total_alumnos_nuevos;

if ($total_alumnos_nuevos!=0) {

?>
#lista_alumnos<?=$lista_aulas["id_aula"]?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 155px; }
#lista_alumnos<?=$lista_aulas["id_aula"]?> li{ margin: 2px; padding: 2px; font-size: 0.8em; }
<?php 
                        }
                }
        }
}

?>

<?=$lista_cuadros_grupos0?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos1?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos2?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos3?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos4?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos5?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos6?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos7?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos8?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }

#lista_alumnos0 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos1 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos2 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos3 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

<?=$lista_li_grupos0;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos1;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos2;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos3;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos4;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos5;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos6;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos7;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos8;?> { margin: 2px; padding: 2px; font-size: 0.8em; }

.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        }

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}

</style>
<script>
$(function() {
$( "ul.droptrue" ).sortable({
connectWith: "ul"
});
$( "ul.dropfalse" ).sortable({
connectWith: "ul",
dropOnEmpty: false
});

$( "alumno1" ).attr( "id_alumno", "123" );

$( "#lista_alumnos1, #lista_alumnos2, #lista_alumnos3, <?=$lista_cuadros_grupos0?>, <?=$lista_cuadros_grupos1?>, <?=$lista_cuadros_grupos2?>, <?=$lista_cuadros_grupos3?>, <?=$lista_cuadros_grupos4?>, <?=$lista_cuadros_grupos5?>, <?=$lista_cuadros_grupos6?>, <?=$lista_cuadros_grupos7?>, <?=$lista_cuadros_grupos8?>" ).disableSelection();

$( "#lista_alumnos1, #lista_alumnos2, #lista_alumnos3, <?=$lista_cuadros_grupos0?>, <?=$lista_cuadros_grupos1?>, <?=$lista_cuadros_grupos2?>, <?=$lista_cuadros_grupos3?>, <?=$lista_cuadros_grupos4?>, <?=$lista_cuadros_grupos5?>, <?=$lista_cuadros_grupos6?>, <?=$lista_cuadros_grupos7?>, <?=$lista_cuadros_grupos8?>" ).droppable({
drop: function( event, ui ) {
if (confirm("¿Quieres cambiar al alumno de grupo?")) {
        cambia_grupo(event.target.id,ui.draggable[0].id);
        } else {
        history.go(0);
        }
}
});

});

var pagina_destino="";

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

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);
        history.go(0);

    }
}


function cambia_grupo(grupo,alumno) {
                pagina_destino="/gestion/grupos_lapices_nuevo/cambio_grupo.php";
                sendAndLoad({desc:'grupo',dato:grupo},{desc:'alumno',dato:alumno},{desc:'id_curso_grupo',dato:<?=$id_curso_actual_neokids?>});
                // window.open(pagina_destino+"?grupo="+grupo+"&alumno="+alumno+"&id_curso_grupo=<?=$id_curso_actual?>");

        }

function nuevo_subgrupo(id_grupo,nombre_mostrar) {
        if (confirm("¿Quieres añadir un grupo nuevo al horario "+nombre_mostrar+"?")) {
                window.location.href='nuevo_subgrupo.php?id_grupo='+id_grupo;
                }
        }

function quitar_subgrupo(id_grupo,id_subgrupo,nombre_mostrar) {
        if (confirm("¿Quieres quitar el grupo "+(Number(id_subgrupo)+1)+" del horario "+nombre_mostrar+"?")) {
                window.location.href='quita_subgrupo.php?id_grupo='+id_grupo;
                }
        }

function gestion_monitores(id_grupo,id_subgrupo,nombre_mostrar,fecha) {
        window.open("gestion_monitores.php?fecha="+fecha+"&id_grupo="+id_grupo+"&id_subgrupo="+id_subgrupo+"&nombre_mostrar="+nombre_mostrar,"edicion_monitores","toolbars=no,scrollbars=yes,width=600,height=550");
        }

function muestra_grupo(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite) {

        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">' + nombre_subgrupo + '</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'detalles_grupo.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_limite);

        }

function agrega_grupo(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite) {

        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Asignar alumnos al ' + nombre_subgrupo + '</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'asignar_a_grupo.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_limite);

        }

function recargar() {

        history.go(0);

}

<?php 
if ($error_al_quitar) {
?>
alert('<?=utf8_decode($error_al_quitar)?>');
<?php 
}
?>
</script>
</head>
<body>
<div class="container-fluid">
<div class="row">
  <div class="col-md-2" data-offset-top="60" data-offset-bottom="200">
  <h4>Alumnos sin asignar</h4>
  <div class="panel-group" id="accordion">
<?php 
// Inicio bucle aulas

$inicial_in=" in";

$busca_centros=db_query("SELECT * FROM escuelamunicipal.centros WHERE piscina=$tipo_piscina ORDER BY id_centro");

while($lista_centros=db_fetch_array($busca_centros)) {

$busca_aulas=db_query_params('SELECT * FROM escuelamunicipal.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual, $lista_centros["id_centro"]));

$nombres_centros[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];

while($lista_aulas=db_fetch_array($busca_aulas)) {

$nombres_aulas[$lista_aulas["id_aula"]]=$lista_aulas["nombre"];

$busca_alumnos=db_query_params('SELECT * FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND escuelamunicipal.alumnos.aula=? AND escuelamunicipal.alumnos.fecha_matricula<=? AND escuelamunicipal.alumnos.fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($lista_aulas["id_aula"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

if ($estado_aulas[$lista_aulas["id_aula"]]!=0) {

if (db_num_rows($busca_alumnos)!=0) {

?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$lista_aulas["id_aula"]?>">
          <small><?=$lista_aulas["nombre"]?> - <?=$lista_centros["nombre_centro"]?></small>
        </a>
      </h4>
    </div>
    <div id="collapse<?=$lista_aulas["id_aula"]?>" class="panel-collapse collapse<?=$inicial_in?>">
      <div class="panel-body">
<ul id="lista_alumnos<?=$lista_aulas["id_aula"]?>" class="droptrue">
<li class="dropfalse"><?=$lista_aulas["nombre"]?></li>
<?php 

while($alumnos=db_fetch_array($busca_alumnos)) {
$inicial_in="";
// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_santpau_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual_neokids));

if (db_num_rows($busca_grupo_alumno)<$num_dias_asisten_externos) {

$dias_faltan=$num_dias_asisten_externos-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }
        
        $swimming="";
        
        if ($alumnos["swiming"]==1) {
        
        	$swimming="<span class='label label-danger'>SWIMMING</span> ";
        	
        }
        

?>
<li class="ui-state-default" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$swimming.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)<br> <?=$lista_aulas["nombre"]?> - <?=$lista_centros["nombre_centro"]?></li>
<?php 
                }
        }
}
?>
</ul>
        </div>
    </div>
  </div>
<?php 
                        }
                }
        }
}

// Fin bucle aulas
?>
<br />
<form method="get" action="<?=$_SERVER["PHP_SELF"];?>" name="monitores">
<input type="hidden" name="fecha" value="<?=$fecha_time_stamp?>">
  <label for="monitor">Filtro por Monitor/a:</label>
        <select name="monitor" class="form-control" onchange="document.monitores.submit();"><option value="0">Todos</option>
                                                            <?php 
                                $busca_docentes=db_query("SELECT * FROM aulas_grupos_monitores,usuarios WHERE aulas_grupos_monitores.id_usuario=usuarios.id_usuario GROUP BY aulas_grupos_monitores.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
                                $nombre_monitor_seleccionado="";
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$filtro_monitores) { print " selected"; $nombre_monitor_seleccionado="<h4>Grupos para el monitor/a: ".$datos_docentes["Nombre"]." ".$datos_docentes["Apellido1"]."</h4>"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?></option>
                                <?php 
                                }
                                ?>
        </select>
    </form>
<br />
<h5>Ocupación actual: <?=$contador_ocupacion;?></h5>
<h5>No asignados a grupos: <?=$contador_no_asignados;?></h5>
<?php 
// Cálculo mes anterior y posterios

$mes=date("n",$dia_inicio_semana);

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
$dia_semana_actual=date("j",$dia_inicio_semana);
$dia_semana_anterior=$dia_semana_actual;
$dia_semana_siguiente=$dia_semana_actual;

if ($dia_semana_anterior>date("t",strtotime($ano_anterior."-".$mes_anterior."-01"))) {
        $dia_semana_anterior=date("t",strtotime($ano_anterior."-".$mes_anterior."-01"));
        }
if ($dia_semana_siguiente>date("t",strtotime($ano_siguiente."-".$mes_siguiente."-01"))) {
        $dia_semana_siguiente=date("t",strtotime($ano_siguiente."-".$mes_siguiente."-01"));
        }

$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-".$dia_semana_anterior);
$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-".$dia_semana_siguiente);

?>
</div>
  </div>
  <div class="col-md-2">
  &nbsp;
  </div>
  <div class="col-md-10">
     <table class="table">
     <tr>
        <td colspan="2"><a class="btn btn-primary" href="index.php?fecha=<?=$dia_inicio_semana-(7*86400);?>"><i class="fa fa-angle-left"></i> Semana Anterior</a> <a class="btn btn-primary" href="index.php?fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a></td>
        <td colspan="2" align="center"><?=$nombre_monitor_seleccionado?><h4>Semana del <?=date("d/m/Y",$dia_inicio_semana);?> al <?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></h4></td>
                <td colspan="2" align="right"><a class="btn btn-primary" href="index.php?fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a> <a class="btn btn-primary" href="index.php?fecha=<?=$dia_inicio_semana+(7*86400);?>">Semana Siguiente <i class="fa fa-angle-right"></i></a></td>
     </tr>
     <tr>
        <td>Lunes<br><?=date("d/m/Y",$dia_inicio_semana);?></td>
        <td>Martes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*1));?></td>
        <td>Miercoles<br><?=date("d/m/Y",$dia_inicio_semana+(86400*2));?></td>
        <td>Jueves<br><?=date("d/m/Y",$dia_inicio_semana+(86400*3));?></td>
        <td>Viernes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*4));?></td>
        <td>Sábado<br><?=date("d/m/Y",$dia_inicio_semana+(86400*5));?></td>
     </tr>
     <?php 
     $busca_turnos=db_query_params('SELECT hora_inicio,hora_fin FROM aulas_grupos WHERE dia=1 AND id_curso=? ORDER BY hora_inicio', 's', array($id_curso_actual_neokids));

         $contador_grupos=0;

     //while($turnos=mysql_fetch_array($busca_turnos))
     for ($turnos=1;$turnos<=24;$turnos++)
     {
     $contador_grupos++;
     ?>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {

     $busca_hora_inicio=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual_neokids AND dia=".$c_dia);

     if (db_num_rows($busca_hora_inicio)!=0) {

            $datos_grupo_inicio=db_fetch_array($busca_hora_inicio);

            $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo_inicio["hora_fin"]));

             ?>
             <td><a href="/gestion/agenda/dia_aula.php?id_grupo=<?=$datos_grupo_inicio["id_grupo"]?>&dia=<?=($dia_inicio_semana+(86400*($c_dia-1)))?>" target="_blank"><?=date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_grupo_inicio["hora_fin"]))?> </a> <button class="btn btn-primary btn-xs" onclick="nuevo_subgrupo(<?=$datos_grupo_inicio["id_grupo"]?>,'<?=$nombre_horario_mostrado?>')"><i class="fa fa-plus"></i></button></td>
             <?php 
             } else {
             ?>
             <td>&nbsp;</td>
             <?php 
             }

     }
     ?>
     </td>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {

     $busca_grupos=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual_neokids AND dia=".$c_dia);

     if (db_num_rows($busca_grupos)!=0) {

     $datos_grupo=db_fetch_array($busca_grupos);

     // Comprueba si es festivo
     $fecha_dia_sel=$dia_inicio_semana+(($c_dia-1)*86400);

     $busca_festivos=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1', 's', array((string)(date("Y-m-d", $fecha_dia_sel))));

 	 $color_fondo="#fff"; 

     if (db_num_rows($busca_festivos)!=0) {
     // es festivo
     $color_fondo="#ffcccc";
     }

     ?>
         <td bgcolor="<?=$color_fondo?>">
         <table class="table">
         <?php 

         $num_subgrupos=$datos_grupo["num_subgrupos"];

         for ($c_subgrupos=0;$c_subgrupos<$num_subgrupos;$c_subgrupos++) {

         $total_grupos++;

         $nivel_actual=$c_subgrupos;

         $busca_alumnos_grupo=db_query("SELECT * FROM escuelamunicipal.usuarios,aulas_alumnos_santpau_grupos,escuelamunicipal.alumnos, escuelamunicipal.centros WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_santpau_grupos.id_alumno=escuelamunicipal.alumnos.id_alumno AND escuelamunicipal.alumnos.id_centro=escuelamunicipal.centros.id_centro AND escuelamunicipal.centros.piscina=$tipo_piscina AND aulas_alumnos_santpau_grupos.id_curso=$id_curso_actual_neokids AND aulas_alumnos_santpau_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_santpau_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND escuelamunicipal.alumnos.fecha_matricula<='".date("Y-m-d",$fecha_dia_sel)."' AND escuelamunicipal.alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."' ORDER BY escuelamunicipal.usuarios.Apellido1, escuelamunicipal.usuarios.Apellido2, escuelamunicipal.usuarios.Nombre") or die(db_error());

         $num_alumnos_subgrupo=db_num_rows($busca_alumnos_grupo);

                // Busca alumnos grupo en el otro horario
         $busca_alumnos_grupo_int=db_query("SELECT id_grupo FROM aulas_alumnos_grupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual");

         $num_alumnos_subgrupo_int=db_num_rows($busca_alumnos_grupo_int);

         $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));

         // Busca monitores
         $lista_monitores_grupo="";
         $busca_monitores=db_query("SELECT * FROM aulas_grupos_monitores,usuarios WHERE aulas_grupos_monitores.id_usuario=usuarios.id_usuario AND aulas_grupos_monitores.id_grupo=".$datos_grupo["id_grupo"]." AND aulas_grupos_monitores.id_subgrupo=$c_subgrupos") or die(db_error());

                 $mostrar_grupo=false;
                                if (db_num_rows($busca_monitores)!=0) {
                                        $lista_monitores_grupo="<b>Monitor/a</b>";
                                        while($lista_monitores=db_fetch_array($busca_monitores)) {
                                                if ($lista_monitores["id_usuario"]!=0) {
                                                        $lista_monitores_grupo.="<br />".$lista_monitores["Nombre"]." ".$lista_monitores["Apellido1"];
                                                        if ($filtro_monitores==$lista_monitores["id_usuario"]) {
                                                                $mostrar_grupo=true;
                                                                }
                                                        }
                                                }
                                        }
                                if ((strlen($filtro_monitores)==0) or ($filtro_monitores==0)) {
                                        $mostrar_grupo=true;
                                        }
                  if ($mostrar_grupo==true) {
                  $total_grupos_monitor++;

                                  $nombre_subgrupo=$nombre_horario_mostrado.", Grupo: ".($c_subgrupos+1);
          ?>
         <tr>
         <td>
                <ul id="grupo_<?=$nivel_actual?>_<?=$datos_grupo["id_grupo"]?>" class="droptrue">
                <li class="dropfalse"><button class="btn btn-primary btn-xs" onclick="gestion_monitores(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_horario_mostrado?>','<?=$fecha_dia_sel?>')"><i class="fa fa-graduation-cap"></i></button> <button class="btn btn-primary btn-xs" onclick="agrega_grupo(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_subgrupo?>','<?=date("Y-m-d",$fecha_dia_sel-$tiempo_gracia);?>')" title="Añadir alumnos al grupo"> <i class="fa fa-user-plus"></i></button> <button onclick="muestra_grupo(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_subgrupo?>','<?=date("Y-m-d",$fecha_dia_sel-$tiempo_gracia);?>')" title="Mostrar detalles Grupo" class="btn btn-info btn-xs">Gr. <?=$c_subgrupos+1?></button> <?php  if (($num_alumnos_subgrupo==0) and ($num_alumnos_subgrupo_int==0) and ($c_subgrupos==($num_subgrupos-1))) { ?> <button class="btn btn-primary btn-xs" onclick="quitar_subgrupo(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_horario_mostrado?>')"><i class="fa fa-minus"></i></button><?php  } ?></li>
                <?php 

                while($alumnos=db_fetch_array($busca_alumnos_grupo)) {

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
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }

				$swimming="";
        
        		if ($alumnos["swiming"]==1) {
        
        			$swimming="<span class='label label-danger'>SWIMMING</span> ";
        	
        		}

                ?>
                <li class="ui-state-default fondo_nivel<?=$alumnos["nivel_actual"]?>" id="alumno_<?=$alumnos["id_alumno"]?>_<?=$alumnos["id_alumno_grupo"]?>"><?=$swimming.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?> (<?=$edad_alumno;?>)</li>
                <?php 
                        }
                ?>
                <li><?=$lista_monitores_grupo;?></li>
                </ul>
         </td>
         </tr>
                <?php 
                }
         }
         ?>
         </table>
         </td>
     <?php 
             } else {
             // En blanco
             ?>
             <td>&nbsp;</td>
             <?php 
             }
     }
     ?>
     </tr>
     <?php 
     }
     ?>
     </table>
<?php 
if (($total_grupos_monitor==0) and ($total_grupos!=0)) {
        ?>
<script>
alert("El monitor seleccionado ya no tiene grupos asignados");
window.location.href="<?=$_SERVER["PHP_SELF"];?>?fecha=<?=$fecha_time_stamp?>&monitor=0";
</script>
        <?php 
        } else {
        if ($filtro_monitores!=0) {
        ?><h4>Total grupos asignados: <?=$total_grupos_monitor?></h4><?php 
                }
        }
?>
  </div>
</div>
</div>

<!-- Modal -->
<div class="modal modal-wide fade" id="grupoModal" tabindex="-1" role="dialog" aria-labelledby="grupoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="grupoModalLabel">Nombre Grupo</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="detalles_grupo.php" name="detalles_grupo" id="detalles_grupo" width=100% height=450 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info pull-left" onclick="recargar();"><i class="fa fa-refresh"></i> Recargar listado de grupos * </button> <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button><br /><div class="pull-left"><small>* Pulsa este botón si has asigando alumnos a los grupos y quieres ver como han quedado distribuidos.</small></div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
