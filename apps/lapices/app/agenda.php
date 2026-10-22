<?php
require __DIR__ . '/agenda_familias.php';
exit;
?>
<?php
require("../configuracion/config.php"); 	

if (empty($_SESSION['permitido_padre'])) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Calendario
        <small>Inicio y fin de los periodos, días de asistencia, festivos, etc...</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app/index.php"><i class="fa fa-home"></i> Inicio</a></li>
		<li class="active"><i class="fa fa-calendar"></i> Calendario</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
if ((isMobile()) and ($comprobarMovil==true)) {
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alertaMoviles">
	<div class="row">
		<div class="col-xs-10">
			<img src="/app/imagenes/rotar_movil_blanco.png" width="48" border=0 class="pull-right">
			<strong>¿Estás usando un movil?</strong><br>El calendario se ve mejor con el movil en posición horizontal...	
		</div>
		<div class="col-xs-2">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		</div>
	</div>    
</div>
<?php	
}	
?>
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
            </div>
            <!-- /.box-body -->
          </div>  
	  
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- fullCalendar 2.2.5 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/fullcalendar/fullcalendar.min.js"></script>
<script src='plugins/fullcalendar/lang/es.js'></script>
<!-- Page specific script -->
<script>
  $(function () {

	var currentLangCode = 'es';
  
    /* initialize the external events
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        };

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 1070,
          revert: true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      });
    }

    ini_events($('#external-events div.external-event'));

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();
    $('#calendar').fullCalendar({
	  eventStartEditable : false,
	  lang: currentLangCode,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      //Eventos del calendario
	  events: {
			url: 'agenda/get-agenda.php',
			error: function() {
				$('#script-warning').show();
			}
	  },
	  eventClick: function(event) {
			if (event.url) {
				window.open(event.url,'info_evento','toolbar=false,scrollbars=yes,resizable=true,width=800,height=600');
				return false;
				}
	  },
      editable: false,
      droppable: false, // this allows things to be dropped onto the calendar !!!
    });

    /* ADDING EVENTS */
    var currColor = "#3c8dbc"; //Red by default
    //Color chooser button
    var colorChooser = $("#color-chooser-btn");
    $("#color-chooser > li > a").click(function (e) {
      e.preventDefault();
      //Save color
      currColor = $(this).css("color");
      //Add color effect to button
      $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
    });
    $("#add-new-event").click(function (e) {
      e.preventDefault();
      //Get value and make sure it is not null
      var val = $("#new-event").val();
      if (val.length == 0) {
        return;
      }

      //Create events
      var event = $("<div />");
      event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
      event.html(val);
      $('#external-events').prepend(event);

      //Add draggable funtionality
      ini_events(event);

      //Remove event from text input
      $("#new-event").val("");
    });
  });
</script>
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