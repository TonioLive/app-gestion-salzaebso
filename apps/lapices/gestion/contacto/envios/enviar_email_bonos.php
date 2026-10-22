<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
require("../../../mailer/PHPMailerAutoload.php");

$enviadosOK1="cbr.iccp@gmail.com,vmartingargallo@gmail.com,CAINGARCIA209@GMAIL.COM,aracely.a.suazo@gmail.com,RAGARPA88@GMAIL.COM,laurapol555@gmail.com,ramadacarlos@gmail.com,joseluisrisueojimenez@gmail.com,pao23575@gmail.com,rodrigosanchot@gmail.com,miriam.azf@gmail.com,danielasensioalcaide@gmail.com,anaserraperales@gmail.com,VICTORIA.RAMON@GMAIL.COM,hfrancesco@gmail.com,baeschlin66@gmail.com,lourdesalmenar013@gmail.com,juanmitch5@hotmail.com,alxnegro@yahoo.es,walasss473@gmail.com,pariis034@gmail.com,cristian.garzoni@gmail.com,isabel.gomgar@gmail.com,ximogramage@gmail.com,m.castellanofont@gmail.com,grandegatosergio@gmail.com,negodasha@gmail.com,jlanzuelavives@gmail.com,maria.dasi@hotmail.com,mariamanriquearona@gmail.com,aragonrodriguez.eliana@gmail.com,martinezmarco.franciscojose@gmail.com,raulpascualabad@gmail.com,salvigiulia7@gmail.com,mague160982@gmail.com,carlospestano@gmail.com,marianopistaccio@hotmail.com,ireperezcalvo@gmail.com,josefina.arevalo@hotmail.com,arqlucasruda@gmail.com,mcabocamararealizador@gmail.com,raquelagusticalixto@gmail.com,sv84maria217@gmail.com,javiercuadrosmiguelez@gmail.com,martagimenocw@gmail.com,j.fornas@aromasdelcampo.com,manzano36@gmail.com,carlesjovani@gmail.com,juliarenardmes@gmail.com,guillermomantese@gmail.com,claragiovanardi@gmail.com,emiliomolinaharo@gmail.com,patguisadoedo@gmail.com,anorba1985@gmail.com,pilar-barrabes@hotmail.com,mariajora19@gmail.com,xavitepp@gmail.com,elenaplafer@gmail.com,ricogonzalo67@gmail.com,agustinabarem@gmail.com,josefernandotoledano@icav.es,CARRIONOLMOSANA@GMAIL.COM,marcosbelenguer@gmail.com,isabeltude@gmail.com,guillermobeltranminguez@gmail.com,SARI.MARTINEZ@GMAIL.COM,sgabarda@gmail.com,miguelmb@coial.org,colomer.raul@gmail.com,masande27@gmail.com,lukedillon1@hotmail.com,marta3891@gmail.com,fernandofreirebouzas@yahoo.es,isagomezpuchades@yahoo.es,cjgahona@gmail.com,evansfranmar@gmail.com,RAGARPA88@GMAIL.COM,laurapol555@gmail.com,fginerginer@gmail.com,msanzvi@yahoo.es,ektubato0p@gmail.com,valeria.lugovaya@gmail.com,elehmakeup@gmail.com,kanivetsigor@gmail.com,nimpusia@gmail.com,damarcol@gmail.com,amchomar@hotmail.com,borjamg1994@gmail.com,mireia.novell@nostrumfinanzas.es,H2O.JUAN@GMAIL.COM,sadovaya.anastasiya77@gmail.com,anaiscmp2010@gmail.com,fernando.tejedor8@gmail.com";
$enviadosOK1.=",sagitario1937.ea@gmail.com,pabloantcanteromoliner@gmail.com,rocky.tuejar@gmail.com,ainasang@gmail.com,yulianest@ukr.net,maria.cabden@gmail.com,sandraescribano91@outlook.com,jesusmartinezdolz@outlook.es,m.pardogonzalez@hotmail.com,zalesko@web.de,rumi23rlg@gmail.com,marietamm.mmm@gmail.com,davahjose@gmail.com,orianacmorag@gmail.com,marxalenes@gmail.com,BERGELINO@GMAIL.COM,matiasgarciallanes@gmail.com,matiasgarciallanes@gmail.com,oslohue@hotmail.com,olga_noe51@hotmail.com,yasminamiralles1@gmail.com,mepas@hptmial.com,luismirallescrespo@gmail.com,lidiaramosnavarro@gmail.com,consuelo.garces.iniesta@gmail.com,consuelo.garces.iniesta@gmail.com,meirendon@gmail.com,jose.felix.olivo@gmail.com,malucdd@gmail.com,ricardo.peralto@gmail.com,aguado.son@gmail.com,torres.belenguer@gmail.com,saray.romero.roca@gmail.com,lin2330et@gmail.com,041213TEAMO@GMAIL.COM,041213TEAMO@GMAIL.COM,lin2330et@gmail.com,maria.baeza.oca.mbo@gmail.com,041213TEAMO@GMAIL.COM,victoria_sp.m@hotmail.es,juankdonado@gmail.com,destrchs@me.com,joangutierrezcalvo@gmail.com,paulaskater@hotmail.com,carlosheller@gmail.com,daryaheller@gmail.com,maisanfe@gmaol.com,carlos@csmfotografo.com,nico_goruian@yahoo.com,alexvillarroya68@gmail.com,jj_bomfunk@hotmail.com,istc22@hotmail.com,marinaandresangles@outlook.es,mariajose.torralba@outlook.es,rodolfocastroquesada1989@gmail.com,lidiceaguilera88@gmail.com,fatima-toledo93@outlook.es,fatima-toledo93@outlook.es,amalhoa1702@gmail.com,miguelangelteruelsanchez@gmail.com,13daniela.mc@gmail.com,karlota1976@hotmail.com,rosa-soriano@hotmail.es,hars_gergo@hotmail.com,clubfitkidvictoria@gmail.com,alberto.hernandezperez11@gmail.com,evaa.ortega.ruiz1990@gmail.com,mlerones@hotmail.com,yesicaeperez@gmail.com,ramaipra@hotmail.com,carmenlapiedraraga@gmail.com,franmecho@gmail.com,rosa00607@gmail.com,sonia40089@gmail.com,daniel23romero19@gmail.com,benizamora24@gmail.com,marisol.86@hotmail.com,idbertelegni@gmail.com,naty.avedikian@gmail.com,yago.gonzalez@renfe.es,desita@live.com,Soniadl90@gmail.com,juandaiglesias89@gmail.com,jorgemar1989@hotmail.com,nuriacaeg@gmail.com,arturoportable@gmail.com,noloma@hotmail.com,noloma@hotmail.com,pablo.monteagudo@yahoo.co.uk,susi_capella@hotmail.com,jomarrue@gmail.com,jlymses@hotmail.com,danco.nestorov@gmail.com,susanmilagros30@gmail.com,serpivi@gmail.com,patriciaguillen1@hotmail.com,milan.proks@gmail.com,raquel.gamezpardo@gmail.com,fatimarpache1@gmail.com,celiafr30@gmail.com,palomarg33@gmail.com,serrano.psc@gmail.com,saramt_itq@hotmail.com,borjaellargo@hotmail.com,laurinasesgon@gmail.com,jtomas1966@hotmail.com,cuca_1980@hotmail.com,francisco90urquia@gmail.com,francisco90urquia@gmail.com,alberto.vizcaino@sothis.tech,talmi_84@hotmail.com,vzahonero@hotmail.com,evabetoret@gmail.com,aitorv7@gmail.com,lilianacaridadsauceda@gmail.com,hernanbdelgado@gmail.com,raquelzarza947@gmail.com,BOUCHER.CHRISTOPHE@GMAIL.COM,CLARISSE.DECLOS@GMAIL.COM,CCHC.1996@GMAIL.COM,DACP.1996@HOTMAIL.COM,pablohn1208@gmail.com,albertomejiagarcia25@gmail.com,almaramirez0321@gmail.com,thenada92@gmail.com,marinacampos99@gmail.com,mialcor85@gmail.com,paula1987@hotmail.com,franciscodiazsegura88@gmail.com,1201veronica.lopez@gmail.com,albarralensis@hotmail.com,menxucarmona@hotmail.com,joferte.t@gmail.com,FERVAR46@GMAIL.COM,danielferreira@icloud.com,liviarocha153@gmail.com,anamontiano@gmail.com,femenfer@gmail.com,danimo77@gmail.com,correcher_ele@gva.es,dayanaminisss@gmail.com,cfmunyoz@gmail.com,jimenezvasquezjhoanna@gmail.com,josmike73@gmail.com,velascorosse@gmail.com,dayanafranco16@icloud.com,lauraaguirretesouro@gmail.com,C.ARASTENY@GMAIL.COM,ceciliabeltranmin@gmail.com,pabolo21@gmail.com,saulbikuybivaga02@gmail.com,mc.di.black@hotmail.com,alejandro4986@hotmail.com,maria-tolsada@gmail.com,manuelchave43@gmail.com,roxanachica_@hotmail.com,pjorak@gmail.com,rrondon.mgv@gmail.com,elsa84aponte@gmail.com,faby_rui@hotmail.com,carlosescrivagilva87@gmail.com,teresatiernomontero@outlook.es,diejuso@gmail.com,MAVIVALENCOSO@GMAIL.COM,robertokrohnfortino@gmail.com,valeriaeteran@gmail.com,natiza1989@gmail.com,ESTERANOVAC877@GMAIL.COM,punzano.esteve.maria@gmail.com,bernave_987@hotmail.com,arredondov848@gmail.com,rosalialeiva2018@gmail.com,alcevalcom@gmail.com,mlucy_1987@hotmail.com,IBM612@GMAIL.COM,vicky_irqu@hotmail.com";
$enviadosOK1.=",castro_panadero@hotmail.com,maricarmen8313@gmail.com,gallartgs@gmail.com,alasernabelenguer@gmail.com,alopezh@gmail.com,tefolgra@gmail.com,mateos.1000@hotmail.com,sabrinabalen80@gmail.com,meza07926@gmail.com,lizkarinaarce1990@gmail.com,rubenramirezluz@gmail.com,elenadiazrubio@gmail.com,proyectopiduo@gmail.com,lunamartingallego88@gmail.com,marpeque91@gmail.com,edsanjuanr@gmail.com,karina.vizcarra88@gmail.com,mariarieragomez@gmail.com,jaguilarseci@gmail.com,gadielalvarezlier@gmail.com,paularodrigo0@gmail.com,martgl@hotmail.com,ximo.bleda.saez@gmail.com,herlanviscarra@gmail.com,krist.bondareva@gmail.com,mertcanatan@me.com,hernandezgilderamales@gmail.com,milquiamerbanyderasmurcia@gmail.com,Rafaelgarcialopez00@gmail.com,elisabeth.blatabad@uchceu.es,daniel.lpz.g@gmail.com,annam.mollar@gmail.com,levislopezn@hotmail.com,virginia_oramas@hotmail.com,designonlysara@gmail.com,dmf.davidmarco@gmail.com,MASTROMOSTEFANO@GMAIL.COM,CLAUDIA_CHIAPPINI@HOTMAIL.COM,anflozu@hotmail.com,pedro23am@hotmail.com,aliciafergi@gmail.com,andrearjss@gmail.com,monin_f@hotmail.com,DENCHUK@GMAIL.COM,oksana.boiko92@gmail.com,robertoalonsofernandez@gmail.com,p.calatayudlacruz@gmail.com,mikecfv@gmail.com,laursanza@gmail.com,silvia@clavecongresos.com,linbervalencia@gmail.com,emm02586@gmail.com,irpegue@gmail.com,rpardocabanes@gmail.com,esther.lavalle@gmail.com,ma.alejandradetoni8790@gmail.com,paresgpedro@gmail.com,e.asensioferrando@gmail.com,mipecor10@gmail.com,maria.pardo.martinez.83@gmail.com,angel@inmaplas.es,allepuz85@hotmail.com,allepuz85@hotmail.com,miguel.arenasmoreno@gmail.com,gemodemi@gmail.com,esther.zg18@gmail.com,esther.zg18@gmail.com,mlorcres@gmail.com,vvg2011@hotmail.es,nsanchez.vlc@gmail.com,gilvatore@gmail.com,henrry12loayza@gmail.com,henrry12loayza@gmail.com,laufenma@gmail.com,laufenma@gmail.com,moncholimartinezanna@gmail.com";

