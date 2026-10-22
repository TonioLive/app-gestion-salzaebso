<?php 
function microtime_float()
{
list($useg, $seg) = explode(" ", microtime());
return ((float)$useg + (float)$seg);
}

$tiempo_inicio = microtime_float();

require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$mostrar_niveles_futuros=$_GET["subniveles"];
$curso_seleccionado=$_GET["curso_seleccionado"];
$mostrar_lista_espera=$_GET["lista_espera"];

$fecha_listado_bebes="";  
if (strlen($curso_seleccionado)!=0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];        
        
        $fecha_time_stamp=strtotime($datos_curso_actual["fecha_inicio"]);
        $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];

        if ($fecha_time_stamp<time()) {
                // Si el curso está iniciado vamos al día actual
                $fecha_time_stamp=time();
        }       
        
        if (strtotime($datos_curso_actual["fecha_fin"])<time()) {
                        // Si el curso está finalizado vamos a la fecha de finalización
                        $fecha_time_stamp=strtotime($datos_curso_actual["fecha_fin"]);
        }
        
} else {

    $fecha_time_stamp=$_GET["fecha"];
        if (strlen($fecha_time_stamp)==0) {
            $fecha_time_stamp=time();      
    }
    $fecha_listado_bebes=$fecha_time_stamp;  
} 
        
$error_al_quitar=$_GET["error_al_quitar"];

$array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");
$colores_listas_de_espera=array("#fff","#9ed09c","#9cbdd0","#d0af9c","#f9a63b","#ffa9f0");

$total_grupos=0;
$total_grupos_monitor=0;

$total_por_tipo=array();

$filtro_monitores=$_GET["monitor"];
if (strlen($filtro_monitores)==0) {
        $filtro_monitores=0;
        }        

// Inicio y fin de semana
$diasemana=date("w",$fecha_time_stamp);
$dia_inicio_semana=$fecha_time_stamp-(($diasemana-1)*86400);

$dias_maximo=7;

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

// Busca el curso actual

        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        $nombre_curso_actual=$datos_curso_actual["nombre"];
                        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                        $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        }

if ((strtotime($fecha_inicio_curso_actual)>time()) and (strlen($_GET["subniveles"])==0)) {
        
	// Si el curso no está iniciado muestra siempre los niveles futuros
	$mostrar_niveles_futuros=1;
        
	}

// Provisional!!!!
$mostrar_niveles_futuros=1;

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
        
// Busca nuevo curso
$busca_nuevo_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso<>? AND fecha_inicio>=? AND month(fecha_inicio)!=7', 'ss', array($id_curso_actual, (string)(date("Y", strtotime($fecha_fin_curso_actual))) . '-08-31'));

if (db_num_rows($busca_nuevo_curso)!=0) {

		$datos_nuevo_curso=db_fetch_array($busca_nuevo_curso);
        $nombre_curso_siguente=$datos_nuevo_curso["nombre"];
        $id_nuevo_curso=$datos_nuevo_curso["id_curso"];

}        

// busca grupos

$busca_numero_maximo_de_grupos=db_query_params('SELECT * FROM aulas_grupos WHERE id_curso=? ORDER BY orden DESC LIMIT 0,1', 's', array($id_curso_actual));
if (db_num_rows($busca_numero_maximo_de_grupos)!=0) {
	
	$datos_num_grupos=db_fetch_array($busca_numero_maximo_de_grupos);
	$num_max_grupo=$datos_num_grupos["orden"];

} else {
	$num_max_grupo=24;
}

$busca_grupos=db_query_params('SELECT * FROM aulas_grupos WHERE id_curso=? ORDER BY dia,hora_inicio', 's', array($id_curso_actual));

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
$contador_ocupacion_prox=0;
$contador_matriculados=0;
$contador_matriculados_prox=0;

$capacidad_total=0;
$matriculados_total=0;

$matriculados_por_tipos=array();
$capacidad_por_tipos=array();

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
#lista_alumnos0 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
#lista_alumnos1 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #9ed09c; padding: 5px; width: 123px; }
#lista_alumnos2 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #9cbdd0; padding: 5px; width: 123px; }
#lista_alumnos3 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #d0af9c; padding: 5px; width: 123px; }
#lista_alumnos4 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #f9a63b; padding: 5px; width: 123px; }
#lista_alumnos5 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #ffa9f0; padding: 5px; width: 123px; }

.lista_espera { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #fcffd4; padding: 5px; width: 123px; }
.lista_espera li{ margin: 2px; padding: 2px; font-size: 0.8em; }
                                     

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

#lista_alumnos4 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos5 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

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
.fondo_nivel4 {
        background: #fff;
        } 
.fondo_nivel5 {
        background: #fff;
        }   
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                }  
                
                     
<?php 
foreach ($colores_subniveles as $indice => $color) {
?>

.fondo_subnivel<?=$indice?> {
background: <?=$color?>;
}

<?php 
}

