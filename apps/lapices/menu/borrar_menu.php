<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_menu=$_GET["id_menu"];

$busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu");

if (db_num_rows($busca_datos_menu)!=0) {
        $datos_menu=db_fetch_array($busca_datos_menu);
        
        // Copia en la tabla de menús borrados
        $inserta_menu=db_query("INSERT INTO menu_menus_borrados (id_menu,nombre_menu,id_centro,id_curso,edad_ini_menu,edad_fin_menu,regimen) VALUES ('".$datos_menu["id_menu"]."','".$datos_menu["nombre_menu"]."','".$datos_menu["id_centro"]."','".$datos_menu["id_curso"]."','".$datos_menu["edad_ini_menu"]."','".$datos_menu["edad_fin_menu"]."','".$datos_menu["regimen"]."');");


        // Borra de la tabla de menús
        $borra_menu=db_query("DELETE FROM menu_menus WHERE id_menu=$id_menu");
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

