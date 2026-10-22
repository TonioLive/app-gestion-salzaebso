<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$fecha_time_stamp=$_GET["dia"];
$id_aula=$_GET["id_aula"];
$fecha=$_GET["fecha"];

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

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
</head>

<body>
<table width="100%" border="0">
  <tr>
    <td align="left">
    <b>Aula: <?=$nombre_aula?> - Evaluación de las actividades trabajadas el día <?=date("d/m/Y",$fecha_time_stamp);?></font></b><br><br>
<?php 
$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($id_aula, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

$busca_actividades_dia=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha=? AND objetivo<>0 ORDER BY tiempo_trabajo,orden_actividad', 'ss', array($id_aula, (string)(date("Y-m-d", $fecha_time_stamp))));
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
        <td align="center" colspan=3><font size=2 face="<?= $fuente_general; ?>"><a title="Tiempo de trabajo: <?=$actividades_dia["tiempo_trabajo"]?>. Actividad Nº: <?=$actividades_dia["orden_actividad"]?>. <?=$actividades_dia["actividad"]?>"><b>Tiempo: <?=$actividades_dia["tiempo_trabajo"]?>/Actividad: <?=$actividades_dia["orden_actividad"]?></b></a></font></td>
        <?php 
        }
        ?>
        </tr>
        <tr bgcolor="#efefef">
        <?php 
        $busca_actividades_dia=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha=? AND objetivo<>0 ORDER BY tiempo_trabajo,orden_actividad', 'ss', array($id_aula, (string)(date("Y-m-d", $fecha_time_stamp))));
        
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
                
                $busca_actividades_dia=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha=? AND objetivo<>0 ORDER BY tiempo_trabajo,orden_actividad', 'ss', array($id_aula, (string)(date("Y-m-d", $fecha_time_stamp))));

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
                
                if ($asiste==true) {
                
                // Busca evaluacion del alumno
                
                $busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_alumno=? and id_prog=?', 'ss', array($listado_alumnos["id_alumno"], $actividades_dia["id_prog"]));
                
                if (db_num_rows($busca_evaluacion)==0) {
                        $intro_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluacion_alumnos (id_prog,id_alumno,asistencia,superada) VALUES (?,?,\'1\',\'1\');', 'ss', array((string)($actividades_dia["id_prog"]), (string)($listado_alumnos["id_alumno"])));
                        $busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_alumno=? and id_prog=?', 'ss', array($listado_alumnos["id_alumno"], $actividades_dia["id_prog"]));
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
