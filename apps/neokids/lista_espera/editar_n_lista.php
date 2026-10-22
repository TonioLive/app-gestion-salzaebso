<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 

$id_n=$_GET["id_n"];

$busca_datos_lista=db_query_params('SELECT * FROM lista_espera WHERE id_usu_lista=?', 's', array($id_n));

$datos_lista=db_fetch_array($busca_datos_lista);

$busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_lista["id_usuario"]));
$datos_ninio=db_fetch_array($busca_datos_usuario);

$id_usuario_seleccionado=$datos_lista["id_usuario"];

$edad_lista=$datos_lista["edad_espera"];
$categoria=$datos_lista["categoria"];
$curso_seleccionado=$datos_lista["id_curso"];
$nivel_natacion=$datos_lista["nivel_natacion"];

if (($datos_lista["fecha_deseada"]=="0000-00-00") or (strlen($datos_lista["fecha_deseada"])==0)) {
        $fechades_dia="";
        $fechades_mes="";
        $fechades_anio="";
        } else {
        $fechades_dia=date("j",strtotime($datos_lista["fecha_deseada"]));
        $fechades_mes=date("n",strtotime($datos_lista["fecha_deseada"]));
        $fechades_anio=date("Y",strtotime($datos_lista["fecha_deseada"]));
        }

if (($datos_lista["fecha_baja"]=="0000-00-00") or (strlen($datos_lista["fecha_baja"])==0)) {
        $fechabaja_dia="";
        $fechabaja_mes="";
        $fechabaja_anio="";
        } else {
        $fechabaja_dia=date("j",strtotime($datos_lista["fecha_baja"]));
        $fechabaja_mes=date("n",strtotime($datos_lista["fecha_baja"]));
        $fechabaja_anio=date("Y",strtotime($datos_lista["fecha_baja"]));
        }

// Busca el curso actual

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guarda_cambios_n.php";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>    

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
                                                                                                                                       <div align="center">
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align=left>
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
<div align="center">
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><table width=690 border=2 bordercolor="#cc424f">
<tr>
<td bgcolor="#ffffff" align="center">
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b>Inscripción en lista de espera, curso <?=$datos_curso_actual["nombre"]?>.<br>
Niños de <?=$edad_lista;?> a <?=$edad_lista+1;?> años.</b><br><br>
Alumno: <?=$datos_ninio["Nombre"]?> <?=$datos_ninio["Apellido1"]?> <?=$datos_ninio["Apellido2"]?></font>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
        <input type="hidden" name="id_n" value="<?=$id_n?>">
        <input type="hidden" name="id_curso_inscripcion" value="<?=$curso_seleccionado?>">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" colspan=3 align="center">
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Edad donde se inscribirá</b></font></td>
                                        <td><select name="edad_espera" size=1><?php 
                                        $array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");

