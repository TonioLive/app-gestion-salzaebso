<?php  require("../configuracion/parte_superior_privado.php");

$formulario_name="nueva_lista";

?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

function comprueba_y_envia(listado) {
        var errores="";

        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_ini_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_ini_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_ini_ano.value
        if (isDate(la_fecha)!=true) { errores=errores+"La fecha de inicio del periodo de asistencia introducida no es correcta.\u000A"; }

        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value
        if (isDate(la_fecha)!=true) { errores=errores+"La fecha de fin del periodo de asistencia introducida no es correcta.\u000A"; }

        self.document.<?=$formulario_name;?>.listado.value=listado;
        
        if (listado==2) {
                self.document.<?=$formulario_name;?>.action="listado_consumos.php";
                }
        if (self.document.<?=$formulario_name;?>.seleccionar_dias.checked==true) {
                self.document.<?=$formulario_name;?>.action="nueva_lista_compra_cal.php";
                }
        
        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.<?=$formulario_name;?>.submit();
                self.location.href='gestion_cocina.php';
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
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Nueva lista de la copra</b><br><br>Selecciona las fechas entre las que quieres hacer la previsión de consumo.</font><br>
                        <br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="<?=$formulario_name;?>" method="POST" action="nueva_lista_compra1.php" target="_new">
                        <input type="hidden" name="listado" value="0">
                        <tr valign="top" id="mensual1">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Fecha inicio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_ini_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_ini_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ini_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_ini_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ini_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=date("Y")-5; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_ini_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_ini','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                        </tr>
                        <tr valign="top" id="mensual2">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Fecha fin:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_fin_dia" style="font-size: 10px">
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
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=date("Y")-5; $c_ano=$c_ano-1) {
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_fin','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                        </tr>
                         </table><font size=2><br>
                         <input type="checkbox" value="1" name="seleccionar_dias"> Marca esta casilla si quieres seleccionar los días individualmente.
                         <br><br></font>
                         <input style="font-size: 10px" type="button" value="Ver cantidades >>" onclick="comprueba_y_envia(0);"> <input style="font-size: 10px" type="button" value="Ver listados >>" onclick="comprueba_y_envia(1);"> <input style="font-size: 10px" type="button" value="Consultar consumo >>" onclick="comprueba_y_envia(2);"> <input type="button" value="Cancelar" onclick="window.location.href='gestion_cocina.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