$enviadosOK=explode(",",$enviadosOK1);

$noEnviados1="Al.bi.ta@hotmail.com,maria.baeza.oca.mbo@gmail.com,041213TEAMO@GMAIL.COM,marigabigs@gmail.com,marigabigs@gmail.com,PAULAGONZALEZJIMENEZ@GMAIL.COM,rubencano86@gmail.com,annamartiguillem@gmail.com,annamartiguillem@gmail.com,glo82vlc@gmail.com,caladan10@gmail.com,maicolandre_00@hotmail.com,vloye9216@gmail.com,gemapoquethernandez@gmail.com,ivancolo605_4@gmail.com,iestevan.conectia@gmail.com,a23864@gmail.com,mcfisiox@gmail.com,juanfel80@gmail.com,tataramirez8910@gmail.com,tataramirez8910@gmail.com,nataliaescuederos@gmail.com,nataliaescuderos@gmail.com,zhalirova4@gmail.com,dafelo.85@gmail.com,dafelo.85@gmail.com,jessi-88_9@hotmail.com,victor_ademuz@hotmail.com,0noelia__tm@hotmail.com,jessyoliva88@gmail.com,silsegil@gmail.com,fjavierrius@hotmail.com,carlos@csmfotografo.com,nico_goruian@yahoo.com,jorgevillagrasa5@gmail.com,corteslm@hotmail.com,daritacrespo@hotmail.com,alfredobuenovicente@gmail.com,a.aleixandre@hotmail.com,campilloe@gmail.com,l.pellicerdominguez@gmail.com,puchpuch1@hotmail.es,Salima.bj@hotmail.com,galerafree@hotmail.com,rocioruizgallardo@gmail.com,mifermo1@gmail.com,ajgague@outlook.es,carlasamper@hotmail.com,javierrierahernandez@gmail.com,elisainfer@gmail.com,martinsanchez.raul@gmail.com,diazsane@gmail.com,nacredes@hotmail.es,elenaabarcatorralba@gmail.com,kleverbetancourth@gmail.com,alexia.buerki@gmail.com,jose_explorador24@hotmail.com,sandrasandra1990@hotmail.com,julian.martinez.moya@gmail.com,anabotella@uv.es,mail@jacopomezzanotti.com,sandravilloraa@gmail.com,kikevilar2@gmail.com,loro@norauto.es,kikevilar2@gmail.com,loro@norauto.es,fernando.palomera.b@gmail.com,eluzivars@gmail.com,rpaviam22@gmail.com,bea-rp90@hotmail.com,pabloreig91@gmail.com,simona.herchi@gmail.com,santibasan@hotmail.com,ardiazcarol@gmail.com,alexhome0181@gmail.com,uliabrod08@gmail.com,ramon@munozarias.com,alivalera87@gmail.com,cesargonzalezfotografia@gmail.com,gloriaferreres@gmail.com,marco.galbis@gmail.com,johanama644@gmail.com,diane.todea@gmail.com,guidogana@hotmail.com,jonathan.gomez.moncholi@gmail.com,civeraelena@gmail.com,ggutierrezmeneses81@gmail.com,miladonisestruch@gmail.com,xaloma88@hotmail.com,lapiles@gmail.com,miriammartinezginer67@gmail.com,jusepemartin@gmail.com,amparooro@hotmail.com,pallardo.andreu@gmail.com,angelahllobregat@gmail.com,sergiopelaez950@gmail.com,gabrielamj1303@gmail.com,aitorperiss@gmail.com,vira.lenart@hotmail.com,manolinpg@hotmail.com,laura_latitu@hotmail.com,maximilianorobotti@gmail.com,fermarzi1@gmail.com,cayu22@gmail.com,coritagamez@gmail.com,leogatova@hotmail.com,mariajeruiz7@gmail.com,sanchocorreo@hotmail.com,martacisan@gmail.com,alfonsotarin@hotmail.com,patriciaberlanga@hotmail.com,yagomaster@hotmail.com,risco.belen@gmail.com,hcastrogarcia07@gmail.com,silvaghambaryan88@gmail.com,gonzalezlizmatina21@gmail.com,carlosmartinez.cmh@gmail.com,anacerezolopez@hotmail.com,fraorde86@gmail.com,anna.diaz.rubio@gmail.com,joonospina@yahoo.com,smikaelyan@gmail.com,josemiguelparra@hotmail.es,finallongo@hotmail.com,poncehappyco5@gmail.com,sandratolosagarcia@gmail.com,rrochinalopez@gmail.com,ana-amparo@hotmail.com,hugovasquez037@gmial.com,riverate898@gmail.com";
$noEnviados=explode(",",$noEnviados1);

