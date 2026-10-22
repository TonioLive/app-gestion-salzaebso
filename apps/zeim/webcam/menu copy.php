<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";
    ?>
        <table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
        <tr><td><img src="/webcam/imagenes/encabezado_principal.gif" border=0></tr></td>
        <tr><td><table width=690 border=2 bordercolor="c4cf2b">
        <tr><td align="center">
            <table>
            <tr>
                <td>
                    <a href="principal.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','/webcam/imagenes/webcam_act.gif',1)"><img src="/webcam/imagenes/webcam_des.gif" alt="Webcam" name="Image1" border=0 id="Image1"></a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="comedor.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','/webcam/imagenes/comedor_act.gif',1)"><img src="/webcam/imagenes/comedor_des.gif" alt="Comedor" name="Image2" border=0 id="Image2"></a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="/webcam/facturas.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','/webcam/imagenes/ver_facturas_act.gif',1)"><img src="/webcam/imagenes/ver_facturas_des.gif" alt="Blog" name="Image5" border=0 id="Image5"></a>
                </td>
            </tr>
            <tr>
                <td>
            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal1">
  				Ver todos los mensajes
			</button>
                </td>
            </tr>
            <!--
            <tr>
                <td>
                    <a href="http://padres.escuelainfantilsantpau.com/blog" target="blog" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/blog_act.gif',1)"><img src="/webcam/imagenes/blog_des.gif" alt="Blog" name="Image3" border=0 id="Image3"></a>
                </td>
            </tr>-->
            <tr>
                <td>
                    <a href="/configuracion/cerrar_sesion_padres.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Salir" name="Image4" border=0 id="Image4"></a>
                </td>
            </tr>
            </table>
        </td></tr>
        </table>
        </td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_menu.gif" border=0></td></tr>
        </table>
    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
