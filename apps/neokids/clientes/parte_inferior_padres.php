  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2014-<?=date("Y");?> <a href="http://www.neo-kids.es">Neokids</a>.</strong>
  </footer>

<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="/clientes/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/clientes/bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/clientes/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/clientes/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/clientes/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/clientes/dist/js/demo.js"></script>
<?php
if(isMobile()) {
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