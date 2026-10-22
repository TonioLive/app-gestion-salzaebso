<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../pdf/importes_como_texto.php");

$id_cliente=$_GET["id_cliente"];
$id_factura=$_GET["id_factura"];
$id_pago=$_GET["id_pago"];

// Busca datos factura

$busca_factura=db_query_params('SELECT * FROM facturas_emitidas WHERE id_factura=? and id_cliente=?;', 'ss', array($id_factura, $id_cliente)) or die(db_error());

if (db_num_rows($busca_factura)==0) {

	exit("Recibo no encontrado.");
		
}
	
// Datos de la factura
$total_absoluto=0;

$consulta1=db_query_params('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND id_factura=? ORDER BY fecha_emision DESC', 's', array($id_factura));
$datos_factura = db_fetch_array($consulta1);

$observacionesRecibo = "";
if (strlen($datos_factura["observaciones_recibo"])!=0) {
    $observacionesRecibo='<b>Observaciones recibo:</b>'.$datos_factura["observaciones_recibo"]."<br /><br />";
}

$horaRecibi = $datos_factura["hora_entrega_recibo"];
$firmaRecibi = $datos_factura["firma"];

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;

$servicios_contratados="";

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

		if ($datos_albaranes["cantidad"]>1) {
			$servicios_contratados.=$datos_albaranes["cantidad"]." uds. de ";
		}

        $servicios_contratados.=$datos_albaranes["concepto"]."\n";

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
        }

$total_factura=number_format(($total_base+$total_iva+$total_recargo), 2, ',', '.');
$total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$nombre_centro=$datos_centro["nombre_centro"];
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}

// Total pagado
$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

$total_a_cuenta=0;
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }

// Datos pago a cuenta

if (strlen($id_pago)!=0) {
    $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_pago=?', 's', array($id_pago));

    $datos_pago=db_fetch_array($busca_pagos);

    $importe_pago=$datos_pago["importe_pago"];
    $forma_de_pago=$pagos["forma_pago"];
    $fecha_pago=$pagos["fecha_pago"];
    if ((strlen($fecha_pago)==0) or ($fecha_pago=="0000-00-00")) {
        
        $fecha_pago=$datos_factura["fecha_recibo"];
        if (strlen($fecha_pago)==0) {
            $fecha_pago=date("d/m/Y",strtotime($datos_factura["fecha_emision"]));
        } else {
            $fecha_pago=date("d/m/Y",strtotime($datos_factura["fecha_recibo"]));
        }
        
    } else {
        $fecha_pago=date("d/m/Y",strtotime($fecha_pago));        
    }

} else {
    $importe_pago=$total_factura;
}

if ($id_factura) {

    // Busca datos alumno
    $busca_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($datos_factura["id_cliente"])) or die(db_error());
    if (db_num_rows($busca_alumno)!=0) {
        $datos_alumno=db_fetch_array($busca_alumno);
        $nombre_alumno_pago=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]." (Alumno nº: ".$datos_alumno["id_usuario"].")";
        $id_usuario_alumno_pago=$datos_alumno["id_usuario"];
        $fecha_alta_alumno=$datos_alumno["fecha_matricula"];
        $fecha_baja_alumno=$datos_alumno["fecha_baja"];
        
        $servicios_contratados.="Nivel: ".$datos_alumno["nivel_actual"].". El servicio contratado se inicia el día ".date("d/m/Y",strtotime($fecha_alta_alumno))." y finaliza el día: ".date("d/m/Y",strtotime($fecha_baja_alumno));
    }                                        

    $lista_grupos_alumno="";

    $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($datos_factura["id_cliente"])) or die(db_error());

    while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
        $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
    }

    if (strlen($lista_grupos_alumno)!=0) {
        $lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
    }


}        

