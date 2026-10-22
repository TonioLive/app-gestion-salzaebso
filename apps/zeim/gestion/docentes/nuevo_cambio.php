<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$mensaje="Nuevo";
$formulario_name="cambios_aula";
$formulario_accion="nuevo_cambio1.php";

$id_docente=$_GET["id_usuario"];
$id_cambio=$_GET["id_cambio"];

if ((strlen($id_docente)==0) and (strlen($id_cambio)!=0)) {

$mensaje="Editar";

        // Busca datos cambio de aula
        
        $busca_cambios=db_query_params('SELECT * FROM personal_historial WHERE id=?', 's', array($id_cambio));

        if (db_num_rows($busca_cambios)!=0) {

                $datos_cambio=db_fetch_array($busca_cambios);
                $id_docente=$datos_cambio["id_empleado"];
                $id_escuela=$datos_cambio["id_escuela"];
                $id_aula=$datos_cambio["id_aula"];

                $observaciones=$datos_cambio["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=stripslashes($observaciones);
                        }
                        // Fechas
                        if (($datos_cambio["fecha_inicio"]=="0000-00-00") or (strlen($datos_cambio["fecha_inicio"])==0)) {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                } else {
                                $fecha_inicio_dia=date("j",strtotime($datos_cambio["fecha_inicio"]));
                                $fecha_inicio_mes=date("m",strtotime($datos_cambio["fecha_inicio"]));
                                $fecha_inicio_ano=date("Y",strtotime($datos_cambio["fecha_inicio"]));
                                }

                        if (($datos_cambio["fecha_fin"]=="0000-00-00") or (strlen($datos_cambio["fecha_fin"])==0)) {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                } else {
                                $fecha_fin_dia=date("j",strtotime($datos_cambio["fecha_fin"]));
                                $fecha_fin_mes=date("m",strtotime($datos_cambio["fecha_fin"]));
                                $fecha_fin_ano=date("Y",strtotime($datos_cambio["fecha_fin"]));
                                }


                }

        }

if (strlen($id_cambio)==0) {
        $id_cambio=0;
        }

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error());

        $datos_docente=db_fetch_array($busca_datos_docente);
        $id_horario=$datos_docente["id_horario"];
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
                $nombre_docente=ucwords(strtolower($nombre_docente));

        }

$url_retorno="horario_docente.php?id_docente=$id_docente";

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar(accion) {

                var errores=0;

                var mensajes_error="";
                
                la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value;
                if (la_fecha!="--/--/----") {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inicio introducida no es correcta.\u000A";errores=1; }
                        } else {
                        mensajes_error=mensajes_error+"Debes introducir la fecha de inicio del cambio.\u000A";
                        errores=1;
                        }
                la_fecha2=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value;
                if (la_fecha2!="--/--/----") {
                        la_fecha2=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value
                        if (isDate(la_fecha2)!=true) { mensajes_error=mensajes_error+"La fecha de finalización introducida no es correcta.\u000A";errores=1; }
                        
                        if (errores==0) {

                                la_fecha_numero1=Number(self.document.<?=$formulario_name;?>.fecha_inicio_ano.value+self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value);
                                la_fecha_numero2=Number(self.document.<?=$formulario_name;?>.fecha_fin_ano.value+self.document.<?=$formulario_name;?>.fecha_fin_mes.value+self.document.<?=$formulario_name;?>.fecha_fin_dia.value);
                                
                                if (la_fecha_numero2<la_fecha_numero1) {
                                        mensajes_error=mensajes_error+"La fecha de finalización no puede ser anterior a la fecha de inicio.\u000A";
                                        errores=1;
                                        }
                                
                                }
                        }
                        
                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
function selecciona_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_ano.value;");

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

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino=<?=$formulario_name;?>&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_cambio" value="<?=$id_cambio;?>">
<input type="hidden" name="id_docente" value="<?=$id_docente;?>">
<input type="hidden" name="o" value="<?=$o;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> cambio de aula</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=4 face="<?= $fuente_general; ?>"><b><?=$nombre_docente?></b></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Aula</b></font></td>
                                <td><select name="id_aula" style="font-size: 10px">
                                <?php 
                                $busca_aulas_cambio=db_query("SELECT *,cursos_academicos.nombre AS nombre_curso,centro_aulas.nombre AS nombre_aula FROM centros,centro_aulas, cursos_academicos WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_curso=cursos_academicos.id_curso ORDER BY cursos_academicos.id_curso DESC,centros.id_centro,centro_aulas.id_aula");
                                
                                while($aulas_cambio=db_fetch_array($busca_aulas_cambio)) {
                                ?>
                                <option value="<?=$aulas_cambio["id_aula"]?>"<?php  if ($aulas_cambio["id_aula"]==$id_aula) { print " selected"; } ?>>Curso: <?=$aulas_cambio["nombre_curso"]?>, <?=$aulas_cambio["nombre_centro"]?>, <?=$aulas_cambio["nombre_aula"]?></option>
                                <?php 
                                }
                                
                                ?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha alta</b></font></td>
                                <td><select name="fecha_inicio_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_inicio_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_inicio_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=2004; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_inicio_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_inicio');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha baja</b></font></td>
                                <td><select name="fecha_fin_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_fin_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_fin_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=2004; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_fin_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_fin');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                                <td><font size=2><textarea name="observaciones" style="font-size: 11px;font-family: Arial" cols=100 rows=4><?=$observaciones?></textarea></font>
                                </td>
                        </tr>
                        </table><br>
                        <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Guardar cambios y salir >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);">
                        <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 

require("../../configuracion/parte_inferior.php"); ?>
