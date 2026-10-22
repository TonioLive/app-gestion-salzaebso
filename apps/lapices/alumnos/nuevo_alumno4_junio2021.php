<?php  require("../configuracion/parte_superior_privado_alumnos.php"); ?>
<?php 
$paso_anterior=3;
$paso_actual=4;
$paso_siguiente=1;

$accion_texto="Editar";
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

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];
				$sip=$datos_usuario["SIP"];
				$nonato=$datos_usuario["nonato"];
                }
        }

$sip_correcto="";
if (strlen($sip)==0) {
	
	$sip_correcto="Falta Nº SIP";
	
} else {
	
	// Validar SIP
	if (is_numeric($sip)) {
		
		if (strlen($sip)==10) {
			
			$dig_control=substr($sip,0,2);
			
			$numero1=(substr($sip,2,1)*9)+(substr($sip,3,1)*8)+(substr($sip,4,1)*7)+(substr($sip,5,1)*6)+(substr($sip,6,1)*5)+(substr($sip,7,1)*4)+(substr($sip,8,1)*3)+(substr($sip,9,1)*2);
			$numero2=$numero1 % 90;
			$numero3=10+$numero2;
			
			if ($numero3==$dig_control) {
				
				$sip_correcto="ok";	
				
			} else {
				
				$sip_correcto="El Nº SIP no es correcto, alguno de los números introducidos ha sido tecleado incorrectamente.";
				
			}
			
			
		} else {
		
			$sip_correcto="El Nº SIP no es correcto, debe tener 10 dígitos y el número que has puesto tiene ".strlen($sip).".";	
			
		}
		
	} else {
		
		if (strtoupper($sip)=="PENDIENTE") {
			
			$sip_correcto="ok";	
			
		}			
		
	}
}

if ($nonato==1) {
	$sip_correcto="ok";	
}

