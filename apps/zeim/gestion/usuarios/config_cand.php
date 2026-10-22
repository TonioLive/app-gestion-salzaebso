<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$id_usuario=(int)($_GET["cod"] ?? 0);

$modificado=$_GET["Modificado"] ?? "";

$query="SELECT * FROM usuarios WHERE id_usuario=".$id_usuario;

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?','i',array($id_usuario)) or die(db_error());

if (db_num_rows($consulta_usuario) != 0) {

$rsslc=db_fetch_array($consulta_usuario);

$nombre_cand=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
$dni_cand=$rsslc["DNI"];
$id_tabla_usu=$rsslc["id_usuario"];

?>
<html>
<head>
<title>Configuración de usuarios</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
<p align="center">
<font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><br>
</p>
<p align="center"><big><strong>Configuración de contraseñas del usuario: <?php  echo htmlspecialchars((string)$nombre_cand, ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1"); ?>
</strong></big> </p>
<?php  
if ($rsslc["nivel_acceso"]<4 || $nivel_acceso==4) {
?>
<form method="post" accept-charset="UTF-8" action="modif_p_cand.php?Cod=<?= $id_usuario; ?>" name="datospers">
<input type="hidden" name="id_tabla_usu" value="<?= $id_tabla_usu; ?>">
<?php  
} else {
?>
<p><br>
No puedes modificar la contraseña y el tipo de acceso de un administrador del sistema.
<br>
</p>
<?php 
}
?>
  <div align="center"><center><table border="0" cellspacing="0" cellpadding="0" class="tablafondo">
    <tr>
      <td bgcolor="#dedede" colspan=3><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente_lat; ?>"><small><b>Configuración de acceso</td>
    </tr>
    <tr>
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Nombre de usuario: </b></font>
      </td>
<?php 
if ($rsslc["nivel_acceso"]>3 && $nivel_acceso<4) {
?>
  <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small>&nbsp;<?php  echo htmlspecialchars((string)$rsslc["usuario"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1"); ?><input type="hidden" name="usuario" value="<?php  echo htmlspecialchars((string)$rsslc["usuario"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1"); ?>"></font></td>
<?php 
} else {
?>
  <td>&nbsp;<input type="text" name="usuario" size="25" value="<?php  echo htmlspecialchars((string)$rsslc["usuario"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1"); ?>"></td>
<?php 
}
?>
    </tr>
    <tr>
      <td></td>
      <?php  
// Existing credentials are never rendered into the form.
?>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Password: </b></font></td>
      <td><font color="#FFFFFF">&nbsp;<input type="password" name="password" size="25" value="" autocomplete="new-password" minlength="8" maxlength="72" placeholder="Dejar vacío para conservar"> </font></td>
    </tr>
    <tr>
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Nivel de acceso: </b></font></td>
      <td><font color="#FFFFFF">&nbsp;<select name="nivel_acceso" size="1" <?php if ($nivel_acceso<4) { echo (" disabled"); }?>>
      <option value="2"<?php  if ($rsslc["nivel_acceso"]==2) { print " selected";} ?>>Educador/a</option>
      <option value="3"<?php  if ($rsslc["nivel_acceso"]==3) { print " selected";} ?>>Coordinador/a de Centro</option>
      <option value="4"<?php  if ($rsslc["nivel_acceso"]==4){ print " selected";} ?>>Administrador de Centros</option>
      <option value="10"<?php  if ($rsslc["nivel_acceso"]==10){ print " selected";} ?>>Cocina</option>
      <option value="11"<?php  if ($rsslc["nivel_acceso"]==11){ print " selected";} ?>>Personal no docente</option>
      </font></td>
    </tr>
    <tr>
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Centro: </b></font></td>
      <td><font color="#FFFFFF">&nbsp;<select name="id_centro_usuario" size=1>
        <option value="">Sin centro asociado</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php  if ($rsslc["id_centro"]==$centros["id_centro"]) { print " selected"; } ?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select></font></td>
    </tr>
<?php 
if ($nivel_acceso==4) {
?>
    <tr>
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Acceso a todos los centros: </b></font></td>
      <td><font color="#FFFFFF">&nbsp;<select name="multicentro" size=1>
        <option value="0"<?php  if ($rsslc["multicentro"]==0) { print " selected"; } ?>>No</option>
        <option value="1"<?php  if ($rsslc["multicentro"]==1) { print " selected"; } ?>>Si</option>
        </select></font></td>
    </tr>
    <tr valign="top">
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>" size=2><b>Contenidos permitidos *: </b></font></td>
      <td><font color="#000000" face="<?php  echo $fuente_general; ?>" size=1>
      <input type="checkbox" name="zona_alumnos" value=1<?php  if ($rsslc["zona_alumnos"]==1) { print " checked"; } ?>>&nbsp; Alumnos<br>
      <input type="checkbox" name="zona_aulas" value=1<?php  if ($rsslc["zona_aulas"]==1) { print " checked"; } ?>>&nbsp; Aulas<br>
      <input type="checkbox" name="zona_facturacion" value=1<?php  if ($rsslc["zona_facturacion"]==1) { print " checked"; } ?>>&nbsp; Facturación<br>
      <input type="checkbox" name="zona_comedor" value=1<?php  if ($rsslc["zona_comedor"]==1) { print " checked"; } ?>>&nbsp; Comedor<br>
      <input type="checkbox" name="zona_almacen" value=1<?php  if ($rsslc["zona_almacen"]==1) { print " checked"; } ?>>&nbsp; Almacén<br>
      <input type="checkbox" name="zona_incidencias" value=1<?php  if ($rsslc["zona_incidencias"]==1) { print " checked"; } ?>>&nbsp; Incicencias<br>
      <input type="checkbox" name="zona_personal" value=1<?php  if ($rsslc["zona_personal"]==1) { print " checked"; } ?>>&nbsp; Personal<br>
      <input type="checkbox" name="zona_pedidos" value=1<?php  if ($rsslc["zona_pedidos"]==1) { print " checked"; } ?>>&nbsp; Pedidos<br>
      <input type="checkbox" name="zona_inferior" value=1<?php  if ($rsslc["zona_inferior"]==1) { print " checked"; } ?>>&nbsp; Contraseñas y cámaras<br>
      <input type="checkbox" name="zona_mensajes" value=1<?php  if ($rsslc["zona_mensajes"]==1) { print " checked"; } ?>>&nbsp; Mensajes<br>
      <input type="checkbox" name="zona_compras" value=1<?php  if ($rsslc["zona_compras"]==1) { print " checked"; } ?>>&nbsp; Compras material<br>
      <input type="checkbox" name="zona_gabinete" value=1<?php  if ($rsslc["zona_gabinete"]==1) { print " checked"; } ?>>&nbsp; Gabinete psicopedagógico<br>
      <input type="checkbox" name="zona_corregir_pruebas" value=1<?php  if ($rsslc["zona_corregir_pruebas"]==1) { print " checked"; } ?>>&nbsp; Cambiar los resultados de las pruebas<br><br>
      * Solo es necesiario indicarlo en los usuarios de tipo "Coordinador de centro".
      </font></td>
    </tr>
<?php 
}
?>
  </table>
  </center></div><div align="center"><center><p>&nbsp;</p>
  <input type="button" style="font-size: 10" value="Volver a datos personales" name="B2" onClick="window.location.href='datos_cand.php?dato=s&al=<?php  echo (mt_rand(0,10000000)/10000000); ?>&id_usu=<?php  echo $id_usuario; ?>'">
<?php 
if ($rsslc["nivel_acceso"]<4 || $nivel_acceso==4) {
?>
    <input type="submit" style="font-size: 10" name="submit" value="Guardar los cambios">
<?php 
}
?>

  </center></div>
<?php  
if ($rsslc["nivel_acceso"]<4 || $nivel_acceso==4) {
?>
</form>
<?php  
} 
?>
<!--
<form name="Recordatorio_password" action="<?php  echo $url_web; ?>/usuarios/recordar_password.php" method="POST">
<input type="hidden" value="<?php  echo $dni_cand; ?>" name="dni">
<input type="submit" value="Enviar recordatorio de contraseña" style="font-size: 10px">
</form>
-->
<div align="center"><center>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%"></td>
  </tr>
</table>
</center></div>
<?php  
} else {
?>
<p align="center"><br><br>
<font size=2 face="$fuente_general">El usuario no existe<br><br>
</font>
<input type="button" value="Cerrar ventana" onclick="window.close()">
</p>
<?php 
}
db_close();
?>
</div>
</body>
</html>

