<?php  require("../configuracion/config.php");

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}

$num_facturas_mostradas=1;
$id_cliente=$_GET["id_hijo"];
$id_curso=$_GET["id_curso"]; 

if (strlen($id_cliente)==0) {
        die();
        }
?>
<?php  require("../configuracion/autentifica_padre.php");

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                } else {
                $id_curso_actual=0;
                $nombre_curso_actual="";
                }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  include("estilos.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>

</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-2"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" border=0></div>
  	<div class="col-md-10"></div>
  </div>
  <div class="row"> <!-- Principal -->
<?php  $fechalimite1=$fechalimite; ?>
<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
    	<?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->
    	<h3>Recibos</h3>
    	<div align="center">
<?php 

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=$_GET["version_js"];

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
if (strlen($version_js)==0) {
        $version_js==0;
        }

$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));
        
        if (db_num_rows($busca_usuario)==0) {
                $errores="El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                // Mostramos la información del niño

                $id_usuario=$datos_usuario["id_usuario"];
                $id_hijo=$datos_usuario["padre_de"];

                                $parentesco=$datos_usuario["parentesco"];

                // Busca datos del padre.
                $busca_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario)) or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                                // Comprueba si es padre de este niño
                                
                                if (strlen($id_cliente)!=0) {
        
                                // Busca datos del alumno
                                $consulta_datos_del_cliente=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_cliente));
                                
                                if (db_num_rows($consulta_datos_del_cliente)!=0) {
                                        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
                                        $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                                        
                                                $busca_hijo=db_query_params('SELECT * FROM padres WHERE id_usuario=? AND padre_de=?', 'ss', array($id_usuario, $id_usuario_cliente));
                                                if (db_num_rows($busca_hijo)==0) {
                                                        $errores="- No tiene autorización para acceder a la información de este alumno -";
                                                        }
                                        } else {
                                        $errores="- El alumno seleccionado no existe -";
                                        }
                                
                                } else {
                                die();
                                }
                        
                                if ($parentesco==-1) {
                                        $errores="- Esta información está restringida a los padres del alumno -";
                                        } 
                }

        }
       
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <input type="button" onclick="window.location.href='/webcam/menu.php'" value="Volver">
        </div>
    <?php 
    } else {
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)!=0) {
        $filtros=" AND year(facturas_emitidas.fecha_emision)=".$ejercicio;
        }
if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        
        // Busca datos del alumno
        $consulta_datos_del_cliente=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_cliente));

        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
        
        $nombre_cliente_facturas=$datos_del_cliente["banco_titular"];
        $dni_cliente_facturas=$datos_del_cliente["banco_dni_titular"];
        $nombre_alumno_facturas=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
        
        } else {
        die();
        }
        
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }

?>
		<table width=98% class="tabla_datos">
                        <tr valign="top">
                           <td align="center" colspan=8 class="tabla_celda_encabezado"><b>Recibos del alumno - <?=$nombre_alumno_facturas;?> durante el curso <?=$nombre_curso_actual;?></b><br>
                           <br>Recibos emitidos a nombre de: <?=$nombre_cliente_facturas;?> (<?=$dni_cliente_facturas;?>)<br><br></td>
                        </tr>
                        <?php 

                        $consulta1="SELECT facturas_emitidas.*,pagos_a_cuenta.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, pagos_a_cuenta, alumnos, usuarios WHERE facturas_emitidas.id_factura=pagos_a_cuenta.id_factura AND facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario$filtros ORDER BY fecha_emision DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top">
                          <td align="center" colspan=8 class="tabla_celda_datos">No se han encontrado recibos</td>
                        </tr>
                        <?php 
                        }
                        
                        $contador=0;
                        
                        $total_absoluto=0;
                        $total_cobrado=0;
                        $nivel_maximo_morosidad=0;
                        $total_subvenciones=0;
                        $total_cobrado_subvenciones=0;
                        
                        $morisidades[0]="<font color=green>No tiene pagos atrasados.";
                        $morisidades[1]="<font color=red>Leve";
                        $morisidades[2]="<font color=red>Grave";
                        $morisidades[3]="<font color=red>Muy Grave";
                        $morisidades[4]="<font color=red>Expulsión";

                        while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){
                        
                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];
// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

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

