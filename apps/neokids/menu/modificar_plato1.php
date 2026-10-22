<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_POST["id_plato"];
$nombre_plato=$_POST["nombre_plato"];
if (strlen($nombre_plato)!=0) {
        $nombre_plato=urlencode($nombre_plato);
        }
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
  
        if ($id_plato==0) {
        $inserta_plato=db_query_params('INSERT INTO menu_platos (nombre_plato) VALUES (?)', 's', array((string)($nombre_plato)));
        $id_plato=db_insert_id();
        
        if ($duplicar!=0) {
                // Busca ingredientes
                $busca_ingredientes_plato=db_query("SELECT * FROM menu_ingredientes WHERE id_plato=$duplicar") or die(db_error());

                while($ingredientes_plato=db_fetch_array($busca_ingredientes_plato)) {
                        $id_alimento=$ingredientes_plato["id_alimento"];
                        $cantidad_racion=$ingredientes_plato["cantidad_racion"];

                        $inserta_plato=db_query_params('INSERT INTO menu_ingredientes (id_plato,id_alimento,cantidad_racion) VALUES (?,?,?)', 'sss', array((string)($id_plato), (string)($id_alimento), (string)($cantidad_racion))) or die(db_error());
                        }
                }
        
        $nuevo=true;
        } else {
        $nuevo=false;
        }
        
        $nuevo_articulo = db_query_params('UPDATE menu_platos SET tipo_plato=?, nombre_plato=?, edad_ini_plato=?, edad_fin_plato=?, nombre_recipiente=?, raciones=?, instrucciones_preparacion=?, instrucciones_servir=?, instrucciones_conservacion=?, instrucciones_transporte=?, gluten=?, leche=?, huevos=?, crustaceos=?, moluscos=?, pescados=?, cacahuete=?, frutos_secos=?, mostaza_sesamo=?, sulfitos=?, soja=?, apio=? WHERE id_plato=?', 'sssssssssssssssssssssss', array((string)($tipo_plato), (string)($nombre_plato), (string)($edad_ini_plato), (string)($edad_fin_plato), (string)($nombre_recipiente), (string)($raciones), (string)($instrucciones_preparacion), (string)($instrucciones_servir), (string)($instrucciones_conservacion), (string)($instrucciones_transporte), (string)($gluten), (string)($leche), (string)($huevos), (string)($crustaceos), (string)($moluscos), (string)($pescados), (string)($cacahuete), (string)($frutos_secos), (string)($mostaza_sesamo), (string)($sulfitos), (string)($soja), (string)($apio), $id_plato)) or die (db_error());

if ($nuevo==false) {
        header("Location:gestion_platos.php");
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

