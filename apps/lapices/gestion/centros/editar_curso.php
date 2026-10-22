<?php  require("../../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php 
$formulario_name="cursos";
$formulario_accion="editar_curso1.php";

$id_curso=$_GET["id_curso"];
$id_aula=$_GET["id_aula"];
$o=$_GET["o"];
if (strlen($o)==0) {
        $url_retorno="editar_aula.php?id_aula=".$id_aula;
        } else {
        $url_retorno=$o;
        }

if (strlen($id_curso)!=0) {

        $busca_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
        if (db_num_rows($busca_datos_curso)!=0) {
                $datos_curso=db_fetch_array($busca_datos_curso);
                $mensaje="Edición";
                $nombre_curso=$datos_curso["nombre"];
                $fecha_inicio_dia=date("j",strtotime($datos_curso["fecha_inicio"]));
                $fecha_inicio_mes=date("n",strtotime($datos_curso["fecha_inicio"]));
                $fecha_inicio_ano=date("Y",strtotime($datos_curso["fecha_inicio"]));
                if ($datos_curso["fecha_inicio"]=="0000-00-00") {
                        $fecha_inicio_dia="";
                        $fecha_inicio_mes="";
                        $fecha_inicio_ano="";
                        }
                $fecha_fin_dia=date("j",strtotime($datos_curso["fecha_fin"]));
                $fecha_fin_mes=date("n",strtotime($datos_curso["fecha_fin"]));
                $fecha_fin_ano=date("Y",strtotime($datos_curso["fecha_fin"]));
                if ($datos_curso["fecha_fin"]=="0000-00-00") {
                        $fecha_fin_dia="";
                        $fecha_fin_mes="";
                        $fecha_fin_ano="";
                        }
                $fecha_limite_dia=date("j",strtotime($datos_curso["fecha_limite_presentacion"]));
                $fecha_limite_mes=date("n",strtotime($datos_curso["fecha_limite_presentacion"]));
                $fecha_limite_ano=date("Y",strtotime($datos_curso["fecha_limite_presentacion"]));
                if ($datos_curso["fecha_limite"]=="0000-00-00") {
                        $fecha_limite_dia="";
                        $fecha_limite_mes="";
                        $fecha_limite_ano="";
                        }
                $estado=$datos_curso["estado"];
                $comprobar_inscripcion=$datos_curso["comprobar_inscripcion"];
                $comprobar_cuotas=$datos_curso["comprobar_cuotas"];
                $dia_fin_pedidos=$datos_curso["dia_fin_pedidos"];
                } else {
                $id_curso="";
                $mensaje="Nuevo";
                }
        } else {
                $id_curso="";
                $mensaje="Nuevo";
        }

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.nombre_curso.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre del curso Académico (Ejemplo. 2004-2005).\u000A";errores=1; }
                
                // Comprueba fecha inicio
                la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inicio del curso introducida no es correcta.\u000A";errores=1; }
                
                // Comprueba fecha fin
                la_fecha=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de finalización del curso introducida no es correcta.\u000A";errores=1; }

                // Comprueba fecha limite
                la_fecha=self.document.<?=$formulario_name;?>.fecha_limite_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_limite_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_limite_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha límite de presentación de subvencions para el curso introducida no es correcta.\u000A";errores=1; }
                
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
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
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_curso" value="<?=$id_curso;?>">
<input type="hidden" name="id_aula" value="<?=$id_aula;?>">
<input type="hidden" name="o" value="<?=$o;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><h3><?=$mensaje;?> Curso Académico</h3><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del Curso</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre del Curso Académico:</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="nombre_curso" value="<?=$nombre_curso?>" size="50"> * Ejemplo: <i>"Curso 2004-2005"</i></font></td>
                        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de inicio del Curso:</b></font></td>
        <td><select name="fecha_inicio_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fecha_inicio_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fecha_inicio_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=strftime("%Y")-15; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fecha_inicio_ano) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=2><a href="javascript:selecciona_fecha('fecha_inicio','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de fin del Curso:</b></font></td>
        <td><select name="fecha_fin_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fecha_fin_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fecha_fin_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=strftime("%Y")-15; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fecha_fin_ano) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=2><a href="javascript:selecciona_fecha('fecha_fin','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha limite de presentación de subvenciones:</b></font></td>
        <td><select name="fecha_limite_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fecha_limite_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_limite_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fecha_limite_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_limite_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=strftime("%Y")-15; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fecha_limite_ano) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=2><a href="javascript:selecciona_fecha('fecha_inicio','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Estado del Curso:</b></font></td>
        <td><select name="estado" class="defaut">
        <option value="0"<?php  if ($estado==0) { print " selected"; } ?>>En peparación</option>
        <option value="1"<?php  if ($estado==1) { print " selected"; } ?>>Iniciado</option>
        <option value="2"<?php  if ($estado==2) { print " selected"; } ?>>Finalizado</option>
        </select></td>
        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>"><b>¿Requerir inscripción para fichar?</b></font></td>
                           <td align="left" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>"><input type="radio" name="comprobar_inscripcion" value="1"<?php  if ($comprobar_inscripcion==1) { echo(" checked"); } ?>>Si &nbsp; &nbsp;<input type="radio" name="comprobar_inscripcion" value="0"<?php  if ($comprobar_inscripcion==0) { echo(" checked"); } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>"><b>¿Requerir servicio de educación?</b></font></td>
                           <td align="left" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>"><input type="radio" name="comprobar_cuotas" value="1"<?php  if ($comprobar_cuotas==1) { echo(" checked"); } ?>>Si &nbsp; &nbsp;<input type="radio" name="comprobar_cuotas" value="0"<?php  if ($comprobar_cuotas==0) { echo(" checked"); } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>"><b>Día del mes en que finaliza el plazo para hacer los pedidos de material</b></font></td>
                           <td align="left" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">
                                <select name="dia_fin_pedidos" class="defaut">
                                        <option value="--">--</option>
                                        <?php 
                                        for ($c_dia=15; $c_dia<=28; $c_dia=$c_dia+1) {
                                        if ($c_dia<10) {
                                        $dia_a="0".$c_dia;
                                        } else {
                                        $dia_a=$c_dia;
                                        }
                                        ?>
                                        <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$dia_fin_pedidos) { print " selected"; } ?>><?= $dia_a-1; ?></option>
                                        <?php 
                                        }
                                        ?>
                                </select>
                        </td>
                        </tr>
                        </table>
                        <?php
	                    
	                    if ($id_curso) {
		                    
	                    ?>
                        <h3>Trimestres</h3>
                        <table class="table table-condensed table-bordered">
							<tr>
		                       <td class="col-md-2">&nbsp;</td>
		                       <td colspan=2 class="col-md-4 text-center">1er Ciclo</td>
		                       <td colspan=2 class="col-md-4 text-center">2º Ciclo</td>
	                       </tr>
	                       <?php
		                   // Busca periodos

						   for($contador=1; $contador<=3;$contador++) {
			                   
			                   if ($datos_curso["inicio_trim".$contador."_1"]) {
				                   $ini_1=date("d/m/Y",strtotime($datos_curso["inicio_trim".$contador."_1"]));
				                } else {
					               $ini_1="";
				                }
			                   if ($datos_curso["inicio_trim".$contador."_2"]) {
				                   $ini_2=date("d/m/Y",strtotime($datos_curso["inicio_trim".$contador."_2"]));
				                } else {
					               $ini_2="";
				                }			                				                				                				                				                			               
			                   if ($datos_curso["fin_trim".$contador."_1"]) {
				                   $fin_1=date("d/m/Y",strtotime($datos_curso["fin_trim".$contador."_1"]));
				                } else {
					               $fin_1="";
				                }
			                   if ($datos_curso["fin_trim".$contador."_2"]) {
				                   $fin_2=date("d/m/Y",strtotime($datos_curso["fin_trim".$contador."_2"]));
				                } else {
					               $fin_2="";
				                } 				                				                				                				                   
		                   ?> 	                        
	                       <tr>
		                       <td>Trimestre <?=$contador?></td>
		                       <td><input type="text" id="inicio_trim<?=$contador?>_1" name="inicio_trim<?=$contador?>_1" value="<?=$ini_1?>" class="form-control" placeholder="Incio trimestre <?=$contador?>"></td>
		                       <td><input type="text" id="fin_trim<?=$contador?>_1" name="fin_trim<?=$contador?>_1" value="<?=$fin_1?>" class="form-control" placeholder="Fin trimestre <?=$contador?>"></td>    		                       
		                       <td><input type="text" id="inicio_trim<?=$contador?>_2" name="inicio_trim<?=$contador?>_2" value="<?=$ini_2?>" class="form-control" placeholder="Inicio trimestre <?=$contador?>"></td>
		                       <td><input type="text" id="fin_trim<?=$contador?>_2" name="fin_trim<?=$contador?>_2" value="<?=$fin_2?>" class="form-control" placeholder="Fin trimestre <?=$contador?>"></td>  
		                                   	                       
	                       </tr> 
	                       <?php
		                   }
		                   ?>  	                       	                       	                        
                        </table>
                        <?php
	                    }
	                    ?>                        
                        <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'"> <input type="button" value="Siguiente >>" onclick="comprobar_y_enviar();">
                </td>
        </tr>        
</form>
</table>
<script>

    $('[name^="inicio_trim"]').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });
    $('[name^="fin_trim"]').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });    
    
    $( document ).ready(function() {
	    
    	$("input[name^='inicio_trim']").on("change",function(e) {		  
		
			if (confirm("¿Confirmas el cambio de fecha?")) {
				var href = "guarda_fecha.php?id_curso=<?=$id_curso?>&nombre_campo=" + $(this).attr("name") + "&fecha=" + $(this).val();
	
				var respuesta = $.ajax ({url: href}); 
				respuesta.always(function() {
					
					var textoRespuesta = respuesta.responseText;
					
					if( textoRespuesta.substr(0, 2) != "ok" ) {
						alert(textoRespuesta);						
					}
					
				});	
			}

		});	
	    
    	$("input[name^='fin_trim']").on("change",function(e) {	
	    		  
			if (confirm("¿Confirmas el cambio de fecha?")) {
				var href = "guarda_fecha.php?id_curso=<?=$id_curso?>&nombre_campo=" + $(this).attr("name") + "&fecha=" + $(this).val();
	
				var respuesta = $.ajax ({url: href}); 
				respuesta.always(function() {
					
					var textoRespuesta = respuesta.responseText;
					
					if( textoRespuesta.substr(0, 2) != "ok" ) {
						alert(textoRespuesta);						
					}
					
				});	
			}

		});			
		
	});	
    
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>
