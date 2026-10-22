<?php  require("../../configuracion/config.php"); ?>
<?php  require("../../configuracion/autentifica.php"); ?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
</style>
<script lenguaje="Javascript">
self.focus();
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="no">
<div align="left">
<?php 
$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

?>
<div align="center" id="principal_mesas" style="display:block;padding:0px;border:0px solid">
<table width=710 height=710 cellpadding=0 cellspacing=0 border=0>
<?php 
// Comprueba turno

$hora_actual=strtotime(date("H:i:s"));
$hora_cambio_turno=strtotime("14:30:00");

if ($hora_actual>$hora_cambio_turno) {
$turno=1;
} else {
$turno=0;
}

// Capa de mesas
$busca_categorias1=db_query("SELECT * FROM usuarios WHERE (nivel_acceso=10 OR nivel_acceso=11) ORDER BY Apellido1, Apellido2, Nombre") or die(db_error());

$numero_de_botones=db_num_rows($busca_categorias1);

$columnas=5;

$filas=round(($numero_de_botones/$columnas),0);

if ($filas<($numero_de_botones/$columnas)) {
        $filas=$filas+1;
        }
if ($filas<5) {
        $filas=5;
        }

$ancho_tabla=710;
$ancho_columnas=round(($ancho_tabla/$columnas),0);
$alto_filas=round(($ancho_tabla/$filas),0);

$fila=0;
$columna=0;
$total_celdas=0;
while($categorias1=db_fetch_array($busca_categorias1)) {
if ($columna==0) {
        echo "<tr>";
        }
        
$busca_registro_entrada=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND hora_entrada<=? AND fecha=?', 'sss', array($categorias1["id_usuario"], (string)(date("H:i:s")), (string)(date("Y-m-d")))) or die(db_error());

if (db_num_rows($busca_registro_entrada)!=0) {
        // Comprueba si ha fichado salida
        while($datos_registro=db_fetch_array($busca_registro_entrada)) {
        if ($datos_registro["hora_salida"]=="") {
                $ok_para_cobrar=true;
                break;
                } else {
                $ok_para_cobrar=false;
                }
        }
        } else {
        $ok_para_cobrar=false;
        }

if ($categorias1["tipo_mesa"]!=0) {
        $ok_para_cobrar=true;
        }

if ($desactivar_fichar==true) {
        $ok_para_cobrar=true;
        }

if ($ok_para_cobrar==true) {

$trozo=" onclick=\"top.window.location.href='ver_platos_hoy.php?id_mesa=".$categorias1["id_usuario"]."';\"";

$color_mesa="#FFFF99";

} else {

$trozo=" onclick=\"self.location.href='registro_accesos.php?id_usuario=".$categorias1["id_usuario"]."';\"";

$color_mesa="#ff5656";

}
?>
<td bgcolor="#ffffff" width=142 height=142 align="center"><input type="button"<?=$trozo?> value="<?=$categorias1["Nombre"];?> <?=$categorias1["Apellido1"];?>" style="font-size: 10px" style="height: <?=$alto_filas-2?>; width: <?=$ancho_columnas-2?>; border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; text-align=center; font-weight: bold; background-color: <?=$color_mesa;?>"></td>
<?php 
$total_celdas++;
$columna++;
if ($columna==$columnas) {
        echo "</tr>";
        $columna=0;
        $fila++;
        }
if ($fila==$filas) {
        break;
        }
}
for ($faltan=$total_celdas;$faltan<=($filas*$columnas);$faltan++) {
        if ($columna==0) {
                echo "<tr>";
                }
        ?>
        <td bgcolor="#FFFFFF" width=<?=$ancho_columnas?> height=<?=$alto_filas?> align="center">&nbsp;</td>
        <?php 
        $columna++;
        if ($columna==$columnas) {
                echo "</tr>";
                $columna=0;
                $fila++;
                }
        if ($fila==$filas) {
                break;
                }
        }

?>
</table>
</div>
<?php 
db_close();
?>
</div>
</body>
</html>
