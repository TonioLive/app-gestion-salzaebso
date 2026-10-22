<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Nombres centros

$nombres_centros=array();
$busca_centros=db_query("SELECT * FROM escuelamunicipal.centros") or die(db_error());

while($datos_centros=db_fetch_array($busca_centros)){
	$nombres_centros[$datos_centros["id_centro"]]=$datos_centros["nombre_centro"];
}

$inicial=$_GET["i"];
if (strlen($inicial)==0) {
        $inicial="A";
        }


// Palabras clave
$tipo_b=$_GET["Tipo_b"];
if (strlen($tipo_b)==0) { $tipo_b="AND"; }

if (strlen($b)==0) { $b=$_GET["b"]; }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?base_datos=escuelamunicipal&u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><font face="Arial" size=2><b>Selecciona el el alumno</b></font></td>
  </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><form name="busqueda" method="GET" action="<?php $HTTP_PHP_SELF;?>"><font face="Arial" size=2><b>Buscar:</b> <input type="text" name="b" style="font-size: 10px" size=60 value="<?=$b;?>"> <input type="radio" name="Tipo_b" <?php  if ($tipo_b=="AND")
{
  print "checked ";} ?>value="AND"> Y <input type="radio" <?php  if ($tipo_b!="AND")
{
  print "checked ";} ?>name="Tipo_b" value="OR"> O <input type="submit" value="Buscar" style="font-size: 10px"></font></form></td>
  </tr> 
