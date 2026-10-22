<?php 
require("../configuracion/config.php");

$sip=$_POST["sip"];

// Validar SIP
if (is_numeric($sip)) {
    
    if (strlen($sip)==10) {
        
        $dig_control=substr($sip,0,2);
        
        $numero1=(substr($sip,2,1)*9)+(substr($sip,3,1)*8)+(substr($sip,4,1)*7)+(substr($sip,5,1)*6)+(substr($sip,6,1)*5)+(substr($sip,7,1)*4)+(substr($sip,8,1)*3)+(substr($sip,9,1)*2);
        $numero2=$numero1 % 90;
        $numero3=10+$numero2;
        
        if ($numero3==$dig_control) {
            
            $sip_correcto="ok";	
            
        } else {
            
            $sip_correcto="El Nº de tarjeta SIP no es correcto, alguno de los números introducidos ha sido tecleado incorrectamente.";
            
        }
        
        
    } else {
    
        $sip_correcto="El Nº de tarjeta SIP no es correcto, debe tener 10 dígitos y el número que has puesto tiene ".strlen($sip).".";	
        
    }
    
} else {
    
    $sip_correcto="El Nº de tarjeta SIP debe ser un número. No puede contener letras o símbolos";

    if (strtoupper($sip)=="PENDIENTE") {
        
        $sip_correcto="ok";	
        
    }			
    
}


echo($sip_correcto);
	
?>