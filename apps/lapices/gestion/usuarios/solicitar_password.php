<?php 
require("../../configuracion/config.php");
?>
<html>

<head>
<title>Formulario de Inscrición</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head><body class="legacy-admin" text=White>

<body bgcolor="<?php  echo $color_lat; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background=<?php  echo $back_image; ?> bgproperties="fixed">
<img src="<?php  echo $url_web; ?>/imagen/logo1.gif">
                                          <div align="center"><center>
                                          <font face="<?php  echo $fuente_general; ?>" size=3>¿Has olvidado tu contraseña?</font><br><br>
                                          <font face="<?php  echo $fuente_general; ?>" size=2>Para que podamos volver a enviarte tu contraseña, debes introducir tu DNI (sin letra de NIF) y pulsar el botón <b>Enviar</b>.</font>
                                          <br><br>
                                          <table width="100%" cellpadding=0 cellspacing=1 border=0>
                                          <form action="recordar_password.php" method="POST" id=form1 name=form1>
                                          <tr bgcolor="#dedede">
                                          <td align="right" valign="top" width="35%"><font face="<?php  echo $fuente_general; ?>" size=2>DNI:</font></td>
                                          <td align="left" valign="top" width="65%"><font face="<?php  echo $fuente_general; ?>" size=2><input type="text" name="dni" style="font-size: 10px"></font></td>
                                          </tr>
                                          <tr bgcolor="#dedede">
                                          <td align="center" valign="top" colspan="2"><font face="<?php  echo $fuente_general; ?>" size=2>Si tiene documentación de extranjero, introduzca el número completo, incluidas las letras.</font></td>
                                          </tr>
                                          </table>
                                          <br>
                                          <input type="submit" value="Enviar" style="font-size: 10px" id=submit1 name=submit1> <input type="button" value="Cancelar" style="font-size: 10px" onclick="window.close()" id=button1 name=button1>
                                          </form>
                                          </center></div><div align="center">
 <font face="<?php  echo $fuente_preins; ?>" size="1"><b><a href="javascript:window.close()">Cerrar ventana</a><b></font>
 </small>
</div>
</body>
</html>

