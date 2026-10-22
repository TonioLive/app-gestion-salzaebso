<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$semana=$_GET["semana"];
$id_menu=$_GET["id_menu"];
$plantilla=$_GET["plantilla"];

$borra_platos_semana=db_query("DELETE FROM menu_calendario_plantilla WHERE id_menu=$id_menu AND plantilla=$plantilla AND semana=$semana;");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
    self.opener.history.go(0);
    window.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

