<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio=$_GET["dia"];

if (strlen($inicio)==0) {
    $inicio=time();  
    $fecha_time_stamp=time();
} else {
    $fecha_time_stamp=$inicio;
}
 
$fecha_albaran=date("Y-m-d",$fecha_time_stamp);

$busca_alabaran=db_query("SELECT * FROM cocina_albaranes_entrega WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."';");
             
$datos_albaran=db_fetch_array($busca_alabaran);

$temperatura_salida=app_safe_unserialize($datos_albaran["temperaturas_salida"]);

foreach ($temperatura_salida as $clave => $valor) {

    // Busca plato
    $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$clave ORDER BY nombre_plato");
    if (db_num_rows($busca_comida)!=0) {
        $datos_comida=db_fetch_array($busca_comida);

        $id_plato=$datos_comida["id_plato"];
        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");

        $alergenos="";
        $ingredientes="";
        $lista_alergenos=array();
        
        while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
            $ingredientes.=app_url_text_utf8($datos_ingredientes["nombre_alimento"]).", ";

            $contadorAlergias=0;
            foreach ($array_alergias as $alergia) {
                if (($datos_ingredientes[$alergia]==1) and (in_array($lista_alergenos, $array_nombres_alergias[$contadorAlergias])==false))  {
                    $alergiasYa[$alergia]=1;
                    $lista_alergenos[$alergia]=$array_nombres_alergias[$contadorAlergias];
                }
                $contadorAlergias++;
            }
        }

        if ($ingredientes!="") {
            $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
        }

        $alergenos = implode(", ", $lista_alergenos);        

        if (strlen($alergenos)==0) {
            $alergenos = "No contiene alergenos conocidos";
        }

        $nombres_platos[$comidaActual]=app_url_text_utf8($datos_comida["nombre_plato"]);
        ?>
        <big><big><?=app_url_text_utf8($datos_comida["nombre_plato"])?></big></big>
        <br /><br />
        <big><b>Ingredientes:</b> <?=$ingredientes?></big><br />
        <b>Tª salida</b> <?=$valor?> ºC
        <br><br>
        <b>Alérgenos: </b><?=$alergenos?><br /><br />
        <b>Conservación:</b> <?=ucfirst(app_url_text_utf8($datos_comida["instrucciones_conservacion"]))?><br />
        <b>Fecha de caducidad / Lote</b> <?=date("d/m/Y",$fecha_time_stamp);?>
        <hr>
        <?php
    }
            
            
}        

?>