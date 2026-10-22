<?php 
include("../configuracion/config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>

<head>
        <title>Programación</title>

        <link rel="StyleSheet" href="dtree.css" type="text/css" />
        <script type="text/javascript" src="dtree.js"></script>

</head>

<body>
<table width=100%>
<tr>
<td width=25%>
<div class="dtree">

        <p><a href="javascript: d.openAll();">Abrir todo</a> | <a href="javascript: d.closeAll();">Cerrar todo</a></p>

        <script type="text/javascript">
                <!--

                d = new dTree('d');


                d.add(0,-1,'Programación 2010-2011');
                
<?php 
$busca_areas=db_query("SELECT * FROM programacion_areas ORDER BY orden");

while($areas=db_fetch_array($busca_areas)){

?>
d.add(<?=$areas["id_area"]?>,0,'<?=stripslashes($areas["nombre_area"])?>','index.php?i=<?=$areas["id_area"]?>');
        <?php 
        $busca_objetivos=db_query("SELECT * FROM programacion_objetivos WHERE id_area=".$areas["id_area"]." ORDER BY orden");
        while($objetivos=db_fetch_array($busca_objetivos)){
        
        if ($objetivos["padre"]==0){
                $padre=$areas["id_area"];
                } else {
                $padre=1000+$objetivos["padre"];
                }
        
                ?>
                d.add(<?=1000+$objetivos["id_objetivo"]?>,<?=$padre?>,'<?=stripslashes($objetivos["nombre_objetivo"])?>','index.php?o=<?=$objetivos["id_objetivo"]?>');
                <?php 

                $busca_actividades=db_query("SELECT * FROM programacion_actividades,programacion_modelos_actividades WHERE programacion_actividades.id_modelo_actividad=programacion_modelos_actividades.id_modelo_actividad AND programacion_actividades.id_objetivo=".$objetivos["id_objetivo"]." ORDER BY orden") or die(db_error());
                while($actividades=db_fetch_array($busca_actividades)){

                if ($actividades["padre"]==0){
                        $padreA=1000+$objetivos["id_objetivo"];
                        } else {
                        $padreA=2000+$actividades["id_actividad"];
                        }

                ?>
                d.add(<?=2000+$actividades["id_actividad"]?>,<?=$padreA?>,'<?=stripslashes($actividades["nombre_actividad"])?>','index.php?a=<?=$actividades["id_actividad"]?>');
                <?php 


                }
        }
}
?>
                document.write(d);

                //-->
        </script>

</div>
</td>
<td width="75%">
<?php 
$a=$_GET["a"];

if (strlen($a)!=0)   {

// Busca datos actividad

        $busca_actividad=db_query("SELECT * FROM programacion_actividades,programacion_modelos_actividades WHERE programacion_actividades.id_modelo_actividad=programacion_modelos_actividades.id_modelo_actividad AND programacion_actividades.id_actividad=".$a.";") or die(db_error());

        $datos_actividad=db_fetch_array($busca_actividad);
        
        ?>
        <p align="center">
        <font size=3 face="Arial"><b><?=stripslashes($datos_actividad["nombre_actividad"])?></b></font><br><br>
        <font size=2 face="Arial"><?=stripslashes($datos_actividad["descripcion_actividad"])?></font><br><br>
        </p>
        <?php 

        }

$o=$_GET["o"];

if (strlen($o)!=0)   {

// Busca datos objetivo

        $busca_objetivo=db_query("SELECT * FROM programacion_objetivos WHERE id_objetivo=".$o.";") or die(db_error());

        $datos_objetivo=db_fetch_array($busca_objetivo);

        ?>
        <p align="center">
        <font size=3 face="Arial"><b><?=stripslashes($datos_objetivo["nombre_objetivo"])?></b></font><br><br>
        <font size=2 face="Arial"><?=stripslashes($datos_objetivo["descripcion_objetivo"])?></font><br><br>
        </p>
        <br>
        <p align="center"><a href="nuevo_objetivo.php?id_area=<?=$datos_objetivo["id_area"]?>">Nuevo</a> |  <a href="nuevo_objetivo.php?id_area=<?=$datos_objetivo["id_area"]?>&id_objetivo=<?=$o?>">Editar</a></p>
        <?php 

        }

$i=$_GET["i"];

if (strlen($i)!=0)   {

// Busca datos area

        $busca_area=db_query("SELECT * FROM programacion_areas WHERE id_area=".$i.";") or die(db_error());

        $datos_area=db_fetch_array($busca_area);

        ?>
        <p align="center">
        <font size=3 face="Arial"><b><?=stripslashes($datos_area["nombre_area"])?></b></font><br><br>
        <font size=2 face="Arial"><?=stripslashes($datos_area["descripcion_area"])?></font><br><br>
        </p>
        <br>
        <p align="center"><a href="nueva_area.php">Nueva área</a> |  <a href="nuevo_objetivo.php?id_area=<?=$datos_area["id_area"]?>">Nuevo objetivo</a> |  <a href="nueva_area.php?id_area=<?=$i?>">Editar</a></p>
        <?php 

        }
?>
</td>
</tr>
</table>
</body>

</html>
