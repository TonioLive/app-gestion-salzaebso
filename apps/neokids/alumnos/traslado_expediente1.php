<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_inscripcion=$_GET["id_alumno"];

$consulta_inscripcion=db_query_params('SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=?', 's', array($id_inscripcion)) or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["id_curso"];
$usuario_centro=$datos_inscripcion["id_centro"];

$consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$usuario_centro) or die(db_error()."-2");
$datos_centro=db_fetch_array($consulta_nombre_centro);

$id_usuario_seleccionado=$datos_inscripcion["id_usuario"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado)) or die(db_error()."-3");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                }
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "traslado_expediente2.php";

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="id_alumno" value="<?=$id_inscripcion;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Edición de Inscripción</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100" align="left"></a>
                                        <font size=2 face="<?= $fuente_general; ?>"><br>Alumno: <b><?=$usuario_apellido1?> <?=$usuario_apellido2?>, <?=$usuario_nombre?><br>
                                        Inscripción en el centro <i>"<?=$datos_centro["nombre_centro"];?>"</i>.<br>
                                        </font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Aula donde se tralada al niño</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><select name="id_nueva_aula" size=1>
                                        <?php 
                                        $busca_nuevas_aulas=db_query("SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centros.id_centro<>$usuario_centro AND centro_aulas.tipo=0 AND centro_aulas.id_curso=$usuario_curso ORDER BY centros.nombre_centro") or die(db_error());
                                        while($datos_nuevas_aulas=db_fetch_array($busca_nuevas_aulas)) {
                                        ?>
                                        <option value="<?=$datos_nuevas_aulas["id_aula"]?>"><?=$datos_nuevas_aulas["nombre_centro"]?>: <?=$datos_nuevas_aulas["nombre"]?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha del traslado</b></font></td>
                                        <td><select name="usuario_fechatras_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval(date("d"))) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechatras_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval(date("m"))) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechatras_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval(date("Y"))) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('usuario_fechatras','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="window.close()" value="Cancelar" style="font-size: 10px"><input type="button" onclick="cambia();" value="Siguiente >>" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
function intro_motivo() {
        window.open('listado_motivos.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
//-->
</script>
</center></div>
</div>
</body>
</html>
