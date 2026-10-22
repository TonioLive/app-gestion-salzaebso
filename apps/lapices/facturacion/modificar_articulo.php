<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$formulario_name="datos_articulo";

// Parametros
$codigo=$_GET["cod"];


$error_duplicado=$_GET["duplicado"];

// Valores de la tabla

$consulta_articulo = db_query("SELECT * FROM tarifa WHERE ID=$codigo") or die (db_query());
$datos_articulo = db_fetch_array($consulta_articulo);

$familia=$datos_articulo["CATEGORIA"];

$codigo_familia_actual=$datos_articulo["codigo_familia"];
$codigo_producto_actual=$datos_articulo["codigo_producto"];
$EAN=$datos_articulo["EAN"];
$EAN1=$datos_articulo["EAN"];
$ARTICULO=$datos_articulo["ARTICULO"];
$PRECIO_VENTA=$datos_articulo["PRECIO_VENTA"];
$PRECIO_COSTE=$datos_articulo["PRECIO_COSTE"];
$STOCK_MINIMO=$datos_articulo["STOCK_MINIMO"];
$PROVEEDOR=$datos_articulo["PROVEEDOR"];
$desglosar_como=$datos_articulo["desglosar_como"];
$UNIDAD=$datos_articulo["UNIDAD"];
$porcion_individual=$datos_articulo["porcion_individual"];
$TIPO_SERVICIO=$datos_articulo["TIPO_SERVICIO"];
$subcat=$datos_articulo["subcat"];
$ARTICULO_GENERICO=$datos_articulo["ARTICULO_GENERICO"];
$ID_ALIMENTO_GENERICO=$datos_articulo["ID_ALIMENTO_GENERICO"];
$SUBVENCION=$datos_articulo["SUBVENCION"];
$CONTROL_ASISTENCIA=$datos_articulo["CONTROL_ASISTENCIA"];
$ID_CENTRO_ASISTENCIA=$datos_articulo["ID_CENTRO_ASISTENCIA"];
$array_ID_CENTRO_ASISTENCIA=explode(",",$ID_CENTRO_ASISTENCIA);
if ($CONTROL_ASISTENCIA==1) {
        $fecha_ini_dia=date("j",strtotime($datos_articulo["FECHA_INICIO_ASISTENCIA"]));
        $fecha_ini_mes=date("n",strtotime($datos_articulo["FECHA_INICIO_ASISTENCIA"]));
        $fecha_ini_ano=date("Y",strtotime($datos_articulo["FECHA_INICIO_ASISTENCIA"]));
        $fecha_fin_dia=date("j",strtotime($datos_articulo["FECHA_FIN_ASISTENCIA"]));
        $fecha_fin_mes=date("n",strtotime($datos_articulo["FECHA_FIN_ASISTENCIA"]));
        $fecha_fin_ano=date("Y",strtotime($datos_articulo["FECHA_FIN_ASISTENCIA"]));
        } else {
        $fecha_ini_dia="";
        $fecha_ini_mes="";
        $fecha_ini_ano="";
        $fecha_fin_dia="";
        $fecha_fin_mes="";
        $fecha_fin_ano="";
        }