$colores_subgrupos=array();

$busca_tipos_grupos=db_query("SELECT * FROM aulas_grupos_tipos;");
while($datos_grupos=db_fetch_array($busca_tipos_grupos)) {
        $colores_subgrupos[$datos_grupos["id_tipo_grupo"]]=$datos_grupos["color_tipo_subgrupo"];
?>
.fondo_tipo_grupo<?=$datos_grupos["id_tipo_grupo"]?> {
        background: <?=$datos_grupos["color_tipo_subgrupo"]?> ;
        }
<?php 
}

for ($c_anios=0;$c_anios<count($colores_listas_de_espera);$c_anios++) {
?>
.fondo_espera<?=$c_anios?> {
        background: <?=$colores_listas_de_espera[$c_anios];?> ;
        }
<?php 
        }
?>   

.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
 
.tachado {
	text-decoration:line-through;
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

$( "#lista_alumnos1, #lista_alumnos2, #lista_alumnos3, #lista_alumnos4, #lista_alumnos5, <?=$lista_cuadros_grupos0?>, <?=$lista_cuadros_grupos1?>, <?=$lista_cuadros_grupos2?>, <?=$lista_cuadros_grupos3?>, <?=$lista_cuadros_grupos4?>, <?=$lista_cuadros_grupos5?>, <?=$lista_cuadros_grupos6?>, <?=$lista_cuadros_grupos7?>, <?=$lista_cuadros_grupos8?>" ).disableSelection();

$( "#lista_alumnos1, #lista_alumnos2, #lista_alumnos3, #lista_alumnos4, #lista_alumnos5, <?=$lista_cuadros_grupos0?>, <?=$lista_cuadros_grupos1?>, <?=$lista_cuadros_grupos2?>, <?=$lista_cuadros_grupos3?>, <?=$lista_cuadros_grupos4?>, <?=$lista_cuadros_grupos5?>, <?=$lista_cuadros_grupos6?>, <?=$lista_cuadros_grupos7?>, <?=$lista_cuadros_grupos8?>" ).droppable({
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
                pagina_destino="/gestion/grupos_pruebas/cambio_grupo.php";
                sendAndLoad({desc:'grupo',dato:grupo},{desc:'alumno',dato:alumno},{desc:'id_curso_grupo',dato:<?=$id_curso_actual?>});
                // window.open(pagina_destino+"?grupo="+grupo+"&alumno="+alumno+"&id_curso_grupo=<?=$id_curso_actual?>");

        }

function nuevo_subgrupo(id_grupo,nombre_mostrar) {
        if (confirm("¿Quieres añadir un grupo nuevo al horario "+nombre_mostrar+"?")) {
                window.location.href='nuevo_subgrupo.php?fecha=<?=$fecha_time_stamp?>&id_grupo='+id_grupo;
                }
        }

function quitar_subgrupo(id_grupo,id_subgrupo,nombre_mostrar) {
        if (confirm("¿Quieres quitar el grupo "+(Number(id_subgrupo)+1)+" del horario "+nombre_mostrar+"?")) {
                window.location.href='quita_subgrupo.php?fecha=<?=$fecha_time_stamp?>&id_grupo='+id_grupo;
                }
        }

function nuevo_grupo(id_dia,id_curso) {
        
                nombre_mostrar = new Array("","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado","Domingo");
        
        if (confirm("¿Quieres añadir un grupo nuevo al "+nombre_mostrar[id_dia]+"?")) {
                window.open('nuevo_grupo.php?fecha=<?=$fecha_time_stamp?>&id_dia='+id_dia+'&id_curso='+id_curso,"edicion_grupos","toolbars=no,scrollbars=yes,width=600,height=550");
                }
        }
function editar_grupo(id_grupo,id_dia,id_curso) {
        window.open('nuevo_grupo.php?fecha=<?=$fecha_time_stamp?>&id_grupo='+id_grupo+'&id_dia='+id_dia+'&id_curso='+id_curso,"edicion_grupos","toolbars=no,scrollbars=yes,width=600,height=550");
        }

function gestion_monitores(id_grupo,id_subgrupo,nombre_mostrar,fecha) {
        window.open("gestion_monitores.php?fecha="+fecha+"&id_grupo="+id_grupo+"&id_subgrupo="+id_subgrupo+"&nombre_mostrar="+nombre_mostrar,"edicion_monitores","toolbars=no,scrollbars=yes,width=600,height=550");
        }
        
function ver_programacion(id_grupo,id_subgrupo,nombre_mostrar,fecha) {
        window.open("ver_programacion.php?fecha="+fecha+"&id_grupo="+id_grupo+"&id_subgrupo="+id_subgrupo+"&nombre_mostrar="+nombre_mostrar,"edicion_monitores","toolbars=no,scrollbars=yes,width=600,height=550");
        }        
        
function gestion_lista_espera(id_grupo,id_subgrupo,nombre_mostrar) {
        window.open("lista_espera.php?fecha=<?=$fecha_time_stamp?>&id_grupo="+id_grupo+"&id_subgrupo="+id_subgrupo+"&nombre_mostrar="+nombre_mostrar,"edicion_monitores","toolbars=no,scrollbars=yes,width=1024,height=768");
        }        

function muestra_grupo(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite,fecha_actual) {
        $('#bebesModal').modal('hide');
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">' + nombre_subgrupo + '</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'detalles_grupo.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_limite);
        
        }

function muestra_grupo_recuperacion(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite,fecha_actual) {
        $('#bebesModal').modal('hide');
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">' + nombre_subgrupo + '</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'detalles_grupo_recuperacion.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_actual);   
        
        }  

function agrega_grupo(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite) {
        
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Asignar alumnos al ' + nombre_subgrupo + '</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'asignar_a_grupo.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_limite);
        
        }

function agrega_grupo_recuperacion(id_grupo, id_subgrupo,nombre_subgrupo,fecha_limite) {
        
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Asignar alumnos al ' + nombre_subgrupo + ' para recuperación de horas</h4>');
        $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'asignar_a_grupo_recuperacion.php?id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo+'&fecha_limite='+fecha_limite);
        
        } 

function muestra_bebes() {
        $('#bebesModal').modal('show');
        $('#detalles_bebes').attr('src', 'detalles_bebes.php?fecha=<?=$fecha_listado_bebes;?>&curso_seleccionado=<?=$id_curso_actual;?>');
        }

function muestra_otros(nivel,nombre_nivel) {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Detalles alumnos ' + nombre_nivel + '</h4>');
    $('#grupoModal').modal('show');
        $('#detalles_grupo').attr('src', 'detalles_otros.php?fecha=<?=$fecha_listado_bebes;?>&curso_seleccionado=<?=$id_curso_actual;?>&tipo_usuario='+nivel);
        }

function editarservicios(fila) {
        $('#nombreServicios').html('<h4 class="modal-title" id="serviciosModalLabel">Editar servicios del alumno.</h4>');
        $('#serviciosModal').modal('show');
        $('#detallesServicios').attr('src', '/facturacion/editar_servicios.php?i='+fila); 
        
        // window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }

function editarinscripciones(fila) {
        $('#nombreServicios').html('<h4 class="modal-title" id="serviciosModalLabel">Editar inscripciones del alumno.</h4>');
        $('#serviciosModal').modal('show');
        $('#detallesServicios').attr('src', '/alumnos/nuevo_alumno4.php?u='+fila+'&id_curso_pre=<?=$id_curso_actual;?>'); 
        
        // window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
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
  <!-- <div class="col-md-2 panelFijo" data-spy="affix" data-offset-top="60" data-offset-bottom="200"> -->
  <div class="col-md-2" data-offset-top="60" data-offset-bottom="200">
  <h4>Alumnos sin asignar</h4>
  <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseZero">
          Bebes <button class="btn btn-primary btn-xs pull-right" onclick="muestra_bebes()" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-child"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseZero" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos0" class="droptrue">
<li class="dropfalse">Bebes </li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=0;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {

                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;

if ($nivel_alumno==0) {

        // Busca servicios contratador
        $busca_servicios_ag=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=3 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_ag)!=0) {
                $nivel_alumno=4;
                }

        $busca_servicios_mn=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=4 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_mn)!=0) {
                $nivel_alumno=5;
                }
        
        }

$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
        </div>
    </div>
  </div>  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Nivel 1 <button class="btn btn-primary btn-xs pull-right" onclick="muestra_otros(1, '<?=$array_listas_de_espera[1]?>')" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-users"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos1" class="droptrue">
<li class="dropfalse">Nivel 1</li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=1;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
        </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Nivel 2 <button class="btn btn-primary btn-xs pull-right" onclick="muestra_otros(2, '<?=$array_listas_de_espera[2]?>')" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-users"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos2" class="droptrue">
<li class="dropfalse">Nivel 2</li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=2;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;                        
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
       </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Nivel 3 <button class="btn btn-primary btn-xs pull-right" onclick="muestra_otros(3, '<?=$array_listas_de_espera[3]?>')" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-users"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos3" class="droptrue">
<li class="dropfalse">Nivel 3</li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=3;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
              
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;                        
                        }
                        
// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
          Aquagym <button class="btn btn-primary btn-xs pull-right" onclick="muestra_otros(4, '<?=$array_listas_de_espera[4]?>')" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-users"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos4" class="droptrue">
<li class="dropfalse">Aquagym</li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=4;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
        </div>
    </div>
  </div>
  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
          Matronatacíon <button class="btn btn-primary btn-xs pull-right" onclick="muestra_otros(5, '<?=$array_listas_de_espera[5]?>')" title="Mostrar detalles Grupo"><span class="textoBlanco"><i class="fa fa-users"></i></span></button>
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse in">
      <div class="panel-body">
<ul id="lista_alumnos5" class="droptrue">
<li class="dropfalse">Matronatacíon</li>
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=5;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp-$tiempo_gracia)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

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

?>
<li class="ui-state-default fondo_nivel<?=$nivel_actual?>" id="alumno_<?=$alumnos["id_alumno"]?>_0"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?><?=$coletilla;?> (<?=$edad_alumno?>)</li>
<?php 
                }
        }
}
?>
</ul>
        </div>
    </div>
  </div>    
  
<br />
<form method="get" action="<?=$_SERVER["PHP_SELF"];?>" name="monitores">
<input type="hidden" name="fecha" value="<?=$fecha_time_stamp?>">
<input type="hidden" name="subniveles" value="<?=$mostrar_niveles_futuros?>">
<input type="hidden" name="lista_espera" value="<?=$mostrar_lista_espera?>">

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
<h5><span class='pull-right'><?=$contador_matriculados;?></span>Matriculados:</h5>
<h6><span class='pull-right'>Próx. inc.: <?=$contador_matriculados_prox?></span>Actuales: <?=$contador_matriculados-$contador_matriculados_prox?></h6>  
<br />
<h5><span class='pull-right'><?=$contador_ocupacion;?></span>Sesiones contratadas:</h5>
<h6><span class='pull-right'>Próx. inc.: <?=$contador_ocupacion_prox?></span>Actuales: <?=$contador_ocupacion-$contador_ocupacion_prox?></h6>  
<br />
<h5>Sesiones por tipo</h5>
<?php 
for ($c_tipos=0;$c_tipos<count($array_listas_de_espera);$c_tipos++) {
                echo("<h6><span class='pull-right'>" . $total_por_tipo[$c_tipos] . "</span>" . $array_listas_de_espera[$c_tipos] . ":</h6>");
        }

