<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$inicial=$_GET["i"];
if (strlen($inicial)==0) {
        $inicial="A";
        }


// Palabras clave
$tipo_b=$_GET["Tipo_b"];
if (strlen($tipo_b)==0) { $tipo_b="AND"; }

if (strlen($b)==0) { $b=$_GET["b"]; }

$filtro_centro="alumnos.id_centro=".$centro_actual;
if ($tipo_centro_actual==1) {

	// Busca centros propios
	$busca_centros_propios=db_query("SELECT * FROM centros WHERE centro_propio=1");
	$filtro_centro="";
	while($centros_propios=db_fetch_array($busca_centros_propios)) {
			$filtro_centro.="alumnos.id_centro=".$centros_propios["id_centro"]." OR ";
	}
	if ($filtro_centro!="") {
		$filtro_centro="(".substr($filtro_centro,0,strlen($filtro_centro)-4).")";
	}
}

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo,curso) {
        window.open('ficha_alumno.php?id_curso_alumno='+curso+'&u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de alumnos de la escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
  <form name="busqueda" method="GET" action="<?php $HTTP_PHP_SELF;?>">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><small><b>Buscar:</b> <input type="text" name="b" style="font-size: 10px" size=60 value="<?=$b;?>"> <input type="radio" name="Tipo_b" <?php  if ($tipo_b=="AND")
{
  print "checked ";} ?>value="AND"> Y <input type="radio" <?php  if ($tipo_b!="AND")
{
  print "checked ";} ?>name="Tipo_b" value="OR"> O <input type="submit" value="Buscar" style="font-size: 10px"></small></small></td>
  </tr>
  </form>
<?php 
if (strlen($b)==0) {
?>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<?php 
for ($letra=65;$letra<=90;$letra++) {
        // Comprueba si hay alumnos con esa letra
        $busca_alumnos_en=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $filtro_centro AND usuarios.tipo_inscripcion=1 AND usuarios.Apellido1 like '".chr($letra)."%'");
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "<font color='#cecece'>";
                } else {
                echo "<a href='".$_SERVER["PHP_SELF"]."?i=".chr($letra)."'><font color='#000000'><b>";
                }
        echo chr($letra);
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "</font>";
                } else {
                echo "</b></font></a>";
                }
        if ($letra<90) { echo " "; }
        }
?>
</font></td>
</tr>
<?php 
$mensaje_busqueda="Alumnos cuyo apellido comienza por la letra <b>".$inicial."</b>";
$mensaje_no_encontrado="cuyo apellido comienza por la letra ".$inicial;
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $filtro_centro AND usuarios.tipo_inscripcion=1 AND usuarios.Apellido1 like '".$inicial."%' GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
} else {
$mensaje_busqueda="Resultado de la búsqueda";
$mensaje_no_encontrado="con el criterio de búsqueda solicitado";

if (strlen($b)!=0) {

        // Desglosa una frase en palabras sueltas

          $palabra=explode(" ",$b);

          $query=" (";
          for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."((usuarios.Nombre like '%".$palabra[$cont]."%')";
                if (is_numeric($palabra[$cont])) {
	                $query=$query." OR (usuarios.id_usuario=".$palabra[$cont].")";                
                }                
                $query=$query." OR (usuarios.Apellido1 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Apellido2 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Poblacion like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.DNI like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Direccion like '%".$palabra[$cont]."%')";
                
                $query=$query." OR (usuarios.telefono1 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.telefono2 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.movil like '%".$palabra[$cont]."%')";
                
                $query=$query." OR (usuarios.email like '%".$palabra[$cont]."%')";
                
                $query=$query." OR (usuarios.observaciones like '%".$palabra[$cont]."%')";
				
				$query=$query." OR (usuarios.SIP like '%".$palabra[$cont]."%')";
                
				$query=$query.") ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";
        }

$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $filtro_centro AND".$query." GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
}
if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>"><i><?=$mensaje_busqueda;?></i><br>Para seleccionar el alumno, haz clic sobre su nombre.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
	    <td><font size=1 face="<?= $fuente_general; ?>"><b>SIP</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Aulas</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $fila_par_impar=0;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
        // Busca aulas
        $busca_aulas_donde=db_query("SELECT centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso, alumnos.id_curso AS id_curso_alumno FROM alumnos, centro_aulas, cursos_academicos WHERE alumnos.aula=centro_aulas.id_aula AND alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=".$listado_alumnos["id_usuario"]." ORDER BY cursos_academicos.id_curso");
        
        $lista_aulas_alumno="";
        
        while($aulas_donde=db_fetch_array($busca_aulas_donde)) {
        	$lista_aulas_alumno.=$aulas_donde["nombre_aula"]." - ".$aulas_donde["nombre_curso"]."<br />";
        	}
        	
		$color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;   
        ?>
        <tr bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
		<td><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["SIP"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_nac_alumno?> - <?=$edad_alumno?> años</font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$lista_aulas_alumno;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>,<?=$listado_alumnos["id_curso_alumno"];?>)"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún alumno <?=$mensaje_no_encontrado;?><br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

