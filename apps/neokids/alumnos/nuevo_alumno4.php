<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $id_curso_actual=$curso_seleccionado;
        }       


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
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                $banco_entidad_aa=$datos_usuario["banco_entidad_alu"];
                $banco_poblacion_aa=$datos_usuario["banco_poblacion_alu"];
                $banco_titular_aa=$datos_usuario["banco_titular_alu"];
                $banco_dni_titular_aa=$datos_usuario["banco_dni_titular_alu"];
                $banco_domicilio_aa=$datos_usuario["banco_domicilio_alu"];
                $banco_cuenta_aa=$datos_usuario["banco_cuenta_alu"];

                }
        }

if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query_params('UPDATE usuarios SET fecha_edicion=? WHERE id_usuario=?', 'ss', array((string)(strftime("%Y/%m/%d")), $id_usuario_seleccionado)) or die(db_error()."-1");
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
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Informaci?n adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#000000"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Informaci?n adicional</font></td>
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
                                $busca_matriculas=db_query_params('SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=? ORDER BY alumnos.fecha_matricula', 's', array($id_usuario_seleccionado)) or die(db_error()."-5");
                                if (db_num_rows($busca_matriculas)!=0) {
                                ?>
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><br><font face="<?= $fuente_general; ?>" size="2"><b>Inscripciones del alumno.</b></font><br><br>
                                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="95%">
                                                <tr bgcolor="#dedede">
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><b>Curso</b></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><b>Fecha Inscripci?n</b></font></td>
                                                                                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><b>Fecha Baja</b></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><b>Centro</b></font></td>
                                                         <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><b>Grupos</b></font></td>
                                                        <td align="center" colspan=3><font size=2 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                while($datos_matriculas=db_fetch_array($busca_matriculas)){
                                                $nombre_curso=$datos_matriculas["nombre"];
                                                $fecha_matricula=date("d/m/Y",strtotime($datos_matriculas["fecha_matricula"]));
                                                $fecha_baja=date("d/m/Y",strtotime($datos_matriculas["fecha_baja"]));
                                                $matricula_actual=$datos_matriculas["id_curso"];

                                                $consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_matriculas["id_centro"])) or die(db_error());
                                                $datos_centro=db_fetch_array($consulta_nombre_centro);
                                                
                                                
$lista_grupos_alumno="";

$busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($datos_matriculas["id_alumno"])) or die(db_error()."-4");

while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).".<br />";
	}

if (db_num_rows($busca_alumnos_grupo)==0) {
        $lista_grupos_alumno="<font color='#ff0000'>Sin grupo asignado</font>";
        }                                           
        										$color_fila="#ffffff";     
                                                
                                                if (strtotime($datos_matriculas["fecha_baja"])>time()) {
                                                	
                                                	$color_fila="#f7f6b4";
                                                
                                                }
                                                
                                                if (strtotime($datos_matriculas["fecha_matricula"])>time()) {
                                                	
                                                	$color_fila="#dab4f7";
                                                
                                                }
                                                
                                                
                                                ?>
                                                <tr bgcolor="<?=$color_fila?>">
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><?=$nombre_curso;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><?=$fecha_matricula;?></font></td>
                                                       <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><?=$fecha_baja;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><?=$datos_centro["nombre_centro"];?> (<?=$datos_centro["poblacion_centro"];?>)</font></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><?=$lista_grupos_alumno;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$datos_matriculas["id_alumno"];?>)" title="Editar inscripciones"><img src="editar.gif" border=0></a></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$datos_matriculas["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><button onclick="responderconsulta(<?=$datos_matriculas["id_usuario"];?>,<?=$datos_matriculas["id_alumno"];?>)" type="button">Renovación 2020</button></font></td>
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
                                        <table bgcolor="#efefef" cellpadding=1 cellspacing=3 width="95%">
