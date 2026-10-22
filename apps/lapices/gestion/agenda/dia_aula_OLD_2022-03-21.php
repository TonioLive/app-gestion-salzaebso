<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$colores_asistencia=array();
$colores_asistencia_par=array();

$estado_nota[0]="<font color='#ff0000'>Pendiente</font>";
$estado_nota[1]="<font color='#00ff00'>Realizada</font>";

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Higiene personal";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

// Fecha

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$estado_botones="";
if (date("d-m-Y",$fecha_time_stamp)!=date("d-m-Y")) {
        if ($nivel_acceso!=4) {
                $estado_botones=" disabled";
                }
        }

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guarda_y_vuelve_aula.php";

$pagina_activa=$_GET["pagina_activa"];
if (strlen($pagina_activa==0)) {
        $pagina_activa=0;
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
                $edad_aula=$datos_aula["edad_minima_prog"];
                }

        // Busca el curso actual
        if ($id_curso_actual==0) {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        }


/*
$array_paginas=array("Alumnos","Comida","Salud","Cambios","Anotaciones","Actividades");
$nombres_partes=array("asistencia","comida","salud","cambios","anotaciones","actividades");
*/
if ($edad_aula==0) {
        $array_paginas=array("Alumnos","Comida","Tomas","Salud","Cambios","Anotaciones","Actividades Programadas","Programación");
        $nombres_partes=array("asistencia","comida","tomas","salud","cambios","anotaciones","actividades","link:../agenda_dia/semana_programacion.php");
        } else {
        $array_paginas=array("Alumnos","Comida","Salud","Cambios","Anotaciones","Actividades Programadas","Programación");
        $nombres_partes=array("asistencia","comida","salud","cambios","anotaciones","actividades","link:../agenda_dia/semana_programacion.php");
        }

$numero_capas=count($array_paginas);
$carpeta_capas="partes_aula";

if (file_exists($web_path."/gestion/agenda/".$carpeta_capas)==false) {
        mkdir($web_path."/gestion/agenda/".$carpeta_capas);
        }
        

$boton_webcam="";
                                
// Comprueba si tiene webcam
                                
$busca_webcam=db_query("SELECT * FROM webcam WHERE id_centro=$centro_aula AND id_aula=$id_aula");
                                
if (db_num_rows($busca_webcam)!=0) {
                                
	while($datos_webcam=db_fetch_array($busca_webcam)) {

		// Comprueba si es su aula

		$consulta_aulas=db_query("SELECT * FROM centro_aulas,personal_historial WHERE centro_aulas.id_aula=personal_historial.id_aula AND personal_historial.id_empleado=$idUsuario AND centro_aulas.id_aula=$id_aula;") or die(db_error());
		
		if ((db_num_rows($consulta_aulas)!=0) or (($nivel_acceso>=3) and ($nivel_acceso<10))) {
			$boton_webcam.=" <input type='button' value='Ver cámara ".$datos_webcam["nombre_webcam"]."' onclick='ver_camaralocal(".$datos_webcam["id_webcam"].")'>";
		}
		
		
	}
                                
}        

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<script>
var pagina_destino_ajax="guarda_dato_programacion.php";

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

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
function sendAndLoadComida(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST','guarda_dato.php',true);
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

    }
}

function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino_ajax,true);
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

</script>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function ver_camaralocal(id_camara) {
        window.open('/gestion/centros/ver_camara.php?local=1&id_webcam='+id_camara,'ver_camara','width=385,height=355,toolbar=no');
        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_time_stamp);?>;
        dia_presel=<?=date("d",$fecha_time_stamp);?>;
        ano_presel=<?=date("Y",$fecha_time_stamp);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;

        window.open('/herramientas/calendario_mes2.php?dia='+fecha_preseleccionada+'&pagina_destino=/gestion/agenda/dia_aula.php?id_aula=<?=$id_aula?>','calendario','toolbar=no,resizable=no,width=200,height=216');
        }

function imprimir() {
        document.getElementById('botonera').style.display='none';
        self.print();
        document.getElementById('botonera').style.display='block';
        }
function mostrar(id_capa) {
        for(capas=0;capas<<?=$numero_capas;?>;capas++) {
                if (capas==id_capa) {
                        document.getElementById("capa_"+capas).style.display='block';
                        document.<?=$formulario_name;?>.pagina_activa.value=capas;
                        } else {
                        document.getElementById("capa_"+capas).style.display='none';
                        }
                }
        }
        
function editar(id_alumno) {
        window.open('dia_alumno.php?id_alumno='+id_alumno+'&dia=<?=$fecha_time_stamp?>','datos_alumno','scrollbars=yes,toolbar=no,resizable=yes')
        }
        
function editar_nota(id_alua,id_alumno,dia,area) {
        window.open("editar_notas.php?area="+area+"&dia="+dia+"&id_aula="+id_alua+"&id_alumno="+id_alumno,"ventana_notas","resizable=yes,toolbar=no,width=750,height=550");
        }
        
function actualizarTabla(nombre_tabla,url_destino) {
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            document.getElementById(nombre_tabla).innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET", url_destino, true);
    xmlhttp.send(null);
}

