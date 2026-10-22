<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$formulario_name="brunet";

$id_prueba=$_GET["id_prueba"];

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["id_alumno"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["id_alumno"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 

$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];
                
if ($datos_usuario["nonato"]==1) {
        $usuario_fechanac="No ha nacido";
        } else {
                
                if (strlen($datos_usuario["fecha_nacimiento"])==0) {
                        $usuario_fechanac="";
                        } else {
                        $nacimiento=explode( '-', $datos_usuario["fecha_nacimiento"]);
                        $edad=date("Y")-$nacimiento[0];
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime(date("Y/m/d"));
                        if (($cumple-$hoy)>0) {
                                $edad=$edad-1;
                                }
                                
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad=$edad_d_l." meses";

                        $usuario_fechanac=$edad." (Fecha nacimiento: ".date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"])).")";
                        }
                }
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                $alergias=$datos_usuario["alergias"];
                $vacunaciones=$datos_usuario["vacunaciones"];
                $observaciones=$datos_usuario["observaciones"];
                $contacto1=$datos_usuario["contacto1"];
                $telefono_contacto1=$datos_usuario["telefono_contacto1"];
                $contacto2=$datos_usuario["contacto2"];
                $telefono_contacto2=$datos_usuario["telefono_contacto2"];
                $contacto_medico=$datos_usuario["contacto_medico"];
                $telefono_medico=$datos_usuario["telefono_medico"];
                $usuario_nacionalidad=$datos_usuario["nacionalidad"];
                }

        // Busca el curso actual
        if ($id_curso_actual==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        }

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }

