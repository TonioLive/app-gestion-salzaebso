<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prog=$_GET["id_prog"];
$capa_activa=$_GET["capa_activa"];


$busca_datos_programacion=db_query("SELECT * FROM agenda_programacion_ingles WHERE id_prog=$id_prog");

$datos_programacion=db_fetch_array($busca_datos_programacion);

$id_aula=$datos_programacion["id_aula"];

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_programacion["id_aula"]);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        
        $ciclo_aula=$datos_aula["ciclo"];
        $edad_aula=$datos_aula["edad_minima_prog"];

        $edad_aula_max=$datos_aula["edad_maxima_prog"];
        }
        
        
$id_objetivo_principal=$datos_programacion["objetivo"];

$area_objetivos=$datos_programacion["area"];

$objetivos_adicionales=str_replace("[", "", $datos_programacion["objetivos"]);
$objetivos_adicionales=str_replace("]", "", $objetivos_adicionales);

$array_objetivos=explode(",", $objetivos_adicionales);        
        
$busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$id_objetivo_principal);

$datos_objetivos=db_fetch_array($busca_objetivos);

$edad_objetivos=$datos_objetivos["edad"];

$mes_objetivos=$datos_objetivos["mes"];

$mes_anterior=$mes_objetivos-1;
$mes_siguiente=$mes_objetivos+1;
if ($mes_objetivos==1) {
        $mes_anterior=12;
        }
if ($mes_objetivos==12) {
        $mes_siguiente=1;
        }
        
$inicio_time_stamp=strtotime($datos_programacion["fecha"]);


?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin">
<table width="100%" border="0">
<form name="objetivos" method="POST" action="guarda_objetivos.php">
<input type="hidden" name="id_prog" value="<?=$id_prog;?>">
<input type="hidden" name="capa_activa" value="<?=$capa_activa;?>">
  <tr>
    <td align="left">
    <b>Objetivos adicionales<br><br>Aula: <?=$nombre_aula?> - Día <?=date("d/m/Y",strtotime($datos_programacion["fecha"]))?>, Tiempo de trabajo <?=$datos_programacion["tiempo_trabajo"]?></font></b><br>Selecciona todos los objetivos que se trabajan en esta actividad y pulsa el botón <b><i>"Guardar cambios"</i></b><br><hr noshade color="#000000" size="1">
                         <?php 
                         $busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_area=".$area_objetivos." AND edad>=$edad_aula AND edad<=$edad_aula_max AND (mes=$mes_objetivos OR mes=$mes_siguiente OR mes=$mes_anterior OR mes=0) ORDER BY mes DESC,nombre_objetivo") or die(db_error());
                        while($objetivos=db_fetch_array($busca_objetivos)){
                        
                        if ($objetivos["edad"]!=0) {
                                if ($objetivos["mes"]==0) {
                                        $nombre_mes_edad="Curso";
                                        } else {
                                        $nombre_mes_edad=$nombre_mes_corto[$objetivos["mes"]];
                                        }
                                } else {
                                $nombre_mes_edad=($objetivos["mes"]+6)." mes";
                                if ($objetivos["mes"]!=1) {
                                        $nombre_mes_edad.="es";
                                        }
                                }
                                
                        $estado_checkbox="";       
                        if ($objetivos["id_objetivo"]==$id_objetivo_principal) {
                        	$estado_checkbox=" disabled checked";
                        	}
                        
                        ?>
                        <input type="checkbox"<?=$estado_checkbox?> name="id_objetivos[]" value="<?=$objetivos["id_objetivo"]?>"<?php  if (in_array($objetivos["id_objetivo"],$array_objetivos)) { print " checked"; } ?>><?=$objetivos["nombre_objetivo"];?> (<?=$nombre_mes_edad?> Edad: <?=$objetivos["edad"]?> año<?php  if ($objetivos["edad"]!=1) { ?>s<?php  } ?>)<br>
                        <?php 
                        }
                        
                        // Busca objetivos adicionales
                        $mes_sel_objetivos=date("n",$inicio_time_stamp);
						$anyo_sel_objetivos=date("Y",$inicio_time_stamp);
						
                        $busca_objetivos_adicionales=db_query("SELECT * FROM agenda_programacion_ingles_objetivos_recuperados,agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles_objetivos_recuperados.id_objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND agenda_programacion_ingles_objetivos_recuperados.aula=$id_aula AND agenda_programacion_ingles_objetivos_recuperados.mes=$mes_sel_objetivos AND agenda_programacion_ingles_objetivos_recuperados.anyo=$anyo_sel_objetivos AND (agenda_programacion_ingles_objetivos.mes<>$mes_objetivos AND agenda_programacion_ingles_objetivos.mes<>$mes_siguiente AND agenda_programacion_ingles_objetivos.mes<>$mes_anterior AND agenda_programacion_ingles_objetivos.mes<>0)") or die(db_error());
                        
                        
                        while($objetivos_adicionales=db_fetch_array($busca_objetivos_adicionales)){

                        $nombre_mes_edad="Recuperado";

                        $estado_checkbox="";       
                        if ($objetivos_adicionales["id_objetivo"]==$id_objetivo_principal) {
                        	$estado_checkbox=" disabled checked";
                        	}
                        
                        ?>
                        <input type="checkbox"<?=$estado_checkbox?>> name="id_objetivos[]" value="<?=$objetivos_adicionales["id_objetivo"]?>"<?php  if (in_array($objetivos_adicionales["id_objetivo"],$array_objetivos)) { print " checked"; } ?>><?=$objetivos_adicionales["nombre_objetivo"];?> (<?=$nombre_mes_edad?>)<br>
                        <?php 
                        }  
                        ?>                      
	<p align="center"><input type="submit" style="font-size: 12px" value="Guardar cambios"> <input type="button" onclick="window.close()" style="font-size: 12px" value="Cerrar"></p>
</td>
  </tr>
  </form>
</table>
</body>
</html>
<?php 
db_close();
?>
