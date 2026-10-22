<?php  require("../../configuracion/parte_superior_docente_ancho.php"); ?>
<div align="center">
<?php 
$focuson=$_GET["focuson"];

$pagina_activa=$_GET["pagina_activa"];
if (strlen($pagina_activa==0)) {
        $pagina_activa=1;
        }

if (strlen($id_aula_seleccionada)==0) {
        $id_aula_seleccionada=$_GET["id_aula"];
        if (strlen($id_aula_seleccionada)==0) {
                $id_aula_seleccionada=$_POST["id_aula"];
                if (strlen($id_aula_seleccionada)==0) {
                        $id_aula_seleccionada=0;
                        $accion_texto="Nueva";
                        }
                }
        }
$id_aula=$id_aula_seleccionada;
$id_curso_actual=$_GET["id_curso_aula"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_aula_seleccionada!=0) {
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula_seleccionada);

        if (db_num_rows($consulta_datos_aula)!=0)
                {
                $datos_aula=db_fetch_array($consulta_datos_aula);

                // Datos Fijos
                $aula_nombre=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $centro_aula=$datos_aula["id_centro"];
                $ciclo_aula=$datos_aula["ciclo"];
                $edad_aula=$datos_aula["edad_minima_prog"];

                $edad_aula_max=$datos_aula["edad_maxima_prog"];
                }

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        }

if ($id_aula_seleccionada!=0) {

if ($id_curso_actual!=0) {

$inicio=$_GET["fecha_inicio"];


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",strtotime($inicio));
        } else {
        $anio_fecha=date("Y");
        }

if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
} 

$mes_programacion=date("n",$fecha_time_stamp);

$formulario_name="calendario";

?>
<font size=3><b>Plan diario - Curso <?=$datos_curso_actual["nombre"]?> de <?=$aula_nombre;?></b><br></font>
<font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.</font><br><br>
<table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000">
<form name="<?=$formulario_name;?>" method="POST" action="">
<input type="hidden" name="pagina_activa" value="<?=$pagina_activa;?>">
<tr><td>
<table>
<tr>
<td colspan=4 align="center" bgcolor="#efefef"><font size=1><b>Leyenda de colores</b></font></td>
<td rowspan=2 align="center"><input type="button" value="Ver informe de objetivos mensual" onclick="ver_informe();"> <input type="button" value="Ver informe de evaluación mensual" onclick="ver_evaluacion();"> <input type="button" value="Ver informe de evaluación" onclick="ver_evaluacion1();"> <input type="button" value="Volver al calendario" onclick="window.location.href='../agenda_dia/calendario_programacion.php?id_aula=<?=$id_aula?>&dia=<?=$inicio?>';"></td>
</tr>
<tr>
<td bgcolor="#F17327"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día festivo</font></td>
<td bgcolor="#FFCE70"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día fuera del periodo lectivo del curso.</font></td>
</tr>
</table></td></tr></table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr>
<td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center"><br>
<?php 
if ($ciclo_aula==1) {
        $tiempos_de_trabajo=4;
        } else {
        $tiempos_de_trabajo=6;
        }

$contados_capas_edades=1;

if ($edad_aula!=0) {
for ($contador_capas=1;$contador_capas<=$tiempos_de_trabajo;$contador_capas++) {

$tiempo_de_trabajo=$contador_capas;
        ?>
        <div id="capa_<?=$contador_capas;?>" style="display:<?php  if ($pagina_activa==$contador_capas) { print "block"; } else { print "none"; }?>;padding:0px;border:0px solid">
        <?php  require("calendario_tiempos_de_trabajo.php"); ?>
        </div>
        <?php 
        }
        } else {
        for ($edad_act=0;$edad_act<=12;$edad_act++) {
                for ($contador_capas=1;$contador_capas<=$tiempos_de_trabajo;$contador_capas++) {

                        $tiempo_de_trabajo=$contador_capas;
                ?>
                <div id="capa_<?=$contados_capas_edades;?>" style="display:<?php  if ($pagina_activa==$contados_capas_edades) { print "block"; } else { print "none"; }?>;padding:0px;border:0px solid">
                <?php  require("calendario_tiempos_de_trabajo_01.php"); ?>
                </div>
                <?php 
                $contados_capas_edades++;
                }
        }
        }
?>
</td>
</tr>
</form>
</table>
</div>
<SCRIPT LANGUAGE=javascript>
<!--

