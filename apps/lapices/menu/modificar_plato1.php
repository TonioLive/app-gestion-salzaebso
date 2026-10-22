<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_POST["id_plato"];
$nombre_plato=$_POST["nombre_plato"];
if (strlen($nombre_plato)!=0) {
        $nombre_plato=urlencode($nombre_plato);
        }
$grupo=$_POST["grupo"];
$tipo_plato=$_POST["tipo_plato"];
$edad_ini_plato=$_POST["edad_ini_plato"];
$edad_fin_plato=$_POST["edad_fin_plato"];
$duplicar=$_POST["duplicar"];

$nombre_recipiente=$_POST["nombre_recipiente"];
if (strlen($nombre_recipiente)!=0) {
        $nombre_recipiente=urlencode($nombre_recipiente);
        }
$raciones=$_POST["raciones"];
$instrucciones_preparacion=$_POST["instrucciones_preparacion"];
if (strlen($instrucciones_preparacion)!=0) {
        $instrucciones_preparacion=urlencode($instrucciones_preparacion);
        }
$instrucciones_servir=$_POST["instrucciones_servir"];
if (strlen($instrucciones_servir)!=0) {
        $instrucciones_servir=urlencode($instrucciones_servir);
        }
$instrucciones_conservacion=$_POST["instrucciones_conservacion"];
if (strlen($instrucciones_conservacion)!=0) {
        $instrucciones_conservacion=urlencode($instrucciones_conservacion);
        }
$instrucciones_transporte=$_POST["instrucciones_transporte"];
if (strlen($instrucciones_transporte)!=0) {
        $instrucciones_transporte=urlencode($instrucciones_transporte);
        }       
        
        
  /*      
  $gluten=$_POST["gluten"];
  $leche=$_POST["leche"];
  $huevos=$_POST["huevos"];
  $crustaceos=$_POST["crustaceos"];
  $moluscos=$_POST["moluscos"];
  $pescados=$_POST["pescados"];
  $cacahuete=$_POST["cacahuete"];
  $frutos_secos=$_POST["frutos_secos"];
  $mostaza_sesamo=$_POST["mostaza_sesamo"];
  $sulfitos=$_POST["sulfitos"];
  $soja=$_POST["soja"];
  $apio=$_POST["apio"];
  */      
  
        if ($id_plato==0) {
        $inserta_plato=db_query("INSERT INTO menu_platos (nombre_plato) VALUES ('$nombre_plato')");
        $id_plato=db_insert_id();
        
        if ($duplicar!=0) {
                // Busca ingredientes
                $busca_ingredientes_plato=db_query("SELECT * FROM menu_ingredientes WHERE id_plato=$duplicar") or die(db_error());

                while($ingredientes_plato=db_fetch_array($busca_ingredientes_plato)) {
                        $id_alimento=$ingredientes_plato["id_alimento"];
                        $cantidad_racion=$ingredientes_plato["cantidad_racion"];

                        $inserta_plato=db_query("INSERT INTO menu_ingredientes (id_plato,id_alimento,cantidad_racion) VALUES ('$id_plato','$id_alimento','$cantidad_racion')") or die(db_error());
                        }
                }
        
        $nuevo=true;
        } else {
        $nuevo=false;
        }
        
        // $nuevo_articulo = mysql_query("UPDATE menu_platos SET tipo_plato='$tipo_plato', nombre_plato='$nombre_plato', edad_ini_plato='$edad_ini_plato', edad_fin_plato='$edad_fin_plato', nombre_recipiente='$nombre_recipiente', raciones='$raciones', instrucciones_preparacion='$instrucciones_preparacion', instrucciones_servir='$instrucciones_servir', instrucciones_conservacion='$instrucciones_conservacion', instrucciones_transporte='$instrucciones_transporte', gluten='$gluten', leche='$leche', huevos='$huevos', crustaceos='$crustaceos', moluscos='$moluscos', pescados='$pescados', cacahuete='$cacahuete', frutos_secos='$frutos_secos', mostaza_sesamo='$mostaza_sesamo', sulfitos='$sulfitos', soja='$soja', apio='$apio' WHERE id_plato=$id_plato") or die (mysql_error());
        
        $nuevo_articulo = db_query("UPDATE menu_platos SET grupo='$grupo', tipo_plato='$tipo_plato', nombre_plato='$nombre_plato', edad_ini_plato='$edad_ini_plato', edad_fin_plato='$edad_fin_plato', nombre_recipiente='$nombre_recipiente', raciones='$raciones', instrucciones_preparacion='$instrucciones_preparacion', instrucciones_servir='$instrucciones_servir', instrucciones_conservacion='$instrucciones_conservacion', instrucciones_transporte='$instrucciones_transporte' WHERE id_plato=$id_plato") or die (db_error());

if ($nuevo==false) {
        header("Location:gestion_platos.php?grupo=$grupo");
        } else {
        header("Location:composicion_plato.php?id_plato=$id_plato");
        }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

