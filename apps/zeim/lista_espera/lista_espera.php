<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($centro_actual));
                                if (db_num_rows($busca_datos_centro)!=0) {
                                        $datos_centro=db_fetch_array($busca_datos_centro);
                                        $lat_centro=$datos_centro["lat"];
                                        $lon_centro=$datos_centro["lon"];
                                }

$edad_lista=$_GET["edad_lista"];
$curso_seleccionado=$_GET["id_curso_lista"];

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

$orden_lista=$_GET["o"];
if (strlen($orden_lista)==0) {
        $orden_lista="motivo_baja,puntuacion DESC";
        }

if ($orden_lista=="fecha_alta") {
        $descripcion_orden="fecha de llegada";
        } else {
        $descripcion_orden="puntos";
        }


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>                                                                                                                                           <div align="center">
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
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b>Lista de espera, curso <?=$datos_curso_actual["nombre"]?>. Ordenada por <?=$descripcion_orden?><br>
Niños de <?=$edad_lista;?> a <?=$edad_lista+1;?> años.</b><br><br></font>
<table border=0 cellspacing="1" cellpadding="2" width="100%">
<tr bgcolor="#cc424f">
<td><font color="#ffffff" size=3 face="<?=$fuente_general?>"><b>Orden</b></font></td>
<td align="left"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Nombre</b></font></td>
<td align="center"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Fecha inscripción</b></font></td>
<td align="center"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Fecha deseada de incorporación</b></font></td>
<td align="center"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Distancia al centro<br />(en linea recta)</b></font></td>
<td align="center"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Puntos</b></font></td>
<td width=5% align="center">&nbsp;</td>
</tr>
<?php 
$busca_ninios=db_query("SELECT lista_espera.*,sum(lista_espera_cuestionarios.valor*lista_espera_respuestas.valor) as puntuacion FROM lista_espera LEFT JOIN lista_espera_respuestas ON lista_espera.id_usu_lista=lista_espera_respuestas.id_usu_lista LEFT JOIN lista_espera_cuestionarios ON lista_espera_respuestas.id_tipo_puntos=lista_espera_cuestionarios.id_tipo_punto WHERE lista_espera.id_curso=$curso_seleccionado AND lista_espera.edad_espera=$edad_lista AND lista_espera.id_centro=$centro_actual GROUP BY lista_espera.id_usu_lista ORDER BY $orden_lista");

$orden_lista=1;

$nombres_motivos_baja=array();
$busca_motivos_baja=db_query("SELECT * FROM lista_espera_motivos_baja");
while($motivos_baja=db_fetch_array($busca_motivos_baja)){
        $nombres_motivos_baja[$motivos_baja["id_motivo"]]=$motivos_baja["descripcion_motivo"];
        }
$nombres_motivos_baja[0]="Actualmente en lista de espera";

$motivo_actual=-1;

while($lista_ninios=db_fetch_array($busca_ninios)) {

if ($motivo_actual!=$lista_ninios["motivo_baja"]) {
$motivo_actual=$lista_ninios["motivo_baja"];
        ?>
        <tr bgcolor="#cc424f">
        <td align="left" colspan=7><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b><?=$nombres_motivos_baja[$motivo_actual]?></b></font></td>
        </tr>
        <?php 
        }

$busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($lista_ninios["id_usuario"]));
$datos_ninio=db_fetch_array($busca_datos_usuario);

$fecha_deseada="";
if ((strlen($lista_ninios["fecha_deseada"])!=0) and ($lista_ninios["fecha_deseada"]!="0000-00-00")) {
        $fecha_deseada=date("d/m/Y",strtotime($lista_ninios["fecha_deseada"]));
        }

// Distancia al centro
if (($datos_ninio["coordenadas"]!="") and ($datos_ninio["coordenadas"]!="NO LOCALIZADO"))   {
        $coodenadas_ninio=explode(",",substr($datos_ninio["coordenadas"],1,strlen($datos_ninio["coordenadas"])-2));
        $distancia=round(distance($lat_centro,$lon_centro,$coodenadas_ninio[0],$coodenadas_ninio[1],"K"),2)." Km.";
        } else {
        $distancia="Domicilio no localizado";
        }

?>
</tr>
<tr bgcolor="#efefef">
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$orden_lista;?></font></td>
<td align="left"><font color="#000000" size=2 face="<?=$fuente_general?>"><a target="edicion_alumnos" href="/alumnos/nuevo_alumno1.php?u=<?=$lista_ninios["id_usuario"];?>"><?=$datos_ninio["Nombre"]?> <?=$datos_ninio["Apellido1"]?> <?=$datos_ninio["Apellido2"]?></a></font></td>
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=date("d/m/Y",strtotime($lista_ninios["fecha_alta"]))?></font></td>
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$fecha_deseada?></font></td>
<td align="center"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$distancia?></font></td>
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$lista_ninios["puntuacion"]?></font></td>
<td width=25% align="center"><input type="button" value="Editar" onclick="self.location.href='editar_n_lista.php?id_n=<?=$lista_ninios["id_usu_lista"]?>'"></td>
</tr>
<?php 
$orden_lista++;
}
?>
</table>
</td>
</tr>
</table></td>
</table>
<br>
<input type="button" onclick="self.location.href='listas_espera.php?curso_seleccionado=<?=$curso_seleccionado?>'" value="Salir"> <?php  if ($idUsuario<>6297) { ?><input type="button" value="Añadir alumno" onclick="self.location.href='/alumnos/nuevo_alumno.php'"><?php  } ?>
<?php  require("../configuracion/parte_inferior.php"); ?>
