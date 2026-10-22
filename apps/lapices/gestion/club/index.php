<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior_no_config.php");

$fecha=strftime("%m/%d/%Y");
$ancho_tabla=$ancho_pagina_total-185
?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="100%" id="AutoNumber2">
                <form name="club" action="<?=$_SERVER["PHP_SELF"];?>" method="GET">
                <tr>
                        <td width="100%" valign="top" bgcolor="<%= $fondo_at%>" align="center">
                            <font size="3" color="#000000"><b>Inscripciones en el Club Lápices<br>
                            Mostrar: <select name="filtro_club" style="font-size: 10px" onchange="document.club.submit()">
                            <option value="0">Todos</option>
                            <option value="1"<?php  if ($_GET["filtro_club"]==1) { print " selected"; } ?>>Padres de alumnos o antiguos alumnos</option>
                            <option value="2"<?php  if ($_GET["filtro_club"]==2) { print " selected"; } ?>>Otros</option>
                            </select></font></b>
                        </td>
                </tr>
                </form>
    </table><br>
    <input type="button" onclick="window.location.href='/'" value="Salir" style="font-size: 10px"><br><br><table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="90%">
    <tr bgcolor="#dfdfdf">
        <td align="center"><b><font color="<?=  $color_fuente; ?>">Fecha Inscripción</b></td>
        <td align="center"><b><font color="<?=  $color_fuente; ?>">Email</b></td>
        <td align="center"><b><font color="<?=  $color_fuente; ?>">¿Padre de<br>alumno?</b></td>
        <td width=25% align="center"><b><font color="<?=  $color_fuente; ?>">Nombre</b></td>
        <td width=25% align="center"><b><font color="<?=  $color_fuente; ?>">Nombre Hijo/Hija</b></td>
  </tr>
<?php 

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);


$filtro_club=$_GET["filtro_club"];
if (strlen($filtro_club)==0) {
        $filtro_club=0;
        }

  $consulta_usuarios=db_query("SELECT * FROM registro_envio_mensaje ORDER BY fecha DESC, hora") or die(db_error());

  mt_srand((double)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  
  $cuenta_email_club=urldecode($rsslc["cuenta"]);
    
    
  // Comprueba si es padre de alumno
  $busca_inscripciones=db_query("SELECT * FROM usuarios WHERE email LIKE '".$cuenta_email_club."'") or die(db_error());
  $nombre_padre="";
  $nombre_hijos="";
  if (db_num_rows($busca_inscripciones)!=0) {
        $tipo_filtro_usuario=1;
        $es_padre_de_alumno="Si";
        
        $datos_padre_club=db_fetch_array($busca_inscripciones);
        $id_usuario_club=$datos_padre_club["id_usuario"];
        $nombre_padre=mb_convert_case($datos_padre_club["Nombre"]." ".$datos_padre_club["Apellido1"]." ".$datos_padre_club["Apellido2"], MB_CASE_TITLE);
        
        // Busca Hijos

        $busca_hijos_club=db_query("SELECT * FROM padres, usuarios WHERE padres.padre_de=usuarios.id_usuario AND padres.id_usuario=$id_usuario_club GROUP BY padres.padre_de");
        
        while($hijos_club=db_fetch_array($busca_hijos_club)) {
        	$nombre_hijos.="- <a href='/alumnos/nuevo_alumno1.php?u=".$hijos_club["id_usuario"]."' target='_blank'>".mb_convert_case($hijos_club["Nombre"]." ".$hijos_club["Apellido1"]." ".$hijos_club["Apellido2"], MB_CASE_TITLE)."</a><br />";
        	}
        
        } else {
        $tipo_filtro_usuario=2;
        $es_padre_de_alumno="No";
        }
  
  if (($tipo_filtro_usuario==$filtro_club) or ($filtro_club==0)) { 
  
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];

    ?>
    <tr valign="top" bgcolor="#ffffff">
    <td><font size=2><?=date("d/m/Y",strtotime($rsslc["fecha"]))?></font></td>
    <td><font size=2><?=strtolower($cuenta_email_club)?></font></td>
    <td><font size=2><?=$es_padre_de_alumno;?></font></td>
    <td><font size=2><?=$nombre_padre;?></font></td>
    <td><font size=2><?=$nombre_hijos;?></font></td>
    </tr>
    <?php 
    }
   }

db_close();
?>
</table><br>
</center></div></div>
</font>
</small>
</small>
<?php  require("../../configuracion/parte_inferior.php"); ?>

