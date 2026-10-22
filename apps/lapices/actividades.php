<?php  require("configuracion/config.php"); ?>
<html>
<head>
<?php  require("configuracion/estilos/estilo1.php"); ?>
</style>
<title><?php  echo $titulo_web; ?></title>
<?php  require("configuracion/scripts/scripts.php"); ?>
</head>

<body bgcolor="<?php  echo $backgrnd; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="100%" valign="top" align="left">
<?php  require("inc/agenda/agenda.php"); ?>
</td>
</tr>
</TABLE>
</body>
</html>