<tr>
<td colspan=6 align="center" bgcolor="#efefef"><font size=1><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#ffffff"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Finalizada</font></td>
<td bgcolor="#f7f6b4"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Actual</font></td>
<td bgcolor="#dab4f7"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Próxima</font></td>
</tr>
</table>
                                        </td>
                                </tr>
                                <tr vAlign="top">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2" color="red"><b>Formulario para nueva inscripción</b></font></td>
                                </tr>
                                <?php 
                                }

                                $cursos_disponibles=0;

                                // Comprueba matr?culas
                                // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                                $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE estado<3 ORDER BY fecha_inicio");
                                $select_options="<option value=''></option>";
                                while($cursos=db_fetch_array($consulta_cursos)){
                                $consulta_matriculas=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($id_usuario_seleccionado, $cursos["id_curso"]));
                                // if (mysql_num_rows($consulta_matriculas)==0) {
                                        $cursos_disponibles=$cursos_disponibles+1;
                                        $estado_select="";
                                        if ($cursos["id_curso"]==$_GET["id_curso_pre"]) {
                                                $estado_select=" selected";
                                        }
                                        $select_options=$select_options."<option value='".$cursos["id_curso"]."'".$estado_select.">".$cursos["nombre"]."</option>";
                                       // }
                                }

                                $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=?', 's', array($centro_actual));
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
                                        <td><select name="curso" size=1 onchange="cambia_curso();"><?php  echo "$select_options"; ?></select></td>
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
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>N? de sesiones semanales:</b></font></td>
                                        <td><select name="num_dias_asiste" size=1>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        </select></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Nivel/Servicio en el que se inscribe:</b></font></td>
                                        <td><select name="nivel_actual" size=1>
                                        <option value="0">Bebes</option>
                                        <option value="1">Nivel 1</option>
                                        <option value="2">Nivel 2</option>
                                        <option value="3">Nivel 3</option>
                                        <option value="4">Aquagym</option>
                                        <option value="5">Matronataci?n</option>
                                        </select></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Nivel estimado para el próximo curso:</b></font></td>
                                        <td>
                                <select id="nivel_proximo" name="nivel_proximo" style="background-color: <?=$colores_subniveles[0]?>; color: #fff">
                        <option value="0" style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1" style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2" style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4" style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5" style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7" style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8" style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td>
                                </tr>                                 
                                <!-
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="comedor" value="2" checked>No &nbsp; &nbsp;<input type="radio" name="comedor" value="1"> Si&nbsp; &nbsp;<input type="radio" name="comedor" value="3"> Regimen</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de menú</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="tipo_menu_comedor" value="1" checked>Peque?os &nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="2"> Mayores&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="3"> Colegial&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="4"> Toma&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="5"> Bebe</font></td>
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
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuenta domiciliaci?n</b></font></td>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="banco_cuenta1" size=4 maxlength=4 value="<?=$banco_cuenta1_aa?>"> <input type="text" name="banco_cuenta2" size=4 maxlength=4 value="<?=$banco_cuenta2_aa?>"> <input type="text" name="banco_cuenta3" size=2 maxlength=2 value="<?=$banco_cuenta3_aa?>"> <input type="text" name="banco_cuenta4" size=10 maxlength=10 value="<?=$banco_cuenta4_aa?>"><br /><br /><b>IBAN:</b> <?php  /* generarDCInToIban(calcularIban( $banco_cuenta1_aa, $banco_cuenta2_aa, $banco_cuenta3_aa, $banco_cuenta4_aa )); */ ?></font></td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2 align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Servicio que contrata</b></font>
<br />                                                                          
<?php 

// Selector de servicios

$id_curso_servicios=$_GET["id_curso_pre"];

if (strlen($id_curso_servicios)==0) {
	$id_curso_servicios=$id_curso_actual;
}
		                            
// Busca periodo_actual
$busca_periodo_actual = db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE id_curso=? AND fecha_inicio<=? AND fecha_fin>=? ORDER BY fecha_inicio', 'sss', array($id_curso_servicios, (string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die (db_error());
if (db_num_rows($busca_periodo_actual)==0) {
	$busca_periodo_actual = db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE id_curso=? AND fecha_inicio>? ORDER BY fecha_inicio', 'ss', array($id_curso_servicios, (string)(date("Y-m-d")))) or die (db_error());    	                
}

if (db_num_rows($busca_periodo_actual)!=0) {
    $datos_periodo=db_fetch_array($busca_periodo_actual);
    $periodo=$datos_periodo["id_trimestre"]; 
} else {
  	$periodo=0;  	                
} 
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr valign="top">
	<td align="right" bgcolor="#ececec" colspan=3><b>Periodo para el que se va a contratar:</b></td>
	<td align="left" bgcolor="#ffffff" colspan=7><select size=1 id="periodo" name="periodo">
        <option value="">Selecciona el periodo</option>
        <?php 
        // Busca periodos
        $consulta_periodos = db_query_params('SELECT * FROM aulas_grupos_trimestres WHERE id_curso=? ORDER BY fecha_inicio', 's', array($id_curso_servicios)) or die (db_error());
        while($datos_periodos = db_fetch_array($consulta_periodos)){
        ?>
        <option value="<?=$datos_periodos["id_trimestre"];?>"<?php if ($periodo==$datos_periodos["id_trimestre"]) { print " selected"; }?>><?=$datos_periodos["nombre_trimestre"];?> (<?=date("d/m/Y",strtotime($datos_periodos["fecha_inicio"]));?> a <?=date("d/m/Y",strtotime($datos_periodos["fecha_fin"]));?>)</option>
        <?php 
        }
        ?>
        </select>
    </td>						                       
</tr>	
<tr>
<td><font size=2><b>Servicio</b></font></td>
<td><font size=2><b>Cantidad</b></font></td>
<td colspan="2"><font size=1><b>Precio completo</b></font></td>
<td colspan="2"><font size=1><b>Precio con 5% Dto.</b></font></td>
<td colspan="2"><font size=1><b>Precio con 10% Dto.</b></font></td>
<td colspan="2"><font size=1><b>Precio con 15% Dto.</b></font></td>
</tr> 		                            
<?php 
$busca_servicios=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva$filtro AND tipos_iva_servicios.tipo_servicios=0 AND tarifa.categoria=1 AND (tarifa.id_curso=$id_curso_servicios OR tarifa.id_curso=0) ORDER BY ID");
                                        