$nombre_cliente_facturas=$datos_factura["banco_titular"];
$dni_cliente_facturas=$datos_factura["banco_dni_titular"];
$nombre_alumno_facturas=$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"]." (Alumno nº: ".$datos_factura["id_usuario"].")";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="https://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<!-- <link rel="stylesheet" href="/js/signature_pad-master/css/signature-pad.css"> -->
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<style>        
    .wrapper {
      width: 100%;
      /* max-width: 500px; */
      height: 300px;
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
      user-select: none;
      border-radius: 10px;
      border-style: solid;
      border-width: 1px;
      border-color: #cdcdcd 
      margin-bottom: 10px!important;  
      background-color: white;   
    }
    
    .signature-pad {
      left: 0;
      top: 0;
      width:100%;
      border-radius: 10px;
      max-width: 500px;
      height:300px;
      background-color: transparent;
    }

    .signature {
      left: 0;
      top: 0;
      width:100%;
      border-radius: 10px;
      max-width: 500px;
      background-color: white;
      border-radius: 10px;
      border-style: solid;
      border-width: 1px;
      border-color: #cdcdcd         
    }   
    </style>  
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="/js/signature_pad-master/js/signature_pad.umd.js"></script>
<script src="/js/signature_pad-master/js/app.js"></script>
</head>
<body>
<div class="container">
    <div class="row"> <!-- Normativa -->
        <div class="col-md-12 text-center"> 
                <img src="/pdf/logo_redondo.jpg" style="height: 200px;">
        </div>            
        <div class="col-md-12"> 
            <p>
                <h2>Normas de la Piscina de Neokids</h2>
            </p>
            <p>    
                1. Es obligatorio el uso de gorro, bañador y calcetines antideslizantes para los niños mayores de veinticuatro (24) meses, y pañal de agua y bañador para los niños menores de veinticuatro (24) meses.<br />
                2. Los padres que accedan a los vestuarios y a la zona del vaso de la piscina deben utilizar calzado adecuado (chanclas de goma). Si no disponen de él se les facilitarán bolsas cubre pies en la recepción.<br />
                3. Es obligatorio ducharse antes de entrar en la piscina.<br />
                4. Si el usuario de la piscina padece algún tipo de patología, su padre o tutor debe informar a los responsables de Neokids. Además, no se debe realizar actividad acuática si el alumno presenta fiebre o febrícula, es decir, si su temperatura corporal supera los treinta y siete grados y medio (37,5ºC). El incumplimiento de esta norma es responsabilidad exclusiva del familiar, tutor legal o responsable que acompaña al alumno. Además, para garantizar la salubridad e higiene de las instalaciones, los usuarios deben informar si padecen alguna enfermedad infecciosa o tienen alguna herida abierta.<br />
                5. Las clases de los niños pueden recuperarse cuando la ausencia a la misma sea por causa de fuerza mayor o prescripción médica, debidamente justificada, entre los que se destaca, a modo enunciativo, pero no limitativo, los siguientes supuestos: ingreso hospitalario, fracturas, esguinces, gastroenteritis, otitis, conjuntivitis, bronquiolitis, neumonía, varicela y/o enfermedades contagiosas que así se especifiquen en el informe médico. Las clases no podrán recuperarse si el motivo es un viaje, festival, graduación o evento similar. La recuperación se realizará en el período contratado y en los días marcados por la dirección de Neokids. No está contemplado la prolongación de ningún período.<br />
                6. Una vez abonado el importe del curso de natación y/o actividad educativa o recreativa por la duración seleccionada, si las familias/usuarios deciden voluntariamente dar de baja al alumno/a, no se reembolsará ningún importe de la cantidad abonada, salvo cuando se ejercite el derecho de desistimiento contemplado en la norma 16. <br />
                7. La reserva de plaza de un curso a otro se realizará en los meses de junio y julio previa entrega de una cantidad fijada. Importe que se descontará del recibo del primer período y no será reembolsable. <br />
                8. Se prohíbe el uso de teléfonos móviles en el interior del perímetro de la piscina y en los vestuarios. Queda totalmente prohibido realizar fotografías o grabación de imágenes en los vestuarios. La realización de fotografías o grabación de imágenes descritas en este punto podrá ser sancionada bajo multa económica de cien euros (100,00 €) y/o expulsión definitiva de las instalaciones de la piscina, sin perjuicio de las acciones legales y de responsabilidad civil que puedan derivarse de dicho acto. <br />
                9. Los únicos elementos flotantes permitidos dentro del vaso de la piscina son aquellos proporcionados y homologados por los monitores de Neokids.<br />
                10. No está permitida la entrada de animales, a excepción de aquellos cuyo uso este prescrito médica o judicialmente y además presenten condiciones de higiene excelentes, como por ejemplo, el uso de perros guía.<br />
                11. Queda prohibido introducir alimentos o material de vidrio en los vestuarios y en el perímetro de la piscina.<br />
                12. No está permitido correr en las instalaciones.<br />
                13. Únicamente puede acceder al vestuario un adulto por niño, salvo en casos concretos en los que, por motivo médico debidamente justificado, el cuidado del menor haga necesaria la presencia de dos adultos simultáneamente.<br />
                14. Los grupos dentro de los diferentes cursos que se imparten en las instalaciones se formarán a partir de tres niños en infantil y cinco en bebes. Los grupos de nivel bajo (verde) podrán tener un máximo de cinco alumnos, y los niveles (azul) y alto (rojo) podrán tener un máximo de seis alumnos por calle. El nivel de los alumnos será evaluado por los monitores y profesionales de Neokids.<br />
                15. El pago de los períodos de los cursos se realiza en los quince (15) días previos a su inicio. De no ser así, la empresa entiende que el alumno no desea continuar y se reserva el derecho de proceder a su baja. <br />
                16. El alumno tiene derecho a desistir de cualquier contrato de servicios que celebre con Neokids en el plazo de catorce (14) días naturales sin necesidad de justificación salvo en aquellos supuestos en los que la Ley establezca un plazo diferente. El plazo de desistimiento expirará a los catorce (14) días naturales a contar desde el día de la celebración del contrato. Para ejercer el derecho de desistimiento previsto en el presente punto, el alumno deberá notificar a Neokids su decisión de desistir del contrato a través de una declaración inequívoca.<br />
                17. Los horarios, los grupos y los monitores de Neokids están sujetos a variaciones de un curso a otro en beneficio de la mejor organización de todos los alumnos.<br />
                18. Una vez alcanzada la edad de dos (2) años y previa evaluación del monitor, los niños que pasen del grupo de bebé al de infantil se les ofrecerá una plaza exclusivamente en el horario de las 19:25h. Aquellos que, al cumplir dos (2) años, no quieran pasar al grupo infantil, tendrán la opción de permanecer en el grupo de bebés solo un trimestre más. En ambos casos, se les ofrecerá la opción de cambiar de horario al terminar cada trimestre, siempre y cuando quedasen plazas disponibles.<br />
                <br />
                El cumplimiento de esta normativa persigue garantizar  una correcta convivencia, el respeto a los demás y el buen manejo de las instalaciones, con el fin de conseguir su máximo aprovechamiento en condiciones de seguridad y comodidad.<br />
            </p>
            <p>
                <h2>Sobre la protección de datos personales</h2>
            </p>        
            <p>
            De acuerdo a lo establecido en la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal, en lo que concierne al tratamiento de los datos personales, las libertades públicas y los derechos fundamentales de las personas físicas, y especialmente de suhonor, intimidad y privacidad personal y familiar. Está ley se desarrolla fundamentándose en el artículo 18 de laconstitución española de 1978, sobre el derecho a la intimidad familiar y personal y el secreto de las comunicaciones.
            Le  informamos de que los datos personales que nos facilite, serán incorporados en un fichero titularidad de NEOKIDS S.L , con domicilio en Avda. General Avilés, 54 - 46015 - VALENCIA que cumple con las medidas de seguridad exigidas en el Real Decreto 1720/2007 de 21 de diciembre, y cuya finalidad es la inscripción , control y gestión de nuestros alumnos matriculados en el centro, y así como el envío  de publicidad de los contenidos comerciales.
            Asimismo le informamos que durante la actividad podrían ser realizadas fotografías de forma Particular por parte de los PADRES/TUTORES de los alumnos,  siendo responsabilidad de estos  y no del CENTRO el mal uso de las mismas o el poder  incluirlas en REDES SOCIALES a las que pertenezcan los mismos.
            Como padre/ Tutor podrá ejercitar los derechos de acceso, rectificación, cancelación y oposición, comunicándolo por escrito a ( marketing@neo-kids.es).
            </p> 
        </div>
    </div>
    <div class="row"> <!-- Datos recibo -->
        <div class="col-md-12"> 
            <?php
            $parte2_nombre_recibo=$nombre_alumno_pago;
            if (strlen($parte2_nombre_recibo)!=0) {
                    $parte2_nombre_recibo=" - Alumno: ".$parte2_nombre_recibo.", Grupos: ".$lista_grupos_alumno;
                    }
            ?>
            <h1>Firmar recibo nº <?=$datos_factura["id_factura"];?></h1>
            <p>
                Se me ha entregado el recibo por el servicio: <?=$servicios_contratados;?><br /><br />
                <?=substr($parte2_nombre_recibo,3,strlen($parte2_nombre_recibo)-3);?><br /><br />
                <b>Importe recibo:</b> <?=number_format($importe_pago, 2, ',', '.')." €";?>
            </p>
        </div>         
    </div>
    <div class="row"> <!-- Panel firmal -->                              
            <?php

            if (strlen($firmaRecibi)!=0) {
                $displayImprimir = "inline-block";
            ?>
            <div class="col-md-6 col-xs-12"> 
                <p>   
                        Firmado el <?=date("d/m/Y",strtotime($horaRecibi));?> a las <?=date("H:i:s",strtotime($horaRecibi));?>.
                <br />
                <?=$observacionesPedido?>      
                </p>
            </div>          
            <div class="col-md-6 col-xs-12 text-center">         
                <div>
                <img id="signature-pad" class="signature" src="<?=$firmaRecibi;?>">
                </div>
            </div>
            <?php

            } else {
                $displayImprimir = "none";

            // '/js/signature_pad-master/js/signature_pad.umd.js'
            // '/js/signature_pad-master/css/signature-pad.css'

            ?>
                <div class="col-md-6 col-xs-12">   
                    <p>  
                        <legend><b>Observaciones:</b></legend>               
                        <textarea id="observaciones_recibo" name="observaciones_recibo" cols="62" rows="4" class="form-control"></textarea>
                    </p>
                </div>          
                <div class="col-md-6 col-sm-12 col-xs-12 text-center">
                        <legend>Firma</legend>
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    <div class="wrapper">
                                    <canvas id="signature-pad" class="signature-pad"></canvas>
                                    </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <button id="save-png" class="btn btn-sm btn-info">Guardar Firma</button>
                                <button id="clear" class="btn btn-sm btn-danger">Limpiar firma</button>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12 col-sm-12 col-xs-12" id="resultado"></div>
                        </div>
                </div>
            <script>

                var canvas = document.getElementById('signature-pad');

                // Adjust canvas coordinate space taking into account pixel ratio,
                // to make it look crisp on mobile devices.
                // This also causes canvas to be cleared.
                function resizeCanvas() {
                    // When zoomed out to less than 100%, for some very strange reason,
                    // some browsers report devicePixelRatio as less than 1
                    // and only part of the canvas is cleared then.
                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                }

                window.onresize = resizeCanvas;
                resizeCanvas();

                var signaturePad = new SignaturePad(canvas, {
                    // backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
                });

                document.getElementById('save-png').addEventListener('click', function () {

                    if (signaturePad.isEmpty()) {
                        return alert("Por favor, firma en el panel antes de finalizar.");
                    }

                    var data = signaturePad.toDataURL('image/png');

                    // Guarda Firma
                    var valoresPost = {
                        "firmaRecibi" : data                        
                    };
                    $.ajax({
                        data:  valoresPost,
                        url:   '/facturacion/guardar_firma.php?id_cliente=<?=$id_cliente?>&id_pago=<?=$id_pago?>&id_factura=<?=$id_factura?>&observaciones='+$("#observaciones_recibo").val(),
                        type:  'post',
                        beforeSend: function () {
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success:  function (response) {
                            var esOk = response.substr(0, 2);
                            if (esOk=='OK') {
                                // Si OK desactiva el panel de firma
                                $("#save-png").prop("disabled",true);
                                $("#clear").prop("disabled",true);
                                $("#signature-pad").prop("disabled",true);
                                // Activa boton imprimir
                                document.getElementById("boton-imprimir").style.display="inline-block";
                            }
                            $("#resultado").html(response);
                        }
                    });


                });
                /*
                document.getElementById('save-jpeg').addEventListener('click', function () {
                    if (signaturePad.isEmpty()) {
                        return alert("Please provide a signature first.");
                    }

                    var data = signaturePad.toDataURL('image/jpeg');
                    console.log(data);
                    window.open(data);
                });

                document.getElementById('save-svg').addEventListener('click', function () {
                    if (signaturePad.isEmpty()) {
                        return alert("Please provide a signature first.");
                    }

                    var data = signaturePad.toDataURL('image/svg+xml');
                    console.log(data);
                    console.log(atob(data.split(',')[1]));
                    window.open(data);
                });
                */
                document.getElementById('clear').addEventListener('click', function () {
                    signaturePad.clear();
                });
                /*
                document.getElementById('undo').addEventListener('click', function () {
                    var data = signaturePad.toData();
                    if (data) {
                        data.pop(); // remove the last dot or line
                        signaturePad.fromData(data);
                    }
                });
                */

            </script>
        <?php
        }
        ?>             
    </div>
    <div class="row"> <!-- Botones -->
        <div class="col-md-12 text-center">
            <br />
         <a class="btn btn-xl btn-info" href="/pdf/recibos_material.php?id_factura=<?=$id_factura;?>" id="boton-imprimir" style="display: <?=$displayImprimir?>"><i class="fa fa-print" aria-hidden="true"></i> Imprimir recibo firmado</a>            
         <button class="btn btn-xl btn-primary" onclick="window.close();"><i class="fa fa-window-close" aria-hidden="true"></i> Cerrar panel de firma</button>            
        </div>
    </div>
</div>
<br />
</body>
</html>