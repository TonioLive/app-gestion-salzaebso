<?php  require("../configuracion/parte_superior_privado.php");

if (strlen($usuario_curso)==0) {
        $usuario_curso=$_GET["id_curso_pre"];
        }

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }
        
$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso4b.php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query_params('UPDATE usuarios SET fecha_edicion=? WHERE id_usuario=?', 'ss', array((string)(strftime("%Y/%m/%d")), $id_usuario_seleccionado)) or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        } else {
        $id_centro_pre=$_GET["id_centro_pre"];
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "inscribir_lista_espera.php";

mt_srand((float)microtime()*1000000);
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
        <input type="hidden" name="id_usuario" value="<?=$id_usuario_seleccionado?>">
        <input type="hidden" name="id_curso_inscripcion" value="<?=$usuario_curso?>">
        <input type="hidden" name="id_centro_pre" value="<?=$id_centro_pre?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><br><b>Inscripción en lista de espera</b></font><br>
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Edad donde se inscribirá</b></font></td>
                                        <td><select name="edad_espera" size=1><?php 
                                        for ($c_anios=0;$c_anios<=5;$c_anios++) {
                                        ?>
                                        <option value="<?=$c_anios?>"><?=$c_anios;?> a <?=$c_anios+1;?> años</option>
                                        <?php 
                                        }
                                        ?></select></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha inscripción en lista</b></font></td>
                                        <td><select name="usuario_fechains_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==date("j")) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechains_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==date("n")) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechains_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==date("Y")) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechains','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de incorporación deseada</b></font></td>
                                        <td><select name="usuario_fechades_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechades_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechades_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechades','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <?php 
                                $busca_preguntas=db_query("SELECT * FROM lista_espera_cuestionarios WHERE desactivada=0 AND (id_curso=0 OR id_curso=$usuario_curso) ORDER BY id_tipo_punto");
                                
                                if (db_num_rows($busca_preguntas)!=0) {
                                ?>
                                <tr valign="top">
                                        <td colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Cuestionario</b></font></td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2>
                                        <table width=100% border=0 cellpadding=1 cellspacing=1>
                                <?php 
                                }
                                
                                while($lista_preguntas=db_fetch_array($busca_preguntas)) {
                                ?>
                                <tr vAlign="top">
                                        <td align="right" width=50% bgcolor="#efefef"><font face="<?= $fuente_general; ?>" size="2"><b><?=$lista_preguntas["descripcion"];?></b></font></td>
                                        <td width=50%><font face="<?= $fuente_general; ?>" size="2"><input name="P<?=$lista_preguntas["id_tipo_punto"]?>" type="radio" value="1"> Si <input name="P<?=$lista_preguntas["id_tipo_punto"]?>" type="radio" value="0"> No</font></td>
                                </tr>
                                <?php 
                                }
                                 if (db_num_rows($busca_preguntas)!=0) {
                                ?>
                                </table>
                                </td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                                        <td><textarea style="FONT-SIZE: 12px; FONT-FAMILY: Arial" name="observaciones" rows="6" cols="85"></textarea></td>
                                </tr>
                         </table>
                        <br>
                        <br><input type="button" onclick="inscribir();" value="Inscribir en lista de espera"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">

function inscribir() {

                var errores=0;

                var mensajes_error="";


                        // Comprueba fecha
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechains_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechains_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechains_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inscripción introducida no es correcta.\u000A";errores=1; }

                        // Comprueba fecha
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechades_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechades_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechades_ano.value
                        if (la_fecha!="--/--/----") {
                                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de incorporación introducida no es correcta.\u000A";errores=1; }
                                }



                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
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
//-->
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
