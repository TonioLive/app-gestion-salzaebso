<?php  require("../configuracion/config.php");
    ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

$formasDePago=array(
        '0' => 'Contado',
        '1' => 'Domiciliacion bancaria, Cuenta: ',
        '2' => 'Transferencia',
        '5' => 'Ticket Guardería',        
);

function calcular_dc($entidad, $oficina, $cuenta){
        $dc = "";
        $pesos = array(6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
    
        foreach(array($entidad . $oficina, $cuenta) as $cadena){
            $suma = 0;
            for($i=0, $len=strlen($cadena); $i<$len; $i++){
                $suma += $pesos[$i] * substr($cadena, $len-$i-1, 1);
            }
            $digito = 11-$suma%11;
            if($digito==11){
                $digito = 0;
            }elseif($digito==10){
                $digito = 1;
            }
            $dc .= $digito;
        }
    
        return $dc;
    }

$nuevo_filtro_facturas=$_POST['filtro_facturas'];

if (strlen($nuevo_filtro_facturas)==0) {
        $filtro_facturas=$_SESSION['filtro_facturas'];
        } else {
        if ($nuevo_filtro_facturas=="0") {
                $_SESSION['filtro_facturas']="";
                $filtro_facturas="";
                } else {
                $_SESSION['filtro_facturas']=$nuevo_filtro_facturas;
                $filtro_facturas=$nuevo_filtro_facturas;
                }
        }

if (strlen($filtro_facturas)!=0) {
        if ($filtro_facturas!="OS") {
                $filtro_facturas_sql=" AND facturas_emitidas.serie='$filtro_facturas'";
                } else {
                $filtro_facturas_sql=" AND facturas_emitidas.serie is Null";
                }
        } else {
        $filtro_facturas_sql="";
        }


?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
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
$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
$id_aula_facturas=$_GET["id_aula_facturas"];

$id_curso_facturas=$_GET["curso_mostrado"];


if ($id_curso_facturas!=0) {
        $busca_datos_curso_mostrado=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_facturas");
        $datos_curso_mostrado=db_fetch_array($busca_datos_curso_mostrado);
        
        $filtros="year(facturas_emitidas.fecha_emision)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(facturas_emitidas.fecha_emision)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));
        $filtros_asistencia="year(servicios_consumidos.fecha)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(servicios_consumidos.fecha)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));

        }

if (strlen($ejercicio)!=0) {
        $filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;
        $filtros_asistencia="year(servicios_consumidos.fecha)=".$ejercicio;
        }

if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        }
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }
if (strlen($id_aula_facturas)!=0) {
        $filtros=$filtros." AND alumnos.aula=".$id_aula_facturas;
        }
if (strlen($id_curso_facturas)!=0) {
        $filtros=$filtros." AND alumnos.id_curso=".$id_curso_facturas;
        }
        
$filtros=$filtros.$filtro_facturas_sql;

$morisidades[0]="<font color=green>En plazo</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<script language="Javascript">

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" }
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function pagos_a_cuenta(id_factura) {
        window.open("pagos_a_cuenta.php?id_factura="+id_factura,'pagos_a_cuenta','width=550,height=450');
        }

function comprobar_y_enviar() {

        top.window.document.albaranes.submit();
}
function eliminar(id_factura) {
        if (confirm("Se eliminará la factra seleccionada.\n\n¿Estas seguro?")) {
                top.window.location.href='eliminar_factura.php?centro_factura=<?=$centro_factura?>&id_cliente=<?=$id_cliente?>&ejercicio=<?=$ejercicio?>&mes=<?=$mes?>&curso_mostrado=<?=$id_curso_facturas?>&id_factura='+id_factura;
                }
        }

function recibos_seleccion() {
        lista_facturas="";

        if (solo_hay_una1==false) {
        for (c_facturas=0;c_facturas<document.facturas.id_facturas.length;c_facturas++) {
                if (document.facturas.id_facturas[c_facturas].checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas[c_facturas].value;
                        lista_facturas=lista_facturas+",";
                        }
                }
                } else {
                if (document.facturas.id_facturas.checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas.value;
                        lista_facturas=lista_facturas+",";
                        }
                }
        if (lista_facturas!="") {
                lista_facturas=lista_facturas.substr(0,(lista_facturas.length-1))
                window.open("csb19_a.php?facturas="+lista_facturas,"csb19","toolbar=no,width=550,height=400");
                } else {
                alert("Debes seleccionar alguna factura para incluir en el fichero");
                }
        }

