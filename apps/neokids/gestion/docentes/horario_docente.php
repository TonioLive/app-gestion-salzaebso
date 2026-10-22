<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$formulario_name="horario";
$formulario_accion="horario_docente1.php";

$id_docente=$_GET["id_docente"];

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error());
        if (db_num_rows($busca_datos_docente)==0) {
                db_query_params('INSERT INTO horario_docentes (id_usuario, id_centro) VALUES (?,?)', 'ss', array($id_docente, $centro_actual)) or die(db_error());
                // Vuelve a ejecutar la consulta
                $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error());
                }
                
        $datos_docente=db_fetch_array($busca_datos_docente);
        $nss=$datos_docente["NSS"];
        
        $titulacion=$datos_docente["titulacion"];
        if (strlen($titulacion)!=0) {
                        $titulacion=stripslashes($titulacion);
                        }
        $especialidad=$datos_docente["especialidad"];
        if (strlen($especialidad)!=0) {
                        $especialidad=stripslashes($especialidad);
                        }
        
        $codigo_nominaplus=$datos_docente["codigo_nominaplus"];
        $id_horario=$datos_docente["id_horario"];
                $mensaje="Edición";
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
                $nombre_docente=ucwords(strtolower($nombre_docente));
                        // Fechas
                        if ($datos_docente["fecha_inicio"]=="0000-00-00") {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                } else {
                                $fecha_inicio_dia=date("j",strtotime($datos_docente["fecha_inicio"]));
                                $fecha_inicio_mes=date("m",strtotime($datos_docente["fecha_inicio"]));
                                $fecha_inicio_ano=date("Y",strtotime($datos_docente["fecha_inicio"]));
                                }

                        if ($datos_docente["fecha_fin"]=="0000-00-00") {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                } else {
                                $fecha_fin_dia=date("j",strtotime($datos_docente["fecha_fin"]));
                                $fecha_fin_mes=date("m",strtotime($datos_docente["fecha_fin"]));
                                $fecha_fin_ano=date("Y",strtotime($datos_docente["fecha_fin"]));
                                }
                $hora_entrada_man=$datos_docente["hora_entrada1"];
                if ($hora_entrada_man=="00:00:00") {
                        $hora_entrada_man=$hora_entrada_centro_man;
                        }
                $hora_salida_man=$datos_docente["hora_salida1"];
                if ($hora_salida_man=="00:00:00") {
                        $hora_salida_man=$hora_salida_centro_man;
                        }
                $hora_entrada_tar=$datos_docente["hora_entrada2"];
                if ($hora_entrada_tar=="00:00:00") {
                        $hora_entrada_tar=$hora_entrada_centro_tar;
                        }
                $hora_salida_tar=$datos_docente["hora_salida2"];
                if ($hora_salida_tar=="00:00:00") {
                        $hora_salida_tar=$hora_salida_centro_tar;
                        }
                $horas_semana=$datos_docente["horas_semana"];
                $observaciones=$datos_docente["observaciones"];
                $l=$datos_docente["l"];
                $m=$datos_docente["m"];
                $x=$datos_docente["x"];
                $j=$datos_docente["j"];
                $v=$datos_docente["v"];
                $s=$datos_docente["s"];
                $d=$datos_docente["d"];
                $servicio_comun=$datos_docente["servicio_comun"];
                $horas_l=$datos_docente["horas_l"];
                $horas_m=$datos_docente["horas_m"];
                $horas_x=$datos_docente["horas_x"];
                $horas_j=$datos_docente["horas_j"];
                $horas_v=$datos_docente["horas_v"];
                $horas_s=$datos_docente["horas_s"];
                $horas_d=$datos_docente["horas_d"];              
                
        }

$url_retorno="listado_docentes.php";

