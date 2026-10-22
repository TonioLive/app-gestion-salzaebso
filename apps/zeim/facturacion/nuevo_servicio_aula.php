<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 

// Parametros
$id_aula=$_GET["ii"];
$id_servicio=$_GET["ids"];

$consulta_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula)) or die(db_error()."-2");

$datos_aula=db_fetch_array($consulta_aula);

$id_curso_aula=$datos_aula["id_curso"];
$nombre_aula=$datos_aula["nombre"];

$consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aula["id_centro"])) or die(db_error()."-2");
$datos_centro=db_fetch_array($consulta_nombre_centro);

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guardar_inscripcion.php";

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      }

if (strlen($id_servicio)==0) {
                $mensaje="Nuevo servicio asociado al aula";
                // Valores por defecto
                $id_servicio=0;
                $familia="";
                $articulo="";
                $periodicidad=1;
                $fecha_ini_dia="";
                $fecha_ini_mes="";
                $fecha_ini_ano="";
                $fecha_fin_dia="";
                $fecha_fin_mes="";
                $fecha_fin_ano="";
                $fecha_dia="";
                $fecha_mes="";
                $fecha_ano="";
                $precio="";
                $cantidad=1;
                $descuento=0;
                $tipo_iva=0;
                $notas="";
        } else {
                $mensaje="Edición de servicio asociado al aula";
                $busca_servicios=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_servicio_contratado=?', 's', array($id_servicio));

                $datos_servicios=db_fetch_array($busca_servicios);
                
                $familia=$datos_servicios["CATEGORIA"];
                $articulo=$datos_servicios["ID"];
                $periodicidad=$datos_servicios["periodicidad"];
                if ($periodicidad==1) {
                        $fecha_ini_dia=date("j",strtotime($datos_servicios["fecha_inicio"]));
                        $fecha_ini_mes=date("n",strtotime($datos_servicios["fecha_inicio"]));
                        $fecha_ini_ano=date("Y",strtotime($datos_servicios["fecha_inicio"]));
                        $fecha_fin_dia=date("j",strtotime($datos_servicios["fecha_fin"]));
                        $fecha_fin_mes=date("n",strtotime($datos_servicios["fecha_fin"]));
                        $fecha_fin_ano=date("Y",strtotime($datos_servicios["fecha_fin"]));
                        $fecha_dia="";
                        $fecha_mes="";
                        $fecha_ano="";
                        } else {
                        $fecha_ini_dia="";
                        $fecha_ini_mes="";
                        $fecha_ini_ano="";
                        $fecha_fin_dia="";
                        $fecha_fin_mes="";
                        $fecha_fin_ano="";
                        $fecha_dia=date("j",strtotime($datos_servicios["fecha"]));
                        $fecha_mes=date("n",strtotime($datos_servicios["fecha"]));
                        $fecha_ano=date("Y",strtotime($datos_servicios["fecha"]));
                        }
                $precio=$datos_servicios["PRECIO_VENTA"];
                $cantidad=$datos_servicios["cantidad"];
                $descuento=$datos_servicios["descuento"];
                $tipo_iva=round(($datos_servicios["porcentaje_iva"]*100),2);
                $notas=$datos_servicios["notas"];
        }
?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

<?php  require("script_tarifas_aulas.php"); ?>

function genera_listado() {
                // Borra la lista de artículos
                var longitud_select = document.datos_articulo.articulo.length;
                if (longitud_select>0) {
                        for (var cont=(longitud_select-1);cont>=0;cont=cont-1) {
                                document.datos_articulo.articulo.remove(cont);
                                }
                        }

                // Genera la nueva lista de artículos

                var navegador = navigator.appName
                
                document.datos_articulo.articulo.disabled=true;

                var oOption = document.createElement("OPTION");
                document.datos_articulo.articulo.options.add(oOption);
                
                if (navegador == "Microsoft Internet Explorer") {
                        oOption.innerText = "Selecciona el artículo";
                        } else {
                        oOption.text = "Selecciona el artículo";
                        }
                        
                oOption.value = "0";
                hay_art=false;
                for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][1]==document.datos_articulo.familia.options[document.datos_articulo.familia.selectedIndex].value) {
                                var oOption = document.createElement("OPTION");
                                document.datos_articulo.articulo.options.add(oOption);

                                if (navegador == "Microsoft Internet Explorer") {
                                        oOption.innerText = tarifa[i][4];
                                        } else {
                                        oOption.text = tarifa[i][4];
                                        }

                                oOption.value = tarifa[i][2];
                                hay_art=true;
                                }
                        }
                if (hay_art==false) {
                                alert("No hay servicios en la categoría seleccionada");
                        } else {
                                document.datos_articulo.articulo.disabled=false;
                        }
        }
        