function imprimir_seleccion() {
        lista_facturas="";

        if (solo_hay_una==false) {
        for (c_facturas=0;c_facturas<document.facturas.id_facturas_imp.length;c_facturas++) {
                if (document.facturas.id_facturas_imp[c_facturas].checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp[c_facturas].value;
                        lista_facturas=lista_facturas+",";
                        }
                }
                } else {
                if (document.facturas.id_facturas_imp.checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp.value;
                        lista_facturas=lista_facturas+",";
                        }
                }

        if (lista_facturas!="") {
                lista_facturas=lista_facturas.substr(0,(lista_facturas.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_facturas;
                ventana_facturas=window.open("about:blank","impresion_faturas","toolbar=yes,width=640,height=480")
                ventana_facturas.location.href="/pdf/facturas.php?facturas="+lista_facturas;
                } else {
                alert("Debes seleccionar alguna factura para imprimir");
                }
        }
        
function imprimir_funica_seleccion() {
        lista_facturas="";

        if (solo_hay_una==false) {
        for (c_facturas=0;c_facturas<document.facturas.id_facturas_imp.length;c_facturas++) {
                if (document.facturas.id_facturas_imp[c_facturas].checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp[c_facturas].value;
                        lista_facturas=lista_facturas+",";
                        }
                }
                } else {
                if (document.facturas.id_facturas_imp.checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp.value;
                        lista_facturas=lista_facturas+",";
                        }
                }

        if (lista_facturas!="") {
                lista_facturas=lista_facturas.substr(0,(lista_facturas.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_facturas;
                ventana_facturas=window.open("about:blank","impresion_faturas","toolbar=yes,width=640,height=480")
                ventana_facturas.location.href="/pdf/facturas_unidas.php?facturas="+lista_facturas;
                } else {
                alert("Debes seleccionar alguna factura para imprimir");
                }
        }
        
function eliminar_seleccion() {
        if (confirm("¿Deseas eliminar las facturas seleccionadas?")) {
        lista_facturas="";

        if (solo_hay_una==false) {
        for (c_facturas=0;c_facturas<document.facturas.id_facturas_imp.length;c_facturas++) {
                if (document.facturas.id_facturas_imp[c_facturas].checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp[c_facturas].value;
                        lista_facturas=lista_facturas+",";
                        }
                }
                } else {
                if (document.facturas.id_facturas_imp.checked) {
                        lista_facturas=lista_facturas+document.facturas.id_facturas_imp.value;
                        lista_facturas=lista_facturas+",";
                        }
                }

        if (lista_facturas!="") {
                lista_facturas=lista_facturas.substr(0,(lista_facturas.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_facturas;
                ventana_facturas=window.open("about:blank","impresion_faturas","toolbar=yes,width=640,height=480")
                ventana_facturas.location.href="eliminar_facturas.php?facturas="+lista_facturas;
                } else {
                alert("Debes seleccionar alguna factura para eliminar");
                }
        }
        }
function check_todas() {
        if (solo_hay_una1==false) {
                for (c_facturas=0;c_facturas<document.facturas.id_facturas.length;c_facturas++) {
                        document.facturas.id_facturas[c_facturas].click();
                        }
                } else {
                        document.facturas.id_facturas.click();
                }
        }
function check_todas1() {
        if (solo_hay_una==false) {
                for (c_facturas=0;c_facturas<document.facturas.id_facturas_imp.length;c_facturas++) {
                        document.facturas.id_facturas_imp[c_facturas].click();
                        }
                } else {
                        document.facturas.id_facturas_imp.click();
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr> <form name="filtro_f" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <br>
                         <input type="button" value="Generar fichero Norma AEB 19 de facturas seleccionadas" onclick="recibos_seleccion();" style="font-size: 10px"> <input type="button" value="Imprimir facturas seleccionadas" onclick="imprimir_seleccion();" style="font-size: 10px"> <!--<input type="button" value="Imprimir seleccionadas como factura única" onclick="imprimir_funica_seleccion();" style="font-size: 10px">--><br> <input type="button" value="Eliminar facturas seleccionadas" onclick="eliminar_seleccion();" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='seleccion_facturas.php';" style="font-size: 10px"><br><br>
                         <font size=1>* Las facturas con fecha de orden de cobro ya se han exportado a un fichero de norma CSB 19 para su comunicación a la entidad bancaria.<br>Si hubiera habido algún error, pueden volver a ser incluidas en otro archivo, pero recuerda que debes destruir el primer archivo generado para evitar confusiones.<br>
                         <input type="button" style="font-size: 10px" value="Examinar ficheros Norma CSB 19 creados" onclick="window.open('directorio.php','directorio','tollbar=no,width=600,height=550,scrollbars=yes,resizable=yes')"></font>
                         <br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=13><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Facturas Emitidas <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b> Tipo: <select onchange="self.document.filtro_f.submit()" name="filtro_facturas" style="font-size: 10px">
                           <option value="0">Todas</value>
                           <option value="EDU"<?php  if ($filtro_facturas=="EDU") { print " selected"; } ?>>Educación</value>
                           <option value="OS"<?php  if ($filtro_facturas=="OS") { print " selected"; } ?>>Otros servicios</value>
                           </select><br><small>Puedes ver, imprimir o editar las facturas haciendo clic en los vínculos que encontrarás en cada línea.<br></small></font></td>
                        </tr>
                        </form>
                        <form name="facturas">
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre DESC";

                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=13><font size=2 face="<?= $fuente_general; ?>">No se han encontrado facturas emitidas con los criterios seleccionados...</font></td>
                        </tr>
                        <?php 
                        }

                        $contador=0;
                        $contador1=0;

                        $total_absoluto=0;
                        $total_cobrado=0;
                        $total_no_cobrado=0;
                        $total_subvenciones=0;
                        $total_cobrado_subvenciones=0;
                        $aula_actual=0;
                        $total_devoluciones=0;
                        $curso_actual=0;
                        $total_cobrado_absoluto=0;

                                                $total_subvenciones_proveedor=array();
                                                $total_subvenciones_pagadas_proveedor=array();

                        $meses_facturacion=array();

                        while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){
// Busca datos del centro
$busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_factura["centro_factura"]);
        if (db_num_rows($busca_centro)!=0) {
                $datos_centro=db_fetch_array($busca_centro);
                $nombre_centro=$datos_centro["nombre_centro"];
                $prefijo_centro=$datos_centro["prefijo_centro"];
                $razon_social=$datos_centro["razon_social"];
                $domicilio=$datos_centro["domicilio_centro"];
                $cp=$datos_centro["CP_centro"];
                $poblacion=$datos_centro["poblacion_centro"];
                $telefono=$datos_centro["telefono_centro"];
                $fax=$datos_centro["fax_centro"];
                $cif=$datos_centro["CIF_centro"];
                }


                        if ($curso_actual!=$datos_factura["id_curso"]) {
                        // Datos del curso
                        $nombre_curso="";
                        $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_factura["id_curso"]);
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $numero_color_curso=intval(substr($datos_factura["id_curso"],strlen($datos_factura["id_curso"])-1,1));
                                $nombre_curso=$datos_curso["nombre"];
                                }
                        ?>
                        <tr valign="top" bgcolor="<?=$colores[$numero_color_curso]?>">
                                <td align="left" colspan=13><font size=3 face="<?= $fuente_general; ?>"><b>Alumnos Curso: <?=$nombre_curso;?></b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$datos_factura["id_curso"];
                        }

                        if ($aula_actual!=$datos_factura["aula"]) {
                        // Datos del aula
                        $nombre_aula="";
                        $busca_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_factura["aula"]);
                        if (db_num_rows($busca_aula)!=0) {
                                $datos_aula=db_fetch_array($busca_aula);
                                $nombre_aula=$datos_aula["nombre"];
                                }
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="left" colspan=13><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_centro." - ".$nombre_aula;?></b></font></td>
                        </tr>
                        <tr valign="middle" bgcolor="#ececec">
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Emitir recibo<br><a href="javascript:check_todas()">Selecciona Todas</a></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Imprimir/Eliminar<br><a href="javascript:check_todas1()">Selecciona Todas</a></font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Nº de Factura</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha de emisión</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha Orden Cobro*</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Cliente</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Alumno</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fechas alta y baja</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Asistencia en el periodo facturado</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Importe</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Estado</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Forma de pago</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                        <?php 
                        $aula_actual=$datos_factura["aula"];
                        }


                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];


// Calcula totales

// Comprueba si ya tenemos los datos en la tabla factura

if (($datos_factura["total_factura"]!=0) and ($datos_factura["total_subvenciones"]==$datos_factura["total_subvenciones_cobrado"])) {
        $no_calcular=true;
        } else {
        $no_calcular=false;
        }

if ($no_calcular==true) {
        $total_factura=$datos_factura["total_factura"];
        $total_subvenciones=$total_subvenciones+$datos_factura["total_subvenciones"];
        $total_cobrado_subvenciones=$total_cobrado_subvenciones+$datos_factura["total_subvenciones_cobrado"];
        if (strlen($datos_factura["filtros_educacion"])!=0) {
                $filtros_asistencia=urldecode($datos_factura["filtros_educacion"]);
                }

        } else {

// Desde aqui

// Busca albaranes
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (db_error());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;
$total_factura=0;


$contador_meses_facturacion=0;
$meses_facturacion[0]="";
$meses_facturacion[1]="";
$meses_facturacion[2]="";
$meses_facturacion[3]="";
$meses_facturacion[4]="";
$meses_facturacion[5]="";
$meses_facturacion[6]="";
$meses_facturacion[7]="";
$meses_facturacion[8]="";
$meses_facturacion[9]="";
$meses_facturacion[10]="";
$meses_facturacion[11]="";
$meses_facturacion[12]="";
$meses_facturacion[13]="";
$meses_facturacion[14]="";
$meses_facturacion[15]="";
$meses_facturacion[16]="";
$meses_facturacion[17]="";
$meses_facturacion[18]="";
$meses_facturacion[19]="";
$meses_facturacion[20]="";
$meses_facturacion[21]="";
$meses_facturacion[22]="";
$meses_facturacion[23]="";
$meses_facturacion[24]="";
$meses_facturacion[25]="";
$meses_facturacion[26]="";
$meses_facturacion[27]="";
$meses_facturacion[28]="";
$meses_facturacion[29]="";

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $concepto_linea=$datos_albaranes["concepto"];
        if (strlen($concepto_linea)!=0) {
                $concepto_linea=strtoupper($concepto_linea);
                }

        if ((is_numeric(strpos($concepto_linea,"EDUCACION"))) or (is_numeric(strpos($concepto_linea,"EDUCACIÓN")))) {

                $fecha_emision_factura=$datos_factura["fecha_emision"];
                $anio_factura=date("Y",strtotime($fecha_emision_factura));
                $mes_emision_factura=date("n",strtotime($fecha_emision_factura));

                $encontrado=false;

                // 11 Meses posteriores
                for ($c_meses=1;$c_meses<=11;$c_meses++) {
                $mes_buscado=$mes_emision_factura+$c_meses;
                if ($mes_buscado>=13) {
                        $anio_buscado=$anio_factura+1;
                        $mes_buscado=12-$mes_buscado;
                        } else {
                        $anio_buscado=$anio_factura;
                        }
                $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                        $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                        $contador_meses_facturacion++;
                        $encontrado=true;
                        break;
                        }
                }

                if ($encontrado==false) {
                        // Mes emision factura
                        $mes_buscado=$mes_emision_factura;
                        $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                        $anio_buscado=$anio_factura;
                        if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                                $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                                $contador_meses_facturacion++;
                                $encontrado=true;
                                }
                        }
                        
                if ($encontrado==false) {
                        // 11 Meses anteriores
                        for ($c_meses=1;$c_meses<=11;$c_meses++) {
                        $mes_buscado=$mes_emision_factura-$c_meses;
                        if ($mes_buscado<=0) {
                                $anio_buscado=$anio_factura-1;
                                $mes_buscado=12+$mes_buscado;
                                } else {
                                $anio_buscado=$anio_factura;
                                }
                        $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                        if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                                $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                                $contador_meses_facturacion++;
                                break;
                                }
                        }
                        }


                }

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
                
                if ((substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") or (strtolower(substr($datos_albaranes["concepto"],0,13))=="abono bono infantil")  or (strtolower(substr($datos_albaranes["concepto"],0,13))=="bono infantil") or (strtolower(substr($datos_albaranes["concepto"],0,17))=="subvencion centro")) {
                                                        $proveedor_actual=141;
                                                        }                               
                if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
                                                        $proveedor_actual=120;                                                        
                                                        }         
                
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                $total_subvenciones_proveedor[$proveedor_actual]=$total_subvenciones_proveedor[$proveedor_actual]+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                $total_subvenciones_pagadas_proveedor[$proveedor_actual]=$total_subvenciones_pagadas_proveedor[$proveedor_actual]+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