if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query("UPDATE usuarios SET fecha_edicion='".strftime("%Y/%m/%d")."' WHERE id_usuario=$id_usuario_seleccionado") or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente.".php?u=".$id_usuario_seleccionado;

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
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Alumno -  Paso 4 de 4</b></font><br>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr bgcolor="#EFEFEF" valign="top">
                                <?php  if ($accion_texto=="Editar") { ?>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font></a><br><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno11.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;&nbsp;1.1. Insertar Foto</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno2.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#000000"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#1D50C3">4. Inscripciones</font></td>
                                <?php  } ?>
                                </tr>
                        </table><BR>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100" align="left"></a><font size=2 face="<?= $fuente_general; ?>"><b>Inscripción</b><br><br>
                                        Para inscribir al alumno en un curso, rellena el formulario.<br>
                                        Para editar una inscripción o consultar datos de inscripciones anteriores, seleccionalas del listado.<br>
                                        </font></td>
                                </tr>
                                <?php  	                                                          	                                
                                $busca_matriculas=db_query("SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=$id_usuario_seleccionado ORDER BY alumnos.fecha_matricula") or die(db_error()."-5");
                                if (db_num_rows($busca_matriculas)!=0) {
                                ?>
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><br><font face="<?= $fuente_general; ?>" size="2"><b>Inscripciones del alumno.</b></font><br><br>
                                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="50%">
                                                <tr bgcolor="#dedede">
                                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Curso</b></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Fecha Inscripción</b></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Centro</b></font></td>
                                                        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                while($datos_matriculas=db_fetch_array($busca_matriculas)){
                                                $nombre_curso=$datos_matriculas["nombre"];
                                                $fecha_matricula=date("d/m/Y",strtotime($datos_matriculas["fecha_matricula"]));
                                                $matricula_actual=$datos_matriculas["id_curso"];

                                                $consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_matriculas["id_centro"]) or die(db_error());
                                                $datos_centro=db_fetch_array($consulta_nombre_centro);
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$nombre_curso;?></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$fecha_matricula;?></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$datos_centro["nombre_centro"];?> (<?=$datos_centro["poblacion_centro"];?>)</font></td>
                                                        <td align="center"><?php if ($datos_centro["tipo_centro"]==$tipo_centro_actual) { ?><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$datos_matriculas["id_alumno"];?>)" title="Editar inscripciones"><img src="editar.gif" border=0></a></font><?php } ?></td>
                                                        <td align="center"><?php if ($datos_centro["tipo_centro"]==$tipo_centro_actual) { ?><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$datos_matriculas["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a></font><?php } ?></td>
                                                </tr>
                                                <?php 
                                                $banco_entidad_aa=$datos_matriculas["banco_entidad"];
                                                $banco_poblacion_aa=$datos_matriculas["banco_poblacion"];
                                                $banco_titular_aa=$datos_matriculas["banco_titular"];
                                                $banco_dni_titular_aa=$datos_matriculas["banco_dni_titular"];
                                                $banco_domicilio_aa=$datos_matriculas["banco_domicilio"];
                                                $banco_cuenta_aa=$datos_matriculas["banco_cuenta"];
                                                $forma_de_pago_aa=$datos_matriculas["forma_de_pago"];

                                                if (strlen($banco_cuenta_aa)!=0) {
                                                                $array_banco=explode(" ",$banco_cuenta_aa);
                                                                $banco_cuenta1_aa=$array_banco[0];
                                                                $banco_cuenta2_aa=$array_banco[1];
                                                                $banco_cuenta3_aa=$array_banco[2];
                                                                $banco_cuenta4_aa=$array_banco[3];
                                                        } else {
                                                                $banco_cuenta1_aa="";
                                                                $banco_cuenta2_aa="";
                                                                $banco_cuenta3_aa="";
                                                                $banco_cuenta4_aa="";
                                                                }
                                                        }
                                                ?>
                                        </table>
                                        <br>
                                        </td>
                                </tr>
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2" color="red"><b>Formulario para nueva inscripción</b></font></td>
                                </tr>
                                <?php 
                                }

								$filtro_ingles=" AND centros.tipo_centro=0";								
								if ($tipo_centro_actual==1) {
									$filtro_ingles=" AND centros.id_centro=".$centro_actual;								
								}

								$filtro_cursos_sip="";
	                            if ($sip_correcto!="ok") {
		                        ?>
		                        <tr>
			                        <td colspan=2><h3 style="color: #ff0000">El alumno solo se puede inscribir en cursos de verano si no introduces su Nº SIP. Vuelve al paso 1 y rellena este dato.<br /><br /><?=$sip_correcto?></h3></td>
		                        </tr>
		                        <?php
								
								$filtro_cursos_sip=" AND month(fecha_inicio)=7";
		                        }
								// else {  

                                $cursos_disponibles=0;

                                // Comprueba matrículas
                                // Estados 0=No iniciado, 1=Iniciado, >=2 Finalizados
                                $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE estado<2".$filtro_cursos_sip." ORDER BY fecha_inicio");
                                $select_options="<option value=''></option>";
                                while($cursos=db_fetch_array($consulta_cursos)){
	                                
                                $consulta_matriculas=db_query("SELECT * FROM alumnos,centros WHERE alumnos.id_centro=centros.id_centro AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=".$cursos["id_curso"].$filtro_ingles) or die(db_error()." "."SELECT * FROM alumnos,centros WHERE alumnos.id_centro=centros.id_centro AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=".$cursos["id_curso"].$filtro_ingles);
                                if (db_num_rows($consulta_matriculas)==0) {
                                        $cursos_disponibles=$cursos_disponibles+1;
										
										$seleccionado="";
										if ($_GET["id_curso_pre"]==$cursos["id_curso"]) {
											$seleccionado=" selected";
										}
										
                                        $select_options=$select_options."<option value='".$cursos["id_curso"]."'".$seleccionado.">".$cursos["nombre"]."</option>";
                                        }
                                }

                                $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=$centro_actual");
                                if ($cursos_disponibles==0) {
                                ?>
                                <input type="hidden" name="mostrar_errores" value="0">
                                <input type="hidden" name="curso" value="">
                                <input type="hidden" name="usuario_fechamat_dia" value="">
                                <input type="hidden" name="usuario_fechamat_mes" value="">
                                <input type="hidden" name="usuario_fechamat_ano" value="">
                                <input type="hidden" name="usuario_fechabaja_dia" value="">
                                <input type="hidden" name="usuario_fechabaja_mes" value="">
                                <input type="hidden" name="usuario_fechabaja_ano" value="">
                                <input type="hidden" name="banco_cuenta1" value="">
                                <input type="hidden" name="banco_cuenta2" value="">
                                <input type="hidden" name="banco_cuenta3" value="">
                                <input type="hidden" name="banco_cuenta4" value="">
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2" color="red"><b>El alumno ya está inscrito, para editar su inscripción seleccionala en el listado.</b></font></td>
                                </tr>
                                <?php 
                                } else {
                                if (db_num_rows($busca_aulas)==0) {
                                ?>
                                <input type="hidden" name="mostrar_errores" value="0">
                                <input type="hidden" name="curso" value="">
                                <input type="hidden" name="usuario_fechamat_dia" value="">
                                <input type="hidden" name="usuario_fechamat_mes" value="">
                                <input type="hidden" name="usuario_fechamat_ano" value="">
                                <input type="hidden" name="usuario_fechabaja_dia" value="">
                                <input type="hidden" name="usuario_fechabaja_mes" value="">
                                <input type="hidden" name="usuario_fechabaja_ano" value="">
                                <input type="hidden" name="banco_cuenta1" value="">
                                <input type="hidden" name="banco_cuenta2" value="">
                                <input type="hidden" name="banco_cuenta3" value="">
                                <input type="hidden" name="banco_cuenta4" value="">
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2" color="red"><b>El centro no tiene aulas disponibles, el alumno no puede ser inscrito. Para añadir nuevas aulas, vuelva a la página principal y pulse sobre el vínculo "Gestión de centros".</b></font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <input type="hidden" name="mostrar_errores" value="1">
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Curso académico</b></font></td>
                                        <td><select name="curso" size=1><?php  echo "$select_options"; ?></select></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Proceso de decisión:</b></font></td>
                                        <td><select name="proceso_decision" size=1>
                                        <option value="1">b1</option>
                                        <option value="2">b2</option>
                                        <option value="3">b3</option>
                                        <option value="4">c</option>
                                        </select></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de inicio docencia</b></font></td>
                                        <td><select name="usuario_fechamat_dia" class="defaut">
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
                                                                      </select> <select name="usuario_fechamat_mes" class="defaut">
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
                                                                      </select> <select name="usuario_fechamat_ano" class="defaut">
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechamat','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha efectiva de baja</b></font></td>
                                        <td><select name="usuario_fechabaja_dia" class="defaut">
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
                                                                      </select> <select name="usuario_fechabaja_mes" class="defaut">
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
                                                                      </select> <select name="usuario_fechabaja_ano" class="defaut">
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechabaja','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de mensualidad</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"> <input type="radio" name="tipo_mensualidad" value="1"> Media jornada&nbsp; &nbsp;<input type="radio" name="tipo_mensualidad" value="2" checked>Jornada Completa </font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="comedor" value="2" checked>No &nbsp; &nbsp;<input type="radio" name="comedor" value="1"> Si&nbsp; &nbsp;<input type="radio" name="comedor" value="3"> Regimen</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de menú</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="tipo_menu_comedor" value="1" checked>Pequeños &nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="2"> Mayores&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="3"> Colegial&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="4"> Toma&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="5"> Bebe</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nº de tomas</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="numero_tomas" value="1" checked>1 &nbsp; &nbsp;<input type="radio" name="numero_tomas" value="2"> 2&nbsp; &nbsp;<input type="radio" name="numero_tomas" value="3"> 3&nbsp; &nbsp;<input type="radio" name="numero_tomas" value="4"> 4</td>
                                </tr>
                                <tr vAlign="top" bgcolor="#efefef">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Batos bancarios</b></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Forma de pago</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="forma_de_pago" value="0" checked>Contado &nbsp; &nbsp;<input type="radio" name="forma_de_pago" value="1"> Domiciliación bancaria.</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_entidad" size=60 value="<?=$banco_entidad_aa?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Población entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_poblacion" size=50 value="<?=$banco_poblacion_aa?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Domicilio entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_domicilio" size=50 value="<?=$banco_domicilio_aa?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Titular de la cuenta</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_titular" size=50 value="<?=$banco_titular_aa?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>DNI/NIE del titular</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_dni_titular" size=35 value="<?=$banco_dni_titular_aa?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuenta domiciliación</b></font></td>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="banco_cuenta1" size=4 maxlength=4 value="<?=$banco_cuenta1_aa?>"> <input type="text" name="banco_cuenta2" size=4 maxlength=4 value="<?=$banco_cuenta2_aa?>"> <input type="text" name="banco_cuenta3" size=2 maxlength=2 value="<?=$banco_cuenta3_aa?>"> <input type="text" name="banco_cuenta4" size=10 maxlength=10 value="<?=$banco_cuenta4_aa?>"><br /><br /><b>IBAN:</b> <?=generarDCInToIban(calcularIban( $banco_cuenta1_aa, $banco_cuenta2_aa, $banco_cuenta3_aa, $banco_cuenta4_aa )); ?></font></td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>" color="red">Una vez completado el formulario, pulsa el botón "Finalizar" para inscribir al alumno en el curso seleccionado.</font></td>
                                </tr>
                                <?php 
                                }

	                        // }
	                     ?>                                 
                         </table>
                         <br><?php
	                         if ($sip_correcto=="ERROR") {
		                     ?>
							 <input type="button" onclick="alert('No se puede realizar la inscripción sin el Nº SIP. Vuelve al paso 1 y rellena este dato.\n\n<?=$sip_correcto?>')" value="Finalizar" style="color: #ff0000"> <input type="button" onclick="window.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado?>';" value="Volver al paso 1 >>">
		                     <?php    
	                         }
	                         ?><input type="button" onclick="cambia(6);" value="Añadir a lista de espera"> <input type="button" onclick="cambia(5);" value="Finalizar"> <input type="button" onclick="cambia(1);" value="Volver al paso 1 >>"><br><br>
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

function editar(fila) {
        window.open('editar_inscripcion.php?i='+fila,'matriculas','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }

function cambia(mostrar) {
        if (mostrar!=<?=$paso_actual;?>) {
                document.<?=$formulario_name;?>.action="nuevo_alumno"+mostrar+".php?u=<?=$id_usuario_seleccionado?>";
                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.curso.value=="") { mensajes_error=mensajes_error+"No has seleccionado el curso académico.\u000A";errores=1; }

                if (mostrar!=6) {

                        // Comprueba fecha
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechamat_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de matrícula introducida no es correcta.\u000A";errores=1; }

                        // Comprueba fecha
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechabaja_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de baja introducida no es correcta.\u000A";errores=1; }
                        
                        }
                
                // Comprueba la entidad bancaria
                cuenta1=self.document.<?=$formulario_name;?>.banco_cuenta1.value;
                cuenta2=self.document.<?=$formulario_name;?>.banco_cuenta2.value;
                cuenta3=self.document.<?=$formulario_name;?>.banco_cuenta3.value;
                cuenta4=self.document.<?=$formulario_name;?>.banco_cuenta4.value;
                if ((cuenta1+cuenta2+cuenta3+cuenta4)!="") {
                        // Parte 1
                        if (isNaN(cuenta1)) {
                                mensajes_error=mensajes_error+"El número de la entidad no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta1=cuenta1.replace(" ","");
                                if (cuenta1.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la entidad no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 2
                        if (isNaN(cuenta2)) {
                                mensajes_error=mensajes_error+"El número de la oficina no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta2=cuenta2.replace(" ","");
                                if (cuenta2.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la oficina no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 3
                        if (isNaN(cuenta3)) {
                                mensajes_error=mensajes_error+"El dígito de control no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta3=cuenta3.replace(" ","");
                                if (cuenta3.length!=2) {
                                        mensajes_error=mensajes_error+"El dígito de control no es correcto,debe tener 2 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 4
                        if (isNaN(cuenta4)) {
                                mensajes_error=mensajes_error+"El número de la cuenta no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta4=cuenta4.replace(" ","");
                                if (cuenta4.length!=10) {
                                        mensajes_error=mensajes_error+"El número de la cuenta no es correcto,debe tener 10 dígitos.\u000A";errores=1;
                                        }
                                }
                        }

                if (errores==0) {

                         document.<?=$formulario_name;?>.submit();
                        } else {
                        if (self.document.<?=$formulario_name;?>.mostrar_errores.value=="1") {
                                if (mostrar==6) {
                                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A.")
                                        } else {
                                        if (confirm("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A¿Deseas continuar sin corregir los errores? Si lo haces la matrícula no se realizará.")) {
                                                document.<?=$formulario_name;?>.submit();
                                                }
                                        }
                                } else {
                                document.<?=$formulario_name;?>.submit();
                                }
                        }
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
<?php 
 require("../configuracion/parte_inferior.php"); ?>