if (strlen($id_prueba)!=0) {
        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba));
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=urldecode($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $hora_hora=date("H",strtotime($datos_prueba["hora"]));
        $hora_min=date("i",strtotime($datos_prueba["hora"]));
        }
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="brunet" action="guarda_brunet.php" method="POST">
<input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_alumno">
<input type="hidden" value="<?=$id_prueba?>" name="id_prueba">
<input type="hidden" value="0" name="accion_despues">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Prueba Brunet</b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="50%">
                        <tr valign="top">
                                <td rowspan=7 align="center" valign="top" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_nombre;?> <?=$usuario_apellido1;?> <?=$usuario_apellido2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?php  if ($usuario_sexo=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha realización prueba</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_prueba["fecha"]))?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Hora realización prueba</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($datos_prueba["hora"]))?></font></td>
                        </tr>
                        <tr>
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>ER:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$edad_real_prueba?> meses</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Observaciones:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$observaciones_prueba;?></font></td>
                        </tr>
                        </table>
                        <br>
                        <?php 

                        $busca_items_brunet=db_query("SELECT * FROM pruebas_brunet_items WHERE num_orden<161");

                        $puntosP=0;
                        $puntosC=0;
                        $puntosL=0;
                        $puntosS=0;
                        $puntosTotal=0;
                        
                        $num_respuestasSI=0;
                        $num_respuestasNO=0;
                        
                        while($items_brunet=db_fetch_array($busca_items_brunet)) {
                        

                        // Busca respuesta
                        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_ampliado_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet["id_item"])) or die(db_error());
                        if (db_num_rows($busca_respuesta_item)!=0) {
                                $datos_respuesta_item=db_fetch_array($busca_respuesta_item);
                                $respuesta_item_actual=$datos_respuesta_item["respuesta"];
                                $valor_item_actual=$items_brunet["puntuacion"];
                                $escala_item_actual=$items_brunet["escala"];
                                
                                if ($respuesta_item_actual==1) {
                                
                                        $puntosTotal=$puntosTotal+$valor_item_actual;
                                        switch($escala_item_actual) {
                                                case 'P';
                                                        $puntosP = $puntosP+$valor_item_actual;
                                                        break;
                                                case 'C';
                                                        $puntosC = $puntosC+$valor_item_actual;
                                                        break;
                                                case 'L';
                                                        $puntosL = $puntosL+$valor_item_actual;
                                                        break;
                                                case 'S';
                                                        $puntosS = $puntosS+$valor_item_actual;
                                                        break;
                                                }
                                        }
                                }
                        }
                        
                        
                        $busca_items_brunet_ampliado=db_query("SELECT * FROM pruebas_brunet_items WHERE num_orden>160");

                        $puntosPA=0;
                        $puntosCA=0;
                        $puntosLA=0;
                        $puntosSA=0;

                        while($items_brunet_ampliado=db_fetch_array($busca_items_brunet_ampliado)) {

                        // Busca respuesta
                        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_ampliado_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet_ampliado["id_item"])) or die(db_error());
                        if (db_num_rows($busca_respuesta_item)!=0) {
                                $datos_respuesta_item=db_fetch_array($busca_respuesta_item);
                                $respuesta_item_actual=$datos_respuesta_item["respuesta"];
                                if ($items_brunet_ampliado["edad"]==36) {
                                        $valor_item_actual=1.5;
                                        }
                                if ($items_brunet_ampliado["edad"]==48) {
                                        $valor_item_actual=3;
                                        }
                                $escala_item_actual=$items_brunet_ampliado["escala"];

                                if ($respuesta_item_actual==1) {

                                        switch($escala_item_actual) {
                                                case 'P';
                                                        $puntosPA = $puntosPA+$valor_item_actual;
                                                        break;
                                                case 'C';
                                                        $puntosCA = $puntosCA+$valor_item_actual;
                                                        break;
                                                case 'L';
                                                        $puntosLA = $puntosLA+$valor_item_actual;
                                                        break;
                                                case 'S';
                                                        $puntosSA = $puntosSA+$valor_item_actual;
                                                        break;
                                                }
                                        }
                                }
                        }

                        $puntosTotalA=($puntosPA+$puntosCA+$puntosLA+$puntosSA)/4;

                        // Busca resultado P
                        $busca_P=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='P' AND p=$puntosP");
                        if (db_num_rows($busca_P)!=0) {
                                $datosP=db_fetch_array($busca_P);

                                $puntosA=$puntosPA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosP["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }

                                $edP=($datosP["m"]+$meses_a)." meses, ".($datosP["d"]+$dias_a)." días";
                                $edLP=($datosP["m"]+$meses_a);
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="P";
                                $puntos_actual=$puntosP;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLPMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLPMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLPMas-$edLPMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLP_1=(($edLPMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLP=floor($edLP_1);
                                $edLP_2=ceil(($edLP_1-$edLP)*30);

                                $edP=$edLP." meses, ".$edLP_2." días";
                                }

                        // Busca resultado C
                        $busca_C=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='C' AND p=$puntosC");
                        if (db_num_rows($busca_C)!=0) {
                                $datosC=db_fetch_array($busca_C);
                                
                                $puntosA=$puntosCA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosC["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }
                                $edC=($datosC["m"]+$meses_a)." meses, ".($datosC["d"]+$dias_a)." días";
                                $edLC=($datosC["m"]+$meses_a);
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="C";
                                $puntos_actual=$puntosC;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLCMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLCMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLCMas-$edLCMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLC_1=$puntosCA+(($edLCMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLC=floor($edLC_1);
                                $edLC_2=ceil(($edLC_1-$edLC)*30);

                                $edC=$edLC." meses, ".$edLC_2." días";
                                }
                                
                        // Busca resultado L
                        $busca_L=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='L' AND p=$puntosL");
                        if (db_num_rows($busca_L)!=0) {
                                $datosL=db_fetch_array($busca_L);
                                
                                $puntosA=$puntosLA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosL["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }

                                $edL=($datosL["m"]+$meses_a)." meses, ".($datosL["d"]+$dias_a)." días";
                                $edLL=($datosL["m"]+$meses_a);
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="L";
                                $puntos_actual=$puntosL;
                                
                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLMas-$edLMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edL_1=$puntosLA+(($edLMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edL=floor($edL_1);
                                $edL_2=ceil(($edL_1-$edL)*30);
                                $edLL=$edL;
                                $edL=$edL." meses, ".$edL_2." días";
                                }
                                
                        // Busca resultado S
                        $busca_S=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='S' AND p=$puntosS");
                        if (db_num_rows($busca_S)!=0) {
                                $datosS=db_fetch_array($busca_S);
                                
                                $puntosA=$puntosSA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosS["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }
                                        
                                $edS=($datosS["m"]+$meses_a)." meses, ".($datosS["d"]+$dias_a)." días";
                                $edLS=($datosS["m"]+$meses_a);
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="S";
                                $puntos_actual=$puntosS;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLSMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLSMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLSMas-$edLSMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLS_1=$puntosSA+(($edLSMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLS=floor($edLS_1);
                                $edLS_2=ceil(($edLS_1-$edLS)*30);

                                $edS=$edLS." meses, ".$edLS_2." días";
                                }

                        $meses_total=($puntosTotal/10)+$puntosTotalA;

                        $edTotalM=floor($meses_total);
                        $edTotalD=30*(round(($meses_total),2)-$edTotalM);
                        
                        $edTotal=$edTotalM." meses, ".$edTotalD." días";
                        
                        ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="50%">
                        <tr valign="top">
                                <td colspan=5 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Resultado</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Nº de puntos Brunet</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Meses Brunet Ampliado</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>E.D.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Dif. ED-ER</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>P</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosP?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosPA?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edP?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edLP-$edad_real_prueba?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>C</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosC?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosCA?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edC?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edLC-$edad_real_prueba?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>L</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosL?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosLA?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edL?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edLL-$edad_real_prueba?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>S</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosS?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosSA?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edS?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edLS-$edad_real_prueba?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosTotal?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$puntosTotalA?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edTotal?></b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$edTotalM-$edad_real_prueba?></b></font></td>
                        </tr>
                        </table>
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="window.location.href='seguimiento.php?id_alumno=<?=$id_alumno?>'" value="Salir" style="font-size:10px">
                        </div><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
</center></div>
</div>
</body>
</html>
<?php 
// Genera informe



?>
