<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Datos
$id_aula=$_GET["id_aula"];

$modelo=$_GET["m"];
if (strlen($modelo)==0) {
        $modelo=2;
        }

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
        $id_curso_actual=$curso_seleccionado;
        }
        
                        $familia=1;

                        $where_de_consulta=" WHERE ";

                        if (strlen($familia)!=0) {
                                $familia=urldecode($familia);
                                $consulta_datos_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$familia");
                                if (db_num_rows($consulta_datos_familia)!=0) {
                                        $datos_familia=db_fetch_array($consulta_datos_familia);
                                        $nombre_familia=$datos_familia["categoria"];
                                        } else {
                                        $nombre_familia="Desconocida";
                                        }
                                if ($where_de_consulta!=" WHERE ") {
                                        $where_de_consulta=$where_de_consulta."AND ";
                                        }
                                $where_de_consulta=$where_de_consulta."CATEGORIA=".$familia." ";
                                }

                        if ($where_de_consulta==" WHERE ") {
                                $where_de_consulta="";
                                }
        
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Servicios que no deben salir en el listado</b></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <input type="button" onclick="document.listado_servicios.submit();" value="Imprimir presupuesto">
                        <br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <form name="listado_servicios" action="/pdf/presupuesto_inscritos_aula.php" method="GET">
                        <input type="hidden" name="id_aula" value="<?=$id_aula?>">
                        <input type="hidden" name="m" value="<?=$modelo?>">
                        <input type="hidden" name="id_servicios[]" value="0">
                        <?php 
                        $consulta1=array('SELECT * FROM tarifa,tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=? OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO', 's', array($id_curso_actual));

                        $consulta_stock = db_query_params($consulta1[0], $consulta1[1], $consulta1[2]) or die (db_error());

                        $numRecords = db_num_rows ($consulta_stock);

                        $familia_actual="";

                        while($datos_stock = db_fetch_array($consulta_stock)){

                        $se_puede_eliminar=true;

                        $id_articulo_tarifa=$datos_stock["ID"];

                        if ($familia_actual!=$datos_stock["categoria"]) {
                                $familia_actual=$datos_stock["categoria"];
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td align="left" colspan=3><font size=2 face="<?= $fuente_general; ?>"><?=$familia_actual?></font></td>
                                </tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>">ID</font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>">Denominación</font></td>
                                </tr>
                                <?php 
                                }

                        ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="checkbox" name="id_servicios[]" value="<?=$id_articulo_tarifa;?>"></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["ID"];?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["ARTICULO"];?></font></td>
                                </tr>
                        <?php 
                        $contador=$contador+1;
                        }
                        ?>
                        </form>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.close()" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
  </td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>