echo("Enviado ya a ".count($enviadosOK)."<br>");
echo("Faltan ".count($noEnviados)."<br>");


$emailsRespuesta[8]="rionervion@escuelas-infantiles.com";
$emailsRespuesta[6]="generalaviles@escuelas-infantiles.com";
$emailsRespuesta[7]="lapicesvicentebranchat@escuelas-infantiles.com";
$emailsRespuesta[13]="granvia@escuelas-infantiles.com";

$id_curso=$_GET["id_curso"];

if (strlen( )==0) {
    $id_curso=40;
}

$filtroCurso=" AND alumnos.id_curso=$id_curso";


$filtroCentro = '(alumnos.id_centro=6 OR alumnos.id_centro=7 OR alumnos.id_centro=8 OR alumnos.id_centro=13)';        		

$consultaAlumnos="SELECT *, usuarios.id_usuario AS id_usuario_alumno, centro_aulas.nombre AS nombre_aula FROM usuarios,alumnos,centro_aulas,centros WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND centro_aulas.id_centro=centros.id_centro AND $filtroCentro".$filtroCurso." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre";
// $consultaAlumnos="SELECT *, usuarios.id_usuario AS id_usuario_alumno, centro_aulas.nombre AS nombre_aula FROM usuarios,alumnos,centro_aulas,centros WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND centro_aulas.id_centro=centros.id_centro AND $filtroCentro".$filtroCurso." ORDER BY alumnos.aula,Apellido1, Apellido2, usuarios.Nombre LIMIT 1"; // DEBUG
// $tos=array("ldoassans@gmail.com","luis@doassans.es","ldoassans@hotmail.com");

