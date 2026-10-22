<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

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

function tipo_sel(tipo) {
                if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
                        self.opener.document.coste.banco.value=tipo;
                        } else {
                        self.opener.coste.banco.value=tipo;
                        }
        window.close();
        }
function intro_nuevo_tipo() {
        if (document.nuevo.nuevo_tipo.value!="") {

                nueva_opcion=document.nuevo.nuevo_tipo.value;

                if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
                        self.opener.document.pon_nueva_opcion(nueva_opcion);
                        } else {
                        self.opener.pon_nueva_opcion(nueva_opcion);
                        }


                window.close();
                } else {
                alert("Debes introducir el nombre de la cuenta a la que se asignará el coste.");
                }
        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<form name="nuevo" action="javascript:intro_nuevo_tipo()">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=2><font size=1><b>Nueva cuenta</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center" colspan=2><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir nueva cuenta"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT * FROM cuentas_gastos ORDER BY id_cuenta");
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre la cuenta.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>CUENTAS DISPONIBLES</b></font></td>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        
        // Comprueba si se puede eliminar
        $busca_gastos=db_query_params('SELECT * FROM gestion_costes WHERE banco=?', 's', array($listado_tipos["id_cuenta"]));
        
        if ((db_num_rows($busca_gastos)==0) and ($listado_tipos["id_cuenta"]!=1)) {
                $link_borrar="<a href='eliminar_caja.php?id_caja=".$listado_tipos["id_cuenta"]."'>Eliminar</a>";
                } else {
                $link_borrar="";
                }
        
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=$listado_tipos["id_cuenta"];?>')"><b><?=$listado_tipos["nombre_cuenta"]?></b></a></font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$link_borrar?></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
</table>
</center></div>
</div>
</body>
</html>

