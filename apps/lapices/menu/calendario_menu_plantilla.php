<?php
require_once __DIR__ . '/../configuracion/encoding.php';
ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

require("../configuracion/parte_superior_privado_ancho_cocina_sin_menu_bootstrap.php"); 
require("./funciones_alergias.php");
?>
<div align="center">
<?php
$id_menu=$_GET["id_menu"];
$plantilla=$_GET["plantilla"];

if (strlen($plantilla)==0) {
        $plantilla=1;
}

$fecha_inicio=$_GET["fecha_inicio"];

$consulta_menu = db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu") or die (db_error());
$datos_menu = db_fetch_array($consulta_menu);


$edad_ini_actual=$datos_menu["edad_ini_menu"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_menu["edad_fin_menu"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;

$nombre_menu=$datos_menu["nombre_menu"];
if (strlen($nombre_menu)!=0) {
        $nombre_menu=app_url_text_utf8($nombre_menu);
        }
        

?>
<font size=3><b>Plantilla <?=$plantilla?> del menú: <?=$nombre_menu;?></b><br><?=$edades?><br></font>
<?php
for ($plantillas=1;$plantillas<=3;$plantillas++) {
        if ($plantillas!=$plantilla) {
        ?>
        <a class="btn btn-primary" href="calendario_menu_plantilla.php?id_menu=<?=$id_menu?>&plantilla=<?=$plantillas?>&fecha_inicio=<?=$fecha_inicio?>">Cambiar a plantilla <?=$plantillas?></a> 
        <?php
        }
}
?>
<table border=0 cellspacing="1" cellpadding="1" width="100%" style="border-collapse: separate; border-spacing: 1px;">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<td align="center">Semana</td>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</font>
</td>
<?php  
}
?>
</tr>
<?php  
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<td width=15% align="center">Semana <?=$semanas?><br /><br />
<?php if (strlen($fecha_inicio)!=0) { ?><input type="button" style="font-size: 10px" value="Copiar semana..." onclick="window.location.href='calendario_copiar_semana_plantilla.php?plantilla=<?=$plantilla?>&id_menu=<?=$id_menu?>&numero_semana=<?=$semanas;?>&fecha_inicio=<?=$fecha_inicio?>'"><?php } ?>
<br> <input type="button" style="font-size: 10px" value="Borrar semana..." onclick="if (confirm('Se borrará toda la semana seleccionada.\n\n¿Estás seguro?')) { window.open('borrar_semana_plantilla.php?plantilla=<?=$plantilla?>&id_menu=<?=$id_menu?>&semana=<?=$semanas;?>','semana','toolbar=no,resizable=yes,scrollbars=yes,width=330,height=216'); } "></td>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

        $fondo_celda_dia="#EFEFEF";
                
        // Comprueba si tiene ya los platos asignados
        
        $busca_platos_dia=db_query("SELECT * FROM menu_calendario_plantilla WHERE id_menu=$id_menu AND plantilla=$plantilla AND semana=".$semanas." AND dia=".$dias.";");
        if (db_num_rows($busca_platos_dia)!=0) {
                if ($fondo_celda_dia=="#F17327") {
                        $fondo_celda_dia="#E7FF30";
                        } else {
                        $fondo_celda_dia="#5DDD6C";
                        }
                } else {
                // Otra cosa
                }
        ?>
        <td align="center" bgcolor="<?=$fondo_celda_dia;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=1>
        <?php 
$busca_platos_dia=db_query("SELECT * FROM menu_calendario_plantilla WHERE id_menu=$id_menu AND plantilla=$plantilla AND semana=".$semanas." AND dia=".$dias.";");
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                // Edita el nuevo día
                $actualizar=false;
                $id_dia_menu=$datos_platos_dia["id_dia"];
                $comida1=$datos_platos_dia["comida1"];
                $comida2=$datos_platos_dia["comida2"];
                $comida3=$datos_platos_dia["comida3"];
                $comida4=$datos_platos_dia["comida4"];
                $comida5=$datos_platos_dia["comida5"];
                $postre1=$datos_platos_dia["postre1"];
                $merienda1=$datos_platos_dia["merienda1"];
                $merienda2=$datos_platos_dia["merienda2"];
                $cena=$datos_platos_dia["cena"];


        echo "<p align='left'>";
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($comida1);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }
                echo "Comida 1: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($comida2);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Comida 2: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida3 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($comida3);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Comida 3: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida4 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($comida4);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Comida 4: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida5 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($comida5);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Comida 5: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$postre1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($postre1);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Postre: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($merienda1);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Merienda 1: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($merienda2);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }                
                $datos_comida=db_fetch_array($busca_comida);
                $modificador1="";
                $modificador2="";
                if ($datos_comida["grupo"]==99) {
                        $modificador1="<span style='color: #001effff;'>";
                        $modificador2="</span>";
                }                
                echo "Merienda 2: ".$modificador1.app_url_text_utf8($datos_comida["nombre_plato"]).$modificador2.$listaAlergenosPlato."<br>";
                }

        if (strlen($cena)!=0) {
                echo "Cena: ".$cena."<br>";
        }

        echo "</p>";
        }
        ?>
        <br><input type="button" style="font-size: 10px" value="Editar día" name=""<?=$estado_boton?> onclick="cambia_dia(<?=$semanas;?>,<?=$dias?>,<?=$id_menu;?>)"><?php  if (db_num_rows($busca_platos_dia)!=0) { ?><input type="button" value="Borrar día" onclick="if (confirm('¿Eliminar la información de este día?')) { window.location.href='eliminar_dia_plantilla.php?plantilla=<?=$plantilla?>&id_dia=<?=$id_dia_menu?>&id_menu=<?=$id_menu?>&semana=<?=$semanas?>&dia=<?=$dias?>&accion=1'; }" style="font-size: 10px"><?php if (strlen($fecha_inicio)!=0) { ?> <br><input type="button" onclick="window.location.href='calendario_copiar_plantilla.php?plantilla=<?=$plantilla?>&id_menu=<?=$id_menu?>&id_dia_copiado=<?=$id_dia_menu?>&semana=<?=$semanas?>&dia=<?=$dias?>&fecha_inicio=<?=$fecha_inicio?>';" style="font-size: 10px" value="Copiar"><?php  } } ?>
        </font>
        </td>
        <?php 
  }
?>
</tr>
<?php  
}
?>
<tr>
<td>
</td>
</tr>
</table>
</div>
<div>
<h3>Alergenos:</h3> 
<?php
foreach ($arrayKeysAlergias as $keyAlg => $numAlg) {
echo("<img src='https://catering.escuelas-infantiles.com/img/iconos/".$keyAlg.".png' border='0' title='".$nombres_alergias[$numAlg-1]."'> ".$numAlg.". ".ucfirst($nombres_alergias[$numAlg-1])." ");
if ($numAlg==6) {
        echo("<br />");
}
}

?>
</div>
<script language="Javascript">
function cambia_dia(semana,dia,id_menu) {
        window.open("editar_menu_plantilla.php?plantilla=<?=$plantilla?>&semana="+semana+"&dia="+dia+"&id_menu="+id_menu,"cambia_dia","toolbar=no,width=700,height=500");
        }
</script>
<?php 
require("../configuracion/parte_inferior.php"); ?>