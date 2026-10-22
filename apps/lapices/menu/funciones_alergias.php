<?php

// Alergias y Alimentos no introducidos
$arrayKeysAlergias=array(
    "alergias_gluten" => 1,
    "alergias_crustaceos" => 2,
    "alergias_huevo" => 3,
    "alergias_pescado" => 4,
    "alergias_cacahuetes" => 5,
    "alergias_soja" => 6,
    "alergias_lacteos" => 7,
    "alergias_frutossecos" => 8,
    "alergias_apio" => 9,
    "alergias_mostaza" => 10,
    "alergias_sesamo" => 11,
    "alergias_sulfitos" => 12,
    "alergias_moluscos" => 13,
    "alergias_altramuces" => 14,
    /*
    "alergias_proternera",
    "alergias_pielmelocoton",
    "alergias_cerdo",
    "alergias_berenjena"
    */
);
$array_alergias=array(
    "alergias_gluten",
    "alergias_crustaceos",
    "alergias_huevo",
    "alergias_pescado",
    "alergias_cacahuetes",
    "alergias_soja",
    "alergias_lacteos",
    "alergias_frutossecos",
    "alergias_apio",
    "alergias_mostaza",
    "alergias_sesamo",
    "alergias_sulfitos",
    "alergias_moluscos",
    "alergias_altramuces",
    /*
    "alergias_proternera",
    "alergias_pielmelocoton",
    "alergias_cerdo",
    "alergias_berenjena"
    */
);
$nombres_alergias=array(
    "gluten",
    "crustaceos",
    "huevo",
    "pescado",
    "frutos secos sin cáscara",
    "soja",
    "leche y sus derivados",
    "frutos de cáscara",
    "apio",
    "mostaza",
    "sésamo",
    "sulfitos",
    "moluscos",
    "altramuces",
    /*
    "intolerancia a la proteina ternera",
    "intolerancia a la piel melocotón",
    "intolerancia al Cerdo",
    "Intolerancia al la Berenjena"
    */
);

function calculaCalorias($id_plato){

    // Calcula contenido calorico plato
    $total_kcal=0;
    $total_hc=0;
    $total_prot=0;
    $total_grasas=0;
    
    $nombresAlergias=array(
        "alergias_gluten" => "gluten",
        "alergias_crustaceos" => "crustaceos",
        "alergias_huevo" => "huevo",
        "alergias_pescado" => "pescado",
        "alergias_cacahuetes" => "frutos secos sin cáscara",
        "alergias_soja" => "soja",
        "alergias_lacteos" => "leche y sus derivados",
        "alergias_frutossecos" => "frutos de cáscara",
        "alergias_apio" => "apio",
        "alergias_mostaza" =>  "mostaza",
        "alergias_sesamo" => "sésamo",
        "alergias_sulfitos" => "sulfitos",
        "alergias_moluscos" => "moluscos",
        "alergias_altramuces" => "altramuces",
        "alergias_proternera" => "intolerancia a la proteina ternera",
        "alergias_pielmelocoton" => "intolerancia a la piel melocotón",
        "alergias_cerdo" => "intolerancia al Cerdo",
        "alergias_berenjena" => "Intolerancia al la Berenjena"
    );

    $arrayKeysAlergias=array(
        "alergias_gluten" => 1,
        "alergias_crustaceos" => 2,
        "alergias_huevo" => 3,
        "alergias_pescado" => 4,
        "alergias_cacahuetes" => 5,
        "alergias_soja" => 6,
        "alergias_lacteos" => 7,
        "alergias_frutossecos" => 8,
        "alergias_apio" => 9,
        "alergias_mostaza" => 10,
        "alergias_sesamo" => 11,
        "alergias_sulfitos" => 12,
        "alergias_moluscos" => 13,
        "alergias_altramuces" => 14,
        /*
        "alergias_proternera",
        "alergias_pielmelocoton",
        "alergias_cerdo",
        "alergias_berenjena"
        */
    );

    $alergenos = array(
        'alimentos_verdura' => 0,
        'alimentos_pollo' => 0,
        'alimentos_ternera' => 0,
        'alimentos_pescado' => 0,
        'alimentos_huevo' => 0,
        'alimentos_lentejas' => 0,
        'alimentos_lacteos' => 0,
        "alergias_gluten" => 0,
        "alergias_crustaceos" => 0,
        "alergias_huevo" => 0,
        "alergias_pescado" => 0,
        "alergias_cacahuetes" => 0,
        "alergias_soja" => 0,
        "alergias_lacteos" => 0,
        "alergias_frutossecos" => 0,
        "alergias_apio" => 0,
        "alergias_mostaza" => 0,
        "alergias_sesamo" => 0,
        "alergias_sulfitos" => 0,
        "alergias_moluscos" => 0,
        "alergias_altramuces" => 0,
        "alergias_proternera" => 0,
        "alergias_pielmelocoton" => 0,
        "alergias_cerdo" => 0,
        "alergias_berenjena" => 0
    );
    
    $selectIngredientes = "SELECT * FROM menu_ingredientes LEFT JOIN menu_alimentos ON menu_ingredientes.id_alimento = menu_alimentos.id_alimento WHERE menu_ingredientes.id_plato = ". $id_plato." ORDER BY nombre_alimento;";

    $busca_ingredientes = db_query($selectIngredientes);

    while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
        $total_kcal = $total_kcal + round((($datos_ingredientes["kcal_gramo"] * $datos_ingredientes["cantidad_racion"]) / 100), 2);
        $total_hc = $total_hc + round((($datos_ingredientes["hc_gramo"] * $datos_ingredientes["cantidad_racion"]) / 100), 2);
        $total_prot = $total_prot + round((($datos_ingredientes["prot_gramo"] * $datos_ingredientes["cantidad_racion"]) / 100), 2);
        $total_grasas = $total_grasas + round((($datos_ingredientes["grasas_gramo"] * $datos_ingredientes["cantidad_racion"]) / 100), 2);

        foreach ($alergenos as $keyAlergenos => $valorAlergenos) {
            if ($datos_ingredientes[$keyAlergenos] == 1) {
                $alergenos[$keyAlergenos] = 1;
            }
        }

    }

    $alergenosPlato = array();
    $nombresAlergenosPlato = array();

    $listaAlergenosPlato='';
    $listaNombresAlergenosPlato='';

    foreach($alergenos as $keyAlergenos => $valorAlergenos) {
        if ($valorAlergenos==1) {
            if ($arrayKeysAlergias[$keyAlergenos]) {
                $alergenosPlato[] = $arrayKeysAlergias[$keyAlergenos];
                $nombresAlergenosPlato[] = $arrayKeysAlergias[$keyAlergenos].". ".$nombresAlergias[$keyAlergenos];
            }
        }
    }

    if (count($alergenosPlato)>0) {
        $listaAlergenosPlato = implode("-",$alergenosPlato);
        $listaNombresAlergenosPlato = implode(",",$nombresAlergenosPlato);
    }


    $resultado=array(
        'kcal' => $total_kcal,
        'hc' => $total_hc,
        'prot' => $total_prot,
        'grasas' => $total_grasas,
        'alergenos' => $alergenos,
        'alergenos_lista' => $listaAlergenosPlato,
        'alergenos_nombre' => $listaNombresAlergenosPlato,
    );

    return $resultado;
}




?>