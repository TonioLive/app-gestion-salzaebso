<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_cocina.php"); ?>
<?php  require("inc/config_platos.php"); ?>
<?php 


$array_valores_alergias=array();

// Valores por defecto
$instrucciones=array("Se introduce el alimento en los equipos isotermos debidamente protegidos y se mantendran los alimentos con tratamiento térmico a > 65ºC.","Se introduce el alimento en los equipos isotermos debidamente protegidos y se mantendran los alimentos en frío  a <8ºC durante el transporte.");

// Valores de la tabla

$id_plato=$_GET["id_plato"];

if (strlen($id_plato)!=0) {

$consulta_articulo = db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato") or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$grupo=$datos_articulo["grupo"];
if (strlen($grupo)==0) {
        $grupo=0;
}

$nombre_plato=$datos_articulo["nombre_plato"];
if (strlen($nombre_plato)!=0) {
        $nombre_plato=app_url_text_utf8($nombre_plato);
        }
$tipo_plato=$datos_articulo["tipo_plato"];
$edad_ini_plato=$datos_articulo["edad_ini_plato"];
$edad_fin_plato=$datos_articulo["edad_fin_plato"];

$nombre_recipiente=$datos_articulo["nombre_recipiente"];
if (strlen($nombre_recipiente)!=0) {
        $nombre_recipiente=app_url_text_utf8($nombre_recipiente);
        }
$raciones=$datos_articulo["raciones"];
$instrucciones_preparacion=$datos_articulo["instrucciones_preparacion"];
if (strlen($instrucciones_preparacion)!=0) {
        $instrucciones_preparacion=app_url_text_utf8($instrucciones_preparacion);
        }
$instrucciones_servir=$datos_articulo["instrucciones_servir"];
if (strlen($instrucciones_servir)!=0) {
        $instrucciones_servir=app_url_text_utf8($instrucciones_servir);
        }
$instrucciones_conservacion=$datos_articulo["instrucciones_conservacion"];
if (strlen($instrucciones_conservacion)!=0) {
        $instrucciones_conservacion=app_url_text_utf8($instrucciones_conservacion);
        }
$instrucciones_transporte=$datos_articulo["instrucciones_transporte"];
if (strlen($instrucciones_transporte)!=0) {
        $instrucciones_transporte=app_url_text_utf8($instrucciones_transporte);
        }        
        
  $gluten=$datos_articulo["gluten"];
  $leche=$datos_articulo["leche"];
  $huevos=$datos_articulo["huevos"];
  $crustaceos=$datos_articulo["crustaceos"];
  $moluscos=$datos_articulo["moluscos"];
  $pescados=$datos_articulo["pescados"];
  $cacahuete=$datos_articulo["cacahuete"];
  $frutos_secos=$datos_articulo["frutos_secos"];
  $mostaza_sesamo=$datos_articulo["mostaza_sesamo"];
  $sulfitos=$datos_articulo["sulfitos"];
  $soja=$datos_articulo["soja"];
  $apio=$datos_articulo["apio"];


$texto_encabezado="Editar";
} else {
$id_plato=0;
$texto_encabezado="Nuevo";
}

// Alergenos

// Comprueba platos
$busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato") or die(db_error());

$numero_ingredientes=db_num_rows($busca_ingredientes);

while($ingredientes=db_fetch_array($busca_ingredientes)) {

	for ($c=0;$c<count($array_alergias);$c++) {
		if ($ingredientes[$array_alergias[$c]]==1) {
		$array_valores_alergias[$c]=1;
		}
	}
}



// Nombre foto
$nombre_foto=$id_plato.".jpg";

if (!file_exists($upload_path_platos."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      }

?>
<script language="Javascript">

function guarda_como() {
        if (confirm("¿Deseas guardar los cambios como un plato nuevo con los ingredientes de en este?")) {
                self.document.datos_familia.id_plato.value="0";
                self.document.datos_familia.duplicar.value="<?=$id_plato;?>";

                comprueba_y_envia();
                }
        }

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_plato.value=="") {
                errores=errores+"Falta introducir la nombre del plato.\n";
                }
        if (Number(self.document.datos_familia.edad_ini_plato.value)>Number(self.document.datos_familia.edad_fin_plato.value)) {
                errores=errores+"La edad mínima no puede ser mayor que la edad máxima.\n";
                }
        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos_familia.submit();
                }
        }

