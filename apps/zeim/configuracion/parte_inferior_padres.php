	</div>
    </td>
  </tr>
<tr>
<td colspan="2">
<br><div align="center"><font size=1 face="<?=$fuente_general?>"><b><?=$texto_pie_pagina;?></b></font></div>
</td>
</tr>
</table>
</div>
<?php  require("inc/capas.php"); ?>
<!-- Modal -->
<div class="modal modal-wide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Tienes nuevos mensajes pendientes de lectura</h4>
      </div>
      <div class="modal-body">
          <iframe src="/webcam/ver_ultimo_mensaje.php" width="950" height="640" frameborder="0"></iframe>
      </div>    
    </div>
  </div>
</div>

<div class="modal modal-wide fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Mensajes enviados a los padres</h4>
      </div>
      <div class="modal-body">
          <iframe src="/webcam/ver_mensajes.php" width="950" height="640" frameborder="0"></iframe>
      </div>    
    </div>
  </div>
</div>

<div class="modal modal-wide fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModal2Label">AYUDAS ESCOLARIZACIÓN EDUCACIÓN INFANTIL PRIMER CICLO (BONO INFANTIL)</h4>
        <h4>CONVOCATORIA CURSO 2016-2017</h4>
      </div>
      <div class="modal-body">
      	<p class="text-center"><big>Aviso a los papas de los alumnos nacidos entre el año 2014 y el año 2016 y que vayan a asistir a nuestro centro el próximo curso. Para poder solicitar el bono infantil para el próximo curso escolar 2016-2017, debéis descargar, rellenar y traer firmada, al centro, la solicitud que encontrareís en el siguiente enlace.</big></p>
      	<p class="text-danger"><big>¡ATENCIÓN! Debéis rellenar la solicitud y firmarla pero no debéis indicar la fecha, ya que debe figurar la fecha en que se presenta en Consellería.</big></p>
		<p class="text-center"><h2 class="text-center"><a href="http://www.ceice.gva.es/documents/162640666/163264526/ANEXO+I+SOLICITUD+PADRES/44db6e79-3271-4686-894d-269f7202e0ad" target="_blank">Clic aquí para descargar solicitud de Ayuda</a></h2></p>
		<p class="text-center"><big>La solicitud debe ser traida al centro antes del día 28 de junio.</big></p>	
		<p class="text-center"><big>En el siguiente enlace teneis información sobre la documentación que debe ser aportada: <a href="http://www.ceice.gva.es/documents/162640666/163264526/DOCUMENTACION+SOLICITUD+CAS/ca174455-b758-46ba-be66-6dd1676880d9" target="_blank">Documentación a aportar</a></big></p>	
      </div>    
    </div>
  </div>
</div>

</body>
</html>

