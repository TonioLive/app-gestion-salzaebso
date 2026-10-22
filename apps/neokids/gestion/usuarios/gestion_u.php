<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior.php");

$fecha=date("m/d/Y");
$ancho_tabla=$ancho_pagina_total-185;

$filtro=(int)($_GET["f"] ?? 0);


$tipo_filtro=1;
$texto_boton="Ver listado completo de usuarios";
$mensaje_usuarios="Usuaros activos (Con contrato en vigor)";

if ($filtro==1) {
        $mensaje_usuarios="Listado completo usuarios";
        $tipo_filtro=0;
        $texto_boton="Ver listado de usuarios activos";
        }

?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" align="center">
                            <font size="3" color="#000000"><b><?=$mensaje_usuarios?></font></b>
                        </td>
                </tr>
    </table><br>
    <input type="button" value="<?=$texto_boton?>" onclick="self.location.href='<?=$_SERVER["PHP_SELF"]?>?f=<?=$tipo_filtro?>'" style="font-size:10px"><?php 
    if ($nivel_acceso==4) {
    ?><input type="button" onclick="window.location.href='nuevo_usuario.php'" value="Nuevo usuario" style="font-size:10px">
    <br><br><?php 
    }
    ?>
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111">
<?php 

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);
 
?>
  <tr bgcolor="<?=  $fondo_at; ?>">
        <td colspan=8>
        <b><font color="<?=  $color_fuente; ?>">Listado de usuarios</b>
        </td>
  </tr>
  <tr bgcolor="#dfdfdf">
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Tipo</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Fecha alta</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Nombre</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Email</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Telefonos</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Usuario</b></small></small></td>
    <td><small><small><b><font color="<?=  $color_fuente; ?>">Acceso</b></small></small></td>
    <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Acción</b></small></small></td>
  </tr>
<?php  
  $filtro_centro="";
  if ($nivel_acceso==3) {
          // $filtro_centro=" AND id_centro=$centro_actual";
        }
  $consulta_usuarios=db_query_params("SELECT u.* FROM usuarios u WHERE u.tipo_inscripcion>=3 AND (?=1 OR EXISTS(SELECT 1 FROM personal_contratos p WHERE p.id_empleado=u.id_usuario AND (p.fecha_inicio>CURDATE() OR (p.fecha_inicio<=CURDATE() AND (p.fecha_fin>=CURDATE() OR p.fecha_fin='0000-00-00' OR p.fecha_fin IS NULL))))) ORDER BY u.Apellido1", 'i', array($filtro)) or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
    $codigo=$rsslc["id_usuario"];
    
    { // Contract filtering is already performed by the main query.
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
    
    $contador=$contador+1;

    $t="";
    $t1=(string)$rsslc["telefono1"];
    $t2=(string)$rsslc["telefono2"];
    $t3=(string)$rsslc["movil"];

    if (strlen($t1)>0)
    {
      $t=$t.$t1.", ";
    } 
    if (strlen($t2)>0)
    {
      $t=$t.$t2.", ";
    } 
    if (strlen($t3)>0)
    {
      $t=$t.$t3.", ";
    } 

    if (strlen($t)>0)
    {
      $t=substr($t,0,strlen($t)-2);
    } 

    print "<tr bgcolor='#ffffff'>";
    ?>
    <td><font size=1 face=Arial><?=$tipo_usuario[$rsslc["nivel_acceso"]];?></font></td>
    <?php 
    print "<td><font size=1 face=Arial>".date("d/m/Y",strtotime($rsslc["fechaalta"]))."</font></td>";
    print "<td><font size=1 face=Arial>";
    print "<em>".htmlspecialchars((string)$nombre_ins, ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1")."</em></font></td>";
    print "<td><font size=1 face=Arial><a href='mailto:".htmlspecialchars((string)$rsslc["email"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1")."'>".htmlspecialchars((string)$rsslc["email"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1")."</a></font></td>";
    print "<td><font size=1 face=Arial>".htmlspecialchars((string)$t, ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1")."</font></td>";
    print "<td><font size=1 face=Arial>".htmlspecialchars((string)$rsslc["usuario"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1")."</font></td>";
    
    print "<td><font size=1 face=Arial>".((string)$rsslc["pass"]!=="" ? "Configurada" : "Sin configurar")."</font></td>";

    print "<td align='center'>";
    print "<font size=1 face=Arial><a href=\"#\" onclick='window.open(\"datos_cand.php?dato=s&id_usu=".$codigo."\",\"Datos_usuario_1\",\"toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=no,width=625,height=485,titlebar=0,minimize=no,maximize=no\")'>Mas datos</a>";
    if ($nivel_acceso==4) {
            print " | <a href='elim_u_b.php?Submenu=".$submenu_activo."&Cod=".$codigo."'>Eliminar</a>";
            }
    print "</font></td></tr>";
    }
   }

  if ($contador==0)
  {
?>
  <tr bgcolor="<?= $fondo_ventana; ?>">
        <td colspan=8><small>
        <font face="<?= $fuente_general; ?>" color="<?= $color_fuente; ?>">
        Actualmente no hay ningún usuario registrado.
        </font>
        </small>
        </td>
  </tr>        
        <?php  
  } 

db_close();
?>
</table><br>
</center></div></div>
</font>
</small>
</small>
<?php  require("../../configuracion/parte_inferior.php"); ?>

