<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<div align="center"><center>
<?php 
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "notificar1.php";

$grupo=$_GET["grupo"];
$filtro_grupo="";
if (strlen($grupo)!=0) {
	$filtro_grupo="WHERE grupo='$grupo' ";
	} 

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=4 face="<?= $fuente_general; ?>"><b>Nueva incidencia</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                          <td  width=250 align="right" bgcolor="#ececec"><font size=3 face="<?= $fuente_general; ?>"><b>Tipo de incidencia:</b> </font></td>
                          <td align="left" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><select name="tipo" size=1 style="font-size: 14px">
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM incidencias_tipos ".$filtro_grupo."ORDER BY id_tipo");
                                while($tipos=db_fetch_array($busca_tipos)){
                                ?>
                                <option value="<?=$tipos["id_tipo"];?>"><?=$tipos["nombre_tipo"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=250 align="right" bgcolor="#ececec"><font size=3 face="Arial, Helvetica"><b>Incidencia</b></font></td>
                                <td><font size="2" face="Arial, Helvetica"><input type="text" name="nombre_incidencia" size=40 readonly value="" style="font-size: 14px" onclick="intro_motivo()"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=250 align="right" bgcolor="#ececec"><font size=3 face="Arial, Helvetica"><b>Alumno/Padre/Personal lápices</b></font></td>
                                <input type="hidden" name="id_usuario_incidencia" value="0">
                                <td><font size="2" face="Arial, Helvetica"><input type="text" name="nombre_usuario_incidencia" size=40 readonly value="" style="font-size: 14px" onclick="intro_usuario()"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Describe aquí la incidencia que deseas notificar</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><textarea name="descripcion" cols=85 rows=8 style="font-size: 11px; font-family: Arial"></textarea></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Describe aquí las medidas adoptadas para solucionar la incidencia</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><textarea name="medidas_adoptadas1" cols=85 rows=8 style="font-size: 11px; font-family: Arial"></textarea></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="history.go(-1);" value="Cancelar" style="font-size: 14px"> <input type="button" onclick="cambia();" value="Finalizar >>" style="font-size: 14px"><br><br>
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
                
                if (self.document.<?=$formulario_name;?>.nombre_incidencia.value=="") { mensajes_error=mensajes_error+"Debes poner un nombre a la incidencia o seleccionar de la lista .\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.descripcion.value=="") { mensajes_error=mensajes_error+"Debes describir la incidencia.\u000A";errores=1; }

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }

function intro_usuario() {
        window.open('seleccionar_usuario.php','tipos','toolbar=no,resizable=yes, scrollbars=yes,width=450,height=400');
        }
function intro_motivo() {
        window.open('listado_tipos_incidencias.php','tipos','toolbar=no,resizable=yes, scrollbars=yes,width=250,height=400');
        }
//-->
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>