for ($c_anios=0;$c_anios<count($array_listas_de_espera);$c_anios++) {
                                        ?>
                                        <option value="<?=$c_anios?>"<?php  if ($c_anios==$edad_lista) { print " selected"; } ?>><?=$array_listas_de_espera[$c_anios];?></option>
                                        <?php 
                                        }
                                        ?></select></td>
                                </tr>
                                <tr vAlign="top">
                                	<td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Tipo de usuario</b></font></td>
									 <td>
                                     	<select name="categoria" size=1>                                       
											<option value="0"<?php  if ($categoria==0) { print " selected"; } ?>>Nueva incorporación o reincorporación después de ausencia</option>
											<option value="1"<?php  if ($categoria==1) { print " selected"; } ?>>Ampliación de días de asistencia</option>
											<option value="2"<?php  if ($categoria==2) { print " selected"; } ?>>Cambio de grupo</option>
										</select></td>
                                </tr>   
                                <tr vAlign="top">
                                	<td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Nivel natación</b></font></td>
									 <td>
                                <select name="nivel_natacion" id="nivel_natacion" style="background-color: <?=$colores_subniveles[$nivel_natacion]?>; color: #fff">
                        <option value="0"<?php  if ($nivel_natacion==0) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1"<?php  if ($nivel_natacion==1) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2"<?php  if ($nivel_natacion==2) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4"<?php  if ($nivel_natacion==4) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5"<?php  if ($nivel_natacion==5) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7"<?php  if ($nivel_natacion==7) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8"<?php  if ($nivel_natacion==8) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==date("j",strtotime($datos_lista["fecha_alta"]))) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==date("n",strtotime($datos_lista["fecha_alta"]))) { print " selected"; } ?>><?= $mes_a; ?></option>
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
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==date("Y",strtotime($datos_lista["fecha_alta"]))) { print " selected"; } ?>><?= $ano_a; ?></option>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fechades_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fechades_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
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
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fechades_anio) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechades','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de baja en lista</b></font></td>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fechabaja_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fechabaja_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
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
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fechabaja_anio) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechabaja','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Motivo de baja</b></font></td>
                                        <td><select name="motivo_baja" size=1 style="font-size: 10px">
                                        <option value="0">Actualmente en lista</option><?php 
                                        $busca_motivos_baja=db_query("SELECT * FROM lista_espera_motivos_baja");
                                        while($motivos_baja=db_fetch_array($busca_motivos_baja)){
                                        ?>
                                        <option value="<?=$motivos_baja["id_motivo"]?>"<?php  if ($datos_lista["motivo_baja"]==$motivos_baja["id_motivo"]) { print " selected"; } ?>><?=$motivos_baja["descripcion_motivo"]?></option>
                                        <?php 
                                        }
                                        ?></select></td>
                                </tr>
                                <?php 
                                $busca_preguntas=db_query_params('SELECT * FROM lista_espera_cuestionarios WHERE desactivada=0 AND (id_curso=0 OR id_curso=?) ORDER BY id_tipo_punto', 's', array($curso_seleccionado));

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
                                
                                $busca_respuesta=db_query_params('SELECT * FROM lista_espera_respuestas WHERE id_usu_lista=? AND id_tipo_puntos=?', 'ss', array($id_n, $lista_preguntas["id_tipo_punto"]));
                                if (db_num_rows($busca_respuesta)!=0) {
                                        $datos_respuesta=db_fetch_array($busca_respuesta);
                                        $respuesta_actual=$datos_respuesta["valor"];
                                        } else {
                                        $inserta_respuesta=db_query_params('INSERT INTO lista_espera_respuestas (id_usu_lista,id_tipo_puntos,valor) VALUES (?,?,\'0\')', 'ss', array((string)($id_n), (string)($lista_preguntas["id_tipo_punto"]))) or die(db_error());
                                        $respuesta_actual=0;
                                        }
                                
                                ?>
                                <tr vAlign="top">
                                        <td align="right" width=50% bgcolor="#efefef"><font face="<?= $fuente_general; ?>" size="2"><b><?=$lista_preguntas["descripcion"];?></b></font></td>
                                        <td width=50%><font face="<?= $fuente_general; ?>" size="2"><input name="P<?=$lista_preguntas["id_tipo_punto"]?>" type="radio" value="1"<?php  if ($respuesta_actual==1) { print " checked"; } ?>> Si <input name="P<?=$lista_preguntas["id_tipo_punto"]?>" type="radio" value="0"<?php  if ($respuesta_actual==0) { print " checked"; } ?>> No</font></td>
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
                                        <td><textarea style="FONT-SIZE: 12px; FONT-FAMILY: Arial" name="observaciones" rows="6" cols="85"><?=$datos_lista["observaciones_lista_espera"]?></textarea></td>
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
                                <td align="center"><input type="checkbox"<?php  if ($datos_lista["dia_deseado_".$c_dia]==1) { print " checked"; } ?> value="1" name="dia_deseado_<?=$c_dia?>" id="dia_deseado_<?=$c_dia?>"></td>
                                <td align="center"><select size=1 style="font-size=10" name=hora_i_<?=$c_dia?>>
    <?php  
  $hora=date("H",strtotime($datos_lista["ini_horario_deseado_".$c_dia]));
  $sele="";
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 
    if (intval($h)==intval($hora))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 


?>
    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min_i_<?=$c_dia?>>
    <?php  
  $minuto=date("i",strtotime($datos_lista["ini_horario_deseado_".$c_dia]));
  $sele="";
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($minuto))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select></td>
                                <td align="center"><select size=1 style="font-size=10" name=hora_f_<?=$c_dia?>>
    <?php  
  $hora=date("H",strtotime($datos_lista["fin_horario_deseado_".$c_dia]));
  $sele="";
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 
    if (intval($h)==intval($hora))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 


?>
    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min_f_<?=$c_dia?>>
    <?php  
  $minuto=date("i",strtotime($datos_lista["fin_horario_deseado_".$c_dia]));
  $sele="";
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($minuto))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
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
                        <br><input type="button" onclick="self.location.href='lista_espera.php?edad_lista=<?=$edad_lista?>&id_curso_lista=<?=$curso_seleccionado?>'" value="Cancelar"> <input type="button" onclick="self.location.href='/alumnos/nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'" value="Inscribir"> <input type="button" onclick="guardar_cambios();" value="Guardar cambios"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="eliminar()" value="Eliminar">
                        <br>
                        <br>
                </td>
        </tr>
</table>
</form>
</td>
</tr>
</table></td>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script>

function eliminar() {
        if (confirm("¿Realmente quieres eliminar esta inscripción?\u000A\u000AEsta acción no tiene vuelta atrás.")) {
                window.location.href="eliminar_n.php?id_n=<?=$id_n;?>";
                }
        }

function guardar_cambios() {

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

                        // Comprueba fecha
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechabaja_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_ano.value
                        if (la_fecha!="--/--/----") {
                                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de baja introducida no es correcta.\u000A";errores=1; }
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
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