function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }

function cambiar_foto() {
                <?php 
                if ($id_plato!=0) {
                ?>
        window.open("upload_documento.php?id_plato=<?=$id_plato?>",'guardar','width=600,height=500');
        <?php 
        } else {
        ?>
        alert("Primero debes guardar el nuevo plato, una vez hecho esto podrás editarlo y añadir la foto.");
        <?php 
        } 
        ?>
        } 
        
</script>
<?php 

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> plato<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_plato1.php">
                        <input type="hidden" name="id_plato" value="<?=$id_plato?>">
                        <input type="hidden" name="duplicar" value="0">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_plato" value="<?=$nombre_plato?>" size=60 style="font-size: 10px"></font></td>
                          <td rowspan=7 align=center bgcolor="#ffffff" valign="middle"><a href="javascript:ampliar('/fotos_platos/<?=$nombre_foto?>')"><img id="foto_plato" src="/fotos_platos/<?=$nombre_foto?>" width="200" border=0></a><br><br>
                          <input type="button" onclick="cambiar_foto();" value="Cambiar Foto"></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Grupo:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="grupo" style="font-size: 10px">
                          <?php 
                          foreach ($grupos as $claveGrupo=>$nombreGrupo) {
                          ?>
                          <option value="<?=$claveGrupo?>"<?php  if ($claveGrupo==$grupo) { print " selected"; } ?>><?=$nombreGrupo?></option>
                          <?php 
                                }
                          ?>
                          </select></font></td> 
                        </tr>                        
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Tipo plato:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="tipo_plato" style="font-size: 10px">
                          <?php 
                          for ($c1=0;$c1<count($tipos_plato);$c1++) {
                          ?>
                          <option value="<?=$c1?>"<?php  if ($c1==$tipo_plato) { print " selected"; } ?>><?=$tipos_plato[$c1]?></option>
                          <?php 
                                }
                          ?>
                          </select></font></td> 
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Edad mínima plato:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="edad_ini_plato" style="text-align: right; font-size: 10px">
                          <?php 
                          for ($c1=0;$c1<=1.92;$c1=$c1+0.083) {
                          ?>
                          <option value="<?=round($c1,3)?>"<?php  if (round($c1,3)==$edad_ini_plato) { print " selected"; } ?>><?=round(($c1*12),0)?> meses</option>
                          <?php 
                                }
                          for ($c1=2;$c1<18;$c1++) {
                          ?>
                          <option value="<?=$c1?>"<?php  if ($c1==$edad_ini_plato) { print " selected"; } ?>><?=$c1?> años</option>
                          <?php 
                                }
                          ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Edad máxima plato:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="edad_fin_plato" style="text-align: right; font-size: 10px">
                          <?php 
                          for ($c1=0;$c1<=1.92;$c1=$c1+0.083) {
                          ?>
                          <option value="<?=round($c1,3)?>"<?php  if (round($c1,3)==$edad_fin_plato) { print " selected"; } ?>><?=round(($c1*12),0)?> meses</option>
                          <?php 
                                }
                          for ($c1=2;$c1<18;$c1++) {
                          ?>
                          <option value="<?=$c1?>"<?php  if ($c1==$edad_fin_plato) { print " selected"; } ?>><?=$c1?> años</option>
                          <?php 
                                }
                          ?>
                          <option value="100"<?php  if ($edad_fin_plato==100) { print " selected"; } ?>>>18 años</option>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre recipiente:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_recipiente" value="<?=$nombre_recipiente?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nº de raciones por recipiente:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="raciones" style="text-align: right; font-size: 10px">
                          <?php 
                          for ($c_raciones=1;$c_raciones<=250;$c_raciones++) {
                          ?>
                          <option value="1"<?php  if ($raciones==$c_raciones) { print " selected"; } ?>><?=$c_raciones?></option>
                          <?php 
                          }
                          ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Instrucciones de preparación:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><textarea name="instrucciones_preparacion" cols=75 rows=8 style="font-size: 11px; font-family: Arial"><?=$instrucciones_preparacion?></textarea></font></td>
                        </tr>
                        <tr valign="top">        
                                                <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Instrucciones transporte:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><textarea name="instrucciones_transporte" cols=75 rows=8 style="font-size: 11px; font-family: Arial"><?=$instrucciones_transporte?></textarea></font></td>                       
                          <td align="left" bgcolor="#ffffff" rowspan="3"><font size=2 face="<?= $fuente_general; ?>"><b>Alérgenos:</b></b></font><br><br><font size=1 face="<?= $fuente_general; ?>">
                          <?php 
                          // alergias
                                        for ($c=0;$c<count($array_alergias);$c++) {
                          ?>
                                        <input disabled type="checkbox" name="<?=$array_alergias[$c]?>" value="1"<?php  if ($array_valores_alergias[$c]==1) { print " checked"; } ?>><?=$array_nombres_alergias[$c];?><br>
                          <?php 
                          }
                          ?>
                          <!--
                          <input type="checkbox" value=1 name="gluten"<?php  if ($gluten==1) { print " checked"; } ?>> Cereales con gluten<br />
                          <input type="checkbox" value=1 name="leche"<?php  if ($leche==1) { print " checked"; } ?>> Leche y derivados<br />
                          <input type="checkbox" value=1 name="huevos"<?php  if ($huevos==1) { print " checked"; } ?>> Huevos y derivados<br />
                          <input type="checkbox" value=1 name="crustaceos"<?php  if ($crustaceos==1) { print " checked"; } ?>> Crustaceos<br />
                          <input type="checkbox" value=1 name="moluscos"<?php  if ($moluscos==1) { print " checked"; } ?>> Moluscos<br />
                          <input type="checkbox" value=1 name="pescados"<?php  if ($pescados==1) { print " checked"; } ?>> Pescados y derivados<br />
                          <input type="checkbox" value=1 name="cacahuete"<?php  if ($cacahuete==1) { print " checked"; } ?>> Cacahuete<br />
                          <input type="checkbox" value=1 name="frutos_secos"<?php  if ($frutos_secos==1) { print " checked"; } ?>> Frutos secos<br />
                          <input type="checkbox" value=1 name="mostaza_sesamo"<?php  if ($mostaza_sesamo==1) { print " checked"; } ?>> Mostaza, sésamo<br />
                          <input type="checkbox" value=1 name="sulfitos"<?php  if ($sulfitos==1) { print " checked"; } ?>> Sulfitos y SO2<br />
                          <input type="checkbox" value=1 name="soja"<?php  if ($soja==1) { print " checked"; } ?>> Soja<br />
                          <input type="checkbox" value=1 name="apio"<?php  if ($apio==1) { print " checked"; } ?>> Apio<br />
                          -->
                          </font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Instrucciones para servir:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><textarea name="instrucciones_servir" cols=75 rows=8 style="font-size: 11px; font-family: Arial"><?=$instrucciones_servir?></textarea></font></td>
                          
                        </tr>
                        <tr valign="top">
                        <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Instrucciones conservación:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">
                                        <input type="radio" id="instrucciones_conservacion1" name="instrucciones_conservacion" value=""<?php  if ($instrucciones_conservacion=="") { ?> checked<?php  } ?>> Sin instrucciones<br />
                                        <input type="radio" id="instrucciones_conservacion2" name="instrucciones_conservacion" value="<?=$instrucciones[0];?>"<?php  if ($instrucciones_conservacion==$instrucciones[0]) { ?> checked<?php  } ?>><?=$instrucciones[0];?><br />
                                        <input type="radio" id="instrucciones_conservacion3" name="instrucciones_conservacion" value="<?=$instrucciones[1];?>"<?php  if ($instrucciones_conservacion==$instrucciones[1]) { ?> checked<?php  } ?>><?=$instrucciones[1];?><br />
                                </font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input style="font-size: 10px" type="button" value="Guardar como..." onclick="guarda_como();"> <input type="button" value="Cancelar" onclick="window.location.href='gestion_platos.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
