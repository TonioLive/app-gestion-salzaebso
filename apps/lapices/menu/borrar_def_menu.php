<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_menu_borrado=$_GET["id_menu_borrado"];

$busca_datos_menu=db_query("SELECT * FROM menu_menus_borrados WHERE id_menu_borrado=$id_menu_borrado");

if (db_num_rows($busca_datos_menu)!=0) {
        $datos_menu=db_fetch_array($busca_datos_menu);
        $id_menu_original=$datos_menu["id_menu"];
        
        // Borra del calendarios
        $borra_calendario=db_query("DELETE FROM menu_calendario WHERE id_menu=$id_menu_original");


        // Borra de la tabla de menús
        $borra_menu=db_query("DELETE FROM menu_menus_borrados WHERE id_menu_borrado=$id_menu_borrado");
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

