<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_cocina.php"); ?>
<?php  require("inc/config_platos.php"); ?>
<?php 
// Desglosa una frase en palabras sueltas
$tipo_b=$_POST["tipo_b"];
if (strlen($tipo_b)==0) { $tipo_b=$_GET["tipo_b"]; }
if (strlen($tipo_b)==0) { $tipo_b="OR"; }

$pu=$_POST["pu"];
if (strlen($pu)==0) { $pu=$_GET["pu"]; }
if (strlen($pu)!=0) {

        $contador_criterios=$contador_criterios+1;

        $criterio=$pu;
          $inicio_p=1;
          $fin=1;
          $palabra_actual=1;
          $query=" (";
          for ($cont=1; $cont<=strlen($criterio); $cont=$cont+1)
          {

            if (substr($criterio,$cont-1,1)==" " || $cont==strlen($criterio))
            {

              $fin=$cont;
              if ($cont==strlen($criterio))
              {

                $palabra[$palabra_actual]=substr($criterio,$inicio_p-1,$fin-$inicio_p+1);
              } else {
                $palabra[$palabra_actual]=substr($criterio,$inicio_p-1,$fin-$inicio_p);
              }

              $query=$query."((menu_alimentos.nombre_alimento like '%".$palabra[$palabra_actual]."%')";

              $query=$query.") ".$tipo_b." ";

              $palabra_actual=$palabra_actual+1;
              $inicio_p=$cont+1;
              if ($palabra_actual==11)
                      {
                break;
                      }
            }
          }
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";

        }
?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <form name="busquedas" action="<?=$_SERVER['PHP_SELF'];?>" method="GET">
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de menús<br></b><small>Búsqueda de Platos por ingredientes</small></font>
                <font size=1 face="<?= $fuente_general; ?>"><br><br>
                Buscar: <input type="text" style="font-size: 10px" size=50 name="pu" value="<?=$pu?>"> <input type="submit" style="font-size: 10px" value="Buscar">
                <input type="radio" value="OR" name="tipo_b"<?php  if ($tipo_b=="OR") { print " checked"; } ?>> Cualquiera de las palabas&nbsp;&nbsp; <input type="radio" value="AND" name="tipo_b"<?php  if ($tipo_b=="AND") { print " checked"; } ?>> Todas las palabras</font><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                </form>
                        <?php 
                        if (strlen($query)!=0) {
                        
                        $consulta_familia = db_query("SELECT * FROM menu_platos, menu_ingredientes, menu_alimentos WHERE menu_platos.id_plato=menu_ingredientes.id_plato AND menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND$query ORDER BY nombre_plato") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=5 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay platos que contengan ingredientes con la/s palabra/s "<?=$pu?>".</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=1 face="<?=$fuente_general;?>"><b>ID</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Plato</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Ingrediente</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Cantidad</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <form name="listado_platos" action="guardar_cambios_platos.php" method="POST">
                        <input type="hidden" name="pu" value="<?=$pu?>">
                        <input type="hidden" name="tipo_b" value="<?=$tipo_b;?>">
                        <input type="hidden" name="id_ingrediente_cambio" value="0">
                        <input type="hidden" name="id_alimento_nuevo" value="0">
                        <input type="hidden" name="id_cantidad_nueva" value="0">
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        // comprueba si hay articulos
                        $id_plato_actual=$datos_familia["id_plato"];
                        $consulta2="SELECT * FROM menu_calendario WHERE comida1=$id_plato_actual OR comida2=$id_plato_actual OR comida3=$id_plato_actual OR comida4=$id_plato_actual OR merienda1=$id_plato_actual OR merienda2=$id_plato_actual";
                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_plato"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar platos que no se encuentren en la composición de los menús.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        $nombre_plato=$datos_familia["nombre_plato"];
                                if (strlen($nombre_plato)!=0) {
                                        $nombre_plato=app_url_text_utf8($nombre_plato);
                                        }
$edad_ini_actual=$datos_familia["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_familia["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$id_plato_actual;?></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_plato;?> <?=$edades;?></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="ingrediente_<?=$datos_familia["id_ingrediente"];?>" size=1 style="font-size: 10px" onchange="cambiar_ingrediente(<?=$datos_familia["id_ingrediente"];?>,<?=$datos_familia["id_alimento"];?>);"><?php 
                                $busca_ingredientes=db_query("SELECT * FROM menu_alimentos ORDER BY nombre_alimento;");
                                while($ingredientes=db_fetch_array($busca_ingredientes)){
                                ?><option value="<?=$ingredientes["id_alimento"]?>"<?php  if ($datos_familia["id_alimento"]==$ingredientes["id_alimento"]) { print " selected"; } ?>><?=$ingredientes["nombre_alimento"];?></option><?php 
                                }
                                ?></select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="cantidad_<?=$datos_familia["id_ingrediente"];?>" value="<?=$datos_familia["cantidad_racion"];?>" style="font-size: 10px; text-align: right" size=8 onblur="cambiar_cantidad(<?=$datos_familia["id_ingrediente"];?>,<?=$datos_familia["cantidad_racion"];?>)"></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a target="editar_platos" href="modificar_plato.php?id_plato=<?=$datos_familia["id_plato"];?>">Modificar</a> | <a target="editar_platos", href="composicion_plato.php?id_plato=<?=$datos_familia["id_plato"];?>">Composición</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </form>
                        <?php 
                        } else {
                        ?>
                        <tr>
                        <td align="center" bgcolor="#ffffff"><br><br>Introduzca el ingrediente que quiere buscar<br><br><br></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_menu.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">
function cambiar_ingrediente(id_ingrediente,ingrediente_inicial) {
eval("dato=document.listado_platos.ingrediente_"+id_ingrediente+".value");
if (dato!=ingrediente_inicial) {
        if (confirm("Se va a cambiar el alimento seleccionado en el plato.\n\n¿Estás seguro?")) {
                document.listado_platos.id_ingrediente_cambio.value=id_ingrediente;
                document.listado_platos.id_alimento_nuevo.value=dato;
                document.listado_platos.id_cantidad_nueva.value=0;
                document.listado_platos.submit();
                }
}
}

function cambiar_cantidad(id_ingrediente,cantidad_inicial) {
// Comprueba si el número es correcto
eval("dato=document.listado_platos.cantidad_"+id_ingrediente+".value");
if (dato!=cantidad_inicial) {
if (isNaN(dato)) {
        alert("La cantidad debe ser un valor númerico");
        } else {
        if (confirm("Se va a cambiar la ración del alimento seleccionado en el plato.\n\n¿Estás seguro?")) {
                document.listado_platos.id_ingrediente_cambio.value=id_ingrediente;
                document.listado_platos.id_cantidad_nueva.value=dato;
                document.listado_platos.id_alimento_nuevo.value=0;
                document.listado_platos.submit();
                }
        }
}
}

</script>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
