<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>


<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<style type="text/css">
<!--
body {  background-color: <?=$fondo_web;?>; font-family: <?=$fuente_general;?>; font-size: x-small; font-style: normal; line-height: normal; font-weight: normal; font-variant: normal; color: <?=$color_fuente;?>; text-decoration: none;}
a {  font-style: normal; font-weight: normal; color: <?=$color_fuente;?>; text-decoration: none}
a:hover {  font-style: normal; font-weight: normal; text-decoration: underline}

.opciones_menu
{
a {font-style: normal; font-weight: normal; color: <?=$color_fuente_opciones?>; text-decoration: none}
a:hover {font-style: normal; font-weight: normal; text-decoration: underline}
}

.submenuStyle {
    position: absolute;
    visibility: hidden;
}
.menuStyle {
        visibility: visible;
}
.m
{
    FONT-SIZE: x-small;
    COLOR: <?=$color_fuente_menu;?>;
    FONT-FAMILY: <?=$fuente_general;?>;
    TEXT-DECORATION: none
}

.menu
{
a {font-style: normal; font-weight: normal; color: <?=$color_fuente_menu;?>; text-decoration: none}
a:hover {font-style: normal; font-weight: normal; text-decoration: underline}
}

.formulario_tipo {font-size: 10px}

#capa1{position:absolute; top:250px; left:100px; width:10px; height:10px; clip:rect(0px,10px,10px,0px); background-color:<?=$fondo_web;?>; layer-background-color:#<?=$fondo_web;?>; visibility: hidden}

-->
</style>
<?php  require("../configuracion/scripts/scripts.php"); ?>
        <style type="text/css">
                .objMovible{position:absolute;cursor:pointer;z-index:1}
        </style>
<script language="JavaScript">

var NS4 = (document.layers);    // Which browser?
var IE4 = (document.all);

var win = window;    // window to search.
var n   = 0;

function findInPage(str) {

  var txt, i, found;

  if (str == "")
    return false;

  // Find next occurance of the given string on the page, wrap around to the
  // start of the page if necessary.

  if (NS4) {

    // Look for match starting at the current point. If not found, rewind
    // back to the first match.

    if (!win.find(str))
      while(win.find(str, false, true))
        n++;
    else
      n++;

    // If not found in either direction, give message.

    if (n == 0)
      alert("Not found.");
  }

  if (IE4) {
    txt = win.document.body.createTextRange();

    // Find the nth match from the top of the page.

    for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
      txt.moveStart("character", 1);
      txt.moveEnd("textedit");
    }

    // If found, mark it and scroll it into view.

    if (found) {
      txt.moveStart("character", -1);
      txt.findText(str);
      txt.select();
      txt.scrollIntoView();
      n++;
    }

    // Otherwise, start over at the top of the page and find first match.

    else {
      if (n > 0) {
        n = 0;
        findInPage(str);
      }

      // Not found anywhere, give message.

      else
        alert("Texto no encontrado");
    }
  }

  return false;
}