$busca_alumnos=db_query($consultaAlumnos) or die(db_error());

$contador_alumnos=0;

$direccionesEnviado = array();
$direccionesNoEnviado = array();

$listaEnviados="";

while($listado_alumnos=db_fetch_array($busca_alumnos)){

    if (strlen($listado_alumnos["codigo_inscripcion"])!=0) {

    $id_usuario_cliente=$listado_alumnos["id_usuario_alumno"];

    // Envía email confirmación
    $contadorMensajes = 0;
    $direccionesEnviadoEste = array();

    $busca_padres=db_query("SELECT * FROM padres,usuarios WHERE padres.id_usuario=usuarios.id_usuario AND padres.padre_de=$id_usuario_cliente");
    while($padres=db_fetch_array($busca_padres)){
        
        $to = $padres["email"];

        // $to=$tos[$contador_alumnos];  // DEBUG

        if (strlen($to)!=0) {

            if (comprobar_email($to)) {                

                if (in_array($to, $enviadosOK)) {

                    echo("Ya enviado a ".$to."<br>");

                } else {

                    echo($to.',');
                    $listaEnviados.= $to.",";

                    /* */

                    $titulo_web="Escuela Infantil Lápices";

                    $mail_webmaster="comunicacion1@escuelas-infantiles.com";
                    $mail_contacto =$emailsRespuesta[$listado_alumnos["id_centro"]];
                    
                    $type="html";
                    $MSGhtml_type="text/html";
                    $MSGCharSet="UTF-8";
                    
                    $asunto = "Ayudas escolarización educación infantil primer ciclo (Bono Infantil)";

                    $cuerpo_mensaje = "<p><b>CONVOCATORIA CURSO 2024-25</b></p>";

                    $cuerpo_mensaje.= "<p>Queridas familias:</p>";
                    $cuerpo_mensaje.= "<p>Os informamos que ya se ha abierto el plazo para presentar las solicitudes de las ayudas a la escolarización de los alumnos de 1º ciclo de Educación Infantil que concede la Consellería de Educación para el próximo curso escolar 2024-25.</p>";
                    
                    $cuerpo_mensaje.= "<p>Para solicitarla, debéis acceder al enlace que os indicamos, leer las instrucciones y rellenar la solicitud de forma telemática, es decir, por el ordenador.</p>";
                    
                    $cuerpo_mensaje.= "<p>En la solicitud se os pediran los datos que os facilitamos a continuación:</p>";
                    $cuerpo_mensaje.= "<p aling='center'>
                    Alumno: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."<br>
                    Curso: 2024-2025<br>
                    Nombre del centro: ".$listado_alumnos["nombre_completo"]."<br>
                    Código del centro: ".$listado_alumnos["codigo_centro"]."<br>
                    Código de Matrícula (ID Solicitud): ".$listado_alumnos["codigo_inscripcion"]."<br>
                    </p>";
                    $cuerpo_mensaje.= "<p>El plazo finaliza el día 22 de julio. Os aconsejamos que lo solicitéis cuanto antes para comprobar que está todo correcto. Este es el enlace:</p>";
                    $cuerpo_mensaje.= "<p aling='center'><a href='https://www.gva.es/es/inicio/procedimientos?id_proc=16885'>https://www.gva.es/es/inicio/procedimientos?id_proc=16885</a></p>";
                    $cuerpo_mensaje.= "<p>Una vez accedáis a la página de la Generalitat debéis hacer clic en el enlace “Solicitud Presentación Autenticada”. Si tenéis alguna duda, en esta página de la Generalitat tenéis toda la información necesaria: Plazos, Documentación requerida, Instrucciones para rellenar el formulario, etc.</p>";
                    $cuerpo_mensaje.= "<p>No podemos aceptar ninguna solicitud que no se haya hecho siguiendo este procedimiento.</p>";
                    $cuerpo_mensaje.= "<p>Atentamente,</p>";                
                    $cuerpo_mensaje.= "<p>C.E.I. LÁPICES</p>";

                    $texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_contacto." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");                
                    
                    $cuerpo_mensaje="<img src='https://www.escuelasinfantileslapices.es/pdf/logo_redondo.jpg' width='150'><br>".$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";                

                    $d=date ("d");
                    $m=date ("m");
                    $y=date ("Y");
                    $t=time();
                    $dmt=$d+$m+$y+$t;
                    $ran= rand(0,10000000);
                    $dmtran= $dmt+$ran;
                    $un= uniqid();
                    $dmtun = $dmt.$un;
                    $mdun = md5($dmtran.$un);
        
                    // Envia email

                    $nombreRemitente=$titulo_web;                
                    $mailFrom=$mail_webmaster;
                    $asuntoMensaje=ucwords($asunto);
                    $MSGhtml_type="text/html";
                    $MSGCharSet="UTF-8";
            
                    $mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
            
                    // $mail->SMTPDebug = 3;                            // Enable verbose debug output
            
                    $mail->isSMTP();                                    // Set mailer to use SMTP
                    $mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');                      // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                             // Enable SMTP authentication
                    $mail->Username = $mailFrom;                        // SMTP username
                    $mail->Password = (getenv('LAPICES_SMTP_PASSWORD_05') ?: '');                       // SMTP password
                    $mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 587;                                  // TCP port to connect to
            
                    $mail->addReplyTo($mail_contacto, $titulo_web);  
                    $mail->SetFrom($mail_webmaster, $titulo_web);                   
                    // $mail->From = $mailFrom;
                    //$mail->FromName = $nombreRemitente;           
                    $mail->addAddress($to, '');
                    $mail->addReplyTo($mailFrom, $nombreRemitente);
                    $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <https://gestion.neo-kids.es/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
                    $mail->WordWrap = 50;                               // Set word wrap to 50 characters
                    $mail->isHTML(true);                                // Set email format to HTML
            
                    $mail->Subject = $asuntoMensaje;
                    $mail->Body    = $cuerpo_mensaje;        
            
                    if(!$mail->send()) {
                        // echo("<span class='text-danger'>Error: El mensaje para ".$to." no ha podido ser enviado.</span><br />");
                        $resultado="Error";
                        $direccionesEnviadoEste[] = $padres["email"]." (ERROR)";
                        $listaEnviados.= $padres["email"].",";
                    } else {
                        // echo("<span class='text-success'>OK: Mensaje para ".$to." enviado correctamente.</span><br />");
                        $resultado="Enviado";
                        $direccionesEnviadoEste[] = $padres["email"];
                        $contadorMensajes++;
                    }
                     
                    // dormir durante 1 segundo
                    sleep(1);                    
                    /* */
                }                            
            }
        }
    } 

    if ($contadorMensajes==0) {
        $direccionesNoEnviado[]=array($listado_alumnos["id_centro"],$listado_alumnos["nombre_aula"],"Alumno: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]);    
    } else {
        $direccionesEnviado[]=array($listado_alumnos["id_centro"],$listado_alumnos["nombre_aula"],"Alumno: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"],implode(',',$direccionesEnviadoEste));    
    }

    $contador_alumnos++;
    // Fin envio recibo
    
    } else {
        // echo("Centro: ".$listado_alumnos["id_centro"]." Aula:".$listado_alumnos["nombre_aula"]." Alumno: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." Sin código de inscripción<br>");
    }
}

?>
<table border=1 width="100%">
    <tr>
        <td>Centro</td>
        <td>Aula</td>
        <td>Alumno</td>
    </tr>
    <?php
    foreach ($direccionesNoEnviado as $elemento) {
        ?>
        <tr>
            <td><?=$elemento[0]?></td>
            <td><?=$elemento[1]?></td>
            <td><?=$elemento[2]?></td>
        </tr>        
        <?php
    }
    ?>
</table>
<table border=1 width="100%">
    <tr>
        <td>Centro</td>
        <td>Aula</td>
        <td>Alumno</td>
        <td>Resultado</td>
    </tr>
    <?php
    foreach ($direccionesEnviado as $elemento) {
        ?>
        <tr>
            <td><?=$elemento[0]?></td>
            <td><?=$elemento[1]?></td>
            <td><?=$elemento[2]?></td>
            <td><?=$elemento[3]?></td>
        </tr>        
        <?php
    }
    ?>
</table>
<?php
echo($listaEnviados);
?>