function comprueba_al_entrar() {
        familia_actual=document.datos_articulo.familia.value;
        }
        
function comprueba_y_envia() {
        var errores="";
        var mensajes_error="";

        // Comprueba fechas
        if (self.document.datos_articulo.periodicidad.value==1) {
                        la_fecha1=self.document.datos_articulo.fecha_ini_mes.value+"/"+self.document.datos_articulo.fecha_ini_dia.value+"/"+self.document.datos_articulo.fecha_ini_ano.value
                        if (isDate(la_fecha1)!=true) { mensajes_error=mensajes_error+"La fecha de inicio introducida no es correcta.\u000A";errores=1; }
        
                        la_fecha2=self.document.datos_articulo.fecha_fin_mes.value+"/"+self.document.datos_articulo.fecha_fin_dia.value+"/"+self.document.datos_articulo.fecha_fin_ano.value
                        if (isDate(la_fecha2)!=true) { mensajes_error=mensajes_error+"La fecha de finalización introducida no es correcta.\u000A";errores=1; }
                        if (errores=="") {
                                if (Date.parse(la_fecha1) >= Date.parse(la_fecha2)) {
                                        mensajes_error=mensajes_error+"La fecha de finalización debe ser posterior a la de inicio.\u000A";errores=1;
                                        }
                                }
                        if (self.document.datos_articulo.cantidad.value=="") {
                                        mensajes_error=mensajes_error+"Falta introducir la cantidad.\n";
                                        errores=1;
                                } else {
                                        if (isNaN(self.document.datos_articulo.cantidad.value)) {
                                        mensajes_error=mensajes_error+"La cantidad debe ser un número.\n";
                                        errores=1;
                                        }
                                 }
                } else {
                        la_fecha3=self.document.datos_articulo.fecha_mes.value+"/"+self.document.datos_articulo.fecha_dia.value+"/"+self.document.datos_articulo.fecha_ano.value
                        if (isDate(la_fecha3)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }
                        if (self.document.datos_articulo.articulo.value=="") {
                                mensajes_error=mensajes_error+"Falta seleccionar el servicio.\n";
                                errores=1;
                                }
                        if (self.document.datos_articulo.cantidad.value=="") {
                                        mensajes_error=mensajes_error+"Falta introducir la cantidad.\n";
                                        errores=1;
                                } else {
                                        if (isNaN(self.document.datos_articulo.cantidad.value)) {
                                        mensajes_error=mensajes_error+"La cantidad debe ser un número.\n";
                                        errores=1;
                                        }
                                 }
                }

        if (self.document.datos_articulo.familia.value=="") {
                mensajes_error=mensajes_error+"Falta seleccionar la familia del servicio.\n";
                errores=1;
                }

        if (self.document.datos_articulo.articulo.value=="0") {
                mensajes_error=mensajes_error+"Falta seleccionar el servicio.\n";
                errores=1;
                }
                
        if (self.document.datos_articulo.descuento.value=="") {
                                        mensajes_error=mensajes_error+"Falta introducir el descuento.\n";
                                        errores=1;
                                } else {
                                        if (isNaN(self.document.datos_articulo.descuento.value)) {
                                                        mensajes_error=mensajes_error+"El descuento debe ser un número.\n";
                                                        errores=1;
                                                } else {
                                                        if ((Number(self.document.datos_articulo.descuento.value)<0)||(Number(self.document.datos_articulo.descuento.value)>100)) {
                                                                mensajes_error=mensajes_error+"El descuento debe ser un número positivo entre 0 y 100.\n";
                                                                errores=1;
                                                                }
                                                }
                                 }
        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+mensajes_error);
                } else {
                self.document.datos_articulo.submit();
                }
        }
        
