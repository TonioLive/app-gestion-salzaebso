<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$dia=$_GET["dia"];
$curso=$_GET["curso"];
$centro=$_GET["centro"];

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<p align="center"><br><font size=2><b>Cambiar estado del día <?=date("d/m/Y",strtotime($dia));?></b><br><br>
Cambiar en las escuelas:
<?php 

?>
</font>
</p>
</body>
</html>