?>
</div>
  </div>
  <div class="col-md-2">
  &nbsp;
  </div>  
  <div class="col-md-10">
     <table class="table">
     <tr>
        <td colspan="2"><a class="btn btn-primary" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$dia_inicio_semana-(7*86400);?>"><i class="fa fa-angle-left"></i> Semana Anterior</a> <a class="btn btn-primary" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a><br /><a class="btn btn-primary btn-xs" href="grupos_plazas_libres.php?fecha=<?=$fecha_time_stamp;?>" title="Mostrar solo grupos con plazas libres">Mostrar solo grupos con plazas libres</a></td>
        <td colspan="2" align="center"><?=$nombre_monitor_seleccionado?><h4>Semana del <?=date("d/m/Y",$dia_inicio_semana);?> al <?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></h4><h5><form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form></h5></td>
                <td colspan="3" align="right"><a class="btn btn-primary" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a> <a class="btn btn-primary" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$dia_inicio_semana+(7*86400);?>">Semana Siguiente <i class="fa fa-angle-right"></i></a><br />
                
                <?php 
                
        	    if ($mostrar_lista_espera==1) {
    	        ?>
					<a class="btn btn-primary btn-xs" href="index.php?lista_espera=0&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$fecha_time_stamp;?>" title="Mostrar nivel estimado para el próximo curso">Ocultar listas de espera</a>                
                	<?php 
            	    } else {
        	        ?>
					<a class="btn btn-primary btn-xs" href="index.php?lista_espera=1&subniveles=<?=$mostrar_niveles_futuros?>&fecha=<?=$fecha_time_stamp;?>" title="Mostrar nivel estimado para el próximo curso">Mostrar listas de espera</a>                
	                <?php 
                	}                
                
                if (strtotime($fecha_inicio_curso_actual)<=time()) {
                
        	        if ($mostrar_niveles_futuros==1) {
    	            ?>
					<a class="btn btn-primary btn-xs" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&fecha=<?=$fecha_time_stamp;?>" title="Mostrar nivel estimado para el próximo curso">Mostrar nivel actual</a>                
                	<?php 
            	    } else {
        	        ?>
					<a class="btn btn-primary btn-xs" href="index.php?lista_espera=<?=$mostrar_lista_espera?>&subniveles=1&fecha=<?=$fecha_time_stamp;?>" title="Mostrar nivel estimado para el próximo curso">Mostrar nivel estimado para el próximo curso</a>                
	                <?php 
                	}
                }
                ?>                                                
                </td>
     </tr>
     <tr><td colspan="7"><small>
     <b>Leyenda:</b>
     	<span class="label label-success">R</span> Reserva de plaza pagada.
	 	<span class="label label-danger">R</span> Reserva de plaza NO pagada. 
		<span class="label label-warning">R</span> Reserva de plaza, recibo no emitido. 
		<span class="label label-default">N</span> Reserva de plaza NO contratada.</small>
     <tr>
        <td>Lunes <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(1,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana);?></td>
        <td>Martes <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(2,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*1));?></td>
        <td>Miércoles <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(3,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*2));?></td>
        <td>Jueves <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(4,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*3));?></td>
        <td>Viernes <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(5,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*4));?></td>
        <td>Sábado <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(6,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*5));?></td>
        <td>Domingo <button class="btn btn-primary btn-xs" onclick="nuevo_grupo(7,<?=$id_curso_actual?>);"><i class="fa fa-plus"></i></button><br><?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></td>
     </tr>
     <?php 
     // $busca_turnos=mysql_query("SELECT hora_inicio,hora_fin FROM aulas_grupos WHERE dia=1 AND id_curso=$id_curso_actual ORDER BY hora_inicio");

         $contador_grupos=0;

     //while($turnos=mysql_fetch_array($busca_turnos))
     for ($turnos=1;$turnos<=$num_max_grupo;$turnos++)
     {
     $contador_grupos++;
     ?>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     
     $busca_hora_inicio=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
     if (db_num_rows($busca_hora_inicio)!=0) {
     
            $datos_grupo_inicio=db_fetch_array($busca_hora_inicio);
            
            $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo_inicio["hora_fin"]));
            
             ?>
             <td><a href="/gestion/agenda/dia_aula.php?id_grupo=<?=$datos_grupo_inicio["id_grupo"]?>&dia=<?=($dia_inicio_semana+(86400*($c_dia-1)))?>" target="_blank"><?=date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_grupo_inicio["hora_fin"]))?> </a> <button class="btn btn-primary btn-xs" onclick="nuevo_subgrupo(<?=$datos_grupo_inicio["id_grupo"]?>,'<?=$nombre_horario_mostrado?>')"><i class="fa fa-plus"></i></button> <button class="btn btn-primary btn-xs" onclick="editar_grupo(<?=$datos_grupo_inicio["id_grupo"]?>,<?=$c_dia?>,<?=$id_curso_actual?>)"><i class="fa fa-edit"></i></button></td>
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
     
     $busca_grupos=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
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
         
         // Busca datos subgrupo
         
         $busca_datos_subgrupo=db_query("SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual;");
         
         if (db_num_rows($busca_datos_subgrupo)==0) {
                 $inserta_subgrupo=db_query("INSERT INTO aulas_grupos_subgrupos (id_grupo,id_subgrupo) VALUES (".$datos_grupo["id_grupo"].",".$nivel_actual.");");
                 $busca_datos_subgrupo=db_query("SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual;");
                 }
         
         $datos_subgrupo=db_fetch_array($busca_datos_subgrupo);
         
         $capacidad_subgrupo=$datos_subgrupo["capacidad"];                  
         
         if ($datos_subgrupo["tipo_subgrupo"]!=5) {
         	$busca_alumnos_grupo=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel-$tiempo_gracia)."') OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."'))") or die(db_error());
         	} else {
         	$busca_alumnos_grupo=db_query("SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.id_curso=$id_curso_actual AND servicios_consumidos_faltas.id_grupo=".$datos_grupo["id_grupo"]." AND servicios_consumidos_faltas.id_subgrupo=$nivel_actual AND servicios_consumidos_faltas.fecha_recuperacion='".date("Y-m-d",$fecha_dia_sel)."' ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2") or die(db_error());
         	}
         
         $num_alumnos_subgrupo=db_num_rows($busca_alumnos_grupo);
         
         
         // Busca alumnos grupo en el otro horario
         $busca_alumnos_grupo_ext=db_query("SELECT id_grupo FROM aulas_alumnos_lapices_grupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual");
         
         $num_alumnos_subgrupo_ext=db_num_rows($busca_alumnos_grupo_ext);
         
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
                  
                  
                $capacidad_total=$capacidad_total+$capacidad_subgrupo;
                $matriculados_total=$matriculados_total+$num_alumnos_subgrupo;
                                
                $matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$num_alumnos_subgrupo;
                $capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$capacidad_subgrupo;    
                                
                                
                 $nombre_subgrupo=$nombre_horario_mostrado.", Grupo: ".($c_subgrupos+1);                   
                  
                 $funcion_agregar_grupos="agrega_grupo";
                 $funcion_mostrar_grupo="muestra_grupo";
                 if ($datos_subgrupo["tipo_subgrupo"]==5) {
                 	$funcion_agregar_grupos="agrega_grupo_recuperacion";
                 	$funcion_mostrar_grupo="muestra_grupo_recuperacion";
                 }   
                 
                 // Bloqueado
                 $texto_bloqueado="";
                 $boton_agregar_alumnos='<i class="fa fa-user-plus"></i>';
                 $color_boton_agregar_alumnos="primary";
                 if ($datos_subgrupo["bloqueado"]==1) {
                 
                 	$boton_agregar_alumnos='<i class="fa fa-lock"></i>';
                 	$color_boton_agregar_alumnos="danger";
                 	$texto_bloqueado="<br /><span class='label label-danger'>Grupo Bloqueado</span>";
                 	
                 }
                              
                                                              
          ?>
         <tr>
         <td>         
                <ul id="grupo_<?=$nivel_actual?>_<?=$datos_grupo["id_grupo"]?>" class="droptrue" style="background: <?=$colores_subgrupos[$datos_subgrupo["tipo_subgrupo"]]?>;">
                <li class="dropfalse"><button class="btn btn-primary btn-xs" onclick="gestion_monitores(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_horario_mostrado?>','<?=$fecha_dia_sel?>')"><i class="fa fa-graduation-cap"></i></button> <button class="btn btn-<?=$color_boton_agregar_alumnos?> btn-xs" onclick="<?=$funcion_agregar_grupos?>(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_subgrupo?>','<?=date("Y-m-d",$fecha_dia_sel);?>')" title="Añadir alumnos al grupo"> <?=$boton_agregar_alumnos?></button> <button onclick="<?=$funcion_mostrar_grupo;?>(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_subgrupo?>','<?=date("Y-m-d",$fecha_dia_sel-$tiempo_gracia);?>','<?=date("Y-m-d",$fecha_dia_sel);?>')" title="Mostrar detalles Grupo" class="btn btn-info btn-xs">Gr. <?=$c_subgrupos+1?></button><?=$texto_bloqueado?> <br />Capacidad: <?=$capacidad_subgrupo?><br />Ocupación: <?=$num_alumnos_subgrupo?><br />Ocupación Ext.: <?=$num_alumnos_subgrupo_ext?><br />Disponibles: <?=$capacidad_subgrupo-$num_alumnos_subgrupo-$num_alumnos_subgrupo_ext?> <br /><?php  if (($num_alumnos_subgrupo==0) and ($num_alumnos_subgrupo_ext==0) and ($c_subgrupos==($num_subgrupos-1))) { ?> <button class="btn btn-primary btn-xs" onclick="quitar_subgrupo(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_horario_mostrado?>')"><i class="fa fa-minus"></i></button><?php  } ?></li>
                <?php 

                while($alumnos=db_fetch_array($busca_alumnos_grupo)) {
                
              	$coletilla_pagos="";
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_dia_sel))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        }
                        
                $color_fuente_linea="";
                if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_dia_sel)) {
                $color_fuente_linea=" text-danger";
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
        
        		if ($mostrar_niveles_futuros==1) {
        		
        			// Nivel proximo curso
        			$nivel_proximo_curso_tmp=($alumnos["nivel_actual"]*3)-2;
        	
        			$nivel_proximo_curso=$alumnos["nivel_proximo"]; 
        			if (($nivel_proximo_curso==0) and ($alumnos["nivel_actual"]!=0)) {
        	
        				$nivel_proximo_curso=$nivel_proximo_curso_tmp;
        	
        			}
        		
        			$estilo_linea="fondo_subnivel".$nivel_proximo_curso." textoBlanco";
        			
        			// Comprueba si está ya inscrito
        			if ($id_nuevo_curso) {
    	    			$busca_si_proximo_alumnos=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($alumnos["id_usuario"], $id_nuevo_curso)); 
	        			if (db_num_rows($busca_si_proximo_alumnos)!=0) {
	        				$estilo_linea.=" tachado";
	        				}
        				}      		
        		
        		} else {
        		
        			$estilo_linea="fondo_nivel".$alumnos["nivel_actual"].$color_fuente_linea;
        			if ($alumnos["tipo_baja"]==2) {
        				$estilo_linea="fondo_nivelB textoBlanco";	
        			}        		
        		
        		}       
                
                // Comprueba si ha pagado inscripción
                
                if (strtotime($fecha_inicio_curso_actual)>strtotime("2016-08-01")) {
                
        		$id_inscripcion=$alumnos["id_alumno"];
        		$busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=1 ORDER BY id_servicio_contratado DESC', 's', array($id_inscripcion)) or die(db_error());

				if (db_num_rows($busca_inscripcion)!=0) {								
					// Tiene el servicio incluido
					// Comprueba si está emitido el recibo
					$datos_servicio=db_fetch_array($busca_inscripcion);
					
					$busca_inscripcion_facturada=db_query_params('SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.concepto=? AND facturas_emitidas.id_cliente=?', 'ss', array((string)($datos_servicio["ARTICULO"]), $id_inscripcion));

        			if (db_num_rows($busca_inscripcion_facturada)!=0) {
                			// Tiene el recibo emitido
                			$datos_inscripcion=db_fetch_array($busca_inscripcion_facturada);
                			// Comprueba si está pagado
                			$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_inscripcion["id_factura"]));

							if (db_num_rows($busca_pagos)!=0) {
								// Está pagado
								$coletilla_pagos=' <span class="label label-success">R</span>';
							} else {
								// No está pagado
								$coletilla_pagos=' <span class="label label-danger">R</span>';
							}
							
                		} else {
                			// No tiene el recibo emitido;
                			$coletilla_pagos=' <span class="label label-warning">R</span>';
                		}
                							
					} else {
						// No tiene el servicio incluido
						$coletilla_pagos=' <span class="label label-default">N</span>';						
					}    
					
					}     		
                                 
                ?>
                <li class="ui-state-default <?=$estilo_linea?>" id="alumno_<?=$alumnos["id_alumno"]?>_<?=$alumnos["id_alumno_grupo"]?>"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?> (<?=$edad_alumno;?>)<?=$coletilla_pagos?></li>
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