</script>
        <script type="text/javascript">
                //Si el navegador del cliente es Mozilla la variable siguiente valdrá true
                var moz = document.getElementById && !document.all;
                //Flag que indica si estamos o no en proceso de arrastrar el ratón
                var estoyArrastrando = false;
                //Variable para almacenar un puntero al objeto que estamos moviendo
                var dobj;

                function arrastrarRaton(e){
                        if (estoyArrastrando) {
                                newLeft = moz ? e.clientX : event.clientX;
                                newTop = moz ? e.clientY : event.clientY;

                                dobj.style.left = newLeft - parseInt(dobj.style.width)/2;
                                dobj.style.top = newTop + document.body.scrollTop - parseInt(dobj.style.height)/2;
                                return false;
                        }
                }


                function soltarBoton(e) {
                        estoyArrastrando = false;
                }


                function presionarBoton(e) {
                        //Obtenemos el elemento sobre el que se ha presionado el botón del ratón
                        var fobj = moz ? e.target : event.srcElement;

                        // Buscamos el primer elemento en la que esté contenido aquel sobre el que se ha pulsado
                        // que pertenezca a la clase objMovible.
                        while (fobj.tagName.toLowerCase() != "html" && fobj.className != "objMovible") {
                                fobj = moz ? fobj.parentNode : fobj.parentElement;
                        }

                        // Si hemos obtenido un objeto movible...
                        if (fobj.className == "objMovible") {
                                // Activamos el flag para indicar que se empieza a arrastrar
                                estoyArrastrando = true;
                                // Guardamos un puntero al objeto que se está moviendo en la variable global
                                dobj = fobj;

                                // Devolvemos false para no realizar ninguna acción posterior
                                return false;
                        }
                }

                document.onmousedown = presionarBoton;
                document.onmouseup = soltarBoton;
                document.onmousemove = arrastrarRaton;

                document.oncontextmenu=new Function("return false");
        </script>
<script language=JavaScript>
/*
 * This is the function that actually highlights a text string by
 * adding HTML tags before and after all occurrences of the search
 * term. You can pass your own tags if you'd like, or if the
 * highlightStartTag or highlightEndTag parameters are omitted or
 * are empty strings then the default <font> tags will be used.
 */
function doHighlight(bodyText, searchTerm, highlightStartTag, highlightEndTag)
{
  // the highlightStartTag and highlightEndTag parameters are optional
  if ((!highlightStartTag) || (!highlightEndTag)) {
    highlightStartTag = "<font style='color:blue; background-color:yellow;'>";
    highlightEndTag = "</font>";
  }

  // find all occurences of the search term in the given text,
  // and add some "highlight" tags to them (we're not using a
  // regular expression search, because we want to filter out
  // matches that occur within HTML tags and script blocks, so
  // we have to do a little extra validation)
  var newText = "";
  var i = -1;
  var lcSearchTerm = searchTerm.toLowerCase();
  var lcBodyText = bodyText.toLowerCase();

  while (bodyText.length > 0) {
    i = lcBodyText.indexOf(lcSearchTerm, i+1);
    if (i < 0) {
      newText += bodyText;
      bodyText = "";
    } else {
      // skip anything inside an HTML tag
      if (bodyText.lastIndexOf(">", i) >= bodyText.lastIndexOf("<", i)) {
        // skip anything inside a <script> block
        if (lcBodyText.lastIndexOf("/script>", i) >= lcBodyText.lastIndexOf("<script", i)) {
          newText += bodyText.substring(0, i) + highlightStartTag + bodyText.substr(i, searchTerm.length) + highlightEndTag;
          bodyText = bodyText.substr(i + searchTerm.length);
          lcBodyText = bodyText.toLowerCase();
          i = -1;
        }
      }
    }
  }

  return newText;
}


/*
 * This is sort of a wrapper function to the doHighlight function.
 * It takes the searchText that you pass, optionally splits it into
 * separate words, and transforms the text on the current web page.
 * Only the "searchText" parameter is required; all other parameters
 * are optional and can be omitted.
 */
function highlightSearchTerms(searchText, treatAsPhrase, warnOnFailure, highlightStartTag, highlightEndTag)
{
  // if the treatAsPhrase parameter is true, then we should search for
  // the entire phrase that was entered; otherwise, we will split the
  // search string so that each word is searched for and highlighted
  // individually
  if (treatAsPhrase) {
    searchArray = [searchText];
  } else {
    searchArray = searchText.split(" ");
  }

  if (!document.body || typeof(document.body.innerHTML) == "undefined") {
    if (warnOnFailure) {
      alert("Sorry, for some reason the text of this page is unavailable. Searching will not work.");
    }
    return false;
  }

  var bodyText = document.body.innerHTML;
  for (var i = 0; i < searchArray.length; i++) {
    bodyText = doHighlight(bodyText, searchArray[i], highlightStartTag, highlightEndTag);
  }

  document.body.innerHTML = bodyText;
  return true;
}


