<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
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

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
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
        }
        
$dias_para_preparar=floor(($dia_fin_periodo-$fecha_time_stamp)/86400);

$id_pedido=$_GET["id_pedido"];

if (strlen($id_pedido)==0) {
        $id_aula=$_GET["id_cliente"];
        
        // Comprueba si hay un pedido para este aula hoy
        
        $busca_pedido_hoy=db_query_params('SELECT * FROM pedidos_material WHERE id_aula=? AND fecha_pedido<?', 'ss', array($id_aula, (string)(date("Y-m-d", $dia_fin_periodo))));
        if (db_num_rows($busca_pedido_hoy)!=0) {
                        $datos_pedido_hoy=db_fetch_array($busca_pedido_hoy);
                        $id_pedido=$datos_pedido_hoy["id_pedido"];
                } else {
                        db_query_params('INSERT INTO pedidos_material (id_aula, id_usuario, fecha_pedido) VALUES (?, ?, ?);', 'sss', array((string)($id_aula), (string)($idUsuario), (string)(date("Y-m-d"))));
                        $id_pedido=db_insert_id();
                }
        }

// Busca articulos genéricos que pueden pedirse

$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido));

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);


?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido.php">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido de material para el <?=$datos_aula["nombre"];?></b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?> - Fecha: <?=$fecha_pedido;?></b><br><br>
                                El periodo para tramitar el pedido finaliza el día <?=date("d/m/Y",$dia_fin_periodo)?>, te quedan <?=$dias_para_preparar?> días para terminar este pedido.
                                </font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_detalle=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.ARTICULO_GENERICO<>'' AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=1 GROUP BY tarifa.ARTICULO_GENERICO ORDER BY tarifa.CATEGORIA, tarifa.ARTICULO_GENERICO");
                                ?><br>
                                <?php 
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
                                        <?php 
                                        }
                                
                                $categoria_actual=$datos_detalle["CATEGORIA"];
                                $busca_nombre_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$categoria_actual");
                                if (db_num_rows($busca_nombre_familia)!=0) {
                                        $datos_familia_actual=db_fetch_array($busca_nombre_familia);
                                        $nombre_familia_actual=$datos_familia_actual["categoria"];
                                        }
                                ?>
                                <table width=75% cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" id="capa_detalles_o_<?=$categoria_actual?>" style="display: block">
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width="40%"><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: mostrar("<?=$categoria_actual?>")'><img src="/imagen/menu/mas.gif" border=0> <b><?=$nombre_familia_actual;?></b></a></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla1?></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla2?></font></td>
                                </tr>
                                </table>
                                <table width=75% cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" id="capa_detalles_m_<?=$categoria_actual?>" style="display: none">
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width="40%"><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$categoria_actual?>")'><img src="/imagen/menu/menos.gif" border=0> <b><?=$nombre_familia_actual;?></b></a></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla1?></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla2?></font></td>
                                </tr>
                                <?php 
                                }
                                
                                $contador_filas++;
                                $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($id_aula, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                                        }

                                $nombre_articulo=$datos_detalle["ARTICULO_GENERICO"];

                                // Busca pedidos abiertos de este articulo
                                $busca_cantidad=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? AND generico_solicitado=?', 'ss', array($id_pedido, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_cantidad)==0) {
                                        $cantidad=number_format(0,2,",",".");
                                        $cantidad_dato=0;
                                        } else {
                                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                        $cantidad=number_format($datos_cantidad_articulo["cantidad"],2,",",".");
                                        $cantidad_dato=$datos_cantidad_articulo["cantidad"];
                                        }
                                
                                // Busca pedidos pendientes de servir de este articulo
                                $busca_cantidad_pendiente=db_query_params('SELECT * FROM detalles_pedido_material,pedidos_material WHERE detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_pedido<>? AND pedidos_material.id_aula=? AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado=?', 'sss', array($id_pedido, $id_aula, (string)($datos_detalle["ARTICULO_GENERICO"])));
                                if (db_num_rows($busca_cantidad_pendiente)==0) {
                                        $pendiente_de_servir="";
                                        } else {
                                        $cantidad_pendiente_de_servir=0;
                                        while($datos_cantidad_pendiente=db_fetch_array($busca_cantidad_pendiente)) {
                                                // Busca si existen albaranes parcialmente servidos
                                                $cantidad_servida=0;
                                                $busca_albaranes_servidos=db_query_params('SELECT * FROM albaranes_entrega, detalles_albaran_entrega WHERE albaranes_entrega.id_albaran=detalles_albaran_entrega.id_albaran AND detalles_albaran_entrega.id_detalle_pedido=?', 's', array($datos_cantidad_pendiente["id_detalle"]));
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
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["ARTICULO_GENERICO"]?>">
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><input type="text" size=5 name="existencias_<?=$contador_filas;?>" value="<?=$existencias_articulo;?>" style="text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"> <?=$unidad_medida?></font></td>
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><input type="text" size=5 name="cantidad_<?=$contador_filas;?>" value="<?=$cantidad;?>" style="text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"> <?=$unidad_medida?> <?=$pendiente_de_servir?></font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="salir()" value="Cancelar pedido"> <input type="button" onclick="guardar()" value="Guardar cambios"><br><br>
                                </td>
                        </tr>
                    <tr valign="top">
                                <td align="right" width=30%><font size=2 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=120 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        <input type="hidden" name="contador_filas" value="<?=$contador_filas;?>">
                    </form>
                        </tr>
                </table>
<script language="Javascript">

function mostrar(id_capa) {
        document.getElementById('capa_detalles_o_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_m_'+id_capa).style.display='block';
        }

function ocultar(id_capa) {
        document.getElementById('capa_detalles_m_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_o_'+id_capa).style.display='block';
        }

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
        var errores=0;
        var mensajes_error="";
        for (fila=1;fila<=<?=$contador_filas;?>;fila++) {
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
        }
        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                } else {
                document.nuevo_pedido.action="guarda_pedido.php?r=1";
                document.nuevo_pedido.submit();
                }
                
        }
function salir() {
        if (confirm("¿Quieres salir y eliminar el pedido actual?")) {
                window.location.href='eliminar_pedido.php?id_pedido=<?=$id_pedido;?>';
                }
        }
        
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
