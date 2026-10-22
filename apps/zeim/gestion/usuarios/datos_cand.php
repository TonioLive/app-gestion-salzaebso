<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

mt_srand((float)microtime()*1000000);

$id_usu=$_GET["id_usu"];

$dato=$_GET["dato"];

if ($dato=="s" && $nivel_acceso<3)
  {
  header("Location: ".$url_web."/inicio_sesion/acceso_denegado.htm");
  } 

$query=array('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usu));

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta_usuario=db_query_params($query[0], $query[1], $query[2]) or die(db_error());

if (db_num_rows($consulta_usuario) != 0) {

$rsslc=db_fetch_array($consulta_usuario);

$id_tabla_usu=$rsslc["id_usuario"];

if ((strlen($rsslc["fecha_nacimiento"])!=0) AND ($rsslc["fecha_nacimiento"]!="0000-00-00")) {
        $dia_i=date("d",strtotime($rsslc["fecha_nacimiento"]));
        $mes_i=date("m",strtotime($rsslc["fecha_nacimiento"]));
        $ano_i=date("Y",strtotime($rsslc["fecha_nacimiento"]));
        
        // Calcula edad
                        $nacimiento=explode( '-', $rsslc["fecha_nacimiento"]);
                        $edad=date("Y")-$nacimiento[0];
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime(date("Y/m/d"));
                        if (($cumple-$hoy)>0) {
                                $edad=$edad-1;
                                }

        $edad=" Edad: $edad años.";
        
        } else {
        $dia_i="";
        $mes_i="";
        $ano_i="";
        $edad="";
        }

if ((strlen($rsslc["fecha_prevencion"])!=0) AND ($rsslc["fecha_prevencion"]!="0000-00-00")) {
        $dia_prl=date("d",strtotime($rsslc["fecha_prevencion"]));
        $mes_prl=date("m",strtotime($rsslc["fecha_prevencion"]));
        $ano_prl=date("Y",strtotime($rsslc["fecha_prevencion"]));
        } else {
        $dia_prl="";
        $mes_prl="";
        $ano_prl="";
        }

$titulacion=$rsslc["titulacion"];
if (strlen($titulacion)!=0) {
                        $titulacion=stripslashes($titulacion);
                        }
$especialidad=$rsslc["especialidad"];
if (strlen($especialidad)!=0) {
                        $especialidad=stripslashes($especialidad);
                        }

$curriculum=$rsslc["curriculum"];
if (strlen($curriculum)!=0) {
                        $curriculum=stripslashes($curriculum);
                        }
        
        
$formulario_name="datospers";
?>
<html>

<head>
<title>Formulario de Edición de Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
</style>
<SCRIPT language=JAVASCRIPT>
<!--
self.focus();
//-->
</SCRIPT>
</head>

<body bgcolor="<?= $color_lat; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background=<?= $back_image; ?> bgproperties="fixed">

<form method="post" action="modif_cand.php" name="<?=$formulario_name;?>">
<input type="hidden" name="Cod" value="<?= $id_usu; ?>">
<input type="hidden" name="id_tabla_usu" value="<?= $id_tabla_usu; ?>">
<input type="hidden" name="Dato" value="<?= $dato; ?>">
<div align="center">
<p><font color="<?= $color_fuente; ?>">
<font size="1"><b>Desde esta página puedes modificar los datos personales del usuario.<br>Pulsa el botón "Modificar los datos" para guardar los cambios realizados.</b></font>
<hr align="center">
</font></p>
</div><div align="center">
<table width="95%" border="0" cellspacing="1" cellpadding="0" bgCOLOR="<?= $color_lat; ?>" face="<?= $fuente_preins; ?>">
    <tr valign="top">      
      <td align="right">
      <font face="<?= $fuente_preins; ?>" size="1" COLOR="<?= $color_fuente; ?>" face="<?= $fuente_preins; ?>"><b>Nombre:</b></font>
      </td>
      <td colspan="3">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="nombre" value="<?= $rsslc["Nombre"]; ?>"></font>
      </td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font color="<?= $color_fuente; ?>"><font face="<?= $fuente_preins; ?>" size="1"><b>Primer Apellido: </b></font></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="apellido1" value="<?= $rsslc["Apellido1"]; ?>">
      </font>
      </td>
      <td align="right">
      <font color="<?= $color_fuente; ?>"><font face="<?= $fuente_preins; ?>" size="1"><b>2º Apellido:</b></font></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="apellido2" value="<?= $rsslc["Apellido2"]; ?>"></font></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <b><font face="<?= $fuente_preins; ?>" size="1" COLOR="<?= $color_fuente; ?>">DNI/NIE:</font></b></td>
      <td colspan=4>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="nif" value="<?= $rsslc["DNI"]; ?>"></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <b><font face="<?= $fuente_preins; ?>" size="1" COLOR="<?= $color_fuente; ?>">NISS:</font></b></td>
      <td colspan=4>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="nss" value="<?= $rsslc["NSS"]; ?>"></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Fecha de nacimiento:</b></font></td><td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">
<select name="fechanac_dia" size=1 style="font-size=10">
<option value="--">--</option>
<?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{
  if ($c_dia<10)
  {
    $dia_a="0".$c_dia;
  }
    else
  {
    $dia_a=$c_dia;
  } 
    ?>
    <option value="<?php    echo $dia_a; ?>"<?php  if (intval($dia_a)==intval($dia_i)) { print " selected"; } ?>><?= $dia_a; ?></option>
    <?php  
} 
?>
</select>/<select name="fechanac_mes" size=1 style="font-size=10">
<font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">
<option value="--">--</option>
<?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{
  if ($c_mes<10)
  {
    $mes_a="0".$c_mes;
  }
    else
  {
    $mes_a=$c_mes;
  } 
?>
    <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($mes_i)) { print " selected"; } ?>><?= $mes_a; ?></option>
<?php 
} 
?>
</select>/<select name="fechanac_ano" size=1 style="font-size=10">
<option value="----">----</option>
<?php 
for ($c_ano=strftime("%Y"); $c_ano>=1899; $c_ano=$c_ano-1)
{
  if ($c_ano<10)
  {
    $ano_a="0".$c_ano;
  }
    else
  {
    $ano_a=$c_ano;
  }
    ?>
    <option value="<?php    echo $ano_a; ?>"<?php    if (intval($ano_a)==intval($ano_i)) { print " selected"; } ?>><?= $ano_a; ?></option>
    <?php  
}
?>
</select>
<font size=1><a href="javascript:selecciona_fecha('fechanac','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a><?=$edad?></font></font></td>
<td align="right">
<font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Sexo:</b></font></td>
<td>
<font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><select style="font-size: 10" name="sexo" size="1">
<?php  
if ($rsslc["sexo"]=="V")
{
  print "<option value='V' selected>Hombre</option>";
  print "<option value='H'>Mujer</option>";
}
  else
{
  print "<option value='V'>Hombre</option>";
  print "<option value='H' selected>Mujer</option>";
} 

?>
      </select> </font></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <b><font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">Teléfono 1:</font></b></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="telefono1" value="<?= $rsslc["telefono1"]; ?>"></td>
      <td align="right">
      <b><font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">Tel. móvil:</font></b></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="movil" value="<?= $rsslc["movil"]; ?>"></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Teléfono 2:</b></font></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="telefono2" value="<?= $rsslc["telefono2"]; ?>"></td>
      <td></td>
      <td></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>E-Mail:</b></font></td>
      <td colspan="3">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="email" size="55" value="<?= $rsslc["email"]; ?>"<?php  if ($rsslc["nivel_acceso"]>$nivel_acceso) { print " readonly";} ?>></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font color="<?= $color_fuente; ?>" face="<?= $fuente_preins; ?>" size="1"><b>Dirección: </b></font>
      </td>
      <td colspan="3">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">
      <input type="text" style="font-size: 10" name="direccion" size="55" value="<?= $rsslc["Direccion"]; ?>"></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <b><font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">Código postal: </font></b></td>
      <td colspan=4>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="cp" value="<?= $rsslc["cp"]; ?>"></td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Población: </b></font></td>
      <td>
      <font size=1 COLOR="<?= $color_fuente; ?>" face="<?= $fuente_preins; ?>"><input type="text" style="font-size: 10" name="poblacion" value="<?= $rsslc["Poblacion"]; ?>"></font></td>
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Provincia:</b></font></td>
      <td>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><select name="provincia" class="defaut" style="font-size: 10px">
                                <option value="">- Seleccione la provincia donde reside -</option>
                                <?php 
                                $provincia=$usuario_provincia;
                                $busca_provincia=db_query("SELECT * FROM provincias ORDER BY id_provincia");
                                if (strlen($provincia)==0) { $provincia=46; }
                                while($lista_provincias=db_fetch_array($busca_provincia)) {
                                        print "<option value=".$lista_provincias["id_provincia"];
                                                if (intval($lista_provincias["id_provincia"])==intval($rsslc["Provincia"])) { print " selected"; }
                                                                           print ">".$lista_provincias["provincia"]."</option>";
                                                                        }
                                ?>
                                </select>
                                </td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Entrega ficha de prevención:</b></font></td>
      <td colspan=4>
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">
<select name="prl_dia" size=1 style="font-size=10">
<option value="--">--</option>
<?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{
  if ($c_dia<10)
  {
    $dia_a="0".$c_dia;
  }
    else
  {
    $dia_a=$c_dia;
  }
    ?>
    <option value="<?php    echo $dia_a; ?>"<?php  if (intval($dia_a)==intval($dia_prl)) { print " selected"; } ?>><?= $dia_a; ?></option>
    <?php 
}
?>
</select>/<select name="prl_mes" size=1 style="font-size=10">
<font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">
<option value="--">--</option>
<?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{
  if ($c_mes<10)
  {
    $mes_a="0".$c_mes;
  }
    else
  {
    $mes_a=$c_mes;
  }
?>
    <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($mes_prl)) { print " selected"; } ?>><?= $mes_a; ?></option>
<?php 
}
?>
</select>/<select name="prl_ano" size=1 style="font-size=10">
<option value="----">----</option>
<?php 
for ($c_ano=strftime("%Y"); $c_ano>=1899; $c_ano=$c_ano-1)
{
  if ($c_ano<10)
  {
    $ano_a="0".$c_ano;
  }
    else
  {
    $ano_a=$c_ano;
  }
    ?>
    <option value="<?php    echo $ano_a; ?>"<?php    if (intval($ano_a)==intval($ano_prl)) { print " selected"; } ?>><?= $ano_a; ?></option>
    <?php 
}
?>
</select>
<font size=1><a href="javascript:selecciona_fecha('prl','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
</tr>
    <tr valign="top">
      <td align="right">
      <font face="<?= $fuente_preins; ?>" size="1" COLOR="<?= $color_fuente; ?>" face="<?= $fuente_preins; ?>"><b>Titulación:</b></font>
      </td>
      <td colspan="3">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><select style="font-size: 10" name="titulacion">
      <option value=""></option>
       <?php 

       $encontrado=false;
       $busca_tipos_cat=db_query("SELECT * FROM personal_cat_laborales ORDER BY id_categoria");

                                while($tipos_cat=db_fetch_array($busca_tipos_cat)) {
                                ?>
                                <option value="<?=$tipos_cat["nombre_categoria"]?>"<?php  if ($tipos_cat["nombre_categoria"]==$titulacion) {
                                print " selected";
                                $encontrado=true;
                                } ?>><?=$tipos_cat["nombre_categoria"]?></option>
                                <?php 
                                }

        if ($encontrado==false) {
        ?>
        <option value="<?=$titulacion?>"><?=$titulacion?></option>
        <?php 

        }
       ?>
       </select></font>
      </td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font face="<?= $fuente_preins; ?>" size="1" COLOR="<?= $color_fuente; ?>" face="<?= $fuente_preins; ?>"><b>Especialidad:</b></font>
      </td>
      <td colspan="3">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><select style="font-size: 10" name="especialidad">
      <option value=""></option>
       <?php 

       $encontrado=false;
       $busca_tipos_cat=db_query("SELECT * FROM personal_especialidades ORDER BY id_especialidad");

                                while($tipos_cat=db_fetch_array($busca_tipos_cat)) {
                                ?>
                                <option value="<?=$tipos_cat["nombre_especialidad"]?>"<?php  if ($tipos_cat["nombre_especialidad"]==$especialidad) {
                                print " selected";
                                $encontrado=true;
                                } ?>><?=$tipos_cat["nombre_especialidad"]?></option>
                                <?php 
                                }

        if ($encontrado==false) {
        ?>
        <option value="<?=$titulacion?>"><?=$titulacion?></option>
        <?php 

        }
       ?>
       </select></font>
      </td>
    </tr>
    <tr valign="top">
      <td align="right">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><b>Escribe aquí el curriculum:</b></font>
      </td>
      <td colspan="3" align="left"><font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>">&nbsp;* Tambien puedes copiar y pegar desde otro documento. Al hacerlo se perderá el formato (negrita, cursiva, tipo de letra) y la imágenes.</font></td>
    </tr>
    <tr valign="top">
      <td colspan="4" align="center">
      <font COLOR="<?= $color_fuente; ?>" size="1" face="<?= $fuente_preins; ?>"><textarea style="font-size: 11;font-family: Arial" name="curriculum" cols=99 rows=25><?= $curriculum; ?></textarea></font>
      </td>
    </tr>