/*
 * This displays a dialog box that allows a user to enter their own
 * search terms to highlight on the page, and then passes the search
 * text or phrase to the highlightSearchTerms function. All parameters
 * are optional.
 */
function searchPrompt(defaultText, treatAsPhrase, textColor, bgColor)
{
  // This function prompts the user for any words that should
  // be highlighted on this web page
  if (!defaultText) {
    defaultText = "";
  }

  // we can optionally use our own highlight tag values
  if ((!textColor) || (!bgColor)) {
    highlightStartTag = "";
    highlightEndTag = "";
  } else {
    highlightStartTag = "<font style='color:" + textColor + "; background-color:" + bgColor + ";'>";
    highlightEndTag = "</font>";
  }

  if (treatAsPhrase) {
    promptText = "Please enter the phrase you'd like to search for:";
  } else {
    promptText = "Please enter the words you'd like to search for, separated by spaces:";
  }

  searchText = prompt(promptText, defaultText);

  if (!searchText)  {
    alert("No search terms were entered. Exiting function.");
    return false;
  }

  return highlightSearchTerms(searchText, treatAsPhrase, true, highlightStartTag, highlightEndTag);
}

function buscaTexto(treatAsPhrase, textColor, bgColor)
{
  // This function prompts the user for any words that should
  // be highlighted on this web page

  // we can optionally use our own highlight tag values
  if ((!textColor) || (!bgColor)) {
    highlightStartTag = "";
    highlightEndTag = "";
  } else {
    highlightStartTag = "<font style='color:" + textColor + "; background-color:" + bgColor + ";'>";
    highlightEndTag = "</font>";
  }

  searchText = self.document.nuevo_pedido.string.value;

  return highlightSearchTerms(searchText, treatAsPhrase, true, highlightStartTag, highlightEndTag);
}


/*
 * This function takes a referer/referrer string and parses it
 * to determine if it contains any search terms. If it does, the
 * search terms are passed to the highlightSearchTerms function
 * so they can be highlighted on the current page.
 */
function highlightGoogleSearchTerms(referrer)
{
  // This function has only been very lightly tested against
  // typical Google search URLs. If you wanted the Google search
  // terms to be automatically highlighted on a page, you could
  // call the function in the onload event of your <body> tag,
  // like this:
  //   <body onload='highlightGoogleSearchTerms(document.referrer);'>

  //var referrer = document.referrer;
  if (!referrer) {
    return false;
  }

  var queryPrefix = "q=";
  var startPos = referrer.toLowerCase().indexOf(queryPrefix);
  if ((startPos < 0) || (startPos + queryPrefix.length == referrer.length)) {
    return false;
  }

  var endPos = referrer.indexOf("&", startPos);
  if (endPos < 0) {
    endPos = referrer.length;
  }

  var queryString = referrer.substring(startPos + queryPrefix.length, endPos);
  // fix the space characters
  queryString = queryString.replace(/%20/gi, " ");
  queryString = queryString.replace(/\+/gi, " ");
  // remove the quotes (if you're really creative, you could search for the
  // terms within the quotes as phrases, and everything else as single terms)
  queryString = queryString.replace(/%22/gi, "");
  queryString = queryString.replace(/\"/gi, "");

  return highlightSearchTerms(queryString, false);
}


/*
 * This function is just an easy way to test the highlightGoogleSearchTerms
 * function.
 */
function testHighlightGoogleSearchTerms()
{
  var referrerString = "https://www.google.com/search?q=javascript%20highlight&start=0";
  referrerString = prompt("Test the following referrer string:", referrerString);
  return highlightGoogleSearchTerms(referrerString);
}

</script>


</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div class="objMovible" id="tabla_colores" style="padding:0px; border:1px solid;width:232px;height:649px;background-color:#ffffff"><img src="/imagen/colores_cartulina.jpg" border=0></div>
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" align="center">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<?php 

// Calcula fecha limite
        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE (fecha_inicio<='".date("Y-m-d")."' OR (month(fecha_inicio)=".date('n')." AND year(fecha_inicio)=".date('Y').")) AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$curso_seleccionado;
        }