if ($meses_facturacion[0]!="") {
        $filtros_asistencia="(month(fecha)=".date("n",strtotime($meses_facturacion[0]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[0])).")";
        for ($c_meses=1;$c_meses<=$contador_meses_facturacion;$c_meses++) {
                if ($meses_facturacion[$c_meses]!="") {
                        $filtros_asistencia=$filtros_asistencia." OR (month(fecha)=".date("n",strtotime($meses_facturacion[$c_meses]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[$c_meses])).")";
                        }
                }
        }

// Hasta aquí

// Introduce la info en la tabla para no tener que recalcular
$update_factura=db_query("UPDATE facturas_emitidas SET total_factura='$total_factura',total_subvenciones='$importe_subvenciones_factura',total_subvenciones_cobrado='$importe_subvenciones_cobradas_factura',filtros_educacion='".urlencode($filtros_asistencia)."' WHERE id_factura=".$id_factura) or die (db_error());

}

$total_absoluto=$total_absoluto+$total_factura;

$total_dias_asistidos=0;

// Comprueba dias asistidos en el mes de facturacion
if ($filtros_asistencia!="") {
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$datos_factura["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        }

// Comprueba pagos a cuenta
$total_a_cuenta=0;

$busca_pagos=db_query("SELECT * FROM pagos_a_cuenta WHERE id_factura=".$datos_factura["id_factura"]);
$forma_de_pago_realizado_array=array();
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        $coletillaPago="";
        if ($pagos["forma_pago"]==1) {
                $coletillaPago=$datos_factura["banco_cuenta"];
        }
        $forma_de_pago_realizado_array[]=date("d/m/Y",strtotime($pagos["fecha_pago"]))." - ".$formasDePago[$pagos["forma_pago"]].$coletillaPago;
}
$forma_de_pago_realizado='';
if (count($forma_de_pago_realizado_array)!=0) {
        $forma_de_pago_realizado=implode('<br>',$forma_de_pago_realizado_array);
}
if (($total_a_cuenta-$total_factura)!=0) {
        if ($total_a_cuenta>0) {
                $total_cobrado=$total_cobrado+$total_a_cuenta;
                $estado_factura="<font color=red><small>Pendiente cobro: ".number_format(($total_factura-$total_a_cuenta), 2, ',', '.')." €</small></font>";
                } else {
                $estado_factura="<font color=red><small>No cobrada</small></font>";
                }
        $morosidad=true;
        $total_cobrado_absoluto=$total_cobrado_absoluto+$total_a_cuenta;
        } else {
        $total_cobrado=$total_factura;
        $estado_factura="<font color=green><small>Cobrada</small></font>";
        $morosidad=false;
        $total_cobrado_absoluto=$total_cobrado_absoluto+$total_factura;
        }

$total_no_cobrado=$total_no_cobrado+($total_factura-$total_a_cuenta);


$tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

$morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==1) {


                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$datos_factura["banco_cuenta"];

                        $partesCuenta = explode(" ",$datos_factura["banco_cuenta"]);

                        $dcCuenta = calcular_dc($partesCuenta[0], $partesCuenta[1], $partesCuenta[3]);

                        if ($dcCuenta!=$partesCuenta[2]) {
                                $forma_de_pago="<font color='#ff0000'>".$forma_de_pago."<br>ERROR NUMERO DE CUENTA</font>";
                        }

                        if (($tiempo_desde_emision<=15) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>15) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>30) {
                                $morosidad_actual=4;
                                }
                        } else {
                        // $forma_de_pago="Contado";
                        $forma_de_pago="";
                        if ($datos_factura["forma_de_pago"]==5) {
                                // $forma_de_pago="Cheque guardería";
                                }
                        if (($tiempo_desde_emision<=5) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>5) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>10) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=4;
                                }
                        }

