<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");
 require("../../configuracion/autentifica_admin.php");
 require_once __DIR__ . '/acceso_usuario_lib.php';

$id_usuario=app_request_int($_GET, 'cod', 0);

$modificado=app_request_value($_GET, 'Modificado', '');

try { $rsslc=gestion_acceso_usuario($id_usuario); }
catch (DomainException $error) { http_response_code(403); exit(app_escape($error->getMessage())); }
if ($rsslc) {

$nombre_cand=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
$dni_cand=$rsslc["DNI"];
$id_tabla_usu=$rsslc["id_usuario"];

?>
<html>
<head>
<title>Configuración de usuarios</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
<p align="center">
<font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><br>
</p>
<p align="center"><big><strong>Configuración de acceso: <?php  echo app_escape($nombre_cand); ?>
</strong></big> </p>
<?php  
if ($rsslc["nivel_acceso"]<4 || $nivel_acceso==4) {
?>
<form method="post" action="modif_p_cand.php?Cod=<?= $id_usuario; ?>" name="datospers">
<input type="hidden" name="id_tabla_usu" value="<?= $id_tabla_usu; ?>">
<input type="hidden" name="_csrf" value="<?= app_escape(app_csrf_token()); ?>">
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
  <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small>&nbsp;<?php  echo app_escape($rsslc["usuario"]); ?><input type="hidden" name="usuario" value="<?php  echo app_escape($rsslc["usuario"]); ?>"></font></td>
<?php 
} else {
?>
  <td>&nbsp;<input type="text" name="usuario" size="25" maxlength="50" autocomplete="off" value="<?php  echo app_escape($rsslc["usuario"]); ?>"></td>
<?php 
}
?>
    </tr>
    <tr>
      <td></td>
      <td><label for="nueva-password"><strong>Nueva contraseña:</strong></label></td>
      <td><input type="password" id="nueva-password" name="password" size="25" autocomplete="new-password" value=""><p>Déjala vacía para conservar la actual.</p>
      <?php if ((int) $nivel_acceso === 4) { ?><p>Las contraseñas nuevas que asignes aquí tendrán una copia cifrada para consultarlas después desde «Mostrar contraseñas» en el listado de usuarios.</p><?php } ?>
      </td>
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
      </select></font></td>
    </tr>
    <tr>
      <td></td>
      <td><font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente; ?>"><small><b>Centro: </b></font></td>
      <td><font color="#FFFFFF">&nbsp;<select name="id_centro_usuario" size=1>
        <option value="">Sin centro asociado</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=(int) $centros["id_centro"];?>"<?php  if ($rsslc["id_centro"]==$centros["id_centro"]) { print " selected"; } ?>><?=app_escape($centros["nombre_centro"]);?> (<?=app_escape($centros["poblacion_centro"]);?>)</option>
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
      <input type="checkbox" name="zona_corregir_pruebas" value=1<?php  if ($rsslc["zona_corregir_pruebas"]==1) { print " checked"; } ?>>&nbsp; Cambiar los resultados de las pruebas<br>
            <input type="checkbox" name="zona_actividades" value=1<?php  if ($rsslc["zona_actividades"]==1) { print " checked"; } ?>>&nbsp; Publicar actividades para todos los usuarios<br><br>
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