$lineas_detalles="<tr bgcolor='#efefef'><td colspan=7><b>Detalles de la factura</b></td></tr><tr>
        <td bgcolor='#efefef' colspan=4><b>Concepto</b></td>
        <td bgcolor='#efefef'><b>Precio</b></td>
        <td bgcolor='#efefef'><b>Cantidad</b></td>
        <td bgcolor='#efefef'><b>Total</b></td>
        </tr>";

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $concepto_linea=$datos_albaranes["concepto"];
        if (strlen($concepto_linea)!=0) {
                $concepto_linea=strtoupper($concepto_linea);
                }

        if ((is_numeric(strpos($concepto_linea,"EDUCACION"))) or (is_numeric(strpos($concepto_linea,"EDUCACIÓN")))) {

                $fecha_emision_factura=$datos_factura["fecha_emision"];
                $anio_factura=date("Y",strtotime($fecha_emision_factura));
                $mes_emision_factura=date("n",strtotime($fecha_emision_factura));

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
                        }
                }

                // Mes emision factura
                $mes_buscado=$mes_emision_factura;
                $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                $anio_buscado=$anio_factura;
                if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                        $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                        $contador_meses_facturacion++;
                        }

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
                
        $nombre_articulo=$datos_albaranes["concepto"];


        $lineas_detalles=$lineas_detalles."<tr bgcolor='#ffffff'>
        <td colspan=4>".$nombre_articulo."</td>
        <td align='right'>".number_format($datos_albaranes["precio"], 2, ',', '.')." &euro;"."</td>
        <td align='right'>".number_format($datos_albaranes["cantidad"], 2, ',', '.')."</td>
        <td align='right'>".number_format($total_abs, 2, ',', '.')." &euro;"."</td>
        </tr>";
        
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                }
        }
        $lineas_detalles=$lineas_detalles."</tr><tr><td colspan=7>&nbsp;</td></tr>";

$total_factura=round(($total_base+$total_iva+$total_recargo),2);
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

// Comprueba pagos a cuenta
$total_a_cuenta=0;

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if ($total_a_cuenta<($total_base+$total_iva+$total_recargo)) {
        if ($total_a_cuenta>0) {
                $estado_factura="<font color=red><small>Pendiente pago: ".number_format((($total_base+$total_iva+$total_recargo)-$total_a_cuenta), 2, ',', '.')." ?</small>";
                } else {
                $estado_factura="<font color=red><small>No pagada</small>";
                }
        $morosidad=true;
        } else {
        $estado_factura="<font color=green><small>Pagada</small>";
        $morosidad=false;
        }

$tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

$morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==1) {
                
                		$numero_de_cuenta=substr($datos_factura["banco_cuenta"], 0, 3)."* **** ** *******".substr($datos_factura["banco_cuenta"], strlen($datos_factura["banco_cuenta"])-3, 3);
                
                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$numero_de_cuenta;
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
                        $forma_de_pago="Contado";
                        if ($datos_factura["forma_de_pago"]==5) {
                                $forma_de_pago="Cheque guardería";
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

if ($morosidad==true) {
        if ($morosidad_actual>=$nivel_maximo_morosidad) {
                $nivel_maximo_morosidad=$morosidad_actual;
                }
        $texto_morosidad="<br>".$morisidades[$morosidad_actual];
        } else {
        $texto_morosidad="";
        }

$total_cobrado=$total_cobrado+round($total_a_cuenta,2);
$total_absoluto=$total_absoluto+$total_factura;

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
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
                                ?>
                        <tr valign="top">
                          <td class="tabla_celda_encabezado" align="center">Fecha de emisión</td>
                          <td class="tabla_celda_encabezado" align="center">Fecha de pago</td>
                          <td class="tabla_celda_encabezado" align="center">Centro</td>
                          <td class="tabla_celda_encabezado" align="center">Importe Total</td>
                          <td class="tabla_celda_encabezado" align="center">Importe Pagado</td>
                          <td class="tabla_celda_encabezado" align="center">Forma de pago</td>
                          <td align="center">&nbsp;</td>
                        </tr>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="center" class="tabla_celda_datos"><?=date("j/n/Y",strtotime($datos_factura["fecha_emision"]));?></td>
                                  <td align="center" class="tabla_celda_datos"><?=date("j/n/Y",strtotime($datos_factura["fecha_pago"]));?></td>
                                  <td align="center" class="tabla_celda_datos"><?=$nombre_centro;?></td>
                                  <td align="right" class="tabla_celda_datos"><?=number_format($total_factura, 2, ',', '.');?> &euro;</td>
                                  <td align="right" class="tabla_celda_datos"><?=number_format($datos_factura["importe_pago"], 2, ',', '.');?> &euro;</td>
                                  <td align="center" class="tabla_celda_datos"><?=$forma_de_pago;?></td>
                                  <td align="center" class="tabla_celda_datos"><a href="/webcam/pdf/recibos.php?id_recibo=<?=$datos_factura["id_factura"]?>&id_pago=<?=$datos_factura["id_pago"]?>" target="_blank">Ver/Imprimir</a></td>
                                </tr>
                                <?php 
                                // echo $lineas_detalles;
                                $contador=$contador+1;
                        }                        
                        ?>
                         </table>
                         <br>
	<button class="tabla_celda_encabezado celda_margen" onclick="window.location.href='recibos.php'"><i class="fa fa-backward"></i> Volver</button><br><br>
        <?php 
        }
    ?>
    </div><br />
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
</div>				<!-- Fin Jumbotron -->	
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