while($servicios=db_fetch_array($busca_servicios)) {

                        $precio_total=number_format(($servicios["PRECIO_VENTA"]*(1+$servicios["porcentaje_iva"])),2,",",".");
                        
                        $precio_5=number_format(($servicios["PRECIO_VENTA"]*0.95*(1+$servicios["porcentaje_iva"])),2,",",".");
                        
                        $precio_10=number_format(($servicios["PRECIO_VENTA"]*0.90*(1+$servicios["porcentaje_iva"])),2,",",".");
                        
                        $precio_15=number_format(($servicios["PRECIO_VENTA"]*0.85*(1+$servicios["porcentaje_iva"])),2,",",".");

        $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;   
        ?>
                <tr bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
<td><?=$servicios["ARTICULO"];?></td>
<td><select name="cantidad<?=$servicios["ID"];?>" id="cantidad<?=$servicios["ID"];?>">
<?php 
for ($num_sesiones=1;$num_sesiones<=24;$num_sesiones++) {
$coletilla_uds="s";
if ($num_sesiones==1) {
        $coletilla_uds="";
        }
?>
<option value="<?=$num_sesiones?>"><?=$num_sesiones?> Ud<?=$coletilla_uds?></option>
<?php 
}
?>
</select></td>
<td align="center"><input type="radio" id="servicio<?=$servicios["ID"];?>" value="1" name="servicio<?=$servicios["ID"];?>"></td>
<td align=right><small><?=$precio_total?> €</small></td>
<td align="center"><input type="radio" id="servicio<?=$servicios["ID"];?>" value="2" name="servicio<?=$servicios["ID"];?>"></td>
<td align=right><small><?=$precio_5?> €</small></td>
<td align="center"><input type="radio" id="servicio<?=$servicios["ID"];?>" value="3" name="servicio<?=$servicios["ID"];?>"></td>
<td align=right><small><?=$precio_10?> €</small></td>
<td align="center"><input type="radio" id="servicio<?=$servicios["ID"];?>" value="4" name="servicio<?=$servicios["ID"];?>"></td>
<td align=right><small><?=$precio_15?> €</small></td>
</tr>
<?php 
        }                                        
                                        
                                        ?></table></td>
                                </tr>
<tr valign="top">
        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Pagado?</b></font></td>
                                        <td><input type="radio" id="pagado" checked value="0" name="pagado"> No &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="pagado" value="0.5" name="pagado"> Si, el 50% &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="pagado" value="1" name="pagado"> Si, el 100% </td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>" color="red">Una vez completado el formulario, pulsa el botón "Finalizar" para inscribir al alumno en el curso seleccionado.</font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                         </table>
                         <br><input type="button" onclick="cambia(6);" value="Añadir a lista de espera"> <input type="button" onclick="cambia(5);" value="Finalizar"> <input type="button" onclick="cambia(1);" value="Volver al paso 1 >>"><br><br>
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

function responderconsulta(id_usuario,id_alumno) {
        consultas=window.open('/formulario/responder1.php?idc=2&idu='+id_usuario+'&ida='+id_alumno,'consulta','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
		consultas.focus();
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
                                mensajes_error=mensajes_error+"El n?mero de la entidad no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta1=cuenta1.replace(" ","");
                                if (cuenta1.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la entidad no es correcto, debe tener 4 d?gitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 2
                        if (isNaN(cuenta2)) {
                                mensajes_error=mensajes_error+"El número de la oficina no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta2=cuenta2.replace(" ","");
                                if (cuenta2.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la oficina no es correcto, debe tener 4 d?gitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 3
                        if (isNaN(cuenta3)) {
                                mensajes_error=mensajes_error+"El dígito de control no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta3=cuenta3.replace(" ","");
                                if (cuenta3.length!=2) {
                                        mensajes_error=mensajes_error+"El dígito de control no es correcto, debe tener 2 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 4
                        if (isNaN(cuenta4)) {
                                mensajes_error=mensajes_error+"El número de la cuenta no es correcto, solo se permiten n?meros.\u000A";errores=1;
                                } else {
                                cuenta4=cuenta4.replace(" ","");
                                if (cuenta4.length!=10) {
                                        mensajes_error=mensajes_error+"El número de la cuenta no es correcto, debe tener dígitos.\u000A";errores=1;
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
                                        if (confirm("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A?Deseas continuar sin corregir los errores? Si lo haces la matrícula no se realizar?.")) {
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
        
function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function cambia_curso() {

	var id_curso_pre = document.<?=$formulario_name;?>.curso.value;
	
	window.location.href='<?=$_SERVER["PHP_SELF"];?>?u=<?=$id_usuario_seleccionado?>&id_curso_pre='+id_curso_pre;

}

$(document).ready(function() {

   $("[id^=nivel_proximo]").change(function() {
   
                   var colores_subniveles = new Array('<?=implode("','",$colores_subniveles)?>');
                   var nombres_subniveles = new Array('<?=implode("','",$nombres_subniveles)?>');  
   
              var current = $(this).val();
      
        $(this).css('background-color', colores_subniveles[current] );

           }); 
}); 
        
//-->
</script>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