var pagina_destino="guarda_dato_programacion.php";
var capa_activa="<?=$pagina_activa;?>";


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
	    
		rpc.onreadystatechange = function() {
	        if (rpc.readyState == 4) {
	            var textoRespuesta = rpc.responseText;
	            
	            if (textoRespuesta!="ok") {
		            
		            alert("Ha ocurrido algún error al guardar los datos, comprueba si tienes comexión a internet o si la sesión ha caducado.");
		            
	            }
	            
	        }
	    } 	    
	    
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';
        var recargar=0;

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
            if (arguments[ii].desc=="recargar") {
                    recargar=recargar+arguments[ii].dato;
                    }
        }

        //Ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        if (recargar==0) {
                rpc.send(aEnviar);
                } else {
                window.location.href=pagina_destino+"?pagina_activa="+document.<?=$formulario_name;?>.pagina_activa.value+"&id_aula=<?=$id_aula?>&fecha_inicio=<?=$fecha_time_stamp?>&"+aEnviar;
                }
    }
}

function ver_actividades(id_prog) {

        var objetivo_prog=document.getElementById("objetivo_"+id_prog).value;

        if (objetivo_prog==0) {
                alert("Selecciona antes el objetivo.");
                } else {
                listado_actividades=window.open("ver_actividades.php?id_prog="+id_prog);
                }

        }
        
function ver_objetivos(id_prog) {

        var objetivo_prog=document.getElementById("objetivo_"+id_prog).value;

        if (objetivo_prog==0) {
                alert("Para añadir objetivos adicionales, selecciona antes el objetivo principal.");
                } else {
                listado_actividades=window.open("ver_objetivos.php?capa_activa="+capa_activa+"&id_prog="+id_prog);
                }

        }
        
function ver_informe() {

        informe_actividades=window.open("informe_actividades.php?id_aula=<?=$id_aula?>&fecha=<?=date('Y-m-d',$fecha_time_stamp);?>");

        }
        
function ver_evaluacion() {

        informe_evaluacion=window.open("informe_evaluacion.php?id_aula=<?=$id_aula?>&fecha=<?=date('Y-m-d',$fecha_time_stamp);?>");

        }
        
function ver_evaluacion1() {

        informe_evaluacion=window.open("seleccion_fechas_informe.php?id_aula=<?=$id_aula?>");

        }
                
function evaluar(dia) {

        evaluacion_dia=window.open("evaluar_dia<?=$coletilla_01?>.php?dia="+dia+"&id_aula=<?=$id_aula?>&fecha=<?=date('Y-m-d',$fecha_time_stamp);?>");

        }

function nueva_actividad(aula,dia,tiempo_trabajo) {

        window.location.href='nueva_actividad.php?id_aula='+aula+'&dia='+dia+'&tiempo_trabajo='+tiempo_trabajo;

        }
        
function quitar_actividad(aula,dia,tiempo_trabajo) {

        if (confirm("¿Quieres eliminar la última actividad del tiempo de trabajo? Esta acción no se puede deshacer")) {
                window.location.href='quitar_actividad.php?id_aula='+aula+'&dia='+dia+'&tiempo_trabajo='+tiempo_trabajo;
                }

        }

function nueva_actividad_01(aula,dia,tiempo_trabajo,edad,capa_activa) {

        window.location.href='nueva_actividad_01.php?capa_activa='+capa_activa+'&edad='+edad+'&id_aula='+aula+'&dia='+dia+'&tiempo_trabajo='+tiempo_trabajo;

        }
        
function quitar_actividad_01(aula,dia,tiempo_trabajo,edad,capa_activa) {

        if (confirm("¿Quieres eliminar la última actividad del tiempo de trabajo? Esta acción no se puede deshacer")) {
                window.location.href='quitar_actividad_01.php?capa_activa='+capa_activa+'&edad='+edad+'&id_aula='+aula+'&dia='+dia+'&tiempo_trabajo='+tiempo_trabajo;
                }

        }

function mostrar_capa(id_capa) {

capa_activa=id_capa;

<?php 
$total_capas_creadas=$contados_capas_edades;
$contados_capas_edades=0;
if ($edad_aula!=0) {
?>
        for(capas=1;capas<=<?=$tiempos_de_trabajo;?>;capas++) {
                if (capas==id_capa) {
                        document.getElementById("capa_"+capas).style.display='block';
                        document.<?=$formulario_name;?>.pagina_activa.value=capas;
                        } else {
                        document.getElementById("capa_"+capas).style.display='none';
                        }
                }
<?php 
} else {
?>
        var contador_capas=1;
        for(edades=0;edades<=12;edades++) {
                for(capas=1;capas<=<?=$total_capas_creadas;?>;capas++) {
                        if (contador_capas==id_capa) {
                                document.getElementById("capa_"+contador_capas).style.display='block';
                                document.<?=$formulario_name;?>.pagina_activa.value=contador_capas;
                                } else {
                                document.getElementById("capa_"+contador_capas).style.display='none';
                                }
                        contador_capas++;
                        }
                }
<?php 
}
?>
        }
        
<?php 
if (strlen($focuson)!=0) {
?>
document.getElementById('<?=$focuson;?>').focus();
<?php 
}
?>

//-->
</SCRIPT>
<?php 
}
} else {
?>
El aula no Existe...
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
