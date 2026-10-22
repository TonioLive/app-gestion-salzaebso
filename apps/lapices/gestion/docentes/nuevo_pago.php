<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
if (($nivel_acceso==4) and ($ip_segura==true)) {

$mensaje="Nuevo";
$formulario_name="pagos";
$formulario_accion="nuevo_pago1.php";


$id_pago=$_GET["id_pago"];
$id_docente=$_GET["id_usuario"];
$id_contrato=$_GET["id_contrato"];

if ((strlen($id_docente)==0) and (strlen($id_contrato)!=0)) {

        // Busca datos contrato
        
        $busca_contratos=db_query("SELECT * FROM personal_contratos WHERE id_contrato=$id_contrato");

        if (db_num_rows($busca_contratos)!=0) {

                $datos_contrato=db_fetch_array($busca_contratos);
                $id_docente=$datos_contrato["id_empleado"];

                $id_categoria=$datos_contrato["id_categoria"];
                $tipo_contrato=$datos_contrato["tipo_contrato"];
                $salario_base=$datos_contrato["salario_base"];
                $observaciones=$datos_contrato["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=stripslashes($observaciones);
                        }
                        // Fechas
                        if ($datos_contrato["fecha_inicio"]=="0000-00-00") {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                } else {
                                $fecha_inicio_dia=date("j",strtotime($datos_contrato["fecha_inicio"]));
                                $fecha_inicio_mes=date("m",strtotime($datos_contrato["fecha_inicio"]));
                                $fecha_inicio_ano=date("Y",strtotime($datos_contrato["fecha_inicio"]));
                                }

                        if ($datos_contrato["fecha_fin"]=="0000-00-00") {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                } else {
                                $fecha_fin_dia=date("j",strtotime($datos_contrato["fecha_fin"]));
                                $fecha_fin_mes=date("m",strtotime($datos_contrato["fecha_fin"]));
                                $fecha_fin_ano=date("Y",strtotime($datos_contrato["fecha_fin"]));
                                }


                }

        }

if (strlen($id_pago)==0) {
        $id_pago=0;
        } else {
        $mensaje="Editar";
        
        // Busca datos del pago
        
        $busca_pago=db_query("SELECT * FROM personal_pagos WHERE id_pago=$id_pago");
        
        $datos_pago=db_fetch_array($busca_pago);
        
        if ($datos_pago["fecha_pago"]=="0000-00-00") {
                                $fecha_pago_dia="";
                                $fecha_pago_mes="";
                                $fecha_pago_ano="";
                                } else {
                                $fecha_pago_dia=date("j",strtotime($datos_pago["fecha_pago"]));
                                $fecha_pago_mes=date("m",strtotime($datos_pago["fecha_pago"]));
                                $fecha_pago_ano=date("Y",strtotime($datos_pago["fecha_pago"]));
                                }

        $importe_pago=$datos_pago["importe"];
        $tipo_pago=$datos_pago["tipo_pago"];
        $cuenta_pago=$datos_pago["cuenta"];
                $observaciones_pago=$datos_pago["observaciones"];
                if (strlen($observaciones_pago)!=0) {
                        $observaciones_pago=stripslashes($observaciones_pago);
                        }
        }

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query("SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=$id_docente AND horario_docentes.id_centro=$centro_actual") or die(db_error());

        $datos_docente=db_fetch_array($busca_datos_docente);
        $id_horario=$datos_docente["id_horario"];
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
                $nombre_docente=ucwords(strtolower($nombre_docente));

        }

$url_retorno="resumen_contrato.php?id_docente=$id_docente&id_contrato=$id_contrato";

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar(accion) {

                var errores=0;

                var mensajes_error="";
                
                la_fecha=self.document.<?=$formulario_name;?>.fecha_pago_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_ano.value;
                if (la_fecha!="--/--/----") {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_pago_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha del pago introducida no es correcta.\u000A";errores=1; }
                        } else {
                        mensajes_error=mensajes_error+"Debes introducir la fecha del pago.\u000A";
                        errores=1;
                        }

                if (self.document.<?=$formulario_name;?>.importe_pago.value!="") {
                        importe_pago=self.document.<?=$formulario_name;?>.importe_pago.value;
                        importe_pago=importe_pago.replace(",",".");
                        if (isNaN(importe_pago)) {
                                mensajes_error=mensajes_error+"El importe debe ser una cantidad numérica.\u000A";
                                errores=1;
                                } else {
                                if (importe_pago==0) {
                                        mensajes_error=mensajes_error+"El importe no puede ser 0.\u000A";
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
<input type="hidden" name="id_contrato" value="<?=$id_contrato;?>">
<input type="hidden" name="id_docente" value="<?=$id_docente;?>">
<input type="hidden" name="id_pago" value="<?=$id_pago;?>">
<input type="hidden" name="o" value="<?=$o;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> pago</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=4 face="<?= $fuente_general; ?>"><b><?=$nombre_docente?></b></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Categoría</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2">
                                <?php 
                                $busca_tipos_cat=db_query("SELECT * FROM personal_cat_laborales WHERE id_categoria=$id_categoria");

                                $tipos_cat=db_fetch_array($busca_tipos_cat);
                                ?>
                                <?=$tipos_cat["nombre_categoria"]?></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Tipo de contrato</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2">
                                <?php 
                                $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=$tipo_contrato");

                                $tipos_contrato=db_fetch_array($busca_tipos_contrato);
                                ?>
                                <?=$tipos_contrato["nombre_tipo_contrato"]?></font>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha alta</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($datos_contrato["fecha_inicio"]));?></font>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha baja</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($datos_contrato["fecha_fin"]));?></font>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Sal. Neto mensual</b></font></td>
                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($salario_base,2,",",".");?>  &#8364;</font>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Tipo de pago</b></font></td>
                                <td><select name="tipo_pago" style="font-size: 10px">
                                <?php 
                                $busca_tipos_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");
                                
                                while($tipos_pago=db_fetch_array($busca_tipos_pago)) {
                                ?>
                                <option value="<?=$tipos_pago["id_tipo_pago"]?>"<?php  if ($tipos_pago["id_tipo_pago"]==$tipo_pago) { print " selected"; } ?>><?=$tipos_pago["nombre_tipo_pago"]?></option>
                                <?php 
                                }
                                
                                ?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cuenta de pago</b></font></td>
                                <td><select name="cuenta_pago" style="font-size: 10px">
                                <?php 
                                $busca_cuentas_pago=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                while($cuentas_pago=db_fetch_array($busca_cuentas_pago)) {
                                ?>
                                <option value="<?=$cuentas_pago["id_cuenta"]?>"<?php  if ($cuentas_pago["id_cuenta"]==$cuenta_pago) { print " selected"; } ?>><?=$cuentas_pago["nombre_cuenta"]?></option>
                                <?php 
                                }

                                ?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha del pago</b></font></td>
                                <td><select name="fecha_pago_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_pago_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_pago_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_pago_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_pago_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=2005; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_pago_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_pago');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                                <td><font size=2><input type="text" name="importe_pago" style="font-size: 10px;text-align: right" size=20 value="<?=$importe_pago?>">  &#8364;</font>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                                <td><font size=2><textarea name="observaciones_pago" style="font-size: 11px;font-family: Arial" cols=100 rows=4><?=$observaciones_pago?></textarea></font>
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
}

require("../../configuracion/parte_inferior.php"); ?>
