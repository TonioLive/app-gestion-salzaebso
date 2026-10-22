<?php 

$asunto_mensaje="Recordatorio cita agenda";
$titulo_web="Escuela infantil lápices";
$mail_webmaster="lapices@escuelas-infantiles.com";

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$sql_host = (getenv('APP_DB_HOST') ?: 'localhost');
$sql_db = (getenv('APP_DB_NAME') ?: '');
$sql_usuario = (getenv('APP_DB_USER') ?: '');
$sql_pass = (getenv('APP_DB_PASSWORD') ?: '');

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass") or die(db_error());
db_select_db("$sql_db",$db_validacion) or die(db_error());

// Consultamos citas de la agenda
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.avisar<='".date("Y-m-d")."' AND Agenda.Fecha>'".date("Y-m-d")."' AND agenda_asistentes.enviado=0 ORDER BY Agenda.Fecha,Agenda.Hora");

if (db_num_rows($rsNot_query) != 0) {

while($rsNot=db_fetch_array($rsNot_query)) {

        // Comprueba si es para usuario o para grupo
        if (strlen($rsNot["id_usuario"])!=0) {
        // Usuario

        // Busca datos usuario
        $query_usuario="SELECT * FROM usuarios WHERE id_usuario=".$rsNot["id_usuario"].";";

        $consulta_usuario=db_query($query_usuario) or die(db_error());

        if (db_num_rows($consulta_usuario)!=0) {
        
                $usuarios=db_fetch_array($consulta_usuario);
                
                if (strlen($usuarios["email"])!=0) {
                        // Envia email

                        $texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");
                        $msg="<font face='Arial' size=2><b>Recordatorio cita:</b> ".htmlentities($rsNot["Titulo"])."<br><br><b>Día:</b> ".date("d/m/Y",strtotime($rsNot["Fecha"]))." <b>Hora:</b> ".date("H:i",strtotime($rsNot["Hora"]))."<br><br>".htmlentities($rsNot["Texto"])."</font>";
                        
                        $nombre_padre_act=$usuarios["Nombre"]." ".$usuarios["Apellido1"]." ".$usuarios["Apellido2"];
                        $email_padre_act=$usuarios["email"];
                        
                        $to = $email_padre_act;

                        if (strlen($to)!=0) {

                        echo "Enviando a $to<br>";

                        $from = ucfirst($titulo_web)."<".$mail_webmaster.">";

                                $headers = "From: " . $titulo_web . " <" . $mail_webmaster . ">\n";
                                $headers .= "Return-Path: <" . $mail_webmaster . ">\n";
                                $headers .= "Reply-To: " . $mail_webmaster . "\n";
                                $headers .= "X-Mailer:PHP" . phpversion() . "\n";
                                $headers .= "Precedence: list\nList-Id: " . $titulo_web . "\n";

                                if ('html' == $type) {
                                        $sub = UTF8_encode($asunto_mensaje);

                                        $headers .= "MIME-Version: 1.0\n";
                                        $headers .= "Content-Type: " . $MSGhtml_type . "; charset=\"". $MSGCharSet . "\"\n";
                                        $mailtext = "<html><head><title>" . $sub . "</title></head><body>" . $msg . "</body></html>";
                                } else {
                                        $sub = UTF8_encode($asunto_mensaje);

                                        $msg=UTF8_encode($msg);

                                        $headers .= "MIME-Version: 1.0\n";
                                        $headers .= "Content-Type: text/plain; charset=\"". $MSGCharSet . "\"\n";
                                        $message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $msg);
                                        $message = preg_replace('|&amp;|', '&', $msg);
                                        $mailtext = wordwrap(strip_tags($msg), 80, "\n");
                                }

                        $hdrs = "From: ".ucfirst($titulo_web)."<".$mail_webmaster.">\n";

                        mail($to, $sub, $msg,$headers);
                        
                        }
                        
                        // Marca como enviado
                        $marca_enviado=db_query("UPDATE agenda_asistentes SET enviado=1 WHERE id_asistencia=".$rsNot["id_asistencia"]);
                        
                        }
                }
        } else {
        // Grupo
        echo($rsNot["id"]." Nivel:".$rsNot["nivel"]."<br>");
        }

        }

}

db_close();

?>
