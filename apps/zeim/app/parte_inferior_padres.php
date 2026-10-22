
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="/app/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/app/bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/app/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/app/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/app/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/app/dist/js/demo.js"></script>

<script src="/webcam/js/lightbox/ekko-lightbox.min.js"></script>
<?php
if ((isMobile()) and ($comprobarMovil==true)) {
?>
<script>
	
	function cierra_alerta() {
		// $('#alertaMoviles').alert('close')
		$('#alertaMoviles').hide();
	}
	
	function comprueba_orientacion() {
		
		var isAndroid = /(android)/i.test(navigator.userAgent);
		
		if(isAndroid)
		{
		    if(screen.width > screen.height){
		        //portrait mode on Android
		        cierra_alerta();
		    } else {
			    $('#alertaMoviles').show();
		    }
		} else {
		    if(window.orientation != 0){
		        //portrait mode iOS and other devices
		        cierra_alerta();
		    }
		}		
				
	}
	
	var supportsOrientationChange = "onorientationchange" in window,
    orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

	window.addEventListener(orientationEvent, function() {
    	
		comprueba_orientacion();
    	
	}, false);
	
	$( document ).ready(function() {
		
	    comprueba_orientacion();
	    
	});

</script>
<?php	
}

// Muestra modal circulares nuevas
/*
  if ($hay_circulares==true) { 
?>
<script>  
	$(document).ready(function(){
			$('#modalCirculares').modal('show');
	});
</script>
<?php
} 
*/ 
?> 
<script>
	// Al hacer clic en el enlace, cambiar el src del iframe
	$('#cambiarSrc').click(function(e) {
		e.preventDefault(); // Evitar el comportamiento por defecto del enlace
		var nuevaUrl = $(this).data('url'); // Obtener la nueva URL del atributo data-url
		$('#iFrameCirculares').attr('src', nuevaUrl); // Cambiar el src del iframe
		$('#modalCirculares').modal('show');
	});
</script>

<?php
if ($hayEncuestas==true) {
?>
<!-- Cargar jQuery antes del script -->
<script>
    $(document).ready(function() {
        $("#validarBtn").click(function(event) {
            let valido = true;

            $(".grupo").each(function() {
                let $grupo = $(this);
                let opcionSeleccionada = $grupo.find('input[type="radio"]:checked');

                if (opcionSeleccionada.length === 0) {
                    alert("Debe seleccionar una opción en cada alumno/a.");
                    valido = false;
                    return false; // Detiene la iteración
                }

                if (opcionSeleccionada.val() === "3") {
                    let inputHorario = $grupo.find('textarea');
                    let textoHorario = inputHorario.val().trim();

                    if (textoHorario === "") {
                        alert("Si selecciona la opción de asistir algunos días, debe indicar cuáles son en el cuadro de texto.");
                        valido = false;
                        return false;
                    }

                    if (textoHorario.length > 255) {
                        alert("El texto no puede superar los 255 caracteres.");
                        valido = false;
                        return false;
                    }
                }
            });

            if (!valido) {
                event.preventDefault(); // Evita el envío si hay errores
            } else {
                $("#validarBtn")
                        .prop("disabled", true)
                        .text("Enviando...");
                $("#encuestas").submit(); // Envía el formulario si es válido
            }
        });

        // Limitar caracteres en tiempo real
        $("textarea").on("input", function() {
            let maxLength = 255;
            let text = $(this).val();

            if (text.length > maxLength) {
                $(this).val(text.substring(0, maxLength)); // Corta el texto al máximo permitido
                alert("Máximo 255 caracteres permitidos.");
            }
        });
    });
</script>
<?php
}
?>
<!-- Modal Circulares-->
<div class="modal fade" id="modalCirculares" tabindex="-1" role="dialog" aria-labelledby="modalCircularesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 100%!important;">
                <div class="modal-content">
                        <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="modalCircularesLabel">Circulares enviadas por email</h4>
                        </div>
                        <div class="modal-body" style="height: calc(100vh - 56px - 30px)!important;">
                                <iframe src="/app/ver_ultimo_mensaje.php" id="iFrameCirculares" width="100%" height="600" frameborder="0" style="width: 100%;height: 100%;"></iframe>
                        </div>    
                </div>
        </div>
</div> 

<script src="/app/assets/familias-app.js?v=20260923"></script><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><script src="/app/assets/zeim-forms.js?v=20260923"></script></body>
</html>