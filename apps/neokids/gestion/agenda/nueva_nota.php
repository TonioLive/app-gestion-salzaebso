<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nueva_nota1.php";

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

function seleccionar_frase(frase) {
        frase_actual=document.<?=$formulario_name?>.nota.value+frase;
        document.<?=$formulario_name?>.nota.value=frase_actual;
        }
function borrar_observaciones() {
        document.<?=$formulario_name?>.nota.value="";
        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$estado_nota[0]="<font color='#ff0000'>Pendiente</font>";
$estado_nota[1]="<font color='#00ff00'>Realizada</font>";

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$id_aula=$_GET["id_grupo"];
$dia=$_GET["dia"];
$id_alumno=$_GET["id_alumno"];
if (strlen($id_alumno)==0) {
        $id_alumno=0;
        }
$id_area=$_GET["area"];

$id_nota=$_GET["id_nota"];

if (strlen($id_nota)==0) {
        $accion_texto="Nueva instrucción, nota o incidencia<br>";
        $hora_nota=date("H:i");
        } else {
        $accion_texto="Editar instrucción, nota o incidencia<br>";
        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE id=?', 's', array($id_nota));

        $datos_nota=db_fetch_array($busca_nota);
        $hora_nota=$datos_nota["hora"];
        $nota=$datos_nota["nota"];
        $avisar=$datos_nota["avisar"];
        $tipo_nota=$datos_nota["tipo_nota"];
        
        $id_aula=$datos_nota["id_aula"];
        $dia=strtotime($datos_nota["dia"]);
        $id_alumno=$datos_nota["id_alumno"];
        $id_area=$datos_nota["area"];
        
        }

if ($id_alumno!=0) {

$busca_datos_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno));

$datos_alumno=db_fetch_array($busca_datos_alumno);

$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
$accion_cancelar="window.close();";
} else {

$nombre_completo_alumno="Aula completa";

switch($id_area){

	case 1: 
		$pagina_Destino=1;
		break;
	
	case 3:
		$pagina_Destino=2;
		break;

	}

$accion_cancelar="self.location.href='dia_aula.php?dia=$dia&id_grupo=$id_aula&pagina_activa=".$pagina_Destino."'";

}

$busca_aulas=db_query_params('SELECT * FROM aulas_grupos WHERE id_grupo=?', 's', array($id_aula));

if (db_num_rows($busca_aulas)!=0){
        $datos_grupo=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$dia_sem_largo[$datos_grupo["dia"]]." ".date("H:i",strtotime($datos_grupo["hora_inicio"]))."-".date("H:i",strtotime($datos_grupo["hora_fin"]));;

        }

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="id_aula" value="<?=$id_aula?>">
<input type="hidden" name="dia" value="<?=$dia?>">
<input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
<input type="hidden" name="area" value="<?=$id_area?>">
<input type="hidden" name="id_nota" value="<?=$id_nota?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=3 face="<?= $fuente_general; ?>"><br><b>Seguimiento de <?=$nombres_areas[$id_area]?><br>Alumno <i><?=$nombre_completo_alumno;?></i> el día <?=date("d/m/Y",$dia)?></font></b><br><br><b><?=$accion_texto?></b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
  <tr>
    <td width="17%" align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Hora:</b></td>
    <td width="83%"><small>
    <select size=1 style="font-size=10" name=hora>
    <?php 
  $hora=date("H",strtotime($hora_nota));
  $sele="";
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    }
    if (intval($h)==intval($hora))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    }

?>
    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
    <?php 

  }

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min>
    <?php 
  $minuto=date("i",strtotime($hora_nota));
  $sele="";
  for ($m=0; $m<=59; $m++)
  {
    if ($m<10)
    {
      $m="0".$m;
    }
    if (intval($m)==intval($minuto))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    }

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php 

  }

?>
    </select>
    </small></td>
  </tr>
  <tr>
    <td width="17%" align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Avisar:</b></small></font></td>
    <td width="83%"><font face="Arial" color="#800000" size=2><select style="font-size=10" name="avisar">
    <option value="0">No avisar</option>
    <option value="1">A la hora indicada</option>
    <?php  for($c_dias=15;$c_dias<=120;$c_dias=$c_dias+15) { ?>
    <option value="<?=$c_dias?>"<?php  if ($avisar==$c_dias) { print " selected"; } ?>><?=$c_dias?> minutos antes</option>
    <?php  } ?>
    </select></font></td>
  </tr>
  <tr>
    <td width="17%" align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo:</b></small></font></td>
    <td width="83%"><font face="Arial" color="#800000" size=2><select style="font-size=10" name="tipo_nota">
    <?php 
    $busca_notas_inci=db_query_params('SELECT * FROM agenda_alumnos_tipos_notas WHERE area=?', 's', array($id_area));
    while($notas_inci=db_fetch_array($busca_notas_inci)) {
    ?>
    <option value="<?=$notas_inci["id_tipo"]?>"<?php  if ($tipo_nota==$notas_inci["id_tipo"]) { print " selected"; } ?>><?=$notas_inci["nombre_tipo"]?></option>
    <?php  } ?>
    </select></font></td>
  </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Comentarios:</b></font></td>
                                <td align="left"><textarea  name="nota" cols=115 rows=8 style="font-size: 11px; font-family: Arial"><?=$nota;?></textarea></td>
</tr>
            <tr>
              <td align="right" valign="top" bgcolor="#efefef"><font size=1><b>Frases tipo<br>
              Clic sobre ellas para añadir al texto</b></font></td>
              <td align="left"><font size=2><?php 

              // Busca frases tipo
              $busca_frases=db_query_params('SELECT * FROM agenda_alumnos_observaciones_tipo WHERE tipo=? ORDER BY observacion', 's', array($id_area));
              while($frases=db_fetch_array($busca_frases)) {
                        ?><a href="javascript:seleccionar_frase('<?=$frases["observacion"]?>')">- <?=$frases["observacion"]?></a><br><?php 
                        }

              ?>&nbsp;</font></td>
            </tr>
                         </table>
                         <br><input type="button" onclick="<?=$accion_cancelar?>" value="Cancelar" style="font-size: 10px"> <input name="Borrar_texto" type="button" id="Borrar_texto" value="Borrar texto" onclick="borrar_observaciones();"  style="font-size: 10px" /> <input type="button" onclick="cambia();" value="Finalizar >>" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">

function cambia() {
                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.nota.value=="") { mensajes_error=mensajes_error+"Debes escribir un texto.\u000A";errores=1; }

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }

//-->
</script>
</body>
</html>