// Inicio lista de espera
if ($mostrar_lista_espera==1) {			 
				 
	// Busca ordenes usuarios en lista de espera
	$id_grupo=$datos_grupo["id_grupo"];
                        
	$busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.id_curso=$id_curso_actual AND lista_espera.dia_deseado_$c_dia=1 AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$c_dia>='".$datos_grupo["hora_fin"]."' ORDER BY lista_espera.fecha_alta");
                            
	$listado_en_lista=array();
                            
	while($datos_lista_espera=db_fetch_array($busca_lista_espera)) {
                            
		$listado_en_lista[$datos_lista_espera["id_usuario"]]=1;
                            
		// comprueba si está en la lista
                                    
		$busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=? AND id_usuario=?', 'ss', array($id_grupo, $datos_lista_espera["id_usuario"]));
                            
		if (db_num_rows($busca_en_lista)==0) {
                                    
			// Busca el ultimo en la lista
			$busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=? ORDER BY orden DESC', 's', array($id_grupo));
                                    
			if (db_num_rows($busca_en_lista)==0) {
				$nuevo_orden=1;                                          
				} else {
				$datos_orden=db_fetch_array($busca_en_lista);
				$nuevo_orden=$datos_orden["orden"]+1;
				}
                                    
			// Introduce en lista
			$intro_lista=db_query_params('INSERT INTO lista_espera_orden (id_grupo,id_usuario,orden) VALUES (?,?,?);', 'sss', array((string)($id_grupo), (string)($datos_lista_espera["id_usuario"]), (string)($nuevo_orden)));
                                    
			}
                         
		}
                            
	// Limpia lista de espera
	$busca_por_orden=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=?', 's', array($id_grupo));
                            
	while($por_orden=db_fetch_array($busca_por_orden)) {
                            
		// Comprueba si está
		if ($listado_en_lista[$por_orden["id_usuario"]]!=1) {
			$borra_de_orden=db_query_params('DELET FROM lista_espera_orden WHERE id=?', 's', array($por_orden["id"]));
			}
                            
		}
                            
	// Busca listas de espera
	$busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios,lista_espera_orden WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.id_usuario=lista_espera_orden.id_usuario AND lista_espera.id_curso=$id_curso_actual AND lista_espera.dia_deseado_$c_dia=1 AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$c_dia>='".$datos_grupo["hora_fin"]."' AND lista_espera_orden.id_grupo=$id_grupo AND lista_espera_orden.no_mostrado=0 ORDER BY lista_espera_orden.orden");
                 
                 
	$lista_espera_grupo="";               
	$num_en_lista=0;
                        
	while($datos_lista_espera=db_fetch_array($busca_lista_espera)) { // Inicio bucle lista espera
                
		$texto_edad="";
        if ($datos_lista_espera["nonato"]==1) {
			$edad_alumno="";
            $fecha_nac_alumno="No ha nacido";
        	} else {
			$edad_alumno=date("Y")-date("Y",strtotime($datos_lista_espera["fecha_nacimiento"]));

			if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($datos_lista_espera["fecha_nacimiento"])))) {
				$edad_alumno=$edad_alumno-1;
				}

        	if ($edad_alumno<2) {
                	// 1. Comprueba la edad restando el año de nacimiento al año actual
                	$fecha_l=$datos_lista_espera["fecha_nacimiento"];
                	$edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                	// 1.1. Cacula edad con decimales
                	$mes_d_l=date("m",strtotime($fecha_l));
                	$mes_d_n=date("m");
                	$dif_meses=$mes_d_n-$mes_d_l;

                	$edad_d_l=($edad_a_l*12)+$dif_meses;
                	$edad_alumno=$edad_d_l." meses";
                } else {
                	// 1. Comprueba la edad restando el año de nacimiento al año actual
                	$fecha_l=$datos_lista_espera["fecha_nacimiento"];
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($datos_lista_espera["fecha_nacimiento"]));

            $texto_edad=" ($edad_alumno)";
			}

			// Comprueba si es alumno de otro grupo
			$id_usuario_lista=$datos_lista_espera["id_usuario"];
			
			$busca_si_grupo=db_query_params('SELECT id_grupo FROM aulas_alumnos_grupos,alumnos WHERE aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.id_usuario=? AND aulas_alumnos_grupos.id_curso=? AND alumnos.tipo_baja=0 AND alumnos.fecha_baja>=?', 'sss', array($id_usuario_lista, $id_curso_actual, (string)(date("Y-m-d", $fecha_dia_sel - $tiempo_gracia))));

			if (db_num_rows($busca_si_grupo)!=0) {
            		$texto_edad.=' <span class="label label-danger">En otro grupo</span>';
            	}
            $lista_espera_grupo.="<li class='fondo_espera".$datos_lista_espera["edad_espera"]."' onclick='window.open(\"/lista_espera/lista_espera.php?edad_lista=".$datos_lista_espera["edad_espera"]."&id_curso_lista=".$id_curso_actual."\")'>".$datos_lista_espera["Nombre"]." ".$datos_lista_espera["Apellido1"]." ".$datos_lista_espera["Apellido2"].$texto_edad."</li>";
            $num_en_lista++;
		} // Fin bucle lista espera
                
	if ($lista_espera_grupo!="") {
		?>
		<tr>
			<td>
				<small><b><button onclick="$('[id^=lista_espera_N]').toggle();"><i class="fa fa-sort-amount-asc"></i></button> <a href="javascript:gestion_lista_espera(<?=$datos_grupo["id_grupo"]?>,<?=$c_subgrupos?>,'<?=$nombre_horario_mostrado?>')"> Lista de espera</a></b><br /><?=$num_en_lista?> en espera.</small>
				<ul class="lista_espera" id="lista_espera_N<?=$datos_grupo["id_grupo"]?>_<?=$c_subgrupos;?>">
					 <?=$lista_espera_grupo;?>
				</ul>
			</td>
		</tr>
		<?php 
		}
}		
// Fin lista de espera
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
window.location.href="<?=$_SERVER["PHP_SELF"];?>?subniveles=<?=$mostrar_niveles_futuros?>&lista_espera=<?=$mostrar_lista_espera?>&fecha=<?=$fecha_time_stamp?>&monitor=0";
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
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-wide fade" id="bebesModal" tabindex="-1" role="dialog" aria-labelledby="bebesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_bebes"><h4 class="modal-title" id="bebesModalLabel">Listado bebes <?=$nombre_curso_actual?></h4></div>
      </div>
      <div class="modal-body">
        <iframe src="detalles_bebes.php?curso_seleccionado=<?=$id_curso_actual?>" name="detalles_bebes" id="detalles_bebes" width=100% height=450 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-wide fade" id="serviciosModal" tabindex="-1" role="dialog" aria-labelledby="grupoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombreServicios"><h4 class="modal-title" id="grupoModalLabel">Título</h4></div>
      </div>    
      <div class="modal-body">
        <iframe src="" name="detallesServicios" id="detallesServicios" width=100% height=550 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div>

<script>
$( document ).ready(function() {

    $('[id^=lista_espera_N]').toggle();

});
</script>
<?php 
$tiempo_fin = microtime_float();
$tiempo = $tiempo_fin - $tiempo_inicio;

// echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio); 
?>
</body>
</html>