$INCLUIR_INFORME=$datos_articulo["INCLUIR_INFORME"];
$anula_comedor=$datos_articulo["anula_comedor"];
$anula_horas_extra=$datos_articulo["anula_horas_extra"];
$id_curso_articulo=$datos_articulo["id_curso"];
$tipo_facturacion_extra=$datos_articulo["tipo_facturacion_extra"];
$findemes=$datos_articulo["findemes"];
$unidad_medida=$datos_articulo["unidad_medida"];
?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">
<?php 
if (strlen($error_duplicado)!=0) {
$codigo_familia_actual=$_GET["codigo_familia"];
$codigo_producto_actual=$_GET["codigo_producto"];
$familia=urldecode($_GET["familia"]);
$EAN=$_GET["EAN"];
$EAN1=$_GET["EAN1"];
$ARTICULO=$_GET["ARTICULO"];
$PRECIO_VENTA=$_GET["PRECIO_VENTA"];
$PRECIO_COSTE=$_GET["PRECIO_COSTE"];
$STOCK_MINIMO=$_GET["STOCK_MINIMO"];
$PROVEEDOR=$_GET["PROVEEDOR"];
$desglosar_como=$_GET["desglosar_como"];
$UNIDAD=$_GET["UNIDAD"];
$porcion_individual=$_GET["porcion_individual"];
$TIPO_SERVICIO=$_GET["TIPO_SERVICIO"];
$subcat=$_GET["subcat"];
$ARTICULO_GENERICO=$_GET["ARTICULO_GENERICO"];
$ID_ALIMENTO_GENERICO=$_GET["ID_ALIMENTO_GENERICO"];
$SUBVENCION=$_GET["SUBVENCION"];
$CONTROL_ASISTENCIA=$_GET["CONTROL_ASISTENCIA"];
$ID_CENTRO_ASISTENCIA=$_GET["ID_CENTRO_ASISTENCIA"];
$array_ID_CENTRO_ASISTENCIA=explode(",",$ID_CENTRO_ASISTENCIA);
$fecha_ini_dia=$_GET["fecha_ini_dia"];
$fecha_ini_mes=$_GET["fecha_ini_mes"];
$fecha_ini_ano=$_GET["fecha_ini_ano"];
$fecha_fin_dia=$_GET["fecha_fin_dia"];
$fecha_fin_mes=$_GET["fecha_fin_mes"];
$fecha_fin_ano=$_GET["fecha_fin_ano"];
$INCLUIR_INFORME=$_GET["INCLUIR_INFORME"];
$anula_comedor=$_GET["anula_comedor"];
$anula_horas_extra=$_GET["anula_horas_extra"];
$id_curso_articulo=$_GET["id_curso_articulo"];
$tipo_facturacion_extra=$_GET["tipo_facturacion_extra"];
$findemes=$_GET["findemes"];
$unidad_medida=$_GET["unidad_medida"];
?>
alert("El código de barras introducido ya se encuentra en uso.");
<?php 
}
?>
        
function comprueba_y_envia(accion) {
        var errores="";

        if (accion==1) {
                self.document.datos_articulo.codigo.value=0;
                }

        if (self.document.datos_articulo.PRECIO_VENTA.value=="") {
                errores=errores+"Falta introducir el precio de venta.\n";
                } else {
                if (isNaN(self.document.datos_articulo.PRECIO_VENTA.value)) {
                        errores=errores+"El precio de venta debe ser un número.\n";
                        }
                }
        if (self.document.datos_articulo.PRECIO_COSTE.value=="") {
                errores=errores+"Falta introducir el precio de coste.\n";
                } else {
                if (isNaN(self.document.datos_articulo.PRECIO_COSTE.value)) {
                        errores=errores+"El precio de coste debe ser un número.\n";
                        }
                }
        if (self.document.datos_articulo.porcion_individual.value=="") {
                // errores=errores+"Falta introducir el precio de venta.\n";
                } else {
                if (isNaN(self.document.datos_articulo.porcion_individual.value)) {
                        errores=errores+"El tamaño de la ración individual debe ser un número.\n";
                        }
                }
        if (self.document.datos_articulo.ARTICULO.value=="") {
                errores=errores+"Falta introducir la denominación del servicio/artículo.\n";
                }
        if (self.document.datos_articulo.PROVEEDOR.value=="") {
                errores=errores+"Falta seleccionar el proveedor.\n";
                }
        if (self.document.datos_articulo.STOCK_MINIMO.value=="") {
                // errores=errores+"Falta introducir el stock mínimo del artículo.\n";
                } else {
                if (isNaN(self.document.datos_articulo.STOCK_MINIMO.value)) {
                        errores=errores+"El stock mínimo del artículo debe ser un número.\n";
                        }
                }
        if (self.document.<?=$formulario_name;?>.CONTROL_ASISTENCIA[0].checked) {
        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_ini_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_ini_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_ini_ano.value
        if (isDate(la_fecha)!=true) { errores=errores+"La fecha de inicio del periodo de asistencia introducida no es correcta.\u000A"; }

        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value
        if (isDate(la_fecha)!=true) { errores=errores+"La fecha de fin del periodo de asistencia introducida no es correcta.\u000A"; }
        }
        
        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos_articulo.submit();
                }
        }
