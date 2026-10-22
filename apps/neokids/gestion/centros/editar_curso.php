<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
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

        $busca_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));
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
				$permitir_tiempo_gracia=$datos_curso["permitir_tiempo_gracia"];
				$num_dias_asisten_externos=$datos_curso["num_dias_asisten_externos"];
				
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
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_curso" value="<?=$id_curso;?>">
<input type="hidden" name="id_aula" value="<?=$id_aula;?>">
<input type="hidden" name="o" value="<?=$o;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><h3><?=$mensaje;?> Curso Académico</h3></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del Curso</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font face="<?= $fuente_general; ?>"><b>Nombre del Curso Académico:</b></font></td>
                                <td><font face="<?= $fuente_general; ?>"><input type="text" name="nombre_curso" value="<?=$nombre_curso?>" size="50"><br /><small><small>* Ejemplo: <i>"2004-2005"</i></small></small></font></td>
                        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font face="<?= $fuente_general; ?>"><b>Fecha de inicio del Curso:</b></font></td>
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_inicio','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font face="<?= $fuente_general; ?>"><b>Fecha de fin del Curso:</b></font></td>
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_fin','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font face="<?= $fuente_general; ?>"><b>Fecha limite de presentación de subvenciones:</b></font></td>
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_inicio','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font face="<?= $fuente_general; ?>"><b>Estado del Curso:</b></font></td>
        <td><select name="estado" class="defaut">
        <option value="0"<?php  if ($estado==0) { print " selected"; } ?>>En peparación</option>
        <option value="1"<?php  if ($estado==1) { print " selected"; } ?>>Iniciado</option>
        <option value="2"<?php  if ($estado==2) { print " selected"; } ?>>Finalizado</option>
        </select></td>
        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font face="<?= $fuente_general; ?>"><b>¿Mostrar en horario grupos después de finalizado?</b></font></td>
                           <td align="left" bgcolor="#ececec"><font face="<?= $fuente_general; ?>"><input type="radio" name="permitir_tiempo_gracia" value="0"<?php  if ($permitir_tiempo_gracia==0) { echo(" checked"); } ?>>Si &nbsp; &nbsp;<input type="radio" name="permitir_tiempo_gracia" value="1"<?php  if ($permitir_tiempo_gracia==1) { echo(" checked"); } ?>> No</font></td>
                        </tr>
				        <tr bgcolor="#efefef">
				        <td align="right"><font face="<?= $fuente_general; ?>"><b>Nº de sesiones semanales para alumnos "Swimming":</b></font></td>
				        <td><select name="num_dias_asisten_externos" class="defaut">
					        <option value="1"<?php  if ($num_dias_asisten_externos==1) { print " selected"; } ?>>1</option>
					        <option value="2"<?php  if ($num_dias_asisten_externos==2) { print " selected"; } ?>>2</option>
					        <option value="3"<?php  if ($num_dias_asisten_externos==3) { print " selected"; } ?>>3</option>
					        <option value="4"<?php  if ($num_dias_asisten_externos==4) { print " selected"; } ?>>4</option>
					        <option value="5"<?php  if ($num_dias_asisten_externos==5) { print " selected"; } ?>>5</option>
				        </select></td>
				        </tr>                        
                        
                        </table>
                        <?php
	                    
	                    if ($id_curso) {
		                    
	                    ?>
                        <h3>Periodos del curso</h3>
                        <table class="table table-condensed table-bordered">
							<tr>
		                       <td colspan=2 class="col-md-4">Nombre Periodo</td>
		                       <td class="col-md-1">Lunes</td>
		                       <td class="col-md-1">Martes</td>
		                       <td class="col-md-1">Miercoles</td>
		                       <td class="col-md-1">Jueves</td>
		                       <td class="col-md-1">Viernes</td>		                       
		                       <td class="col-md-1">Sábado</td>
		                       <td class="col-md-1">Domingo</td>
	                       </tr>
	                       <?php
		                   // Busca periodos
		                   $busca_periodos=db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE id_curso=? ORDER BY id_trimestre', 's', array($id_curso));
		                   while($periodos=db_fetch_array($busca_periodos)) {
			                   
			                   if ($periodos["ini_l"]) {
				                   $ini_l=date("d/m/Y",strtotime($periodos["ini_l"]));
				                } else {
					               $ini_l="";
				                }
			                   if ($periodos["ini_m"]) {
				                   $ini_m=date("d/m/Y",strtotime($periodos["ini_m"]));
				                } else {
					               $ini_m="";
				                }
			                   if ($periodos["ini_x"]) {
				                   $ini_x=date("d/m/Y",strtotime($periodos["ini_x"]));
				                } else {
					               $ini_x="";
				                }
			                   if ($periodos["ini_j"]) {
				                   $ini_j=date("d/m/Y",strtotime($periodos["ini_j"]));
				                } else {
					               $ini_j="";
				                }
			                   if ($periodos["ini_v"]) {
				                   $ini_v=date("d/m/Y",strtotime($periodos["ini_v"]));
				                } else {
					               $ini_v="";
				                }
			                   if ($periodos["ini_s"]) {
				                   $ini_s=date("d/m/Y",strtotime($periodos["ini_s"]));
				                } else {
					               $ini_s="";
				                }
			                   if ($periodos["ini_d"]) {
				                   $ini_d=date("d/m/Y",strtotime($periodos["ini_d"]));
				                } else {
					               $ini_d="";
				                }				                				                				                				                				                
			               
			                   if ($periodos["fin_l"]) {
				                   $fin_l=date("d/m/Y",strtotime($periodos["fin_l"]));
				                } else {
					               $fin_l="";
				                }
			                   if ($periodos["fin_m"]) {
				                   $fin_m=date("d/m/Y",strtotime($periodos["fin_m"]));
				                } else {
					               $fin_m="";
				                }
			                   if ($periodos["fin_x"]) {
				                   $fin_x=date("d/m/Y",strtotime($periodos["fin_x"]));
				                } else {
					               $fin_x="";
				                }
			                   if ($periodos["fin_j"]) {
				                   $fin_j=date("d/m/Y",strtotime($periodos["fin_j"]));
				                } else {
					               $fin_j="";
				                }
			                   if ($periodos["fin_v"]) {
				                   $fin_v=date("d/m/Y",strtotime($periodos["fin_v"]));
				                } else {
					               $fin_v="";
				                }
			                   if ($periodos["fin_s"]) {
				                   $fin_s=date("d/m/Y",strtotime($periodos["fin_s"]));
				                } else {
					               $fin_s="";
				                }
			                   if ($periodos["fin_d"]) {
				                   $fin_d=date("d/m/Y",strtotime($periodos["fin_d"]));
				                } else {
					               $fin_d="";
				                }				                				                				                				                				                   
		                   ?> 	                        
	                       <tr>
		                       <td rowspan=2><input type="text" value="<?=$periodos["nombre_trimestre"]?>" id="<?=$periodos["id_trimestre"]?>" name="nombre_trimestre" class="form-control"></td>
		                       <td>Inicio</td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_l_<?=$periodos["id_trimestre"]?>" value="<?=$ini_l?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_m_<?=$periodos["id_trimestre"]?>" value="<?=$ini_m?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_x_<?=$periodos["id_trimestre"]?>" value="<?=$ini_x?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_j_<?=$periodos["id_trimestre"]?>" value="<?=$ini_j?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_v_<?=$periodos["id_trimestre"]?>" value="<?=$ini_v?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_s_<?=$periodos["id_trimestre"]?>" value="<?=$ini_s?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="ini_d_<?=$periodos["id_trimestre"]?>" value="<?=$ini_d?>" class="form-control" placeholder=""></td>		                                           	                       
	                       </tr> 
	                       <tr>
		                       <td>Fin</td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_l_<?=$periodos["id_trimestre"]?>" value="<?=$fin_l?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_m_<?=$periodos["id_trimestre"]?>" value="<?=$fin_m?>" class="form-control" placeholder=""></td> 
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_x_<?=$periodos["id_trimestre"]?>" value="<?=$fin_x?>" class="form-control" placeholder=""></td> 
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_j_<?=$periodos["id_trimestre"]?>" value="<?=$fin_j?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_v_<?=$periodos["id_trimestre"]?>" value="<?=$fin_v?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_s_<?=$periodos["id_trimestre"]?>" value="<?=$fin_s?>" class="form-control" placeholder=""></td>
		                       <td><input type="text" id="<?=$periodos["id_trimestre"]?>" name="fin_d_<?=$periodos["id_trimestre"]?>" value="<?=$fin_d?>" class="form-control" placeholder=""></td>		                                          	                       
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
</table><br><br>
<script>

    $('[name^="ini_"]').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });
    $('[name^="fin_"]').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });    
    
    $( document ).ready(function() {
	    
    	$("input[name^='ini_']").on("change",function(e) {		  
		
			if (confirm("¿Confirmas el cambio de fecha?")) {
				var href = "guarda_fecha.php?id_curso=<?=$id_curso?>&nombre_campo=" + $(this).attr("name") + "&id_trimestre=" + $(this).attr("id") + "&fecha=" + $(this).val();
	
				var respuesta = $.ajax ({url: href}); 
				respuesta.always(function() {
					
					var textoRespuesta = respuesta.responseText;
					
					if( textoRespuesta.substr(0, 2) != "ok" ) {
						alert(textoRespuesta);						
					}
					
				});	
			}

		});	
	    
    	$("input[name^='fin_']").on("change",function(e) {	
	    		  
			if (confirm("¿Confirmas el cambio de fecha?")) {
				var href = "guarda_fecha.php?id_curso=<?=$id_curso?>&nombre_campo=" + $(this).attr("name") + "&id_trimestre=" + $(this).attr("id") + "&fecha=" + $(this).val();
	
				var respuesta = $.ajax ({url: href}); 
				respuesta.always(function() {
					
					var textoRespuesta = respuesta.responseText;
					
					if( textoRespuesta.substr(0, 2) != "ok" ) {
						alert(textoRespuesta);						
					}
					
				});	
			}

		});
		
    	$("input[name^='nombre_trimestre']").on("change",function(e) {		
	    	  
			if (confirm("¿Confirmas el cambio de nombre?")) {
				var href = "guarda_dato.php?id_curso=<?=$id_curso?>&nombre_tabla=aulas_grupos_trimestres&nombre_campo=" + $(this).attr("name") + "&id_trimestre=" + $(this).attr("id") + "&valor_campo=" + $(this).val();
	
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