$fecha_time_stamp=time();

$dia_fin_periodo=strtotime(date("Y",$fecha_time_stamp)."-".date("m",$fecha_time_stamp)."-".$datos_curso_actual["dia_fin_pedidos"]);
if ($dia_fin_periodo<$fecha_time_stamp) {
        $mes_fin_perido=intval(date("n",$fecha_time_stamp))+1;
        $ano_fin_periodo=intval(date("Y",$fecha_time_stamp));
        if ($mes_fin_perido==13) {
                $mes_fin_perido=1;
                $ano_fin_periodo=$ano_fin_periodo+1;
                }

        $dia_fin_periodo=strtotime($ano_fin_periodo."-".$mes_fin_perido."-".$datos_curso_actual["dia_fin_pedidos"]);
        $mes_ini_periodo=$mes_fin_perido-1;
        $ano_ini_periodo=$ano_fin_periodo;
        if ($mes_ini_periodo==0) {
                $mes_ini_periodo=12;
                $ano_ini_periodo=$ano_fin_periodo-1;
                }
        $dia_ini_periodo=strtotime($ano_ini_periodo."-".$mes_ini_periodo."-".$datos_curso_actual["dia_fin_pedidos"]);
        } else {
        $mes_ini_periodo=date("n",$fecha_time_stamp)-1;
        $ano_ini_periodo=date("Y",$fecha_time_stamp);
        if ($mes_ini_periodo==0) {
                $mes_ini_periodo=12;
                $ano_ini_periodo=$ano_ini_periodo-1;
                }

        $dia_ini_periodo=strtotime($ano_ini_periodo."-".$mes_ini_periodo."-".$datos_curso_actual["dia_fin_pedidos"]);
        }

// Opciones para poder añadir pedidos despues
if (strlen($_GET["diafin"])!=0) {
  $dia_fin_periodo=strtotime(date("Y",$fecha_time_stamp)."-".date("m",$fecha_time_stamp)."-".$_GET["diafin"]);
} 

$id_pedido_centro = $_GET["id_pedido_centro"];

if (strlen($id_pedido_centro)==0) {
  $id_pedido_centro=0;
}

// Fin opciones pedidos despues

