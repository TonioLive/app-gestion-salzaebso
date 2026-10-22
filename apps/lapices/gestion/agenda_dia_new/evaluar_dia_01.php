<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$fecha_time_stamp=$_GET["dia"];
$id_aula=$_GET["id_aula"];
$fecha=$_GET["fecha"];

$busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND objetivo<>0 ORDER BY edad,tiempo_trabajo,orden_actividad");

$esta_evaluado=false;
        
        while($actividades_dia=db_fetch_array($busca_actividades_dia)) {
                $busca_evaluacion=db_query("SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_prog=".$actividades_dia["id_prog"]);
                if (db_num_rows($busca_evaluacion)!=0) {
                        $esta_evaluado=true;
                        }
                }
                
$evaluar=$_GET["evaluar"];
if (strlen($evaluar)==0) {
        $evaluar=0;
        }

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        }
        
        
// Busca programacion

        
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
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin">
<table width="100%" border="0">
  <tr>
    <td align="left">
    <b>Aula: <?=$nombre_aula?> - Evaluación de las actividades trabajadas el día <?=date("d/m/Y",$fecha_time_stamp);?></font></b><br><br>
<?php 

if (($esta_evaluado==false) and ($evaluar!=1)) {
?>
<p align="center"><font size=3 color="#f00"><b>Actividades NO evaluadas. ¿Deseas evaluarlas? En caso afirmativo, <a href="evaluar_dia_01.php?evaluar=1&dia=<?=$fecha_time_stamp?>&id_aula=<?=$id_aula?>&fecha=<?=$fecha?>">haz clic aquí</a></b></font></p>
<?php 
        }

$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=$id_aula AND fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY Apellido1, Apellido2, Nombre") or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

$busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND objetivo<>0 ORDER BY edad,tiempo_trabajo,orden_actividad");
$num_columnas=(db_num_rows($busca_actividades_dia)*2)*3;
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td rowspan=3><font size=2 face="<?= $fuente_general; ?>"><b>Alumno</b></font></td>
        <td align="center" colspan="<?=$num_columnas;?>"><font size=2 face="<?= $fuente_general; ?>"><b>Tiempos de trabajo/Actividades</b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <?php 
        while($actividades_dia=db_fetch_array($busca_actividades_dia)) {
        ?>
        <td align="center" colspan=3>Grupo: <?=$actividades_dia["edad"]+1?><br /><font size=2 face="<?= $fuente_general; ?>"><a title="Tiempo de trabajo: <?=$actividades_dia["tiempo_trabajo"]?>. Actividad Nº: <?=$actividades_dia["orden_actividad"]?>. <?=$actividades_dia["actividad"]?>"><b>Tiempo: <?=$actividades_dia["tiempo_trabajo"]?>/Actividad: <?=$actividades_dia["orden_actividad"]?></b></a></font></td>
        <?php 
        }
        ?>
        </tr>
        <tr bgcolor="#efefef">
        <?php 
        $busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND objetivo<>0 ORDER BY edad,tiempo_trabajo,orden_actividad");
        
        while($actividades_dia=db_fetch_array($busca_actividades_dia)) {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Asiste</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Supera</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Obs.</b></font></td>
        <?php 
        }
        ?>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        $lista_clicks_asi="";
        $lista_clicks_sup="";
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        $agenda_grupo=$listado_alumnos["agenda_grupo"];
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
        ?>
        <tr valign="top">
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></font></td>
        <?php 
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td align="center" colspan=<?=$num_columnas?>><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                </tr>
        <?php 
                } else {
        // Jornada completa o turno de mañana
                
                $busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND objetivo<>0 ORDER BY edad,tiempo_trabajo,orden_actividad");

                while($actividades_dia=db_fetch_array($busca_actividades_dia)) {
                
                // Quitar esta linea en la versión definitiva
                // $asiste=true;
                
                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."' AND turno=".$actividades_dia["turno"]) or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                $asiste=false;
                } else {
                $asiste=true;
                }
                
                // Busca si su grupo tiene esa actividad programada
                $agenda_grupo=$listado_alumnos["agenda_grupo"];
                
                if ($agenda_grupo!=($actividades_dia["edad"]+1)) {
                        $asiste=false;
                        }
                
                if ($asiste==true) {
                
                // Busca evaluacion del alumno
                
                $busca_evaluacion=db_query("SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." and id_prog=".$actividades_dia["id_prog"]);
                
                if ((db_num_rows($busca_evaluacion)==0) and ($evaluar==1)) {
                        $intro_evaluacion=db_query("INSERT INTO agenda_programacion_evaluacion_alumnos (id_prog,id_alumno,asistencia,superada) VALUES ('".$actividades_dia["id_prog"]."','".$listado_alumnos["id_alumno"]."','1','1');");
                        $busca_evaluacion=db_query("SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." and id_prog=".$actividades_dia["id_prog"]);
                        }
                $datos_evaluacion=db_fetch_array($busca_evaluacion);
                if ($datos_evaluacion["asistencia"]==1) {
                        $estado_boton1=" checked";
                        } else {
                        $estado_boton1="";
                        }
                if ($datos_evaluacion["superada"]==1) {
                        $estado_boton2=" checked";
                        } else {
                        $estado_boton2="";
                        }
                $estado_boton3="";
                $observaciones_actividad=$datos_evaluacion["observaciones"];
                $id_eval=$datos_evaluacion["id_eval"];
                } else {
                $estado_boton1=" disabled";
                $estado_boton2=" disabled";
                $estado_boton3=" disabled";
                $observaciones_actividad="";
                $id_eval=0;
                }
                
                $lista_clicks_asi.="document.getElementById('asi_".$id_eval."').click;";
                $lista_clicks_sup.="document.getElementById('sup_".$id_eval."').click;";
                
                ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input onclick="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_eval?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluacion_alumnos'},{desc:'campo',dato:'asistencia'},{desc:'valor',dato:document.getElementById('asi_<?=$id_eval?>').checked});" type="checkbox" name="asi_<?=$id_eval?>" id="asi_<?=$id_eval?>"<?=$estado_boton1?>></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input onclick="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_eval?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluacion_alumnos'},{desc:'campo',dato:'superada'},{desc:'valor',dato:document.getElementById('sup_<?=$id_eval?>').checked});" type="checkbox" name="sup_<?=$id_eval?>" id="sup_<?=$id_eval?>"<?=$estado_boton2?>></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input onblur="sendAndLoad({desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_eval?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluacion_alumnos'},{desc:'campo',dato:'observaciones'},{desc:'valor',dato:document.getElementById('obs_<?=$id_eval?>').value});" type="text" value="<?=$observaciones_actividad?>" maxlength="255" name="obs_<?=$id_eval?>" id="obs_<?=$id_eval?>" size="20"<?=$estado_boton3?>></font></td>
                <?php 
                }
                
                ?></font></td>
                </tr>
                <?php 
            }
        }
        ?>
        </form>
        </table>
        </td>
</tr>
</table>
<SCRIPT LANGUAGE=javascript>
<!--

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

function marcar_todos(campo) {

<?=$lista_clicks_asi;?>
<?=$lista_clicks_sup;?>

        }

//-->
</SCRIPT>
<?php 
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
