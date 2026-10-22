<div id="agendaHoy" align="center"><?php  include("tabla_citas.php"); ?></div>
<small>
<p align="center">
<br>
<a href="<?=$SERVER["PHP_SELF"]?>?id_curso=<?= $id_curso; ?>&dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> | <?php 
if ($nivel_acceso!=1)
{

?>
<a href="javascript:ampliar_dia('/agenda/add_cita.php?oc=1&dia=<?= $fecha_time_stamp; ?>');">Introducir actividad</a> |
<?php  
} 

?><a href="<?=$SERVER["PHP_SELF"]?>?id_curso=<?= $id_curso; ?>&dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a>
</small>
</p>
<SCRIPT language="JavaScript">
<!--hide

function ampliar_dia(direccion)
{
window.open(direccion,'ampliar_dia','toolbar=no,resizable=yes,scrollbars=yes,width=550,height=550');
}

function mostrar_dia(dia)
{
window.open('/actividades.php?dia='+dia,'ampliar_dia','toolbar=no,scrollbars=yes,resizable=yes,width=550,height=550')
}

function mostrar_avisos(direccion)
{
window.open(direccion,'ampliar_dia','toolbar=no,resizable=yes,scrollbars=yes,width=550,height=550')
}

function actualizarCitas() {
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            document.getElementById("agendaHoy").innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET", "/inc/agenda/citas.php?dia=<?=$fecha_time_stamp?>", true);
    xmlhttp.send(null);
}

setInterval("actualizarCitas()",30000);

//-->
</SCRIPT>