<?php 
if (strlen($b)==0) {
?>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<?php 
for ($letra=65;$letra<=90;$letra++) {
        // Comprueba si hay alumnos con esa letra
        $busca_alumnos_en=db_query_params('SELECT * FROM escuelamunicipal.usuarios WHERE tipo_inscripcion=1 AND Apellido1 like ?', 's', array((string)(chr($letra)) . '%'));
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
$busca_alumnos=db_query_params('SELECT * FROM escuelamunicipal.usuarios WHERE tipo_inscripcion=1 AND Apellido1 like ? ORDER BY Apellido1, Apellido2, Nombre', 's', array((string)($inicial) . '%'));
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
                $query=$query." OR (usuarios.Apellido1 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Apellido2 like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Poblacion like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.DNI like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.Direccion like '%".$palabra[$cont]."%')";
                $query=$query." OR (usuarios.observaciones like '%".$palabra[$cont]."%')";

                $query=$query.") ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";
        }

$busca_alumnos=db_query("SELECT * FROM escuelamunicipal.usuarios,escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND".$query." GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");
}

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i><?=$mensaje_busqueda?></b></i><br>Para seleccionar el alumno, haz clic sobre su nombre.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                
        if ($listado_alumnos["sexo"]=="H") {
                        $sexo_alumno_numero=1;
                        } else {
                        $sexo_alumno_numero=0;
                        }
                        
        // Busca aulas
        $busca_aulas_donde=db_query_params('SELECT centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso, alumnos.id_curso AS id_curso_alumno, centro_aulas.id_centro AS id_centro_alumno FROM escuelamunicipal.alumnos, escuelamunicipal.centro_aulas, escuelamunicipal.cursos_academicos WHERE alumnos.aula=centro_aulas.id_aula AND alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=? ORDER BY cursos_academicos.id_curso', 's', array($listado_alumnos["id_usuario"]));
        
        $lista_aulas_alumno="";
        
        while($aulas_donde=db_fetch_array($busca_aulas_donde)) {
        	$lista_aulas_alumno.=$nombres_centros[$aulas_donde["id_centro_alumno"]]. ": ".$aulas_donde["nombre_aula"]." - ".$aulas_donde["nombre_curso"]."<br />";
        	}
        	                        
        ?>
        <tr>
        <td><script language="javascript">
        function seleccionar<?=$listado_alumnos["id_usuario"];?>() {

                if (confirm('¿Quieres sustituir la información del formulario de contacto por la información de la tabla de usuarios?\n\nPara conservar la información introducida en el formulario de contacto pulsa Cancelar.')) {
                        self.opener.contactos.nombre.value='<?=$listado_alumnos["Nombre"];?>';
                        self.opener.contactos.apellido1.value='<?=$listado_alumnos["Apellido1"];?>';
                        self.opener.contactos.apellido2.value='<?=$listado_alumnos["Apellido2"];?>';
                        self.opener.contactos.fecha_nacimiento_dia.value='<?=date("d",strtotime($listado_alumnos["fecha_nacimiento"]));?>';
                        self.opener.contactos.fecha_nacimiento_mes.value='<?=date("m",strtotime($listado_alumnos["fecha_nacimiento"]));?>';
                        self.opener.contactos.fecha_nacimiento_ano.value='<?=date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));?>';
                        self.opener.contactos.sexo[<?=$sexo_alumno_numero;?>].click();
                        
                        self.opener.contactos.antiguo_alumno_lapices[0].click();
                        
                        <?php 
                        // Busca padres                        
                        $busca_padres=db_query_params('SELECT * FROM escuelamunicipal.usuarios, escuelamunicipal.padres WHERE escuelamunicipal.usuarios.id_usuario=escuelamunicipal.padres.id_usuario AND padre_de=? ORDER BY padres.parentesco LIMIT 0,2', 's', array($listado_alumnos["id_usuario"])) or die(db_error());
						
						$contador_padres=0;
						while($datos_padres=db_fetch_array($busca_padres)) {
						
							if ($contador_padres==0) {
							?>
                        	// Contacto 1                        
                        	self.opener.contactos.persona_contacto.value='<?=$datos_padres["Nombre"]?> <?=$datos_padres["Apellido1"]?> <?=$datos_padres["Apellido2"]?>';
                        	self.opener.contactos.telefono.value='<?=$datos_padres["telefono1"]?> <?=$datos_padres["telefono2"]?> <?=$datos_padres["movil"]?>';
                        	self.opener.contactos.email.value='<?=$datos_padres["email"]?>';
                        	self.opener.contactos.dni_padre1.value='<?=$datos_padres["DNI"]?>';
                        	self.opener.contactos.parentesco_padre1.value='<?=$datos_padres["parentesco"]?>';
							<?php 
							} else {
							?>
                        	// Contacto 2                        
                        	self.opener.contactos.nombre_padre2.value='<?=$datos_padres["Nombre"]?> <?=$datos_padres["Apellido1"]?> <?=$datos_padres["Apellido2"]?>';
                        	self.opener.contactos.telefono_padre2.value='<?=$datos_padres["telefono1"]?> <?=$datos_padres["telefono2"]?> <?=$datos_padres["movil"]?>';
                        	self.opener.contactos.email_padre2.value='<?=$datos_padres["email"]?>';
                        	self.opener.contactos.dni_padre2.value='<?=$datos_padres["DNI"]?>';
                        	self.opener.contactos.parentesco_padre2.value='<?=$datos_padres["parentesco"]?>';							
							<?php 
							}
						
						
							$contador_padres++;
						}
                        ?>                      
                        
                        // Datos familiares
                        self.opener.contactos.calle.value='<?=$listado_alumnos["Direccion"]?>';
                        self.opener.contactos.poblacion.value='<?=$listado_alumnos["Poblacion"]?>';
                        self.opener.contactos.codigo_postal.value='<?=$listado_alumnos["cp"]?>';
                        self.opener.contactos.telefonos_familia.value='<?=$listado_alumnos["telefono1"]?> <?=$listado_alumnos["telefono2"]?> <?=$listado_alumnos["movil"]?>';
                        self.opener.contactos.email_familia.value='<?=$listado_alumnos["email"]?>';
                        self.opener.contactos.coordenadas.value='<?=$listado_alumnos["coordenadas"]?>';
                        
                        };
                window.close();
                }
        </script><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:seleccionar<?=$listado_alumnos["id_usuario"];?>();"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$lista_aulas_alumno?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)"><img src="editar.gif" border=0></a></font></td>
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
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún alumno  <?=$mensaje_no_encontrado?><br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
</body>
</html>