function intro_generico() {
        window.open('listado_genericos.php','genericos','toolbar=no,resizable=no,width=250,height=400');
        }
function nuevo_proveedor() {
        window.open('/almacen/nuevo_proveedor.php','proveedores','toolbar=no,resizable=no,width=600,height=585');
        }
function editar_proveedor() {
        if (document.datos_articulo.PROVEEDOR.value=="") {
                alert("No has seleccionado un agente para editar");
                } else {
                window.open('/almacen/nuevo_proveedor.php?u='+document.datos_articulo.PROVEEDOR.value,'proveedores','toolbar=no,resizable=no,width=600,height=585');
                }
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
</script>
<?php 


$familia=urldecode($familia);
$consulta_datos_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$familia");
if (db_num_rows($consulta_datos_familia)!=0) {
                                $datos_familia=db_fetch_array($consulta_datos_familia);
                                $nombre_familia=$datos_familia["categoria"];
                                $tipo_servicios=$datos_familia["tipo_servicios"];
                                } else {
                                $nombre_familia="Desconocida";
                                $tipo_servicios=0;
                                }
// Busca info tipo servicios
$busca_nombres_tipo=db_query("SELECT * FROM tipos_categorias WHERE id_tipo=".$tipo_servicios);
$nombres_tipo=db_fetch_array($busca_nombres_tipo);
$tipo_actual_nombre=$nombres_tipo["nombre_tipo_cat"];


?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Modificar servicio/artículo de la familia <i><?=$nombre_familia?></i><br></b></font><br>
                        Tipo de servicio: <?=$tipo_actual_nombre?><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_articulo" method="POST" action="modificar_articulo1.php">
                        <input type="hidden" name="volver" value="0">
                        <input type="hidden" name="codigo" value="<?=$codigo;?>">
                        <input type="hidden" name="EAN1" value="<?=$EAN1;?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Curso:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">
<select name="id_curso_articulo" size=1 style="font-size: 10px">
                        <option value="0">Todos</option>
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_articulo) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></font></td>
                        </tr>
                        <tr>      
                                <td colspan="2"><label><b>Datos para Contabilidad</b></label></td>
                        </tr>    
                        <tr>
                                <td align="right" bgcolor="#ececec">
                                        <font size=1 face="<?= $fuente_general; ?>">Familia contable:</font>
                                </td>
                                <td align="left" bgcolor="#ffffff">
                                        <?php
                                        $sql = "SELECT codigo_familia, nombre_familia
                                                FROM tarifa_familias_contabilidad
                                                ORDER BY nombre_familia";

                                        $rsFamilias = db_query($sql);
                                        ?>                                                
                                        <select name="codigo_familia" id="codigo_familia">
                                        <option value="0">-- Sin familia contable --</option>

                                        <?php while ($familiaCont = db_fetch_assoc($rsFamilias)) { ?>
                                                <option value="<?php echo $familiaCont["codigo_familia"]; ?>"
                                                <?php if ($codigo_familia_actual == $familiaCont["codigo_familia"]) echo "selected"; ?>>
                                                <?php echo $familiaCont["nombre_familia"]." - ".$familiaCont["codigo_familia"]; ?>
                                                </option>
                                        <?php } ?>
                                        </select>
                                </td>
                        </tr>
                        <tr>
                                <td align="right" bgcolor="#ececec">
                                        <font size=1 face="<?= $fuente_general; ?>">Producto contable:</font>
                                </td>
                                <td align="left" bgcolor="#ffffff">
                                        <select name="codigo_producto" id="codigo_producto">
                                        <option value="0">-- Sin producto contable --</option>
                                        </select>
                                </td>
                        </tr>        
                        <tr>
                                <td colspan="2">
                                        <label><b>Datos servicio</b></label>
                                </td>  
                        </tr>                          
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Familia:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="familia" size=1 style="font-size: 10px">
                                <?php 
                                $consulta_familia = db_query("SELECT * FROM tipos_iva_servicios,tipos_categorias WHERE tipos_iva_servicios.tipo_servicios=tipos_categorias.id_tipo ORDER BY tipo_servicios,categoria") or die (db_error());
								$tipoActual="";
                                while($datos_familia = db_fetch_array($consulta_familia)){
									if ($tipoActual!=$datos_familia["nombre_tipo_cat"]) {										
										if ($tipoActual!="") {
											echo("</optgroup>");
										}										
										$tipoActual=$datos_familia["nombre_tipo_cat"];																				
										?>										
										<optgroup label="<?=$tipoActual?>">
										<?php
									}
                                ?>
                                <option value="<?=$datos_familia["id_tipo_iva"];?>"<?php  if ($datos_familia["id_tipo_iva"]==$familia) { echo(" selected"); } ?>><?=$datos_familia["categoria"];?></option>
                                <?php 
                                }
                                ?>
								</optgroup>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Servicio incluido en factura fin de mes:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="findemes" value="0"<?php  if ($findemes==0) { print " checked"; } ?>>Si &nbsp; &nbsp;<input type="radio" name="findemes" value="1"<?php  if ($findemes==1) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Desglosar en facturación como:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="desglosar_como" size=1 style="font-size: 10px">
                                <?php 
                                $consulta_desglsoe = db_query("SELECT * FROM tipos_deslose_facturacion ORDER BY nombre_desglose") or die (db_error());
                                while($datos_desglsoe = db_fetch_array($consulta_desglsoe)){
                                ?>
                                <option value="<?=$datos_desglsoe["id_desglose"];?>"<?php  if ($datos_desglsoe["id_desglose"]==$desglosar_como) { echo(" selected"); } ?>><?=$datos_desglsoe["nombre_desglose"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Cuota:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="subcat" size=1 style="font-size: 10px">
                          <option value="0"<?php  if ($subcat==0) { print " selected"; } ?>>Otras</option>
                          <?php 
                                $consulta_familia = db_query("SELECT * FROM subcategorias ORDER BY nombre_subcat") or die (db_error());
                                while($datos_familia = db_fetch_array($consulta_familia)){
                                ?>
                                <option value="<?=$datos_familia["id_subcat"];?>"<?php  if ($datos_familia["id_subcat"]==$subcat) { echo(" selected"); } ?>><?=$datos_familia["nombre_subcat"];?></option>
                                <?php 
                                }
                          ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Cód. de barras:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="EAN" value="<?=$EAN?>" size=25 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Denominación:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="ARTICULO" value="<?=$ARTICULO?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre genérico:</font></td>
                                <td bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="ARTICULO_GENERICO" size=60 readonly value="<?=$ARTICULO_GENERICO;?>" style="font-size: 10px" onclick="intro_generico()"></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Alimento genérico:</font></td>
                                <td bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="ID_ALIMENTO_GENERICO" style="font-size: 10px">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM menu_alimentos ORDER BY nombre_alimento");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
                                // Busca si esta
                                $busca_ing=db_query("SELECT * FROM menu_ingredientes WHERE id_plato=$id_plato AND id_alimento=".$listado_articulos["id_alimento"]);
                                if (db_num_rows($busca_ing)==0) {
                                        ?>
                                        <option value="<?=$listado_articulos["id_alimento"]?>"<?php  if ($listado_articulos["id_alimento"]==$ID_ALIMENTO_GENERICO) { print " selected"; } ?>><?=urldecode($listado_articulos["nombre_alimento"])?></option>
                                        <?php 
                                        }
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <?php 
                        if ($tipo_servicios==0) {
                        ?>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Tipo (horas extra):</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">                          <select name="TIPO_SERVICIO" size=1 style="font-size: 10px">
                          <option value="0"<?php  if ($TIPO_SERVICIO==0) { print " selected"; } ?>>Normal</option>
                          <option value="1"<?php  if ($TIPO_SERVICIO==1) { print " selected"; } ?>>Hora extra mensual</option>
                          <option value="2"<?php  if ($TIPO_SERVICIO==2) { print " selected"; } ?>>Hora extra suelta</option>
                          </select></font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <input type="hidden" name="TIPO_SERVICIO" value="<?=$tipo_servicios+2?>">
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">¿Anula comedor?</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="anula_comedor" value="1"<?php  if ($anula_comedor==1) { print " checked"; } ?>>Si &nbsp; &nbsp;<input type="radio" name="anula_comedor" value="0"<?php  if ($anula_comedor==0) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">¿Anula horas extra?</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="anula_horas_extra" value="1"<?php  if ($anula_horas_extra==1) { print " checked"; } ?>>Si &nbsp; &nbsp;<input type="radio" name="anula_horas_extra" value="0"<?php  if ($anula_horas_extra==0) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">¿Es una subvención?</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="SUBVENCION" value="1"<?php  if ($SUBVENCION==1) { print " checked"; } ?>>Si &nbsp; &nbsp;<input type="radio" name="SUBVENCION" value="0"<?php  if ($SUBVENCION==0) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Unidades o cantidad del paquete:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="UNIDAD" value="<?=$UNIDAD?>" size=30 style="font-size: 10px"><br><i><b>ATENCION:</b> Indicar el peso (en gr.) o el volumen (en ml.) de la unidad de venta. Por ejemplo: 1 bote de tomate frito = 450 (solo hay que poner la cifra)</i></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Unidades (para pedidos de centro):</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="unidad_medida" value="<?=$unidad_medida?>" size=30 style="font-size: 10px"><br><i><b>NOTA:</b> Son las cantidades que se visualizan a la hora de realizar un pedido de material.</i></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Ración individual:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="porcion_individual" value="<?=$porcion_individual?>" size=30 style="font-size: 10px"><br><i><b>ATENCIÓN:</b> Rellenar solo en el caso de los alimentos que se suministren en porciones individuales (Quesitos, yogures, huevos, etc.) En estos casos en este dato hay que introducir el peso (en gr.) o el volumen (en ml.) de dicha porción.</i></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Precio de venta:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="PRECIO_VENTA" value="<?=$PRECIO_VENTA?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Precio de coste:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="PRECIO_COSTE" value="<?=$PRECIO_COSTE?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Stock mínimo:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="STOCK_MINIMO" value="<?=$STOCK_MINIMO?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Proveedor:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="PROVEEDOR" size=1 style="font-size: 10px">
                                <option value="">Selecciona el proveedor</option>
                                <?php 
                                $consulta_familia = db_query("SELECT * FROM proveedores ORDER BY RAZON_SOCIAL") or die (odbc_errormsg());
                                while($datos_familia = db_fetch_array($consulta_familia)){
                                ?>
                                <option value="<?=$datos_familia["ID_PROVEEDOR"];?>"<?php  if ($datos_familia["ID_PROVEEDOR"]==$PROVEEDOR) { echo(" selected"); } ?>><?=$datos_familia["RAZON_SOCIAL"];?></option>
                                <?php 
                                }
                                ?>
                          </select> <input type="button" style="font-size: 10px" value="Nuevo proveedor" onclick="nuevo_proveedor()"> <input type="button" style="font-size: 10px" value="Editar proveedor" onclick="editar_proveedor()"> </font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">¿Incluir en informe?</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="INCLUIR_INFORME" value="1"<?php  if ($INCLUIR_INFORME==1) { echo(" checked"); } ?>>Si &nbsp; &nbsp;<input type="radio" name="INCLUIR_INFORME" value="0"<?php  if ($INCLUIR_INFORME==0) { echo(" checked"); } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">¿Necesita control de asistencia?</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="CONTROL_ASISTENCIA" value="1"<?php  if ($CONTROL_ASISTENCIA==1) { echo(" checked"); } ?>>Si &nbsp; &nbsp;<input type="radio" name="CONTROL_ASISTENCIA" value="0"<?php  if ($CONTROL_ASISTENCIA==0) { echo(" checked"); } ?>> No</font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Escuela</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                $contador_centros=0;
                while($centros=db_fetch_array($consulta_centros)){
                $mostrar="";
                for ($c_array_centro=0;$c_array_centro<=count($array_ID_CENTRO_ASISTENCIA);$c_array_centro++) {
                        if ($array_ID_CENTRO_ASISTENCIA[$c_array_centro]==$centros["id_centro"]) {
                                $mostrar=" checked";
                                }
                        }
                ?>
                <input type="checkbox" name="ID_CENTRO_ASISTENCIA[<?=$contador_centros;?>]" value="<?=$centros["id_centro"];?>"<?=$mostrar;?>> <?=$centros["nombre_centro"];?><br>
                <?php 
                $contador_centros++;
                }
                ?></font></td>
                        </tr>
                        <tr valign="top" id="mensual1">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Fecha inicio control de asistencia:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_ini_dia" style="font-size: 10px">
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
                                                                      </select> <select name="fecha_ini_mes" style="font-size: 10px">
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
                                                                      </select> <select name="fecha_ini_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=date("Y")-15; $c_ano=$c_ano-1) {
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_ini','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                        </tr>
                        <tr valign="top" id="mensual2">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Fecha fin control de asistencia:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_fin_dia" style="font-size: 10px">
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
                                                                      </select> <select name="fecha_fin_mes" style="font-size: 10px">
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
                                                                      </select> <select name="fecha_fin_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=date("Y")-15; $c_ano=$c_ano-1) {
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
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_fin','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                        </tr>
                        <tr valign="top">
                           <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Método de cobro:</font></td>
                           <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="tipo_facturacion_extra" value="0"<?php  if ($tipo_facturacion_extra==0) { echo(" checked"); } ?>>Día a día &nbsp; &nbsp;<input type="radio" name="tipo_facturacion_extra" value="1"<?php  if ($tipo_facturacion_extra==1) { echo(" checked"); } ?>> En factura mensual asignando el servicio al alumno.</font></td>
                        </tr>
                        </form>
                         </table><br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia(0);"> <input style="font-size: 10px" type="button" value="Guardar como duplicado" onclick="comprueba_y_envia(1);"> <input type="button" value="Cancelar" onclick="window.location.href='ver_familia.php?familia=<?=urlencode($familia);?>'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br>
<br>
<script>
        var codigoProductoActual = <?php echo intval($codigo_producto_actual); ?>;

        function cargarProductosContabilidad(codigoFamilia, codigoProductoSeleccionado) {
        fetch("ajax_productos_contabilidad.php?codigo_familia=" + encodeURIComponent(codigoFamilia) +
                "&codigo_producto=" + encodeURIComponent(codigoProductoSeleccionado))
                .then(response => response.text())
                .then(html => {
                document.getElementById("codigo_producto").innerHTML = html;
                });
        }

        document.getElementById("codigo_familia").addEventListener("change", function () {
        var codigoFamilia = this.value;

        if (codigoFamilia == "0") {
                document.getElementById("codigo_producto").innerHTML =
                '<option value="0">-- Sin producto contable --</option>';
                return;
        }

        cargarProductosContabilidad(codigoFamilia, 0);
        });

        if (<?php echo intval($codigo_familia_actual); ?> > 0) {
        cargarProductosContabilidad(
                <?php echo intval($codigo_familia_actual); ?>,
                codigoProductoActual
        );
        }
</script>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
