
</div>
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
<?php
if (!empty($familias_footer_scripts) && is_array($familias_footer_scripts)) {
    foreach ($familias_footer_scripts as $familias_footer_script) {
?>
<script src="<?php echo htmlspecialchars($familias_footer_script, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php
    }
}
?>
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
?> 
</body>
</html>