if ($forma_de_pago_realizado!="") {
        $forma_de_pago=$forma_de_pago_realizado;
}

if ($morosidad==true) {
        if ($morosidad_actual>=$nivel_maximo_morosidad) {
                $nivel_maximo_morosidad=$morosidad_actual;
                }
        $texto_morosidad="<br>".$morisidades[$morosidad_actual];
        } else {
        $texto_morosidad="";
        }

if ($datos_factura["fecha_orden_cobro"]!="0000-00-00") {
        $fecha_orden_de_cobro=date("j/n/Y",strtotime($datos_factura["fecha_orden_cobro"]));
        } else {
        $fecha_orden_de_cobro="";
        }

        $fecha_baja=$datos_factura["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_factura["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($datos_factura["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#45A9F6";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }
                                ?>
                                <tr valign="top" bgcolor="#fff" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'#fff')">
                                  <?php 
                                  if (($datos_factura["forma_de_pago"]==1) and ($estado_factura!="<font color=green><small>Cobrada</small></font>")) {
                                  // if ($datos_factura["forma_de_pago"]==1) {
                                  $contador1++;
                                  ?>
                                  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="checkbox" name="id_facturas" value="<?=$id_factura?>"></font></td>
                                  <?php 
                                  } else {
                                  ?>
                                  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="checkbox" name="nada" disabled></font></td>
                                  <?php 
                                  }
                                  
                                  if ($datos_factura["serie"]!="EDU") {
                                        $num_factura="R".$datos_factura["id_factura"];
                                        } else {
                                        $num_factura=$datos_factura["num_factura"];
                                        }                                

                                  ?>
                                  <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="checkbox" name="id_facturas_imp" value="<?=$id_factura?>"></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$datos_factura["serie"];?><?=$num_factura;?>/<?=$datos_factura["ejercicio_factura"];?><br>Cód Dev: <?=$datos_factura["id_factura"]?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("j/n/Y",strtotime($datos_factura["fecha_emision"]));?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_orden_de_cobro;?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$datos_factura["banco_titular"];?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($datos_factura["fecha_matricula"]));?><br><?=date("d/m/Y",strtotime($datos_factura["fecha_baja"]));?></font></td>
                                  <td align="center" width=10%><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$total_dias_asistidos;?> días.</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=number_format($total_factura, 2, ',', '.');?> €</font></td>
                                  <td align="center" width=10%><font size=1 face="<?= $fuente_general; ?>"><?=$estado_factura.$texto_morosidad;?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$forma_de_pago;?></font></td>
                                  <td align="center" width=12%><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:pagos_a_cuenta(<?=$datos_factura["id_factura"]?>)">Pagos a cuenta</a><br><a href="/pdf/factura.php?id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir</a><br> <a href="/pdf/factura.php?alb=1&id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir Albarán</a><br> <a href="/pdf/factura_old.php?id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir fact. separada</a><br><a href="/pdf/factura.php?id_factura=<?=$datos_factura["id_factura"]?>&dom=1" target="_blank">Imprimir con domicilo</a><br><a href="/pdf/factura.php?id_factura=<?=$datos_factura["id_factura"]?>&dom=1" target="_blank">Imprimir F. única con domicilo</a><br><a href="javascript:eliminar(<?=$id_factura;?>)">Eliminar</a></font></td>
                                </tr>
                                <?php 
                                $contador=$contador+1;
                        }
                        ?>
                        </form>
                         </table>
                         <br>
                         <?php 
                         if ($nivel_acceso==4) {
                         ?>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Facturas emitidas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total facturado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_cobrado_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_no_cobrado, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total subvenciones:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_cobrado_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*($total_subvenciones-$total_cobrado_subvenciones), 2, ',', '.');?> €</font></td>
                        </tr>
                        
<tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>
                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[120]-$total_subvenciones_pagadas_proveedor[120])*-1,2,",",".");?> €</font></td>
                                </tr> 
                        
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Total subvenciones Conselleria Educación <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[141]-$total_subvenciones_pagadas_proveedor141)*-1,2,",",".");?> €</font></td>
                                </tr> 
                        </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_facturas.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><?php 
}
?><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<script labguage="Javascript">
<?php 
if ($contador==1) {
?>
var solo_hay_una=true;
<?php  } else { ?>
var solo_hay_una=false;
<?php 
}
if ($contador1==1) {
?>
var solo_hay_una1=true;
<?php  } else { ?>
var solo_hay_una1=false;
<?php 
}
?>
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
