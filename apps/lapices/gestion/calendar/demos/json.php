<!DOCTYPE html>
<?php 
$id_aula=$_GET["id_aula"];
?>
<html>
<head>
<meta charset='utf-8' />
<link href='../fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='../fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../lib/moment.min.js'></script>
<script src='../lib/jquery.min.js'></script>
<script src='../lib/jquery-ui.custom.min.js'></script>
<script src='../fullcalendar/fullcalendar.min.js'></script>
<script src='../fullcalendar/lang/es.js'></script>
<script>

	$(document).ready(function() {
	var currentLangCode = 'es';
		$('#calendar').fullCalendar({
			lang: currentLangCode,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			defaultDate: '<?=date("Y-m-d");?>',
			editable: true,
			weekends: false,
			allDaySlot: true,
			events: {
				url: 'php/get-agenda.php?id_aula=<?=$id_aula?>',
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
		    eventDrop: function(event, revertFunc) {

        		// alert(event.title + " va a ser movido a " + event.start.format());

        		if (!confirm("¿Estás segura/o de este cambio?")) {
            		revertFunc();
        			} else {
        			// envía los cambios a la base de datos
        			
        			}

    		},
			loading: function(bool) {
				$('#loading').toggle(bool);
			}
		});
		
	});

</script>
<style>

	body {
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#script-warning {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}

	#loading {
		display: none;
		position: absolute;
		top: 10px;
		right: 10px;
	}

	#calendar {
		width: 900px;
		margin: 40px auto;
	}

</style>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">

	<div id='script-warning'>
		<code>php/get-events.php</code> must be running.
	</div>

	<div id='loading'>loading...</div>

	<div id='calendar'></div>

</body>
</html>
