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
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
        <input type="hidden" name="id_usuario" value="<?=$id_usuario_seleccionado?>">
        <input type="hidden" name="id_curso_inscripcion" value="<?=$usuario_curso?>">
        <input type="hidden" name="id_centro_pre" value="<?=$id_centro_pre?>">
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><br><b>Inscripción en lista de espera</b></font><br>
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Grupo donde se inscribirá</b></font></td>
                                        <td><select name="edad_espera" size=1><?php 
                                        
                                        $array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");

for ($c_anios=0;$c_anios<count($array_listas_de_espera);$c_anios++) {
                                        ?>
                                        <option value="<?=$c_anios?>"><?=$array_listas_de_espera[$c_anios];?></option>
                                        <?php 
                                        }
                                        ?></select></td>
                                </tr>
                                <tr vAlign="top">
                                	<td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Nivel natación</b></font></td>
									 <td>
                                <select name="nivel_natacion" id="nivel_natacion" style="background-color: <?=$colores_subniveles[$nivel_natacion]?>; color: #fff">
                        <option value="0" style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1" style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2" style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4" style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5" style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7" style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8" style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td>
                                </tr>                                  
                                <tr vAlign="top">
                                	<td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Tipo de usuario</b></font></td>
									 <td>
                                     	<select name="categoria" size=1>                                       
											<option value="0">Nueva incorporación o reincorporación después de ausencia</option>
											<option value="1">Ampliación de días de asistencia</option>
											<option value="2">Cambio de grupo</option>
										</select></td>
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
                                <tr>
                                <td valign="top" colspan="2"><h4>Días y horarios deseados</h4>
                                <table width=100% border=1 cellpadding="2" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                <td width=30%><b>Día de la semana</b></td>
                                <td width=30% align="center"><i>Marcar el que desea</i></td>
                                <td width=20% align="center"><b>De...</b></td>
                                <td width=20% align="center"><b>...a</b></td>
                                </tr>
                                <?php 
                                for ($c_dia=1; $c_dia<=7; $c_dia=$c_dia+1) {
                                ?>
                                <tr>
                                <td><b><?=$dia_sem_largo[$c_dia]?></b></td>
                                <td align="center"><input type="checkbox" value="1" name="dia_deseado_<?=$c_dia?>" id="dia_deseado_<?=$c_dia?>"></td>
                                <td align="center"><select size=1 style="font-size=10" name=hora_i_<?=$c_dia?>>
    <?php  
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 

?>
    <option value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min_i_<?=$c_dia?>>
    <?php  
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 

?>
    <option value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select></td>
                                <td align="center"><select size=1 style="font-size=10" name=hora_f_<?=$c_dia?>>
    <?php  
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 

?>
    <option value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min_f_<?=$c_dia?>>
    <?php  
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 

?>
    <option value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select></td>
                                </tr>
                                <?php 
                                	}
                                ?>
                                </table>
                                </td>
                                
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
</form>
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
        
$('#nivel_natacion').change(function() {

	var colores_subniveles = new Array('<?=implode("','",$colores_subniveles)?>');
	var nombres_subniveles = new Array('<?=implode("','",$nombres_subniveles)?>');  
	
	var current = $(this).val();
	
	$(this).css('background-color', colores_subniveles[current] );

});        
        
//-->
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