?>
<script language="Javascript">
function comprobar_y_enviar(accion) {

                var errores=0;

                var mensajes_error="";
                
                if (errores==0) {
                        if (accion==1) {
                                 document.<?=$formulario_name;?>.action='calendario_mes.php';
                                }
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }

function borrar_cambio(id_cambio) {

        if (confirm("Vas a eliminar el cambio de aula seleccionado.\n\n¿Estás seguro/a?")) {
                window.location.href="borrar_cambio.php?id_cambio="+id_cambio+"&id_docente=<?=$id_docente?>";
                }

        }


</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_horario" value="<?=$id_horario;?>">
<input type="hidden" name="id_docente" value="<?=$id_docente;?>">
<input type="hidden" name="o" value="<?=$o;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> de personal</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=4 face="<?= $fuente_general; ?>"><b><?=$nombre_docente?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>NISS: </b></font></td>
                                <td align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="nss" value="<?=$nss;?>" style="font-size: 10px" size=50></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Código NOMINAPLUS: </b></font></td>
                                <td align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="codigo_nominaplus" value="<?=$codigo_nominaplus;?>" style="font-size: 10px" size=3></font></td>
                        </tr>
    <tr valign="top">
      <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Titulación:</b></font></td>
      <td align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><select style="font-size: 10" name="titulacion">
      <option value=""></option>
       <?php 

       $encontrado=false;
       $busca_tipos_cat=db_query("SELECT * FROM personal_cat_laborales ORDER BY id_categoria");

                                while($tipos_cat=db_fetch_array($busca_tipos_cat)) {
                                ?>
                                <option value="<?=$tipos_cat["nombre_categoria"]?>"<?php  if ($tipos_cat["nombre_categoria"]==$titulacion) {
                                print " selected";
                                $encontrado=true;
                                } ?>><?=$tipos_cat["nombre_categoria"]?></option>
                                <?php 
                                }

        if ($encontrado==false) {
        ?>
        <option value="<?=$titulacion?>"><?=$titulacion?></option>
        <?php 

        }
       ?>
       </select></font></td>
    </tr>
    <tr valign="top">
      <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Especialidad:</b></font></td>
      <td align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><select style="font-size: 10" name="especialidad">
      <option value=""></option>
       <?php 

       $encontrado=false;
       $busca_tipos_cat=db_query("SELECT * FROM personal_especialidades ORDER BY id_especialidad");

                                while($tipos_cat=db_fetch_array($busca_tipos_cat)) {
                                ?>
                                <option value="<?=$tipos_cat["nombre_especialidad"]?>"<?php  if ($tipos_cat["nombre_especialidad"]==$especialidad) {
                                print " selected";
                                $encontrado=true;
                                } ?>><?=$tipos_cat["nombre_especialidad"]?></option>
                                <?php 
                                }

        if ($encontrado==false) {
        ?>
        <option value="<?=$titulacion?>"><?=$titulacion?></option>
        <?php 

        }
       ?>
       </select></font></td>
    </tr>
    <tr valign="top">
      <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>¿Servicio común?</b></font></td>
      <td align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><input type="radio" name="servicio_comun" value=0<?php  if ($servicio_comun==0) { print " checked"; } ?>> No <input type="radio" name="servicio_comun" value=1<?php  if ($servicio_comun==1) { print " checked"; } ?>> Si</font></td>
    </tr>
                        <?php 
                        if (($nivel_acceso==4) and ($ip_segura==true)) {
                        ?>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Contratos laborales</b></font></td>
                        </tr>
                        <?php 
                        // Busca contratos
                        
                        $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=?', 's', array($id_docente));
                        
                        if (db_num_rows($busca_contratos)==0) {
                                ?>
                        <tr bgcolor="#ffffff">
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>">No se han encontrado contratos.</font></td>
                        </tr>
                                <?php 
                                } else {
                                ?>
                        <tr bgcolor="#ffffff">
                        <td colspan=2 align="center">
                        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                        <tr bgcolor="#efefef">
                                <td><font size=2><b>Categoría</b></font></td>
                                <td><font size=2><b>Tipo contrato</b></font></td>
                                <td><font size=2><b>Inicio/Fin</b></font></td>
                                <td><font size=2><b>Salario Neto</b></font></td>
                                <td><font size=2><b>&nbsp;</b></font></td>
                        </tr>
                                <?php 

                        while($lista_contratos=db_fetch_array($busca_contratos)){
                        
                        // Busca cat laboral
                        $busca_tipos_cat=db_query_params('SELECT * FROM personal_cat_laborales WHERE id_categoria=?', 's', array($lista_contratos["id_categoria"]));

                        if (db_num_rows($busca_tipos_cat)==0) {
                                $nombre_tipo_cat="Desconocida";
                                } else {
                                $tipos_cat=db_fetch_array($busca_tipos_cat);
                                $nombre_tipo_cat=$tipos_cat["nombre_categoria"];
                                }
                        
                        // Busca tipos contrato
                        $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=".$lista_contratos["tipo_contrato"]);
                        
                        if (db_num_rows($busca_tipos_contrato)==0) {
                                $nombre_tipo_contrato="Desconocido";
                                } else {
                                $tipos_contrato=db_fetch_array($busca_tipos_contrato);
                                $nombre_tipo_contrato=$tipos_contrato["nombre_tipo_contrato"];
                                }
                                
                        // Fechas
                        if ($lista_contratos["tipo_contrato"]!=2) {
                                $fechas_contrato=date("d/m/Y",strtotime($lista_contratos["fecha_inicio"]))."-".date("d/m/Y",strtotime($lista_contratos["fecha_fin"]));
                                } else {
                                if (($lista_contratos["fecha_fin"]=="0000-00-00") or (strlen($lista_contratos["fecha_fin"])==0) or (is_null($lista_contratos["fecha_fin"]))) {
                                        $fechas_contrato=date("d/m/Y",strtotime($lista_contratos["fecha_inicio"]));
                                        } else {
                                        $fechas_contrato=date("d/m/Y",strtotime($lista_contratos["fecha_inicio"]))."-".date("d/m/Y",strtotime($lista_contratos["fecha_fin"]));
                                        }
                                }
                        
                        // Comprueba si hay actividad en el contrato
                        $busca_actividad=db_query_params('SELECT * FROM personal_pagos WHERE id_contrato=?', 's', array($lista_contratos["id_contrato"]));
                        
                        if (db_num_rows($busca_actividad)!=0) {
                                $link_link="alert('No se puede eliminar un contrato en el que extista registro de actividad.');";
                                } else {
                                $link_link="if (confirm('¿Quieres borrar el contrato seleccionado?')) { window.location.href='borrar_contrato.php?id_docente=$id_docente&id_contrato=".$lista_contratos["id_contrato"]."'; }";
                                }
                        
                        ?>
                        <tr bgcolor="#ffffff">
                                <td><font size=2><?=$nombre_tipo_cat?></font></td>
                                <td><font size=2><?=$nombre_tipo_contrato?></font></td>
                                <td align="center"><font size=2><?=$fechas_contrato?></font></td>
                                <td align="right"><font size=2><?=number_format($lista_contratos["salario_base"],2,",",".")?> &#8364;</font></td>
                                <td align="center"><a title="Editar contrato" href="nuevo_contrato.php?id_contrato=<?=$lista_contratos["id_contrato"];?>"><img src="editar.gif" border=0></a> <a title="Borrar contrato" href="#" onclick="<?=$link_link;?>"><img src="borrar.gif" border=0></a> <a title="Ver resumen económico del contrato" href="resumen_contrato.php?id_contrato=<?=$lista_contratos["id_contrato"];?>"><img src="servicios.gif" border=0></a></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        </td></tr>
                        <?php 
                        }
                        ?>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><input type="button" onclick="window.location.href='nuevo_contrato.php?id_usuario=<?=$id_docente?>'" value="Nuevo contrato" style="font-size: 10px"></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Historial de cambios de aula</b></font></td>
                        </tr>
                        <?php 
                        // Busca cambios

                        $busca_cambios=db_query_params('SELECT * FROM personal_historial WHERE id_empleado=?', 's', array($id_docente));

                        if (db_num_rows($busca_cambios)==0) {
                                ?>
                        <tr bgcolor="#ffffff">
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>">No se han encontrado cambios de aula.</font></td>
                        </tr>
                                <?php 
                                } else {
                                ?>
                        <tr bgcolor="#ffffff">
                        <td colspan=2 align="center">
                        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                        <tr bgcolor="#efefef">
                                <td><font size=2><b>Escuela</b></font></td>
                                <td><font size=2><b>Aula</b></font></td>
                                <td><font size=2><b>Inicio/Fin</b></font></td>
                                <td><font size=2><b>&nbsp;</b></font></td>
                        </tr>
                                <?php 

                        while($lista_cambios=db_fetch_array($busca_cambios)){

                        // Busca centro
                        $busca_centros_cambio=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($lista_cambios["id_escuela"]));

                        if (db_num_rows($busca_centros_cambio)==0) {
                                $nombre_centro_cambio="Desconocida";
                                } else {
                                $centros_cambio=db_fetch_array($busca_centros_cambio);
                                $nombre_centro_cambio=$centros_cambio["nombre_centro"];
                                }

                        // Busca aula
                        $busca_aulas_cambio=db_query_params('SELECT cursos_academicos.nombre AS nombre_curso,centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?', 's', array($lista_cambios["id_aula"]));

                        if (db_num_rows($busca_aulas_cambio)==0) {
                                $nombre_aula_cambio="Desconocida";
                                } else {
                                $aulas_cambio=db_fetch_array($busca_aulas_cambio);
                                $nombre_aula_cambio=$aulas_cambio["nombre_aula"].", Curso: ".$aulas_cambio["nombre_curso"];
                                }

                        // Fechas
                        if (strlen($lista_cambios["fecha_fin"])!=0) {
                                $fechas_cambio=date("d/m/Y",strtotime($lista_cambios["fecha_inicio"]))."-".date("d/m/Y",strtotime($lista_cambios["fecha_fin"]));
                                } else {
                                $fechas_cambio=date("d/m/Y",strtotime($lista_cambios["fecha_inicio"]))."-Actualidad";
                                }

                        ?>
                        <tr bgcolor="#ffffff">
                                <td><font size=2><?=$nombre_centro_cambio?></font></td>
                                <td><font size=2><?=$nombre_aula_cambio?></font></td>
                                <td align="center"><font size=2><?=$fechas_cambio?></font></td>
                                <td align="center"><a title="Editar" href="nuevo_cambio.php?id_cambio=<?=$lista_cambios["id"];?>"><img src="editar.gif" border=0></a> <a title="Borrar" href="#" onclick="borrar_cambio(<?=$lista_cambios["id"]?>)"><img src="borrar.gif" border=0></a></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        </td></tr>
                        <?php 
                        }
                        ?>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><input type="button" onclick="window.location.href='nuevo_cambio.php?id_usuario=<?=$id_docente?>'" value="Nuevo cambio de aula" style="font-size: 10px"></font></td>
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
                                                                      for ($c_ano=2005; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
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
                                                                      </select>
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
                                                                      for ($c_ano=2005; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
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
                                                                      </select>
                                </td>
                                </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Horario de entrada y salida</b></font></td>
                        </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Horas semanales:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>"><input type="text" size=8 value="<?=$horas_semana;?>" name="horas_semana"></font></td>
                        </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mañanas:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tardes:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Días de la semana</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2>
                                <table border=1 bordercolor="#000000" style="border-collapse: collapse" cellpadding=2 cellspacing=0>
                                <tr>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>L</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>M</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>X</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>J</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>V</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>S</b></font></td>
                                <td bgcolor="#EECDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>D</b></font></td>
                                </tr>
                                <tr>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="l"<?php  if ($l==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="m"<?php  if ($m==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="x"<?php  if ($x==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="j"<?php  if ($j==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="v"<?php  if ($v==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="s"<?php  if ($s==1) { print " checked"; } ?>></td>
                                <td bgcolor="#ffffff" align="center"><input type="checkbox" value="1" name="d"<?php  if ($d==1) { print " checked"; } ?>></td>
                                </tr>
                                <tr>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_l?>" name="horas_l"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_m?>" name="horas_m"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_x?>" name="horas_x"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_j?>" name="horas_j"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_v?>" name="horas_v"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_s?>" name="horas_s"></td>
                                <td bgcolor="#ffffff" align="center"><input type="text" size=2 value="<?=$horas_d?>" name="horas_d"></td>
                                </tr>                                
                                </table>
                                </td>
                        </tr>
                        </table><br>
                        <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Calendario detallado" onclick="comprobar_y_enviar(1);" style="font-size: 10px"> <input type="button" value="Guardar cambios y salir >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);">
                        <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
