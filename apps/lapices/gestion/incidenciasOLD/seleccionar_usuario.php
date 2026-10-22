<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$inicial=$_GET["i"];
if (strlen($inicial)==0) {
        $inicial="A";
        }
                
$filtros_escuelas_cursos="";

$idc=$_GET["idc"];
if (strlen($idc)!=0) {
	$filtros_escuelas_cursos.=" AND alumnos.id_curso=".$idc;
	$busca_c=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$idc");
	$datos_c=db_fetch_array($busca_c);
	$nombre_c=" Curso: ".$datos_c["nombre"];
	}
$ide=$_GET["ide"];
if (strlen($ide)!=0) {
	$filtros_escuelas_cursos.=" AND alumnos.id_centro=".$ide;
	$busca_e=db_query("SELECT * FROM centros WHERE id_centro=$ide");
	$datos_e=db_fetch_array($busca_e);
	$nombre_e=" Centro: ".$datos_e["nombre_centro"];
	}
if ((strlen($ide)!=0) and (strlen($idc)!=0)) {
	$ida=$_GET["ida"];
	if (strlen($ida)!=0) {
		$filtros_escuelas_cursos.=" AND alumnos.aula=".$ida;
		$busca_a=db_query("SELECT * FROM centro_aulas WHERE id_aula=$ida");
		$datos_a=db_fetch_array($busca_a);
		$nombre_a=" Aula: ".$datos_a["nombre"];
		}
	} else {
	$ida="";
	$nombre_a="";
	}
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function seleccionar(id_usuario_sel, nombre_usuario_Sel) {
        top.window.opener.document.formulario1.id_usuario_incidencia.value=id_usuario_sel;
        top.window.opener.document.formulario1.nombre_usuario_incidencia.value=nombre_usuario_Sel;
        window.close();
        }

//-->
</SCRIPT>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><font size=2 face="Arial"><b>Selecciona el el alumno</b><br />
    <?=$nombre_c.$nombre_e.$nombre_a?><br>
	<form name="centros" method="GET" action="<?=$path_index;?>">
        <select name="ide" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <option value="">Selecciona el centro</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php  if ($ide==$centros["id_centro"]) { print " selected"; } ?>><?=$centros["nombre_centro"];?></option>
        <?php 
        }
        ?>
        </select> <select name="idc" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <option value="">Selecciona el curso</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_curso"];?>"<?php  if ($idc==$centros["id_curso"]) { print " selected"; } ?>><?=$centros["nombre"];?></option>
        <?php 
        }
        ?>
        </select><?php 
        if ((strlen($ide)!=0) and (strlen($idc)!=0)) {
        ?> <select name="ida" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <option value="">Selecciona el aula</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centro_aulas WHERE id_curso=$idc AND id_centro=$ide AND tipo=0 ORDER BY edad_minima;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_aula"];?>"<?php  if ($ida==$centros["id_aula"]) { print " selected"; } ?>><?=$centros["nombre"];?></option>
        <?php 
        }
        ?>
        </select>
        <?php 
        	}
        ?>
	</form></font></td>
  </tr>
  <?php 
  if ($ida!=0) {
$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.tipo_inscripcion=1".$filtros_escuelas_cursos." GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
<?php 
  } else {
  ?>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<?php 
for ($letra=65;$letra<=90;$letra++) {
        // Comprueba si hay alumnos con esa letra
        $busca_alumnos_en=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.tipo_inscripcion=1 AND usuarios.Apellido1 like '".chr($letra)."%'".$filtros_escuelas_cursos." GROUP BY usuarios.id_usuario");
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "<font color='#cecece'>";
                } else {
                echo "<a href='".$_SERVER["PHP_SELF"]."?ida=$ida&idc=$idc&ide=$ide&i=".chr($letra)."'><font color='#000000'><b>";
                }
        echo chr($letra);
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "</font>";
                } else {
                echo "</b></font></a>";
                }
        if ($letra<90) { echo " "; }
        }
?>
</font></td>
</tr>
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.tipo_inscripcion=1 AND usuarios.Apellido1 like '".$inicial."%'".$filtros_escuelas_cursos." GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=2><font size=2 face="<?= $fuente_general; ?>"><i>Personas cuyo apellido comienza por la letra <b><?=$inicial?></b></i><br>Para seleccionar una persona, haz clic sobre su nombre.</font></td>
        </tr>
<?php 
}
if (db_num_rows($busca_alumnos)!=0) {
?>        
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        </tr>
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        ?>
        <tr>
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:seleccionar(<?=$listado_alumnos["id_usuario"];?>,'<?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?>');"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ninguna persona cuyo apellido comience por la letra <?=$inicial;?><br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
</body>
</html>

