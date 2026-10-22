<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 

// Busca el curso actual

$curso_seleccionado=$_GET["curso_seleccionado"];

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        $curso_seleccionado=$id_curso_actual;
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
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
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><table width=690 border=2 bordercolor="#cc424f">
<tr>
<td bgcolor="#ffffff" align="center">
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b>Listas de espera, curso <?=$datos_curso_actual["nombre"]?></b><br><br></font>
<table border=0 cellspacing="1" cellpadding="2" width="100%">
<tr bgcolor="#cc424f">
<td><font color="#ffffff" size=3 face="<?=$fuente_general?>"><b>Lista de espera</b></font></td>
<td align="right"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Actualmente en lista</b></font></td>
<td align="right"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Incorporados al centro</b></font></td>
<td align="right"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Descartados</b></font></td>
<td width=25% align="center">&nbsp;</td>
</tr>
<?php 

for ($c_anios=0;$c_anios<=5;$c_anios++) {

$busca_ninios_lista=db_query_params('SELECT * FROM lista_espera WHERE id_centro=? AND edad_espera=? AND motivo_baja=0 AND id_curso=?', 'sss', array($centro_actual, $c_anios, $curso_seleccionado)) or die(db_error());

$ninios_en_espera=db_num_rows($busca_ninios_lista);

$busca_ninios_incorporados=db_query_params('SELECT * FROM lista_espera WHERE id_centro=? AND edad_espera=? AND motivo_baja=1 AND id_curso=?', 'sss', array($centro_actual, $c_anios, $curso_seleccionado));

$ninios_incorporados=db_num_rows($busca_ninios_incorporados);

$busca_ninios_descartados=db_query_params('SELECT * FROM lista_espera WHERE id_centro=? AND edad_espera=? AND motivo_baja<>1 AND motivo_baja<>0 AND id_curso=?', 'sss', array($centro_actual, $c_anios, $curso_seleccionado));

$ninios_descartados=db_num_rows($busca_ninios_descartados);

?>
</tr>
<tr bgcolor="#efefef">
<td><font color="#000000" size=2 face="<?=$fuente_general?>">Niños de <?=$c_anios;?> a <?=$c_anios+1;?> años.</font></td>
<td align="right"><font color="#000000" size=3 face="<?=$fuente_general?>"><?=$ninios_en_espera?></font></td>
<td align="right"><font color="#000000" size=3 face="<?=$fuente_general?>"><?=$ninios_incorporados?></font></td>
<td align="right"><font color="#000000" size=3 face="<?=$fuente_general?>"><?=$ninios_descartados?></font></td>
<td width=25% align="center"><input type="button" value="Ver lista" onclick="self.location.href='lista_espera.php?edad_lista=<?=$c_anios?>&id_curso_lista=<?=$id_curso_actual?>'"></td>
</tr>
<?php 
}
?>
</table>
</td>
</tr>
</table></td>
</table>
<br>
<input type="button" value="Salir" onclick="self.location.href='/index.php'"> <?php  if ($idUsuario<>6297) { ?><input type="button" value="Añadir alumno" onclick="self.location.href='/alumnos/nuevo_alumno.php'"><?php  } ?>
<?php  require("../configuracion/parte_inferior.php"); ?>
