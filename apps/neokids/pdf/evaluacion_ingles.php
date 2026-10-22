<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$id_escuela=$_GET["id_escuela"];
$id_alumno=$_GET["id_alumno"];
$id_padre=$_GET["id_padre"];

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                } else {
                $id_curso=0;
                }
                

                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_escuela));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];
                        }
// --- FIN DATOS ---

// error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

// Aquí el logo
/*
$x_logo=75;
$y_logo=715;
$pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,150,54);
*/
$x_logo=75;
$y_logo=635;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);

$busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_alumno)) or die(db_error());

$alumnos_escuela=db_fetch_array($busca_alumnos_escuela);

$busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.id_padre=?', 's', array($id_padre));

// Busca padres
$padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');

$posicion_inicial=625;
$posicion_actual=638;

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText($alumnos_escuela["Nombre"]." ".$alumnos_escuela["Apellido1"]." ".$alumnos_escuela["Apellido2"]."

".$padres_alumnos_escuela["Nombre"]." ".$padres_alumnos_escuela["Apellido1"]." ".$padres_alumnos_escuela["Apellido2"],11,array('justification'=>'center'));
$pdf->ezText("

Estimado ".$padres_alumnos_escuela["Nombre"].":



A partir de este momento, ya puedes disponer del servicio de cámaras web de Lápices. Para ello, únicamente tienes que seguir los siguientes pasos:

1º- Accede a la página web de nuestras escuelas: www.escuelas-infantiles.com

2º- En la parte superior derecha, podrás ver un recuadro para introducir un nombre de usuario y contraseña.

3º- Introduce el nombre de usuario y la contraseña que te proporcionamos a continuación:

",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("USUARIO: ".$padres_alumnos_escuela["usuario_webcam"]."
CONTRASEÑA: ".security_family_password($padres_alumnos_escuela),11,array('justification'=>'center'));
$pdf->ezText("

4º- Pulsa sobre la palabra ENVIAR.


5º- Acabas de acceder a una zona privada exclusiva de los padres de los alumnos de Lápices.






Esperamos que disfrutéis de este servicio. Un saludo.

",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("ESCUELA INFANTIL LÁPICES",9,array('justification'=>'right'));


$pdf->ezStream();
?>