function cambia_fechas() {

        if (self.document.datos_articulo.periodicidad.value==1) {
                /*
                document.getElementById('mensual1').style.display='block';
                document.getElementById('mensual2').style.display='block';
                document.getElementById('cantidades').style.display='block';
                document.getElementById('unico').style.display='none';
                */
                document.getElementById('fecha_ini_dia').disabled=false;
                document.getElementById('fecha_ini_mes').disabled=false;
                document.getElementById('fecha_ini_ano').disabled=false;
                document.getElementById('fecha_fin_dia').disabled=false;
                document.getElementById('fecha_fin_mes').disabled=false;
                document.getElementById('fecha_fin_ano').disabled=false;
                document.getElementById('fecha_dia').disabled=true;
                document.getElementById('fecha_mes').disabled=true;
                document.getElementById('fecha_ano').disabled=true;

                self.document.datos_articulo.cantidad.value=Number(self.document.datos_articulo.cantidad.value);
                recalcular_precios();
                } else {
                /*
                document.getElementById('mensual1').style.display='none';
                document.getElementById('mensual2').style.display='none';
                document.getElementById('cantidades').style.display='block';
                document.getElementById('unico').style.display='block';
                */
                document.getElementById('fecha_ini_dia').disabled=true;
                document.getElementById('fecha_ini_mes').disabled=true;
                document.getElementById('fecha_ini_ano').disabled=true;
                document.getElementById('fecha_fin_dia').disabled=true;
                document.getElementById('fecha_fin_mes').disabled=true;
                document.getElementById('fecha_fin_ano').disabled=true;
                document.getElementById('fecha_dia').disabled=false;
                document.getElementById('fecha_mes').disabled=false;
                document.getElementById('fecha_ano').disabled=false;
                self.document.datos_articulo.cantidad.value=Number(self.document.datos_articulo.cantidad.value);
                recalcular_precios();
                }
        }
        
function selecciona_unidad() {
        var indice_array=-1;

        for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][0]==document.datos_articulo.articulo.value) {
                                indice_array=i;
                                }
                        }
        if (indice_array==-1) {
                self.document.datos_articulo.unidad.value="Unidades";
                } else {
                if (tarifa[indice_array][5]!="") {
                                self.document.datos_articulo.unidad.value=tarifa[indice_array][5];
                        } else {
                                self.document.datos_articulo.unidad.value="Unidades";
                        }
                self.document.datos_articulo.tipo_iva.value=100*tarifa[indice_array][6];
                self.document.datos_articulo.precio.value=tarifa[indice_array][3];
                recalcular_precios();
                }
        }

