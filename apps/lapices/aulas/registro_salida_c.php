<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_registro=$_GET["id_registro"];

$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_servicio_consumido=$id_registro");

$datos_registro=db_fetch_array($busca_registro_entrada);

$id_aula=$datos_registro["id_servicio"];
$id_alumno=$datos_registro["id_alumno"];
$fecha=strtotime($datos_registro["fecha"]);
$hora_entrada=$datos_registro["hora_entrada_c"];
if ($hora_entrada=="") {
        $hora_entrada=date("H:i:s");
        } else {
        $hora_entrada=date("H:i:s",strtotime($datos_registro["hora_entrada_c"]));
        }
$hora_salida=$datos_registro["hora_salida_c"];
if ($hora_salida=="") {
        $hora_salida=date("H:i:s");
        } else {
        $hora_salida=date("H:i:s",strtotime($datos_registro["hora_salida_c"]));
        }
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$observaciones=$datos_registro["observaciones"];
$comedor=$datos_registro["tipo_menu"];
$tipo_menu_servido=$datos_registro["tipo_menu_comedor"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

$busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
$datos_alumno=db_fetch_array($busca_alumno);

switch($tipo_menu_servido) {
                case 1:
                $nombre_menu_servido="Pequeños";
                break;
                case 2:
                $nombre_menu_servido="Mayores";
                break;
                case 3:
                $nombre_menu_servido="Colegial";
                break;
                case 4:
                $nombre_menu_servido="Toma";
                break;
                case 5:
                $nombre_menu_servido="Bebé";
                break;
                }

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
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Registro de Comidas</b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br><i><?=$datos_aula["nombre"];?></i> - Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><br>
  Alumno: <b><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></b><br>
  Tipo de menú servido: <b><?=$nombre_menu_servido;?></b><br><br>
  Hora entrada comedor: <b><?=$hora_entrada;?></b>, Hora de salida comedor: <b><?=$hora_salida;?></b><br>
  <form method=POST action="registro_salida_c1.php" name="registro">
  <input type="hidden" name="id_registro" value="<?=$id_registro?>">
  <input type="hidden" name="hora" value="<?=time()?>">
  
        <table width="90%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#ededed">
        <td colspan=5 align="center"><font size=3 face="<?= $fuente_general; ?>"><b>Alimentación</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Almuerzo</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>1er. Plato</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>2º Plato</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Postre</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Merienda</b></font></td>
        </tr><tr>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="almuerzo">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["almuerzo"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["almuerzo"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["almuerzo"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="comida1">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["comida1"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["comida1"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["comida1"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="comida2">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["comida2"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["comida2"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["comida2"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="postre">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["postre"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["postre"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["postre"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="merienda">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["merienda"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["merienda"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["merienda"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        </tr>
        </table>
        
        <table width="90%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#ededed">
        <td colspan=5 align="center"><font size=3 face="<?= $fuente_general; ?>"><b>Deposiciones/Siesta</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>1</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>2</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>3</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Siesta</b></font></td>
        </tr><tr>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="deposicion1">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["deposicion1"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["deposicion1"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["deposicion1"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="deposicion2">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["deposicion2"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["deposicion2"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["deposicion2"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="deposicion3">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["deposicion3"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["deposicion3"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["deposicion3"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="siesta">
        <option value=""></option>
        <option value="B"<?php if ($datos_registro["siesta"]=="B") { print " selected"; } ?>>B</option>
        <option value="R"<?php if ($datos_registro["siesta"]=="R") { print " selected"; } ?>>R</option>
        <option value="M"<?php if ($datos_registro["siesta"]=="M") { print " selected"; } ?>>M</option>
        </select></font></td>
        </tr>
        </table>
        <br>
        <input type="submit" value="Aceptar"> <input type="button" onclick="window.close()" value="Cancelar"><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

