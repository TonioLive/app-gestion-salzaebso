<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php");

$formulario_name="escuelas";
$formulario_accion="nueva_escuela1.php";

$url_retorno="listado_escuelas.php";

$id_competencia=$_GET["id_competencia"];

if (strlen($id_competencia)==0) {
        $id_competencia=0;
        $texto_mostrar="Nueva";
        $query="";
        } else {
        $texto_mostrar="Editar";
        // Busca datos escuela
        $busca_escuela=db_query_params('SELECT * FROM escuelas_competencia WHERE id_competencia=?', 's', array($id_competencia)) or die(db_error());
        $datos_escuela=db_fetch_array($busca_escuela);
        $nombre_escuela=$datos_escuela["nombre_escuela"];
        $latitud=$datos_escuela["latitud"];
        $longitud=$datos_escuela["longitud"];
        $coordenadas="($latitud,$longitud)";
        $calle=$datos_escuela["calle"];
        $poblacion=$datos_escuela["poblacion"];
        $cp=$datos_escuela["cp"];
        $municipal=$datos_escuela["municipal"];
        $query=$datos_escuela["calle"]." ".$datos_escuela["cp"]." ".$datos_escuela["Poblacion"];
        }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=ES"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  var errores = "";
  var query = "";

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom:15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.cp.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
            codeAddress();
            }
  }

  function codeAddress() {
    var address = query;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        guarda_dato(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        guarda_dato("");
        // alert("No se ha encontrado la dirección en el mapa: " + status + " " + consulta);
        // errores = errores+"No se ha encontrado la dirección en el mapa: " + status + "Dirección: " + consulta + "<br>";
      }
    });
  }

  function localiza() {

        query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.cp.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.cp.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                codeAddress();
                } else {
                alert("Escribe antes la dirección: Cale, Número, Código postal y población");
                }

        }

function guarda_dato(coordenadas) {

        document.<?=$formulario_name?>.coordenadas.value=coordenadas;

        }

</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="initialize();">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align="left">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">

<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.nombre_escuela.value=="") { mensajes_error=mensajes_error+"Falta introducir el nombre de la escuela infantil.\u000A";errores=1; }

                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.cp.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.cp.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        if (self.document.<?=$formulario_name;?>.coordenadas.value=="") { mensajes_error=mensajes_error+"No se han podido obtener las coordenadas de la escuela infantil. Comprueba si la dirección es correcta\u000A";errores=1; }
                        } else {
                        mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
                        
        }
        
function selecciona_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_ano.value;");

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

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino=<?=$formulario_name;?>&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
        
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_competencia" value="<?=$id_competencia;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_mostrar;?> Escuela infantil</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de la escuela</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre de la escuela:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_escuela" value="<?=$nombre_escuela?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Calle y Número:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="calle" value="<?=$calle?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Población:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion" value="<?=$poblacion?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Código postal:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="cp" value="<?=$cp?>" size="10" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Municipal?</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="municipal" value="0"<?php  if ($municipal==0) { print " checked"; } ?>> No <input type="radio" name="municipal" value="1"<?php  if ($municipal==1) { print " checked"; } ?>> Si</font></td>
                        </tr>

                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Coordenadas (Latitud, Longitud):</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="coordenadas" value="<?=$coordenadas?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center"><input type="button" value="Localizar escuela en el mapa" onclick="localiza();"><br><div id="map_canvas" style="width:450; height:300"></div></td>
                        </tr>
                        </table>
                        <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