function recalcular_precios() {
        precio_unitario=self.document.datos_articulo.precio.value;
        if ((isNaN(precio_unitario)==false)&&(precio_unitario!="")) {
                if (isNaN(self.document.datos_articulo.cantidad.value)==false) {
                // Descuento
                porcentaje_des=Number(self.document.datos_articulo.descuento.value);
                if (porcentaje_des!="") {
                        if ((isNaN(porcentaje_des))||(porcentaje_des>100)||(porcentaje_des<0)) {
                                alert("El descuento debe ser un número positivo entre 0 y 100.");
                                porcentaje_des=0;
                                } else {
                                porcentaje_des=porcentaje_des/100;
                                }
                        }
                porcentaje_iva=self.document.datos_articulo.tipo_iva.value;
                if ((isNaN(precio_unitario)==false)&&(precio_unitario!="")) {
                        porcentaje_iva=porcentaje_iva/100;
                        } else {
                        importe_iva=0;
                        }
                precio_unitario=Number(precio_unitario);
                unidades_servicio=Number(self.document.datos_articulo.cantidad.value);
                self.document.datos_articulo.base_imp.value=Math.round(precio_unitario*unidades_servicio*100)/100;
                base_imponible=Number(self.document.datos_articulo.base_imp.value);
                self.document.datos_articulo.importe_descuento.value=Math.round(base_imponible*porcentaje_des*100)/100;
                importe_desc=Number(self.document.datos_articulo.importe_descuento.value);
                self.document.datos_articulo.total_inc_desc.value=Math.round((base_imponible-importe_desc)*100)/100;
                total_base=Number(self.document.datos_articulo.total_inc_desc.value);
                self.document.datos_articulo.iva.value=Math.round(total_base*porcentaje_iva*100)/100;
                importe_iva=Number(self.document.datos_articulo.iva.value);
                self.document.datos_articulo.total_servicio.value=Math.round((total_base+importe_iva)*100)/100;
                } else {
                alert("Las cantidad debe indicarse con un número.");
                }
                }
        if (self.document.datos_articulo.periodicidad.value==1) {
                /*
                document.getElementById('mensual1').style.display='block';
                document.getElementById('mensual2').style.display='block';
                document.getElementById('cantidades').style.display='block';
                document.getElementById('unico').style.display='none';
                */
                document.getElementById('fecha_ini_dia').disabled=false;
                document.getElementById('fecha_ini_mes').disabled=false;
                document.getElementById('fecha_ini_ano').disabled=false;
                document.getElementById('fecha_fin_dia').disabled=false;
                document.getElementById('fecha_fin_mes').disabled=false;
                document.getElementById('fecha_fin_ano').disabled=false;
                document.getElementById('cantidad').disabled=false;
                document.getElementById('fecha_dia').disabled=true;
                document.getElementById('fecha_mes').disabled=true;
                document.getElementById('fecha_ano').disabled=true;

                self.document.datos_articulo.cantidad.value=Number(self.document.datos_articulo.cantidad.value);
                } else {
                /*
                document.getElementById('mensual1').style.display='none';
                document.getElementById('mensual2').style.display='none';
                document.getElementById('cantidades').style.display='block';
                document.getElementById('unico').style.display='block';
                */
                document.getElementById('fecha_ini_dia').disabled=true;
                document.getElementById('fecha_ini_mes').disabled=true;
                document.getElementById('fecha_ini_ano').disabled=true;
                document.getElementById('fecha_fin_dia').disabled=true;
                document.getElementById('fecha_fin_mes').disabled=true;
                document.getElementById('fecha_fin_ano').disabled=true;
                document.getElementById('fecha_dia').disabled=false;
                document.getElementById('fecha_mes').disabled=false;
                document.getElementById('fecha_ano').disabled=false;
                self.document.datos_articulo.cantidad.value=Number(self.document.datos_articulo.cantidad.value);
                }
        }