</form>
   </table>
    </center></div>
    <div align="center">
<table width="95%" border="0" cellspacing="0" cellpadding="0" bgCOLOR="<?= $color_lat; ?>" face="<?= $fuente_preins; ?>">
   <tr align="center"><td><br>
   <input type="submit" onclick="cambia();" value="Guardar cambios" style="font-size: 10px"><?php  if ($nivel_acceso==4) { ?><input type="button" value="Cambiar contraseña/Tipo acceso" style="font-size: 10px" onclick="window.location.href='config_cand.php?cod=<?= $id_usu; ?>&ran=<?= (mt_rand(0,10000000)/10000000); ?>'" id=button1 name=button1> <?php  } ?>
  </td></tr>
</table>
</div>  
<?php 
} else {
?>
<br><br><p align="center"><font size=3 face="<?=$fuente_general;?>">El usuario no existe</font></p>
<?php 
}
db_close();
?>
<div align="center">
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">
<!--
function cambia() {
                var errores=0;

                var mensajes_error="";

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
function salir_de_aqui() {
                         window.opener.history.go(0);
                         window.close();
                         }
function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
//-->
</script>
 <font face="<?= $fuente_preins; ?>" size="1" face="<?=$fuente_general;?>"><b><a href="javascript:salir_de_aqui()">Cerrar la ventana</a><b></font>
 </small>
</div>
</body>
</html>