function actualizarCambios(nombre_tabla,url_destino) {
    if (window.XMLHttpRequest) {
        xmlhttp1 = new XMLHttpRequest();
    } else {
        xmlhttp1 = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp1.onreadystatechange = function() {
        if (xmlhttp1.readyState == 4) {
            document.getElementById(nombre_tabla).innerHTML = xmlhttp1.responseText;
        }
    }
    xmlhttp1.open("GET", url_destino, true);
    xmlhttp1.send(null);
}

function nueva_nota(area) {
        window.location.href='nueva_nota.php?id_aula=<?=$id_aula;?>&dia=<?=$fecha_time_stamp;?>&area='+area;
        }
function editar_nota_comun(fila) {
        if (fila!="") {
                window.location.href='nueva_nota.php?id_nota='+fila;
                } else {
                alert("Primero debes seleccionar la nota que deseas editar");
                }
        }
function borrar_nota(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación de la nota?")) {
                window.location.href='borrar_nota.php?id_nota='+id_borrar;
                }
        }
function cambiar_estado(fila,estado_actual) {
        nuevo_estado="Realizada";
        if (estado_actual==1) {
                nuevo_estado="Pendiente";
                }
        if (confirm("¿Deseas cambiar el estado a "+nuevo_estado+"?")) {
                window.location.href='cambiar_estado_nota.php?id_nota='+fila;
                }
        }

function cambiar_estado(fila,estado_actual) {
        var nuevo_estado="Realizada";
                var estado_final=1;
        if (estado_actual==1) {
                nuevo_estado="Pendiente";
                estado_final=0;
                } 
        if (confirm("¿Deseas cambiar el estado a "+nuevo_estado+"?")) {
                        nueva_ventana=window.open(pagina_destino_ajax+"?cerrar_ventana=1&texto_largo=0&fila="+fila+"&nombre_id=id&tabla=agenda_alumnos_seguimiento&campo=realizada&valor="+estado_final,'ventana','width=200,height=200');
                //sendAndLoad({desc:'recargar',dato:'0'},{desc:'texto_largo',dato:'0'},{desc:'fila',dato:fila},{desc:'nombre_id',dato:'id'},{desc:'tabla',dato:'agenda_alumnos_seguimiento'},{desc:'campo',dato:'realizada'},{desc:'valor',dato:estado_final});;
                }
        }


function cambia_estado_actividad(id_actividad) {
        pagina_destino="/gestion/agenda/guarda_checkbox.php";
        sendAndLoad({desc:'fila',dato:id_actividad},{desc:'campo',dato:'realizada'});
        }

setInterval("actualizarTabla('alertas','/gestion/agenda/control_alertas.php?id_aula=<?=$id_aula_seleccionada?>')",1000);

//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="pagina_activa" value="<?=$pagina_activa;?>">
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top"><td align="center" width=50%><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Aula <?=$aula_nombre?> - Curso: <?=$datos_curso_actual["nombre"]?><br><br>
                <?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br>
                <a href="javascript:selecciona_fecha();" title="Selecciona la fecha en el calendario"><font size=1 face="<?= $fuente_general; ?>"><font size=1>Cambiar día <img src="/imagen/calendario.gif" border=0 valign="absmiddle"></font></a></b></font> <?=$boton_webcam?></td>
<td width=50%><div id="alertas"></div></td></tr>
</table>                
                        <br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr>
<td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
<?php 

for ($contador_capas=0;$contador_capas<count($array_paginas);$contador_capas++) {


if (substr($nombres_partes[$contador_capas],0,4)!="link") {

if (file_exists($web_path."/gestion/agenda/".$carpeta_capas."/".$nombres_partes[$contador_capas].".php")==false) {

        $nombre_archivo=$web_path."/gestion/agenda/modelos/modelo_partes.php";
        $gestor = fopen($nombre_archivo, "r");
        $cuerpo_documento = fread($gestor, filesize($nombre_archivo));
        fclose($gestor);

        $nuevo_archivo=fopen($web_path."/gestion/agenda/".$carpeta_capas."/".$nombres_partes[$contador_capas].".php","a");
        fputs($nuevo_archivo,$cuerpo_documento);
        fclose($nuevo_archivo);
        
        }
       

?>
<div id="capa_<?=$contador_capas;?>" style="display:<?php  if ($pagina_activa==$contador_capas) { print "block"; } else { print "none"; }?>;padding:0px;border:0px solid">
<?php  require($carpeta_capas.'/'.$nombres_partes[$contador_capas].'.php'); ?>
</div>
<?php 
        } else {
?>
<div id="capa_<?=$contador_capas;?>" style="display:<?php  if ($pagina_activa==$contador_capas) { print "block"; } else { print "none"; }?>;padding:0px;border:0px solid">
</div>
<?php 
        }

}
?>
</td>
</tr>
</table>
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="imprimir();" value="Imprimir" style="font-size:10px"> <input type="button" onclick="window.location.href='/';" value="Cerrar" style="font-size:10px">
                        </div><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
</center></div>
</div>
<?php 
/*
if(($_SERVER['REMOTE_ADDR'] == '84.127.234.248') or ($_SERVER['REMOTE_ADDR'] == '213.129.165.204')){

    echo '<table class="table table-striped table-bordered">';

    $profiler = mysql_query("SHOW PROFILES");
    while($pRow = mysql_fetch_array($profiler)) {

      echo '<tr><th>' . $pRow['Query_ID'] . '</th>'
         . '<td>' . $pRow['Duration'] . '</td>'
         . '<td>' . $pRow['Query'] . '</td></tr>';
    }
}
*/
?>
</body>
</html>
