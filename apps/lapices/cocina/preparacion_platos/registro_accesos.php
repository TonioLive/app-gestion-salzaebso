<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php"); ?>
<?php  require("../../configuracion/autentifica.php"); ?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
<script lenguaje="Javascript">
self.focus();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="left">
<?php 
$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$idUsuario=$_GET["id_usuario"];

$consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=$idUsuario");

$datos_usuario=db_fetch_array($consulta_datos_usuario);

$nombre_completo_usuario=$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];

?>
<div align="center" id="principal_mesas" style="display:block;padding:0px;border:0px solid">
<table width=100% height=710 cellpadding=0 cellspacing=0 border=0>
<tr>
<td width=100% valign="top" align="center">
<form name="clave_usuario" method="POST" action="registro_accesos1.php">
<input type="hidden" name="id_usuario" value="<?=$idUsuario;?>">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br><br>
                <font size=5 face="<?= $fuente_general; ?>"><b><?=$nombre_completo_usuario?><br><br>Control de presencia</b></font><br><br><br>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="right" width=50%><font size=4 face="<?= $fuente_general; ?>"><i>Clave de empleado</i></font></td>
                <td align="left" width=50%><font size=4 face="<?= $fuente_general; ?>"><input type="password" size="4" maxlength="4" name="clave" style="font-size: 18px"></font></td>
                </tr>
        </table>
        <br><font size=4 face="<?= $fuente_general; ?>">Introduce tu clave de empleado y pulsa el botón <i>Aceptar</i></font><br><br>
        <input type="button" onclick="self.location.href='index.php'" value="Salir" style="font-size: 18px"> <input type="button" onclick="enviarpassword()" value="Aceptar" style="font-size: 18px"><br><br>
        </form>
</td>
</tr>
</table>
<?php 

        // Comprueba si hay mensajes

        if (strlen($centro_actual)==0) {
                $centro_actual=0;
                }

        $busca_mensajes=db_query("SELECT * FROM mensajeria_interna WHERE (id_centro_destino=0 OR id_centro_destino=$centro_actual) AND fecha_inicio<='".date("Y-m-d")."' AND fecha_caducidad>'".date("Y-m-d")."' ORDER BY fecha_publicacion DESC");

        if (db_num_rows($busca_mensajes)!=0) {
                // Antes
                ?><br>
                <font size=3><b>Circulares Internas</b></font><br><br>
                <?php 
                }

        $hay_no_ledidos=false;
        while($mensajes=db_fetch_array($busca_mensajes)) {
                // Comprueba si lo ha leido ya
                // Busca si el mensaje ha sido leido

                $busca_leido=db_query("SELECT * FROM mensajes_registro_lectura WHERE id_mensaje=".$mensajes["id_mensaje"]." AND id_usuario=$idUsuario;") or die(db_error());
                if (db_num_rows($busca_leido)==0) {
                        $texto_atencion=" <font color=red><b>¡NUEVO!</b></font>";
                        } else {
                        $texto_atencion="";
                        }
                ?>
                <table width="95%" border=1 cellpadding=2 cellspacing=0 style="border-collapse: collapse" bordercolor="#000000" bgcolor="#ffffff">
                <form name="mensaje<?=$mensajes["id_mensaje"];?>" method="POST" action="firmar_mensaje1.php">
                        <input type="hidden" name="id_usuario" value="<?=$idUsuario;?>">
                        <input type="hidden" name="id_mensaje" value="<?=$mensajes["id_mensaje"];?>">
                <tr>
                        <td colspan=2><font size=2><b>Asunto:</b> <?=app_url_text_utf8($mensajes["asunto"]);?></font></td>
                        <td colspan=2><font size=2><b>Fecha publicación:</b> <?=date("d/m/Y",strtotime($mensajes["fecha_publicacion"]));?><?=$texto_atencion?></font></td>
                </tr>
                <tr>
                        <td colspan=2><font size=2><b>Válido desde:</b> <?=date("d/m/Y",strtotime($mensajes["fecha_inicio"]));?></font></td>
                        <td colspan=2><font size=2><b>Hasta:</b> <?=date("d/m/Y",strtotime($mensajes["fecha_caducidad"]));?></font></td>
                </tr>
                <tr>
                        <td colspan=4><font size=2></font><?=app_url_text_utf8($mensajes["cuerpo"]);?></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <?php 
                if (db_num_rows($busca_leido)==0) {
                if ($mensajes["requerida"]==1) {
                        $hay_no_ledidos=true;
                        }
                ?>
                <td align="right" width=25%><font size=2 face="<?= $fuente_general; ?>"><b>Clave de empleado: </b></font></td>
                <td align="left" width=75% colspan=3><font size=2 face="<?= $fuente_general; ?>"><input type="password" size="4" maxlength="4" name="clave" style="font-size: 10px"> <input type="submit" value="Firmar mensaje" style="font-size: 10px"></font></td>
                <?php 
                } else {
                // datos leido
                $datos_leido=db_fetch_array($busca_leido);
                ?>
                <td align="center" colspan=4><font size=2 face="<?= $fuente_general; ?>"><i>Mensaje firmado el día <?=date("d/m/Y",strtotime($datos_leido["fecha"]));?> a las <?=date("d/m/Y",strtotime($datos_leido["hora"]));?></i></font></td>
                <?php 
                }
                ?>
                </tr>
                </form>
                </table>
                <br>
                <?php 
                }

        if (db_num_rows($busca_mensajes)!=0) {
                // Despues
                }

?>
</div>
</div>
<script language="Javascript">
function enviarpassword() {
        <?php 
        if ($hay_no_ledidos==false) {
                ?>
                document.clave_usuario.submit();
                <?php 
                } else {
                ?>
                alert("Hay circulares internas sin firmar.\n\nAntes de fichar debes leer y firmar todas las circulares.\n\nPara firmar una circular debes introducr tu clave de usuario en el cuadro de texto que encontrarás en cada mensaje y pulsar el botón 'Firmar mensaje'.\n\nUna vez firmados todos los mensajes podrás fichar.");
                <?php 
                }
        ?>
        }
</script>
</body>
</html>
