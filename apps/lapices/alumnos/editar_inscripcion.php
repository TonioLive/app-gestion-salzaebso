<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$array_valores_alimentos=array();



$array_valores_alergias=array();

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_inscripcion=$_GET["i"];

$consulta_inscripcion=db_query("SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=$id_inscripcion") or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["curso"];
$usuario_aula=$datos_inscripcion["aula"];
$usuario_aula_ayudas=$datos_inscripcion["aula_ayudas"];

$fecha_limite_presentacion=$datos_inscripcion["fecha_limite_presentacion"];

$usuario_fechains_dia=date("j",strtotime($datos_inscripcion["fecha_inscripcion"]));
$usuario_fechains_mes=date("n",strtotime($datos_inscripcion["fecha_inscripcion"]));
$usuario_fechains_ano=date("Y",strtotime($datos_inscripcion["fecha_inscripcion"]));
if ($datos_inscripcion["fecha_inscripcion"]=="0000-00-00") {
                                $usuario_fechains_dia="";
                                $usuario_fechains_mes="";
                                $usuario_fechains_ano="";
                                }

$usuario_fechamat_dia=date("j",strtotime($datos_inscripcion["fecha_matricula"]));
$usuario_fechamat_mes=date("n",strtotime($datos_inscripcion["fecha_matricula"]));
$usuario_fechamat_ano=date("Y",strtotime($datos_inscripcion["fecha_matricula"]));
if ($datos_inscripcion["fecha_matricula"]=="0000-00-00") {
                                $usuario_fechamat_dia=date("j",strtotime($datos_inscripcion["fecha_inicio"]));
                                $usuario_fechamat_mes=date("n",strtotime($datos_inscripcion["fecha_inicio"]));
                                $usuario_fechamat_ano=date("Y",strtotime($datos_inscripcion["fecha_inicio"]));
                                }

$usuario_fechabaja_dia=date("j",strtotime($datos_inscripcion["fecha_baja"]));
$usuario_fechabaja_mes=date("n",strtotime($datos_inscripcion["fecha_baja"]));
$usuario_fechabaja_ano=date("Y",strtotime($datos_inscripcion["fecha_baja"]));
if ($datos_inscripcion["fecha_baja"]=="0000-00-00") {
                                $usuario_fechabaja_dia=date("j",strtotime($datos_inscripcion["fecha_fin"]));
                                $usuario_fechabaja_mes=date("n",strtotime($datos_inscripcion["fecha_fin"]));
                                $usuario_fechabaja_ano=date("Y",strtotime($datos_inscripcion["fecha_fin"]));
                                }
                                
$usuario_fechamat=strtotime($datos_inscripcion["fecha_matricula"]);
$usuario_fechabaja=strtotime($datos_inscripcion["fecha_baja"]);
                                
$motivo_baja=$datos_inscripcion["motivo_baja"];
$proceso_decision=$datos_inscripcion["proceso_decision"];
$tipo_mensualidad=$datos_inscripcion["tipo_mensualidad"];
$comedor=$datos_inscripcion["comedor"];
$tipo_menu_comedor=$datos_inscripcion["tipo_menu_comedor"];
$numero_tomas=$datos_inscripcion["numero_tomas"];
$banco_entidad=$datos_inscripcion["banco_entidad"];
$banco_poblacion=$datos_inscripcion["banco_poblacion"];
$banco_titular=$datos_inscripcion["banco_titular"];
$banco_dni_titular=$datos_inscripcion["banco_dni_titular"];
$banco_domicilio=$datos_inscripcion["banco_domicilio"];
$banco_iban=$datos_inscripcion["banco_iban"];
$banco_cuenta=$datos_inscripcion["banco_cuenta"];
$forma_de_pago=$datos_inscripcion["forma_de_pago"];
$menu_actual=$datos_inscripcion["menu_actual"];
$comedor_especial=$datos_inscripcion["comedor_especial"];
$comedor_especial_precio=$datos_inscripcion["comedor_especial_precio"];
$codigo_inscripcion=$datos_inscripcion["codigo_inscripcion"];

$no_merienda=$datos_inscripcion["no_merienda"];