</script>
<?php 

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?><br></b></font><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width=450>
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <font size=2 face="<?= $fuente_general; ?>"><br><b><?=$nombre_aula?><br><small>
                                        Centro <i>"<?=$datos_centro["nombre_centro"];?>"</i>.</b><br>
                                        <b>Curso académico:</b>
                                <?php 
                                $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_aula["id_curso"])) or die(db_error()."-4");;
                                $cursos=db_fetch_array($consulta_cursos);

                                echo $cursos["nombre"];
                                ?><br></font></small></td>
                                </tr>
                        </table>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width=450>
                        <form name="datos_articulo" method="POST" action="nuevo_servicio_aula1.php">
                        <input type="hidden" name="id_aula" value="<?=$id_aula?>">
                        <input type="hidden" name="id_servicio" value="<?=$id_servicio?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Categoría:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="familia" onfocus="comprueba_al_entrar()" onchange="genera_listado()">
                                <option value="">Selecciona la categoria</option>
                                <?php 
                                $consulta_familias = db_query("SELECT * FROM tipos_iva_servicios WHERE tipo_servicios=0 ORDER BY categoria") or die (db_error());
                                while($datos_familias = db_fetch_array($consulta_familias)){
                                ?>
                                <option value="<?=$datos_familias["id_tipo_iva"];?>"<?php if ($familia==$datos_familias["id_tipo_iva"]) { print " selected"; }?>><?=$datos_familias["categoria"];?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                        <?php 
                        if ($articulo!=0) {
                                $estado_select="";
                                } else {
                                $estado_select=" disabled";
                                }
                        ?>
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Servicio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="articulo"<?=$estado_select;?> onchange="selecciona_unidad();">
                                  <option value="0">Selecciona el servicio</option>
                                  <?php 
                                  if (strlen($articulo)!=0) {
                                  $consulta_datos_articulos=db_query("SELECT * FROM tarifa WHERE CATEGORIA=$familia ORDER BY ARTICULO") or die(db_error());
                                  while($datos_articulos=db_fetch_array($consulta_datos_articulos)){
                                        ?>
                                        <option value="<?=$datos_articulos["ID"];?>"<?php if ($articulo==$datos_articulos["ID"]) { print " selected"; }?>><?=$datos_articulos["ARTICULO"];?></option>
                                        <?php 
                                        }
                                  }
                                  ?>
                                  </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Notas concepto:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="notas" value="<?=$notas?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Periodicidad:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="periodicidad" style="font-size: 10px" onchange="cambia_fechas()">
                                <option value="1"<?php  if ($periodicidad==1) { print " selected"; } ?>>Anual</option>
                                <option value="2"<?php  if ($periodicidad==2) { print " selected"; } ?>>Mensual</option>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Fecha inicio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_ini_dia" id="fecha_ini_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_ini_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ini_mes" id="fecha_ini_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_ini_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ini_ano" id="fecha_ini_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=date("Y")-5; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_ini_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Fecha fin:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_fin_dia" id="fecha_fin_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_fin_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_mes" id="fecha_fin_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_fin_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_ano" id="fecha_fin_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=date("Y")-5; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_fin_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Fecha única:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_dia" id="fecha_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_mes" id="fecha_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ano" id="fecha_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=date("Y")-5; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Precio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio" value="<?=$precio?>" size=10 style="font-size: 10px; text-align: right; background-color: #efefef; border: 1px solid #C0C0C0" readonly> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Cantidad:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" onblur="recalcular_precios();" value="<?=$cantidad;?>" name="cantidad" id="cantidad" size=10 style="font-size: 10px; text-align: right"> <input type="text" name="unidad" size=10 style="font-size: 10px; background-color: #efefef; border: 1px solid #C0C0C0" readonly></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Base imponible:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="base_imp" size=10 style="font-size: 10px; text-align: right; background-color: #efefef; border: 1px solid #C0C0C0" readonly> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Descuento:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="descuento" onblur="recalcular_precios();" value="<?=$descuento?>" size=10 style="font-size: 10px; text-align: right"> % <input type="text" name="importe_descuento" size=10 style="font-size: 10px; background-color: #efefef; border: 1px solid #C0C0C0; text-align: right" readonly> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Base Imp. Inc desc.:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_inc_desc" size=10 style="font-size: 10px; text-align: right; background-color: #efefef; border: 1px solid #C0C0C0" readonly> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">IVA:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="tipo_iva" value="<?=$tipo_iva;?>" size=10 style="font-size: 10px; text-align: right; background-color: #efefef; border: 1px solid #C0C0C0" readonly> % <input type="text" name="iva" size=10 style="font-size: 10px; background-color: #efefef; border: 1px solid #C0C0C0; text-align: right" readonly> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Total IVA Inc.:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_servicio" size=10 style="font-size: 10px; text-align: right; background-color: #efefef; border: 1px solid #C0C0C0" readonly> €</font></td>
                        </tr>
                        </form>
                         </table><br>
                         <input type="button" value="Cancelar" onclick="window.location.href='editar_servicios_aula.php?i=<?=$id_aula;?>'" style="font-size: 10px"> <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
</center></div>
</div>
<script languege="Javascript">
recalcular_precios();
</script>
</body>
</html>
