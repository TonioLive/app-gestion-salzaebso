<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

        $id_tipo_punto=$_POST["id_tipo_punto"];

        $descripcion=$_POST["descripcion"];
        $valor=$_POST["valor"];
        $id_curso_pregunta=$_POST["id_curso"];
        $desactivada=$_POST["desactivada"];

        // Introduce la nueva familia
        if ($id_tipo_punto==0) {
                $inserta_nueva=db_query("INSERT INTO lista_espera_cuestionarios (desactivada) VALUES ('0');");
                $id_tipo_punto=db_insert_id();
                }
        
        $modifica = db_query_params('UPDATE lista_espera_cuestionarios SET descripcion=?, valor=?, id_curso=?, desactivada=? WHERE id_tipo_punto=?', 'sssss', array((string)($descripcion), (string)($valor), (string)($id_curso_pregunta), (string)($desactivada), $id_tipo_punto)) or die (db_error());

        
        header("Location: edicion.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

