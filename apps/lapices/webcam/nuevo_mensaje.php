<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";
                  
                  
    // Busca mensajes
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $tipos_acciones_mensaje=array("Enviado","Introducida","Recibido");
        $nombres_tipos_mensajes=array("Mensaje Enviado","Instrucciones de Medicación","Mensaje Recibido");
        $nombres_tipos_mensajes=array("Mensaje","Instrucciones","Mensaje");
        $estados_mensajes=array("No leido","Leido","Cerrado");

    $formulario_name="formulario_mensaje";              
    ?>
         <div align="left"><font size=3><b>&nbsp;&nbsp;Nuevo mensaje</b></font></div><br>
         <table width=98%  class="tabla_datos" align="center">
         <form name="<?=$formulario_name;?>" action="nuevo_mensaje1.php" method="POST">
         <input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
         <input type="hidden" name="id_remitente" value="<?=$id_usuario?>">
         <tr>
         <td class="tabla_celda_encabezado" align="right" width="73"><font size=2><b>Para:</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><select size=1 style="font-size: 10px" name="destinatario">
         <?php 
    // Busca destinatarios (docentes de las aulas de sus hijos)
$busca_datos_alumno=db_query("SELECT *,usuarios.Nombre AS nombre_hijo FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d")."' AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.id_curso=$id_curso_actual AND alumnos.id_centro<>9 AND alumnos.id_centro<>10 AND alumnos.id_centro<>12 ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.") or die(db_error());

        $array_centros_alumno=array();

    if (db_num_rows($busca_datos_alumno)!=0) {
              while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
              
        if ($datos_alumno["sexo"]=="V") {
                        $sufijo_hijo="o";
                        } else {
                        $sufijo_hijo="a";
                        }
        
        // Finalmente muestra las cámaras
        
        // Datos de la escuela del niño
        $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_alumno["id_centro"]);
        
        $datos_escuela=db_fetch_array($busca_datos_escuela);
        ?>
        <option value="A_<?=$datos_alumno["padre_de"];?>_<?=$datos_alumno["id_centro"]?>">Educadora de <?=$datos_alumno["nombre_hijo"];?> de <?=$datos_escuela["nombre_centro"];?></option>
        <option value="C_<?=$datos_alumno["padre_de"];?>_<?=$datos_alumno["id_centro"]?>">Mensaje a la coordinadora de <?=$datos_escuela["nombre_centro"];?> sobre <?=$datos_alumno["nombre_hijo"]?></option>
        <?php 

        $array_centros_alumno[$datos_alumno["id_centro"]]=1;

       
     }   
    }
         ?>
         </select></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Asunto:</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><input type="text" name="asunto" value="" placeholder="Escribe aquí el asunto" size=90></font></td>
         </tr>
         <tr valign="top">
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Mensaje:</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><textarea id="texto_mensaje" name="texto_mensaje" placeholder="Escribe aquí tu mensaje o instrucciones para la educadora" style="font-size: 12px; font-family: Arial" cols="115" rows="8"></textarea></font></td>
         </tr>
         
         
         <tr>
         <td class="tabla_celda_encabezado" align="left" colspan=2><font size=3><b>Instrucciones medicación</b><br><i><small>(Información Opcional, completar solo en caso de querer transmitir a la educadora las instrucciones de toma de medicamentos)</small></i></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Nombre del medicamento</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><input type="text" name="medicamento" value="" placeholder="Escribe aquí el nombre del medicamente" size=90></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Cantidad que se debe administrar</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><input type="text" name="cantidad" value="" placeholder="Indicar la contidad a tomar y tambien la unidad (milimitros=ml, miligramos=mg, etc)" size=90></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Hora a la que se debe administrar</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><select size=1 style="font-size=10" name=hora>
  <?php 
for ($h=0; $h<=23; $h=$h+1)
  {
  if ($h<10)
  {
  $h="0".$h;
  }
  ?>
  <option value="<?php    echo $h; ?>"><?php    echo $h; ?></option>
  <?php 
  }
  ?>
  </select>:
  <select size=1 style="font-size=10" id=select1 name=min>
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
    </select></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Desde el día:</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><select name="fecha_inicio_dia" style="font-size: 10px">
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
                                                                      for ($c_ano=strftime("%Y")-1; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
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
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_inicio');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
         </tr>
         <tr>
         <td class="tabla_celda_encabezado" align="right"><font size=2><b>Hasta el día (incluido):</b></font></td>
         <td class="tabla_celda_datos" align="left"><font size=2><select name="fecha_fin_dia" style="font-size: 10px">
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
                                                                      for ($c_ano=strftime("%Y")-1; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
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
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_fin');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
         </tr>
         </table><br>
         <input type="button" value="Cancelar" onclick="window.location.href='mensajeria.php'" class="tabla_celda_encabezado"> <input type="button" onclick="enviar_mensaje()" value="Enviar" class="tabla_celda_encabezado">
         </form>        
         <br>
 <script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function enviar_mensaje() {

                var errores=0;

                var mensajes_error="";
             
             	if (self.document.<?=$formulario_name;?>.medicamento.value!="") {
             
                la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value;
                if (la_fecha!="--/--/----") {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inicio introducida no es correcta.\u000A";errores=1; }
                        } else {
                        mensajes_error=mensajes_error+"Debes introducir la fecha de inicio de la medicación.\u000A";errores=1;
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
                                /*        
                                if (la_fecha_numero1<=<?=date("Y")+date("n")+date("j")?>) {
                                                mensajes_error=mensajes_error+"La fecha de inicio debe ser posterior a hoy. Las instrucciones de medicación se deben enviar con un día de antelación.\u000A";
                                        errores=1;
                                        }
                                */
                                
                                }
                        } else {
                        mensajes_error=mensajes_error+"Debes introducir la fecha de finalización de la medicación.\u000A";errores=1;
                        }
       
       			}
       
                if (self.document.<?=$formulario_name;?>.asunto.value=="") {
                         mensajes_error=mensajes_error+"No has escrito el asunto del mensaje.\u000A";
                                                 errores=1;
                        } 
                if (self.document.<?=$formulario_name;?>.texto_mensaje.value=="") {
                         mensajes_error=mensajes_error+"No has escrito el texto del mensaje.\u000A";
                                                 errores=1;
                        }  
                        
                if (self.document.<?=$formulario_name;?>.medicamento.value!="") {
                        if (self.document.<?=$formulario_name;?>.cantidad.value=="") {
                         mensajes_error=mensajes_error+"Falta indicar la cantidad de medicamento que se debe administrar.\u000A";
                                                 errores=1;
                                                 }
                                        if ((Number(self.document.<?=$formulario_name;?>.hora.value)<7)||(Number(self.document.<?=$formulario_name;?>.hora.value)>19)) {
                                                mensajes_error=mensajes_error+"La hora introducida parece ser incorrecta, a esa hora tu hijo/a posiblemente no se encuentre en el centro.\u000A";
                                                errores=1;
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
    <?php                 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
