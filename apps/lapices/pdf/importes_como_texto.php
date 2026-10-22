<?php 
function a_texto($numero_a_convertir) {

// Importe en texto

$unidades[1]="UN";
$unidades[2]="DOS";
$unidades[3]="TRES";
$unidades[4]="CUATRO";
$unidades[5]="CINCO";
$unidades[6]="SEIS";
$unidades[7]="SIETE";
$unidades[8]="OCHO";
$unidades[9]="NUEVE";

$decenas[1]="DIEZ";
$decenas[2]="VEINTE";
$decenas[3]="TREINTA";
$decenas[4]="CUARENTA";
$decenas[5]="CINCUENTA";
$decenas[6]="SESENTA";
$decenas[7]="SETENTA";
$decenas[8]="OCHENTA";
$decenas[9]="NOVENTA";

$oncenas[1]="ONCE";
$oncenas[2]="DOCE";
$oncenas[3]="TRECE";
$oncenas[4]="CATORCE";
$oncenas[5]="QUINCE";
$oncenas[6]="DIECISEIS";
$oncenas[7]="DIECISIETE";
$oncenas[8]="DIECIOCHO";
$oncenas[9]="DIECINUEVE";

$veintenas[1]="VEINTIUN";
$veintenas[2]="VEINTIDOS";
$veintenas[3]="VEINTITRES";
$veintenas[4]="VEINTICUATRO";
$veintenas[5]="VEINTICINCO";
$veintenas[6]="VEINTISEIS";
$veintenas[7]="VEINTISIETE";
$veintenas[8]="VEINTIOCHO";
$veintenas[9]="VEINTINUEVE";

$centenas[1]="CIENTO";
$centenas[2]="DOSCIENTOS";
$centenas[3]="TRESCIENTOS";
$centenas[4]="CUATROCIENTOS";
$centenas[5]="QUINIENTOS";
$centenas[6]="SEISCIENTOS";
$centenas[7]="SETECIENTOS";
$centenas[8]="OCHOCIENTOS";
$centenas[9]="NOVECIENTOS";

if ($numero_a_convertir!=0) {

// Siempre positivo
if ($numero_a_convertir<0) {
        $prefijo="MENOS ";
        }
$numero_a_convertir=abs($numero_a_convertir);

// Separamos la parte entera de la parte decimal
$importe_mostrado=number_format(round($numero_a_convertir,2), 2, ',', '.');

$array_importe=explode(",",$importe_mostrado);

$parte_entera=$array_importe[0];
$parte_decimal=$array_importe[1];

$array_parte_entera=explode(".",$parte_entera);

// Vamos a ver
$texto0="";
$texto1="";
$texto2="";
$texto3="";

$numero0=null;
$numero1=null;
$numero2=null;
$numero3=null;

// Parte decimal
$numero0="00".$parte_decimal;
$numero0=substr($numero0,strlen($numero0)-3,3);

$uds_numero1=substr($numero0,strlen($numero0)-1,1);
$dec_numero1=substr($numero0,strlen($numero0)-2,1);
$cen_numero1=substr($numero0,strlen($numero0)-3,1);

$texto0="";
if ($cen_numero1!=0) {
        if ($numero0=="100") {
                $texto0="CIEN";
                } else {
                $texto0=$centenas[$cen_numero1];
                }
        }

if ($dec_numero1==2) {
                $separador0="";
                if (strlen($texto0)!=0) {
                        $separador0=" ";
                        }
                if ($uds_numero1!=0) {
                        $texto0=$texto0.$separador0.$veintenas[$uds_numero1];
                        } else {
                        $texto0=$texto0.$separador0.$decenas[$dec_numero1];
                        }

                } else {
                if ($dec_numero1==1) {
                        $separador0="";
                        if (strlen($texto0)!=0) {
                                $separador0=" ";
                                }
                        if ($uds_numero1!=0) {
                                $texto0=$texto0.$separador0.$oncenas[$uds_numero1];
                                } else {
                                $texto0=$texto0.$separador0.$decenas[$dec_numero1];
                                }

                        } else {
                        $separador0="";
                        if (strlen($texto0)!=0) {
                                $separador0=" ";
                                }
                        $texto0=$texto0.$separador0.$decenas[$dec_numero1];

                        if ($uds_numero1!=0) {
                                        $separador0="";
                                        if (strlen($texto0)!=0) {
                                                $separador0=" Y ";
                                                }
                                        if (($cen_numero1!=0) and ($dec_numero1==0)) {
                                                $separador0="";
                                                }
                                        $texto0=$texto0.$separador0.$unidades[$uds_numero1];
                                }
                        }
                }
if ($numero0>0) {
        $texto_parte_decimal=" CON ".$texto0." CÉNTIMOS DE EURO";
        }

// Parte 1 (0-999)
$numero1="00".$array_parte_entera[count($array_parte_entera)-1];
$numero1=substr($numero1,strlen($numero1)-3,3);

$uds_numero1=substr($numero1,strlen($numero1)-1,1);
$dec_numero1=substr($numero1,strlen($numero1)-2,1);
$cen_numero1=substr($numero1,strlen($numero1)-3,1);

$texto1="";
if ($cen_numero1!=0) {
        if ($numero1=="100") {
                $texto1="CIEN";
                } else {
                $texto1=$centenas[$cen_numero1];
                }
        }

if ($dec_numero1==2) {
                $separador1="";
                if (strlen($texto1)!=0) {
                        $separador1=" ";
                        }
                if ($uds_numero1!=0) {
                        $texto1=$texto1.$separador1.$veintenas[$uds_numero1];
                        } else {
                        $texto1=$texto1.$separador1.$decenas[$dec_numero1];
                        }

                } else {
                if ($dec_numero1==1) {
                        $separador1="";
                        if (strlen($texto1)!=0) {
                                $separador1=" ";
                                }
                        if ($uds_numero1!=0) {
                                $texto1=$texto1.$separador1.$oncenas[$uds_numero1];
                                } else {
                                $texto1=$texto1.$separador1.$decenas[$dec_numero1];
                                }

                        } else {
                        $separador1="";
                        if (strlen($texto1)!=0) {
                                $separador1=" ";
                                }
                        $texto1=$texto1.$separador1.$decenas[$dec_numero1];
                        
                        if ($uds_numero1!=0) {
                                        $separador1="";
                                        if (strlen($texto1)!=0) {
                                                $separador1=" Y ";
                                                }
                                        if (($cen_numero1!=0) and ($dec_numero1==0)) {
                                                $separador1="";
                                                }
                                        $texto1=$texto1.$separador1.$unidades[$uds_numero1];
                                }
                        }
                }
                
// Parte 2 (1000-999999)
if (count($array_parte_entera)>1) {

$numero2="00".$array_parte_entera[count($array_parte_entera)-2];
$numero2=substr($numero2,strlen($numero2)-3,3);

$uds_numero1=substr($numero2,strlen($numero2)-1,1);
$dec_numero1=substr($numero2,strlen($numero2)-2,1);
$cen_numero1=substr($numero2,strlen($numero2)-3,1);

$texto2="";
if ($cen_numero1!=0) {
        if ($numero2=="100") {
                $texto2="CIEN";
                } else {
                $texto2=$centenas[$cen_numero1];
                }
        }

if ($dec_numero1==2) {
                $separador2="";
                if (strlen($texto2)!=0) {
                        $separador2=" ";
                        }
                if ($uds_numero1!=0) {
                        $texto2=$texto2.$separador2.$veintenas[$uds_numero1];
                        } else {
                        $texto2=$texto2.$separador2.$decenas[$dec_numero1];
                        }

                } else {
                if ($dec_numero1==1) {
                        $separador2="";
                        if (strlen($texto2)!=0) {
                                $separador2=" ";
                                }
                        if ($uds_numero1!=0) {
                                $texto2=$texto2.$separador2.$oncenas[$uds_numero1];
                                } else {
                                $texto2=$texto2.$separador2.$decenas[$dec_numero1];
                                }

                        } else {
                        $separador2="";
                        if (strlen($texto2)!=0) {
                                $separador2=" ";
                                }
                        $texto2=$texto2.$separador2.$decenas[$dec_numero1];

                        if ($uds_numero1!=0) {
                                        $separador2="";
                                        if (strlen($texto2)!=0) {
                                                $separador2=" Y ";
                                                }
                                        if (($cen_numero1!=0) and ($dec_numero1==0)) {
                                                $separador2="";
                                                }
                                        $texto2=$texto2.$separador2.$unidades[$uds_numero1];
                                }
                        }
                }
}

// Parte 3 (1000000-999999999)
if (count($array_parte_entera)>2) {

$numero3="00".$array_parte_entera[count($array_parte_entera)-3];
$numero3=substr($numero3,strlen($numero3)-3,3);

$uds_numero1=substr($numero3,strlen($numero3)-1,1);
$dec_numero1=substr($numero3,strlen($numero3)-2,1);
$cen_numero1=substr($numero3,strlen($numero3)-3,1);

$texto3="";
if ($cen_numero1!=0) {
        if ($numero3=="100") {
                $texto3="CIEN";
                } else {
                $texto3=$centenas[$cen_numero1];
                }
        }

if ($dec_numero1==2) {
                $separador3="";
                if (strlen($texto3)!=0) {
                        $separador3=" ";
                        }
                if ($uds_numero1!=0) {
                        $texto3=$texto3.$separador3.$veintenas[$uds_numero1];
                        } else {
                        $texto3=$texto3.$separador3.$decenas[$dec_numero1];
                        }

                } else {
                if ($dec_numero1==1) {
                        $separador3="";
                        if (strlen($texto3)!=0) {
                                $separador3=" ";
                                }
                        if ($uds_numero1!=0) {
                                $texto3=$texto3.$separador3.$oncenas[$uds_numero1];
                                } else {
                                $texto3=$texto3.$separador3.$decenas[$dec_numero1];
                                }

                        } else {
                        $separador3="";
                        if (strlen($texto3)!=0) {
                                $separador3=" ";
                                }
                        $texto3=$texto3.$separador3.$decenas[$dec_numero1];

                        if ($uds_numero1!=0) {
                                        $separador3="";
                                        if (strlen($texto3)!=0) {
                                                $separador3=" Y ";
                                                }
                                        if (($cen_numero1!=0) and ($dec_numero1==0)) {
                                                $separador3="";
                                                }
                                        $texto3=$texto3.$separador3.$unidades[$uds_numero1];
                                }
                        }
                }
}

if ($numero3!=0) {
if ($numero3==1) {
        $texto3="UN MILLÓN";
        if ((strlen($texto2)!=0) or (strlen($texto2)!=0)) {
                $texto3=$texto3." ";
                }
        } else {
        if ((strlen($texto2)!=0) or (strlen($texto1)!=0)) {
                $texto3=$texto3." MILLONES ";
                } else {
                $texto3=$texto3." MILLONES";
                }
        }
}
if ($numero2!=0) {
if ($numero2==1) {
        $texto2="MIL";
        if (strlen($texto1)!=0) {
                $texto2=$texto2." ";
                }
        } else {
        if ((strlen($texto1)!=0) and (strlen($texto2)!=0)) {
                $texto2=$texto2." MIL ";
                } else {
                $texto2=$texto2." MIL";
                }
        }
}


$importe_como_texto=$prefijo.$texto3.$texto2.$texto1;

} else {
$importe_como_texto="CERO";
}

$unidad_medida="EUROS";
if ($numero_a_convertir==1) {
        $unidad_medida="EURO";
        }
        
$texto_devuelto=" ------".$importe_como_texto." ".$unidad_medida.$texto_parte_decimal.".-------";

return($texto_devuelto);

}
?>