if (date("j")<$datos_curso_actual["dia_fin_pedidos"]) {

$dias_para_preparar=floor(($dia_fin_periodo-$fecha_time_stamp)/86400);

$id_pedido=$_GET["id_pedido"];

if (strlen($id_pedido)==0) {
        $id_aula=$_GET["id_cliente"];
        
        // Comprueba si hay un pedido para este aula hoy
                // echo("SELECT * FROM pedidos_material WHERE id_aula=$id_aula AND fecha_pedido<'".date("Y-m-d",$dia_fin_periodo)."' AND fecha_pedido>='".date("Y-m-d",$dia_ini_periodo)."'");

        $busca_pedido_hoy=db_query("SELECT * FROM pedidos_material WHERE id_aula=$id_aula AND fecha_pedido<'".date("Y-m-d",$dia_fin_periodo)."' AND fecha_pedido>='".date("Y-m-d",$dia_ini_periodo)."' ORDER BY id_pedido DESC");
        if (db_num_rows($busca_pedido_hoy)!=0) {
                        $datos_pedido_hoy=db_fetch_array($busca_pedido_hoy);
                        $id_pedido=$datos_pedido_hoy["id_pedido"];
                } else {
                        db_query("INSERT INTO pedidos_material (id_aula, id_usuario, fecha_pedido) VALUES ('$id_aula', '$idUsuario', '".date("Y-m-d")."');");
                        $id_pedido=db_insert_id();
                }
        }

// Busca articulos genéricos que pueden pedirse

$busca_datos_pedido=db_query("SELECT * FROM pedidos_material WHERE id_pedido=$id_pedido");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

?>
<div class="container">
  <div class="row">
    <div class="col-md-2">
      &nbsp;
    </div>
    <div class="col-md-9">
      <form name="nuevo_pedido">
        <div class="panel panel-default">
          <div class="panel-body text-center">
            <input name="string" type="text" size=35 onChange="n = 0;"> <input class="btn btn-md btn-primary" type="button" onclick="buscaTexto(false)" value="Resaltar texto buscado"> <input type="button" onclick="salir()" value="Cancelar pedido" class="btn btn-md btn-danger"> <input type="button" onclick="guardar()" value="Guardar cambios" class="btn btn-md btn-success">        
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body text-center">
            <h4>Pedido de material para el <?=$datos_aula["nombre"];?></b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?> - Fecha: <?=$fecha_pedido;?></h4>
            <h5>El periodo para tramitar el pedido finaliza el día <?=date("d/m/Y",($dia_fin_periodo-86400))?>, te quedan <?=$dias_para_preparar-1?> días para terminar este pedido.</h5>
            <h5>El pedido se debe rellenar entre el día <?=date("01/m/Y",$dia_fin_periodo);?> y el día <?=date("d/m/Y",($dia_fin_periodo-86400));?></h5>
          </div>
        </div>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
<?php 
        // Busca genéricos
        $busca_detalle=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.ARTICULO_GENERICO<>'' AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=1 GROUP BY tarifa.ARTICULO_GENERICO ORDER BY tarifa.CATEGORIA, tarifa.ARTICULO_GENERICO");

        $contador_filas=0;
        $categoria_actual=0;
        while($datos_detalle=db_fetch_array($busca_detalle)) {
        
        if ($categoria_actual!=$datos_detalle["CATEGORIA"]) {
        
        if ($categoria_actual==0) {
                $coletilla1="<b>Existencias</b><br>Corregir para ajustar<br>a las existencias disponibles.";
                $coletilla2="<b>Cantidad</b><br>Introduce aquí<br>la cantidad que necesitas";
                } else {
                $coletilla1="&nbsp;";
                $coletilla2="&nbsp;";
?>
                </table>
              </div>
            </div>        
          </div>  
<?php 
                }
        
        $categoria_actual=$datos_detalle["CATEGORIA"];
        $busca_nombre_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$categoria_actual");
        if (db_num_rows($busca_nombre_familia)!=0) {
                $datos_familia_actual=db_fetch_array($busca_nombre_familia);
                $nombre_familia_actual=$datos_familia_actual["categoria"];
                }
        $nombre_capa=$contador_filas;
?>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading<?=$nombre_capa?>">
                <div class="row">
                  <div class="col-md-6 text-left"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$nombre_capa?>" aria-expanded="true" aria-controls="collapse<?=$nombre_capa?>"><b><big><big><?=$nombre_familia_actual;?></big></big></b></a></div>
                  <div class="col-md-3 text-center"><?=$coletilla1?></div>
                  <div class="col-md-3 text-center"><?=$coletilla2?></div>
                </div>
            </div>
            <div id="collapse<?=$nombre_capa?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?=$nombre_capa;?>">
              <div class="panel-body">
                <table class="table table-hover">
<?php 
            }

            $contador_filas++;
            $busca_existencias_articulo=db_query("SELECT * FROM existencias_aulas WHERE id_aula=".$id_aula." AND nombre_generico='".$datos_detalle["ARTICULO_GENERICO"]."'");
            if (db_num_rows($busca_existencias_articulo)==0) {
                    $existencias_articulo=number_format(0,2,",",".");
                    } else {
                    $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                    $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                    }

            $nombre_articulo=$datos_detalle["ARTICULO_GENERICO"];

            // Busca pedidos abiertos de este articulo
            $busca_cantidad=db_query("SELECT * FROM detalles_pedido_material WHERE id_pedido=$id_pedido AND generico_solicitado='".$datos_detalle["ARTICULO_GENERICO"]."'");
            if (db_num_rows($busca_cantidad)==0) {
                    $cantidad=number_format(0,2,",",".");
                    $cantidad_dato=0;
                    } else {
                    $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                    $cantidad=number_format($datos_cantidad_articulo["cantidad"],2,",",".");
                    $cantidad_dato=$datos_cantidad_articulo["cantidad"];
                    }
            
            // Busca pedidos pendientes de servir de este articulo
            $pendiente_de_servir="";
           
            $busca_cantidad_pendiente=db_query("SELECT * FROM detalles_pedido_material,pedidos_material WHERE detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_pedido<>$id_pedido AND pedidos_material.id_aula=$id_aula AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado='".$datos_detalle["ARTICULO_GENERICO"]."'");
            
            if (db_num_rows($busca_cantidad_pendiente)!=0) {
                    $cantidad_pendiente_de_servir=0;
                    while($datos_cantidad_pendiente=db_fetch_array($busca_cantidad_pendiente)) {
                            // Busca si existen albaranes parcialmente servidos
                            $cantidad_servida=0;
                            $busca_albaranes_servidos=db_query("SELECT * FROM albaranes_entrega, detalles_albaran_entrega WHERE albaranes_entrega.id_albaran=detalles_albaran_entrega.id_albaran AND detalles_albaran_entrega.id_detalle_pedido=".$datos_cantidad_pendiente["id_detalle"]);
                            while($datos_cantidad_albaranes_servidos=db_fetch_array($busca_albaranes_servidos)) {
                                    $cantidad_servida=$cantidad_servida+$datos_cantidad_albaranes_servidos["cantidad"];
                                    }
                            $cantidad_pendiente_de_servir=$cantidad_pendiente_de_servir+$datos_cantidad_pendiente["cantidad"]-$cantidad_servida;
                            }
                            
                    $pendiente_de_servir=" <font color=red>(".number_format($cantidad_pendiente_de_servir,2,",",".").")</font>";
                    }

            $unidad_medida=$datos_detalle["unidad_medida"];
            if (is_numeric($unidad_medida)) {
                    $unidad_medida="";
                    }
?>
                  <tr>
                    <td class="col-md-6"><input type="hidden" name="nombre_generico_<?=$contador_filas;?>" id="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["ARTICULO_GENERICO"]?>"><?=$nombre_articulo;?></td>
                    <td class="col-md-3"><input type="text" size=5 name="existencias_<?=$contador_filas;?>" id="existencias_<?=$contador_filas;?>" value="<?=$existencias_articulo;?>" style="text-align: right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"> <?=$unidad_medida?></td>
                    <td class="col-md-3"><input type="text" size=5 name="cantidad_<?=$contador_filas;?>" id="cantidad_<?=$contador_filas;?>" value="<?=$cantidad;?>" style="text-align: right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"> <?=$unidad_medida?> <?=$pendiente_de_servir?></td> 
                  </tr>           
<?php 
          }
?>
              </table>
            </div>    
          </div>                       
      </div>   
      <!-- Final grupos --> 
</form>
<form name="nuevo_pedido1" method="POST" action="guarda_pedido.php">
  <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
  <input type="hidden" name="id_pedido_centro" value="<?=$id_pedido_centro?>">
  <input type="hidden" name="nombres_genericos" id="nombres_genericos" value="">
  <input type="hidden" name="existencias" id="existencias" value="">
  <input type="hidden" name="cantidades" id="cantidades" value="">
  <div class="panel panel-default">
    <div class="panel-heading">Observaciones</div>
      <div class="panel-body">
        <textarea  name="observaciones" id="observaciones" rows=5 class="form-control"><?=$observaciones;?></textarea>
      </div>
    </div>  
  </div>                  
  <input type="hidden" name="contador_filas" value="<?=$contador_filas;?>">
</form> 
<br><input type="button" onclick="salir()" value="Cancelar pedido" class="btn btn-md btn-danger"> <input type="button" onclick="guardar()" value="Guardar cambios" class="btn btn-md btn-success"><br><br>
        <script language="Javascript">

        function comprueba_cantidades(fila) {
                var errores=0;
                var mensajes_error="";

                existencias=eval("self.document.nuevo_pedido.existencias_"+fila+".value");
                cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
                nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");
                
                if (existencias!="") {
                                existencias=existencias.replace(",",".");
                                if (isNaN(existencias)) {
                                        mensajes_error=mensajes_error+"Las existencias de '"+nombre_fila+"' deben ser un valor numérico.\u000A";
                                        errores=1;
                                        }
                                }
                if (cantidad!="") {
                                cantidad=cantidad.replace(",",".");
                                if (isNaN(cantidad)) {
                                        mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                        errores=1;
                                        }
                                }

                if (errores==1) {
                        alert("Error:\n\n"+mensajes_error);
                        }
                }
        function guardar() {

                var nombres_genericos_array = "";
                var existencias_array = "";
                var cantidades_array = "";

                var errores = 0;
                var mensajes_error = "";

                for (fila=1;fila<=<?=$contador_filas;?>;fila++) {

                  existencias=$("#existencias_"+fila).val();
                  cantidad=$("#cantidad_"+fila).val();
                  nombre_fila=$("#nombre_generico_"+fila).val();

                  var incluir_array = false;

                  if (existencias!="") {
                                existencias=existencias.replace(",",".");
                                if (isNaN(existencias)) {

                                        mensajes_error=mensajes_error+"Las existencias de '"+nombre_fila+"' deben ser un valor numérico.\u000A";

                                        errores=1;
                                        
                                        } else {

                                          if (existencias!=0) {
                                            incluir_array = true;                                            
                                          }

                                        }
                                }
                  if (cantidad!="") {
                                cantidad=cantidad.replace(",",".");
                                if (isNaN(cantidad)) {
                                        
                                        mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                        errores=1;                                      

                                        } else {

                                          if (cantidad!=0) {
                                            incluir_array = true;                                            
                                          }

                                        }
                                }
                  if (incluir_array==true) {

                    existencias_array = existencias_array + existencias + '#' ;
                    cantidades_array = cantidades_array + cantidad + '#' ;
                    nombres_genericos_array = nombres_genericos_array + nombre_fila + '#' ;

                  }
                }
                if (errores==1) {

                          alert("Error:\n\n"+mensajes_error);

                        } else {

                          $("#existencias").val(existencias_array);
                          $("#cantidades").val(cantidades_array);
                          $("#nombres_genericos").val(nombres_genericos_array);

                          document.nuevo_pedido1.action="guarda_pedido.php?r=1";
                          document.nuevo_pedido1.submit();
                        }
                        
                }
        function salir() {
                if (confirm("¿Quieres salir y eliminar el pedido actual?")) {
                        window.location.href='eliminar_pedido.php?id_pedido=<?=$id_pedido;?>';
                        }
                }
              
        </script>
        <?php 

        } else {
        ?>
        <h2>Pedidos cerrados</h2>
        <h3>Tu pedido del més actual está siendo tramitado.</h3>
        <h3>El nuevo periodo para la realización de pedidos se inicia el día <?=date("01/m/Y",$dia_fin_periodo);?> y se cierra el día <?=date("d/m/Y",$dia_fin_periodo);?>.</h3>
        <?php 
        }
      ?>
    </div>
    <div class="col-md-1">
      &nbsp;
    </div>    
  </div>
 <?php require("../configuracion/parte_inferior.php"); ?>
