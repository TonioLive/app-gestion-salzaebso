<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$incidencia=$_POST["incidencia"];
$observaciones=$_POST["observaciones"];
$comedor=$_POST["comedor"];
$tipo_menu_comedor=$_POST["tipo_menu_comedor"];
$menu_seleccionado_hoy=$_POST["menu_seleccionado_hoy"];


// Busca info del menu
if ($menu_seleccionado_hoy==0) {
        $comedor=2;
        } else {
        // Busca menu en BD
        $consulta_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=$menu_seleccionado_hoy");
        if (db_num_rows($consulta_datos_menu)!=0) {
                $datos_menu=db_fetch_array($consulta_datos_menu);
				if ($datos_menu["regimen"]==1) {				
					$comedor=3;					
				} else {					
					$comedor=1;
				}
                // $comedor=1+($datos_menu["regimen"]*2);
                } else {
                $comedor=1;
                }
        }


$hora_salida=date("H:i:s",$hora);

db_query("UPDATE servicios_consumidos SET observaciones='$observaciones', tipo_menu=$comedor, tipo_menu_comedor='$tipo_menu_comedor', menu_seleccionado_hoy=$menu_seleccionado_hoy, incidencia='$incidencia' WHERE id_servicio_consumido=$id_registro") or dei(db_error());

?>
<script language="Javascript">
window.close();
self.opener.history.go(0);
</script>