// Nº de orden y alumno al que sustituye
$orden_en_aula=$datos_inscripcion["orden_en_aula"];
$sustituye_a=$datos_inscripcion["sustituye_a"];

if (strlen($banco_cuenta)!=0) {
                $array_banco=explode(" ",$banco_cuenta);
                $banco_cuenta1=$array_banco[0];
                $banco_cuenta2=$array_banco[1];
                $banco_cuenta3=$array_banco[2];
                $banco_cuenta4=$array_banco[3];
        } else {
                $banco_cuenta1="";
                $banco_cuenta2="";
                $banco_cuenta3="";
                $banco_cuenta4="";
        }

// calcula IBAN
if (strlen($banco_iban)==0) {
        $banco_iban=generarDCIdelIban(calcularIban( $banco_cuenta1, $banco_cuenta2, $banco_cuenta3, $banco_cuenta4 ));
}



$consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_inscripcion["id_centro"]) or die(db_error()."-2");
$datos_centro=db_fetch_array($consulta_nombre_centro);

$id_usuario_seleccionado=$datos_inscripcion["id_usuario"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-3");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                $string_alimentos="";
                for ($c=0;$c<count($array_alimentos);$c++) {
                        $array_valores_alimentos[$c]=$datos_usuario[$array_alimentos[$c]];
                        if ($array_valores_alimentos[$c]==0) {
                                $string_alimentos=$string_alimentos."menu_alimentos".$array_alimentos[$c]."=1 OR ";
                                }
                        }
                if ($string_alimentos!="") {
                        $string_alimentos="(".substr($string_alimentos,0,strlen($string_alimentos)-4).")";
                        }
                $string_alergias="";
                for ($c=0;$c<count($array_alergias);$c++) {
                        $array_valores_alergias[$c]=$datos_usuario[$array_alergias[$c]];
                        if ($array_valores_alergias[$c]==1) {
                                $string_alergias=$string_alergias."menu_alimentos".$array_alergias[$c]."=1 OR ";
                                }
                        }
                if ($string_alergias!="") {
                        $string_alergias="(".substr($string_alergias,0,strlen($string_alergias)-4).")";
                        }
                        
                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y")-date("Y",strtotime($datos_usuario["fecha_nacimiento"]));
                        if (date("m")<date("m",strtotime($datos_usuario["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n")-date("n",strtotime($datos_usuario["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                }
        if ($edad_alumno_anos>1) {
                $plural_anios="s";
                } else {
                $plural_anios="";
                }
        if ($edad_alumno_meses>1) {
                $plural_meses="es";
                } else {
                $plural_meses="";
                }

        // Comprueba si es de regimen
        if ($comedor==3) {
                        $filtro_regimen=" AND regimen=1";
                } else {
                        $filtro_regimen=" AND regimen=0";
                }

        // Calcula el menú que le toca por edades
        // echo $edad_alumno;
        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus WHERE (menu_menus.disponible_desde<='".date("Y-m-d")."' AND menu_menus.disponible_hasta>='".date("Y-m-d")."') AND edad_ini_menu<=$edad_alumno $filtro_regimen");
        $diferencia_actual=1000;
        $id_menu_seleccionado=0;
        $nombre_menu_seleccionado="";
        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                if ($diferencia<=$diferencia_actual) {
                        $diferencia_actual=$diferencia;
                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                        $nombre_menu_seleccionado=urldecode($menu_apropiado["nombre_menu"]);
                        }
                }
        // Menu defecto
        if ($id_menu_seleccionado==0) {
                $texto_menu_seleccionado="<font color=red>No hay menús apropiados para la edad del niño.</font>";
                } else {
                $texto_menu_seleccionado="<font color=green>El menú que más se ajusta a la edad del niño es<br>\"$nombre_menu_seleccionado\".</font>";
                }

                }
        }

if (($menu_actual==0) and ($id_menu_seleccionado!=0)) {

        // Comprueba si el alumno tiene algún tipo de alergia o introducción de alimentos

        // $busca_alergias_en_menu=mysql_query("SELECT * FROM menu_calendario, ");


        // $menu_actual=$id_menu_seleccionado;
        // $actualiza_menu=mysql_query("UPDATE alumnos SET menu_seleccionado=$menu_actual WHERE id_alumno=$id_inscripcion");
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guardar_inscripcion.php";

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
      
      
// Busca registro de actividad en el centro

// 1ª Asistencia

$busca_primera_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_seleccionado AND id_servicio=$usuario_aula ORDER BY fecha LIMIT 0,1");

if (db_num_rows($busca_primera_asistencia)!=0) {
        $datos_primera_asistencia=db_fetch_array($busca_primera_asistencia);
        $fecha_primera_asistencia=strtotime($datos_primera_asistencia["fecha"]);
        $fecha_primera_asistencia_js=date("Ymd",$fecha_primera_asistencia);

        if ($usuario_fechamat>$fecha_primera_asistencia) {
                // echo "Error1: Fecha alta ".date("d/m/Y",$usuario_fechamat).", Primera asistencia: ".date("d/m/Y",$fecha_primera_asistencia);
                } else {
                // echo "OK1: Fecha alta ".date("d/m/Y",$usuario_fechamat).", Primera asistencia: ".date("d/m/Y",$fecha_primera_asistencia);
                }
        
        } else {
        $fecha_primera_asistencia=0;
        }

// Ultima asistencia

$busca_ultima_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_seleccionado AND id_servicio=$usuario_aula ORDER BY fecha DESC LIMIT 0,1");

if (db_num_rows($busca_ultima_asistencia)!=0) {
        $datos_ultima_asistencia=db_fetch_array($busca_ultima_asistencia);
        $fecha_ultima_asistencia=strtotime($datos_ultima_asistencia["fecha"]);
        $fecha_ultima_asistencia_js=date("Ymd",$fecha_ultima_asistencia);
        
        if ($usuario_fechabaja<$fecha_ultima_asistencia) {
                // echo "Error2: Fecha baja ".date("d/m/Y",$usuario_fechabaja).", Ultima asistencia: ".date("d/m/Y",$fecha_ultima_asistencia);
                } else {
                // echo "OK2: Fecha baja ".date("d/m/Y",$usuario_fechabaja).", Ultima asistencia: ".date("d/m/Y",$fecha_ultima_asistencia);
                }
        
        } else {
        $fecha_ultima_asistencia=0;
        }
      
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="id_alumno" value="<?=$id_inscripcion;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Edición de Inscripción</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100" align="left"></a>
                                        <font size=2 face="<?= $fuente_general; ?>"><br>Alumno: <b><?=$usuario_apellido1?> <?=$usuario_apellido2?>, <?=$usuario_nombre?><br>
                                        Inscripción en el centro <i>"<?=$datos_centro["nombre_centro"];?>"</i>.<br><br>
                                        Edad actual del niño: <?=$edad_alumno_anos;?> año<?=$plural_anios?> y <?=$edad_alumno_meses?> mes<?=$plural_meses?></b><br>
                                        <?=$texto_menu_seleccionado?>
                                        </font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Nº</b></font></td>
                                        <td><font face="<?= $fuente_general; ?>" size="2"><?php if ($orden_en_aula!=0) { echo($orden_en_aula); } else { echo("No asignado"); } ?></font></td>
                                </tr>                                
                                <?php 
                                // if ((($orden_en_aula==0) and (intval(strtotime(date("Y-m-d")))<intval(strtotime($fecha_limite_presentacion)))) or ($sustituye_a!=0)) {
                                if (($orden_en_aula==0) or ($sustituye_a!=0)) {
                                        ?>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Sustituye a:</b></font></td>
                                        <td><select name="sustituye_a" size=1>
                                        <option value="0"></option>
                                <?php 
                                $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula_ayudas=$usuario_aula_ayudas AND alumnos.orden_en_aula<>0 AND alumnos.id_alumno<>$id_inscripcion ORDER BY Apellido1, Apellido2, Nombre");
                                while($alumnos=db_fetch_array($busca_alumnos)){
                                ?>
                                <option value="<?=$alumnos["id_alumno"];?>"<?php  if ($alumnos["id_alumno"]==$sustituye_a) { print " selected"; } ?>><?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?>, <?=$alumnos["Nombre"]?></option>
                                <?php 
                                }
                                ?>
                                </select></td>
                                </tr>
                                        <?php 
                                        } else {
                                        ?>
                                        <input type="hidden" name="sustituye_a" value="<?=$sustituye_a?>">
                                        <?php 
                                        }
                                ?>                                
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Curso académico</b></font></td>
                                        <td>
                                <?php 
                                // Comprueba matrículas
                                // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                                $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_inscripcion["id_curso"]) or die(db_error()."-4");;
                                $datos_curso_inscrito=db_fetch_array($consulta_cursos);
                                ?>
                                <font size=2><?=$datos_curso_inscrito["nombre"];?></font>
                                </td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Código de Matrícula<br />(ID Solicitud)</b></font></td>
                                        <td><input type="text" name="codigo_inscripcion" size=60 value="<?=$codigo_inscripcion;?>"></td>
                                </tr>                                 
                                <!--
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Proceso de decisión:</b></font></td>
                                        <td><select name="proceso_decision" size=1 disabled>
                                        <option value="1">b1</option>
                                        <option value="2">b2</option>
                                        <option value="3">b3</option>
                                        <option value="4" selected>c</option>
                                        </select></td>
                                </tr>
                                -->                                
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de inscripción</b></font></td>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($usuario_fechains_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($usuario_fechains_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechains_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-25; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($usuario_fechains_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('usuario_fechains','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></td>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($usuario_fechamat_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($usuario_fechamat_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechamat_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-25; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($usuario_fechamat_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechamat','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a> * Por defecto la fecha de inicio del curso</font></td>
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
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($usuario_fechabaja_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
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
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($usuario_fechabaja_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechabaja_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-25; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($usuario_fechabaja_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('usuario_fechabaja','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a> * Por defecto la fecha de fin del curso</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Motivo de la baja</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="text" name="motivo_baja" size=60 readonly value="<?=$motivo_baja;?>" style="font-size: 10px" onclick="intro_motivo()"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td colspan=2><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de mensualidad</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"> <input type="radio" name="tipo_mensualidad" value="1"<?php  if ($tipo_mensualidad==1) { print " checked"; } ?>> Media jornada&nbsp; &nbsp;<input type="radio" name="tipo_mensualidad" value="2"<?php  if ($tipo_mensualidad==2) { print " checked"; } ?>>Jornada Completa </font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="comedor" value="2"<?php  if ($comedor==2) { print " checked"; } ?>>No &nbsp; &nbsp;<input type="radio" name="comedor" value="1"<?php  if ($comedor==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="comedor" value="3"<?php  if ($comedor==3) { print " checked"; } ?>> Regimen</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Merienda?</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="no_merienda" value="0"<?php  if ($no_merienda==0) { print " checked"; } ?>>Si &nbsp; &nbsp;<input type="radio" name="no_merienda" value="1"<?php  if ($no_merienda==1) { print " checked"; } ?>> No</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Menú seleccionado</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><select name="menu_actual">
                                        <option value="0"<?php  if ($menu_actual==0) { print " selected"; } ?>>Decidir por edad</option>
                                        <option value="-1"<?php  if ($menu_actual==-1) { print " selected"; } ?>>Decidir por aula</option>
                                        <?php 
                                        $consulta_datos_menus=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE (menu_menus.disponible_desde<='".date("Y-m-d")."' AND menu_menus.disponible_hasta>='".date("Y-m-d")."') ORDER BY edad_ini_menu;") or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_actual) { print " selected"; }  if ($datos_menus["id_menu"]==$id_menu_seleccionado) { print " style='color: #1eae32'"; } ?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de menú</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="tipo_menu_comedor" value="1"<?php  if ($tipo_menu_comedor==1) { print " checked"; } ?>>Pequeños &nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="2"<?php  if ($tipo_menu_comedor==2) { print " checked"; } ?>> Mayores&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="3"<?php  if ($tipo_menu_comedor==3) { print " checked"; } ?>> Colegial&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="4"<?php  if ($tipo_menu_comedor==4) { print " checked"; } ?>> Toma&nbsp; &nbsp;<input type="radio" name="tipo_menu_comedor" value="5"<?php  if ($tipo_menu_comedor==5) { print " checked"; } ?>> Bebe</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nº de tomas</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="numero_tomas" value="1"<?php  if ($numero_tomas==1) { print " checked"; } ?>>1 &nbsp; &nbsp;<input type="radio" name="numero_tomas" value="2"<?php  if ($numero_tomas==2) { print " checked"; } ?>> 2&nbsp; &nbsp;<input type="radio" name="numero_tomas" value="3"<?php  if ($numero_tomas==3) { print " checked"; } ?>> 3&nbsp; &nbsp;<input type="radio" name="numero_tomas" value="4"<?php  if ($numero_tomas==4) { print " checked"; } ?>> 4</td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Precio especial de comedor?</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="comedor_especial" value="0"<?php  if ($comedor_especial==0) { print " checked"; } ?>>No &nbsp; &nbsp;<input type="radio" name="comedor_especial" value="1"<?php  if ($comedor_especial==1) { print " checked"; } ?>> Sí &nbsp; &nbsp; Precio: <input type="text" name="comedor_especial_precio" style="text-align: right" size=6 value="<?=$comedor_especial_precio;?>"> €</font></td>
                                </tr>
                                <tr vAlign="top" bgcolor="#efefef">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Batos bancarios y de facturación</b></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre del cliente <span style="color:#ff0000;">*</span></b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_titular" size=50 value="<?=$banco_titular;?>">
                                        <br /><span style="color:#ff0000;">* Nombre de quien paga las facturas.</span></font>
                                </td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>DNI/NIE del cliente <span style="color:#ff0000;">*</span></b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_dni_titular" size=35 value="<?=$banco_dni_titular;?>">
                                        <br /><span style="color:#ff0000;">* DNI/NIE de quien paga las facturas.</span></font></td>
                                </tr>                                
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Forma de pago</b></font></td>
                                        <td><font size="2" face="<?= $fuente_general; ?>"><input type="radio" name="forma_de_pago" value="0"<?php  if ($forma_de_pago==0) { print " checked"; } ?>>Contado &nbsp; &nbsp;<input type="radio" name="forma_de_pago" value="5"<?php  if ($forma_de_pago==5) { print " checked"; } ?>>Cheque guardería &nbsp; &nbsp;<input type="radio" name="forma_de_pago" value="1"<?php  if ($forma_de_pago==1) { print " checked"; } ?>> Domiciliación bancaria.</font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_entidad" size=60 value="<?=$banco_entidad;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Población entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_poblacion" size=50 value="<?=$banco_poblacion;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Domicilio entidad</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="banco_domicilio" size=50 value="<?=$banco_domicilio;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuenta domiciliación</b></font></td>
                                        <td><font size=2 face="<?= $fuente_general; ?>">ES<input type="text" name="banco_iban" size=4 maxlength=4 value="<?=$banco_iban?>">  <input type="text" name="banco_cuenta1" size=4 maxlength=4 value="<?=$banco_cuenta1;?>"> <input type="text" name="banco_cuenta2" size=4 maxlength=4 value="<?=$banco_cuenta2;?>"> <input type="text" name="banco_cuenta3" size=2 maxlength=2 value="<?=$banco_cuenta3;?>"> <input type="text" name="banco_cuenta4" size=10 maxlength=10 value="<?=$banco_cuenta4;?>"></font>
                                        <br><br>
                                        <?php
                                        $cuentaConIBAN=generarDCInToIban(calcularIban( $banco_cuenta1, $banco_cuenta2, $banco_cuenta3, $banco_cuenta4 ));
                                        if ($cuentaConIBAN!='ES'.$banco_iban.$banco_cuenta1.$banco_cuenta2.$banco_cuenta3.$banco_cuenta4) {
                                                ?>
                                                <font size=2 face="<?= $fuente_general; ?>" color="red">El Número de cuenta introducido no es correcto, el dígito de control del IBAN no coincide.</font>
                                                <?php
                                        }
                                        ?>
                                        <font size=2 face="<?= $fuente_general; ?>"><b>IBAN calculado:</b> <?=$cuentaConIBAN; ?></font></td>
                                </td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="if (confirm('Vas a salir SIN guardar los cambios. ¿Realmente quieres salir?')) { window.close(); }" value="Salir" style="font-size: 10px"><input type="button" onclick="cambia();" value="Guardar cambios" style="font-size: 10px"><?php  if (($nivel_acceso==4) or ($nivel_acceso==3)) { ?> &nbsp;&nbsp;<input type="button" onclick="eliminar_ins()" value="Eliminar inscripción" style="font-size: 10px"> &nbsp;&nbsp;<input type="button" onclick="traslado()" value="Traladar a otra escuela" style="font-size: 10px"><?php  } ?><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function traslado() {
        if (confirm("¿Estás totalmente seguro de querer traladar a este alumno?")) {
                window.location.href='traslado_expediente1.php?id_alumno=<?=$id_inscripcion?>&u=<?=$id_usuario_seleccionado?>';
                }
        }
function eliminar_ins() {
        if (confirm("¿Estás totalmente seguro de querer eliminar esta inscripción?\u000A\u000AEsta acción no tiene vuelta atrás y se perderá toda la información acumulada del alumno durante este curso (asistencia).")) {
                window.location.href='eliminar_inscripcion.php?id_alumno=<?=$id_inscripcion?>';
                }
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.usuario_fechains_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechains_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechains_ano.value;
                la_fecha_numero1=self.document.<?=$formulario_name;?>.usuario_fechains_ano.value+self.document.<?=$formulario_name;?>.usuario_fechains_mes.value+self.document.<?=$formulario_name;?>.usuario_fechains_dia.value;
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inscripción introducida no es correcta.\u000A";errores=1; }

                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.usuario_fechamat_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_ano.value;
                la_fecha_numero2=self.document.<?=$formulario_name;?>.usuario_fechamat_ano.value+self.document.<?=$formulario_name;?>.usuario_fechamat_mes.value+self.document.<?=$formulario_name;?>.usuario_fechamat_dia.value;
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inicio de la docencia introducida no es correcta.\u000A";errores=1; }

                la_fecha1=self.document.<?=$formulario_name;?>.usuario_fechabaja_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechabaja_ano.value;
                la_fecha_numero3=self.document.<?=$formulario_name;?>.usuario_fechabaja_ano.value+self.document.<?=$formulario_name;?>.usuario_fechabaja_mes.value+self.document.<?=$formulario_name;?>.usuario_fechabaja_dia.value;
                if (isDate(la_fecha1)!=true) { mensajes_error=mensajes_error+"La fecha de baja introducida no es correcta.\u000A";errores=1; }
                
                
                // Comprueba si las fechas son correctas
                if (errores==0) {
                
                if  (self.document.<?=$formulario_name;?>.sustituye_a.value=="0") {
                        if (Number(la_fecha_numero2)>Number(la_fecha_numero3)) {
                                mensajes_error=mensajes_error+"La fecha de baja no puede ser anterior a la fecha de alta.\u000A";
                                errores=1;
                                }
                
                <?php 

                if ($fecha_primera_asistencia!=0) {
                ?>
                        if (Number(la_fecha_numero2)>Number(<?=$fecha_primera_asistencia_js?>)) {
                                mensajes_error=mensajes_error+"La fecha de alta no puede ser posterior al primer dia de asistencia del alumno (<?=date("d/m/Y",$fecha_primera_asistencia);?>).\u000A";
                                errores=1;
                                }
                <?php 
                }

                if ($fecha_ultima_asistencia!=0) {
                ?>
                        if (Number(la_fecha_numero3)<Number(<?=$fecha_ultima_asistencia_js?>)) {
                                mensajes_error=mensajes_error+"La fecha de baja no puede ser anterior al último dia de asistencia del alumno (<?=date("d/m/Y",$fecha_ultima_asistencia);?>).\u000A";
                                errores=1;
                                }
                <?php 
                }
                ?>
                }
                
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
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
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
function intro_motivo() {
        window.open('listado_motivos.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
//-->
</script>
</center></div>
</div>
</body>
</html>
