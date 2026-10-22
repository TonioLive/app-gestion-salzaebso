<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio_semana=$_GET["inicio_semana"];
$id_menu=$_GET["id_menu"];

$inicio_semana=$inicio_semana+7200;
$incremento=86400;

for ($contador_repeticiones=0;$contador_repeticiones<7;$contador_repeticiones++) {

$borra_platos_dia=db_query("DELETE FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_semana)."'");

$inicio_semana=$inicio_semana+$incremento;

}

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

