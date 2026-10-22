<?php
// Datos
include("../../conf/config.php");
require("../configuracion/autentifica.php");

echo $_SESSION['usuario_candidato_id'];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass") or die(db_error());
db_select_db("$sql_db",$db) or die(db_error());

$id_usuario=$_GET["u"];

if (strlen($id_usuario)==0) { $id_usuario=0; }

$consulta_datos_usuario=db_query_params('SELECT * FROM usuarios, inscripciones WHERE usuarios.id_usuario=inscripciones.id_usuario AND usuarios.id_usuario=?', 's', array($id_usuario));

if (db_num_rows($consulta_datos_usuario)!=0) {

$datos_usuario=db_fetch_array($consulta_datos_usuario);

// Referencia
$ref_oft="000000".$datos_usuario["id_usuario"];
$ref_oft=substr($ref_oft,(strlen($ref_oft)-7),7);

switch($datos_usuario["introducido_desde"])
{
        case 0:
        $ref_oft="WEB".$ref_oft;
        break;
        case 1:
        $ref_oft="PROSEL".$ref_oft;
        break;
        case 1:
        $ref_oft="DALGO".$ref_oft;
        break;
}

          // ¿Tiene CV adjunto?
          $upload_path = "/var/www/docs/www.proseleccion.net/usuarios/cv_usuarios";
          $nombre_archivo1="cv_usu_".$datos_usuario["id_usuario"].".doc";
          $nombre_archivo2="cv_usu_".$datos_usuario["id_usuario"].".pdf";

          if ((file_exists($upload_path."/$nombre_archivo1")) or (file_exists($upload_path."/$nombre_archivo2"))) {

                        if (file_exists($upload_path."/$nombre_archivo1")) {
                                        $nombre_archivo_cv=$nombre_archivo1;
                                        } else {
                                        $nombre_archivo_cv=$nombre_archivo2;
                                        }
                        // esto es provisional... ya que cualuiera puede descargarse cualquier curriculum
                        // Hay que inventar una carpeta TMP que esté activa durante 30 minutos +/-
                        $upload_url="/usuarios/cv_usuarios";

        $link_cv="<a href='".$upload_url."/".$nombre_archivo_cv."' target='_new'>Ver CV</a>";
          } else {
          $link_cv="No tiene";
          }

// Letra NIF
if (($datos_usuario["tipo_documento"]=="D.N.I.") and (is_numeric($datos_usuario["dni"]))) {
        $cadena = 'TRWAGMYFPDXBNJZSQVHLCKET';
        $posicion = fmod($datos_usuario["dni"],23);
    $letra_nif=substr($cadena,$posicion,1);
        } else {
        $letra_nif='';
        }

// Busca nombre de nivel profesional
if (strlen($datos_usuario["nivelprof"])>0) {
$consulta_niveles_prof=db_query("SELECT * FROM niveles_profesionales_es WHERE cod_nivel=".$datos_usuario["nivelprof"]);
if (db_num_rows($consulta_niveles_prof)!=0) {
        $niveles_prof=db_fetch_array($consulta_niveles_prof);
        $nivel_profesional=$niveles_prof["nivel"];
        } else {
        $nivel_profesional="Desconocido";
        }
        } else {
        $nivel_profesional="Desconocido";
        }

// Busca Carnets
$consulta_carnets=db_query_params('SELECT * FROM usuario_carnets WHERE id_curriculum=?', 's', array($id_usuario));
$carnet_c=0;
$usuario_carnets="";
$usuario_carnets_otros="";
while($carnets_usuario=db_fetch_array($consulta_carnets))
                                                    {
                                                    $carnet_c=1;
                                                        switch($carnets_usuario["nombre_carnet"])
                                                        {
                                                                case 'Permiso de conducción A':
                                                                        $usuario_carnets=$usuario_carnets."A ";
                                                                break;
                                                                case 'Permiso de conducción B':
                                                                        $usuario_carnets=$usuario_carnets."B ";
                                                                break;
                                                                case 'Permiso de conducción C':
                                                                $usuario_carnets=$usuario_carnets."C ";
                                                                break;
                                                                case 'Permiso de conducción D':
                                                                        $usuario_carnets=$usuario_carnets."D ";
                                                                break;
                                                                case 'Permiso de conducción E':
                                                                $usuario_carnets=$usuario_carnets."E ";
                                                                break;
                                                                case 'Otros permisos de conducción':
                                                                $usuario_carnets_otros=$carnets_usuario["descripcion_carnet"];
                                                                break;
                                                        }
                                                    }
// Nombre de pais y provincia
$consulta_nombre_pais=db_query("SELECT * FROM paises_es WHERE id_pais=".$datos_usuario["pais"]) or die(db_error());

if (db_num_rows($consulta_nombre_pais)!=0) {
        $datos_pais=db_fetch_array($consulta_nombre_pais);
        $nombre_pais=$datos_pais["pais"];
        } else {
        $nombre_pais="";
        }

$consulta_nombre_provincia=db_query("SELECT * FROM provincias_42 WHERE id_provincia=".$datos_usuario["provincia"]) or die(db_error());

if (db_num_rows($consulta_nombre_provincia)!=0) {
        $datos_provincia=db_fetch_array($consulta_nombre_provincia);
        $nombre_provincia=$datos_provincia["provincia"];
        } else {
        $nombre_provincia="";
        }


$vehiculo=$datos_usuario["vehiculo"];
$trabajando=$datos_usuario["estado_actual"];



// --- FIN DATOS ---

error_reporting(E_ALL);
set_time_limit(1800);

include 'class.ezpdf.php';

$pdf = new Cezpdf('A3','landscape');

$pdf->ezSetDy(0);

// Imagen de fondo
$pdf->addJpegFromFile('enero2004_pag1.jpg',0,0,1190.55,841.89);

// Color y tipo de letra
$pdf->setColor('00','00','00','');
$pdf->selectFont('./fonts/Helvetica');

// Datos de registro
// Num. reg
$pdf->addText(738,788,12,$ref_oft);
// Fecha alta
$pdf->addText(750,758,14,date("j/n/Y",strtotime($datos_usuario["fecha_alta"])));

// Datos Personales
// Nif, nombre, 1 y 2 apellidos
$pdf->addText(638,415,10,$datos_usuario["tipo_documento"].": ".$datos_usuario["dni"].$letra_nif);
$pdf->addText(737,415,12,$datos_usuario["apellido1"]);
$pdf->addText(892,415,12,$datos_usuario["apellido2"]);
$pdf->addText(1044,415,12,$datos_usuario["nombre"]);

// Sexo (una X)
if ($datos_usuario["sexo"]=="Hombre") {
        //Hombre
        $pdf->addText(653,365,14,'X');
        } else {
        // Mujer
        $pdf->addText(653,346,14,'X');
        }
        
// Telefonos
$pdf->addText(790,370,12,$datos_usuario["telefono1"]);
$pdf->addText(790,345,12,$datos_usuario["movil"]);
// Fecha nacimiento, estado civil y número de hijos

$nacimiento=explode( '-', $datos_usuario["fecha_nacimiento"]);
$edad_candidato=date("Y")-$nacimiento[0];
$cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
$hoy=strtotime(date("Y/m/d"));
if (($cumple-$hoy)>0) {
                      $edad_candidato=$edad_candidato-1;
                      }
$pdf->addText(945,370,12,$nacimiento[2]."/".$nacimiento[1]."/".$nacimiento[0]);

// Estado civil:
$pdf->addText(945,345,12,$datos_usuario["estado_civil"]);

// Número de hijos:
$pdf->addText(1120,345,12,$datos_usuario["hijos"]);

// Dirección, población, CP y provincia
$domicilio_completo=$datos_usuario["domicilio1"]." ".$datos_usuario["domicilio2"];
$tam_domicilio=12;
if (strlen($domicilio_completo)>45) { $tam_domicilio=10; }
if (strlen($domicilio_completo)>55) { $tam_domicilio=8; }
if (strlen($domicilio_completo)>75) { $tam_domicilio=6; }
$pdf->addText(635,297,$tam_domicilio,$domicilio_completo);
$pdf->addText(920,297,12,$nombre_provincia);
$tam_poblacion=12;
if (strlen($datos_usuario["poblacion"])>16) { $tam_poblacion=10; }
if (strlen($datos_usuario["poblacion"])>18) { $tam_poblacion=8; }
if (strlen($datos_usuario["poblacion"])>23) { $tam_poblacion=6; }
$pdf->addText(1012,297,$tam_poblacion,$datos_usuario["poblacion"]);
$pdf->addText(1115,297,12,$datos_usuario["codigo_postal"]);

// Carnet de conducir y vehiculo propio (una X)
if ($carnet_c==1) {
        // Si
        $pdf->addText(656,226,14,'X');
        // Tipo
        $pdf->addText(718,228,10,$usuario_carnets);
        } else {
        // No
        $pdf->addText(656,244,14,'X');
        }
        
if ($vehiculo==1) {
        // Si
        $pdf->addText(828,226,14,'X');
        } else {
        // No
        $pdf->addText(828,244,14,'X');
        }

// Nacionalidad
$pdf->addText(985,260,12,$datos_usuario["nacionalidad"]);

// Email
$pdf->addText(925,221,12,$datos_usuario["email"]);

// Situacion socio-laboral
// Trabajando
if ($trabajando==1) {
        // Si
        $pdf->addText(393,766,14,'X');
        // Observaciones, motivo del cambio
        $pdf->addText(50,550,10,'<b>Motivo del cambio:</b> '.$datos_usuario["motivo_cambio"]);
        $pdf->addText(50,565,10,'<b>¿Desea cambiar de trabajo?:</b> '.$datos_usuario["cambio_trabajo"]);
        // Fecha prevista cambio
        if ($datos_usuario["fecha_cambio"]!=0) {
                $pdf->addText(473,710,12,date("j/n/Y",strtotime($datos_usuario["fecha_cambio"])));
                }
        } else {
        // No

        }
$pdf->addText(50,595,10,'<b>Nivel profesional:</b> '.$nivel_profesional);

$permiso_trab="No disponde de permiso de trabajo o no informa de su situación";
if ($datos_usuario["permisotrabajo"]=="1") {
        $permiso_trab=" Dispone de permiso de trabajo o es ciudadano comunitario";
        }

$pdf->addText(50,580,10,'<b>Permiso de trabajo:</b> '.$permiso_trab);

if (strlen($usuario_carnets_otros)!=0) {
        $pdf->addText(50,535,10,'<b>Otros carnets:</b> '.$usuario_carnets_otros);
        }

// Página 2

$pdf->ezNewPage();

$pdf->ezSetDy(0);

$pdf->addJpegFromFile('enero2004_pag2.jpg',0,0,1190.55,841.89);

$pdf->ezSetDy(-10);

// Experiencia profesional
// Maximo número de puestos mostrados
$max_experiencia=4;
$cont=0;

$busca_esperiencia=db_query_params('SELECT * FROM usuario_experiencia_profesional WHERE id_usuario=? ORDER BY ano_inicio DESC', 's', array($id_usuario)) or die(db_error()."-2");
if (db_num_rows($busca_esperiencia)==0) {

        $pdf->addText(70,575,14,'El candidato no dispone de experiencia o no ha completado este apartado.');

} else {

while($experiencia=db_fetch_array($busca_esperiencia))
{
        $cont=$cont+1;
        if ($cont==($max_experiencia+1)) {
                $pdf->ezSetDy(-5);
                $pdf->ezText("                     <b>NOTA:</b> El usuario disponde de más experiencia, pero toda la información no puede mostrarse en esta página. Para consultar esta información, acceda a la base de datos.");
                break;
                }
        
// Busca nombres
if (strlen($experiencia["tipo_empleo"])!=0) {
        $busca_nombre_contrato=db_query("SELECT * FROM empleo_tipos WHERE id_empleo=".$experiencia["tipo_empleo"]) or die(db_error()."-2");
        if (db_num_rows($busca_nombre_contrato)!=0) {
                        $nombre_contrato=db_fetch_array($busca_nombre_contrato);
                        $puesto_nombre_tipo=$nombre_contrato["nombre_tipo"];
                        } else {
                        $puesto_nombre_tipo="Desconocido";
                        }
        } else {
        $puesto_nombre_tipo="Desconocido";
        }
if (strlen($experiencia["tipo_contrato"])!=0) {
        $busca_tipo_contrato=db_query("SELECT * FROM contratos_tipos WHERE id_tipo_contrato=".$experiencia["tipo_contrato"]) or die(db_error()."-2b");
        if (db_num_rows($busca_tipo_contrato)!=0) {
                        $nombre_tipo_contrato=db_fetch_array($busca_tipo_contrato);
                        $contrato_nombre_tipo=$nombre_tipo_contrato["nombre_tipo_contrato"];
                        } else {
                        $contrato_nombre_tipo="Desconocido";
                        }
        } else {
        $contrato_nombre_tipo="Desconocido";
        }
if (strlen($experiencia["ano_fin"])==0) {
        $fin_contrato="Actualidad";
} else {
        $fin_contrato=$experiencia["ano_fin"];
}

        $data = array(
         array('Num'=>$cont,'Empresa'=>$experiencia["empresa"],'Pob'=>$experiencia["poblacion_empresa"],'Puesto'=>$experiencia["nombre_puesto"],'Tipo'=>$puesto_nombre_tipo,'Duracion'=>$experiencia["duracion"]." ".$experiencia["unidad_tiempo"],'Inicio'=>$experiencia["ano_inicio"],'Fin'=>$fin_contrato,'contrato'=>$contrato_nombre_tipo)
        );
        $pdf->ezSetDy(-10);

        $pdf->ezTable($data,array('Num'=>'Num.','Empresa'=>'Empresa','Pob'=>'Población','Puesto'=>'Puesto','Tipo'=>'Tipo de puesto','Duracion'=>'Duración','Inicio'=>'Año Inicio','Fin'=>'Año Fin','contrato'=>'Tipo de contrato'),'',array('xPos'=>303,'width'=>510,'fontSize'=>8,'titleFontSize'=>10));

        $funciones_del_puesto=$experiencia["tareas_puesto"];
        if (strlen($funciones_del_puesto)>500) {
                $funciones_del_puesto=substr($funciones_del_puesto,0,500);
                }
                
        $observaciones_del_puesto=$experiencia["observaciones"];
        if (strlen($observaciones_del_puesto)>500) {
                $observaciones_del_puesto=substr($observaciones_del_puesto,0,500);
                }


        $data = array(
         array('Func'=>$funciones_del_puesto,'Obser'=>$observaciones_del_puesto)
        );
        $pdf->ezSetDy(-5);

        $pdf->ezTable($data,array('Func'=>'Funciones','Obser'=>'Observaciones'),'',array('xPos'=>303,'width'=>510,'fontSize'=>6,'titleFontSize'=>8,'cols'=>array('Func'=>array('width'=>255),'Obser'=>array('width'=>255))));
}
}

// Formación reglada
// Maximo número de titulos mostrados
$max_formacion=5;

$pdf->ezSetY(325);

$busca_formacion_reglada=db_query_params('SELECT * FROM usuario_formacion_reglada WHERE id_usuario=? ORDER BY nivel DESC', 's', array($id_usuario)) or die(db_error());
if (db_num_rows($busca_formacion_reglada)==0) {

        $pdf->addText(60,200,12,'El candidato no dispone de formación reglada o no ha completado este apartado.');
        
} else {

$data=array();
$contador=0;
$mostar_mensaje_fc=false;
while($formacion_reglada=db_fetch_array($busca_formacion_reglada))
{
        $contador=$contador+1;
        if ($contador==($max_formacion+1)) {
                $mostar_mensaje_fc=true;
                break;
                }
// Busca nombres

$busca_nombre_nivel=db_query("SELECT * FROM niveles_estudio WHERE id_nivel_estudios=".$formacion_reglada["nivel"]) or die(db_error());
if (db_num_rows($busca_nombre_nivel)!=0) {
                $nombre_nivel=db_fetch_array($busca_nombre_nivel);
                $formacion_reglada_nombre_nivel=$nombre_nivel["nivel_estudios"];
                } else {
                $formacion_reglada_nombre_nivel="Desconocido";
                }


if ((strlen($formacion_reglada["titulo_obtenido"])==0) or ($formacion_reglada["titulo_obtenido"]==0)) {
        $formacion_reglada_nombre_tiulo=$formacion_reglada["titulo_obtenido_texto"];
        } else {
        $busca_nombre_titulo=db_query("SELECT * FROM estudios_listado WHERE id_estudio=".$formacion_reglada["titulo_obtenido"]) or die(db_error());
        if (db_num_rows($busca_nombre_titulo)!=0) {
                $nombre_titulo=db_fetch_array($busca_nombre_titulo);
                $formacion_reglada_nombre_tiulo=$nombre_titulo["titulo_estudio"];
                } else {
                $formacion_reglada_nombre_tiulo="Desconocido";
                }
        }
        $data[$contador-1]=array('Num'=>$contador,'Nivel'=>$formacion_reglada_nombre_nivel,'Titulo'=>$formacion_reglada_nombre_tiulo,'Centro'=>$formacion_reglada["centro"],'Inicio'=>$formacion_reglada["ano_inicio"],'Fin'=>$formacion_reglada["ano_fin"],'Obser'=>$formacion_reglada["observaciones"]);
}

        $pdf->ezSetDy(-10);

        $pdf->ezTable($data,array('Num'=>'Num.','Nivel'=>'Nivel de estudios','Titulo'=>'Titulo obtenido','Centro'=>'Centro de estudios','Inicio'=>'Año de inicio','Fin'=>'Año de finalización','Obser'=>'Observaciones'),'',array('xPos'=>303,'width'=>510,'fontSize'=>8,'titleFontSize'=>10));
        if ($mostar_mensaje_fc==true) {
                $pdf->ezSetDy(-5);
                $pdf->ezText("                            <b>NOTA:</b> El usuario disponde de más títulos. Para consultar esta información, acceda a la base de datos.");
                }

}

// Formación complementaria
// Maximo número de titulos mostrados
$max_formacion_nor=5;

$pdf->ezSetY(795);

$busca_formacion_reglada=db_query_params('SELECT * FROM usuario_formacion_complementaria WHERE id_usuario=? ORDER BY tipo_curso DESC', 's', array($id_usuario)) or die(db_error());
if (db_num_rows($busca_formacion_reglada)==0) {

        $pdf->addText(650,720,12,'El candidato no dispone de formación complementaria o no ha completado este apartado.');

} else {
$contador=0;
$data = array();
$mostar_mensaje_fc=false;
while($formacion_reglada=db_fetch_array($busca_formacion_reglada))
{

        $contador=$contador+1;
        if ($contador==($max_formacion_nor+1)) {
                $mostar_mensaje_fc=true;
                break;
                }

// Busca nombres

$busca_nombre_nivel=db_query("SELECT * FROM formacion_complementaria_tipos WHERE id_tipo=".$formacion_reglada["tipo_curso"]) or die(db_error());
if (db_num_rows($busca_nombre_nivel)!=0) {
                $nombre_nivel=db_fetch_array($busca_nombre_nivel);
                $formacion_reglada_nombre_nivel=$nombre_nivel["tipo_curso"];
                } else {
                $formacion_reglada_nombre_nivel="Desconocido";
                }


$formacion_reglada_nombre_tiulo=$formacion_reglada["nombre_curso"];

        $data[$contador-1]=array('Tipo'=>$formacion_reglada_nombre_nivel,'Curso'=>$formacion_reglada_nombre_tiulo,'Centro'=>$formacion_reglada["centro"],'Fecha'=>$formacion_reglada["anio_curso"],'Duracion'=>$formacion_reglada["duracion_curso"],'Obser'=>$formacion_reglada["observaciones"]);

        }

        $pdf->ezSetDy(-10);

        $pdf->ezTable($data,array('Tipo'=>'Tipo de curso','Curso'=>'Nombre del curso','Centro'=>'Centro de estudios','Fecha'=>'Año de realización','Duracion'=>'Duración'),'',array('xPos'=>899,'width'=>510,'fontSize'=>6,'titleFontSize'=>10));
        if ($mostar_mensaje_fc==true) {
                $pdf->addText(680,650,6,"<b>NOTA:</b> El usuario disponde de más cursos, pero toda la información no puede mostrarse en esta página. Para consultar esta información, acceda a la base de datos.");
                }
}

// Informatica
$max_infor=19;

$pdf->ezSetY(615);

$grupo_actual=2;
$busca_idiomas=db_query("SELECT * FROM usuario_nivel_conocimientos WHERE grupo=$grupo_actual AND id_usuario=".$id_usuario." ORDER BY nivel DESC") or die(db_error());
if (db_num_rows($busca_idiomas)==0) {

        $pdf->addText(675,475,10,'El candidato no dispone de conocimientos de informática o no ha completado este apartado.');

} else {
$contador=0;
$data = array();
$mostar_mensaje_fc=false;

while($idiomas=db_fetch_array($busca_idiomas))
{


        $contador=$contador+1;
        if ($contador==($max_infor+1)) {
                $mostar_mensaje_fc=true;
                break;
                }

// Busca nombres
if (strlen($idiomas["conocimiento_personal"])==0) {
$busca_nombre_conocimiento=db_query_params('SELECT * FROM conocimientos_tipos WHERE id_tipo_conocimiento=?', 's', array($idiomas["id_conocimiento"])) or die(db_error());
if (db_num_rows($busca_nombre_conocimiento)!=0) {
                $conocimiento=db_fetch_array($busca_nombre_conocimiento);
                $nombre_idioma=$conocimiento["nombre_conocimiento"];
                } else {
                $nombre_idioma="Desconocido";
                }
        } else {
        $nombre_idioma=$idiomas["conocimiento_personal"];
        }

switch($idiomas["nivel"])
{
        case "1":
        $nombre_nivel="1 (Bajo)";
        break;
        case "2":
        $nombre_nivel="2 (Medio-Bajo)";
        break;
        case "3":
        $nombre_nivel="3 (Medio)";
        break;
        case "4":
        $nombre_nivel="4 (Medio-Alto)";
        break;
        case "5":
        $nombre_nivel="5 (Alto)";
        break;
}

        $data[$contador-1]=array('Num'=>$contador,'Conocimiento'=>$nombre_idioma,'Nivel'=>$nombre_nivel);
}

        $pdf->ezSetDy(-10);

        $pdf->ezTable($data,array('Num'=>'Num.','Conocimiento'=>'Programa, aplicación, Sistema Operativo o lenguaje de programación','Nivel'=>'Nivel de conocimiento'),'',array('xPos'=>899,'width'=>510,'fontSize'=>8,'titleFontSize'=>10));
        
        if ($mostar_mensaje_fc==true) {
                $pdf->addText(645,320,6,"<b>NOTA:</b> El usuario disponde de más conocimientos de informática, pero toda la información no puede mostrarse en esta página. Para consultar esta información, acceda a la base de datos.");
                }
                
}


// Idiomas
$max_infor=14;

$pdf->ezSetY(275);

$grupo_actual=1;
$busca_idiomas=db_query("SELECT * FROM usuario_nivel_conocimientos WHERE grupo=$grupo_actual AND id_usuario=".$id_usuario." ORDER BY nivel DESC") or die(db_error());
if (db_num_rows($busca_idiomas)==0) {

        $pdf->addText(690,150,10,'El candidato no dispone de conocimientos de idiomas o no ha completado este apartado.');

} else {
$contador=0;
$data = array();
$mostar_mensaje_fc=false;

while($idiomas=db_fetch_array($busca_idiomas))
{


        $contador=$contador+1;
        if ($contador==($max_infor+1)) {
                $mostar_mensaje_fc=true;
                break;
                }

// Busca nombres
if (strlen($idiomas["conocimiento_personal"])==0) {
$busca_nombre_conocimiento=db_query_params('SELECT * FROM conocimientos_tipos WHERE id_tipo_conocimiento=?', 's', array($idiomas["id_conocimiento"])) or die(db_error());
if (db_num_rows($busca_nombre_conocimiento)!=0) {
                $conocimiento=db_fetch_array($busca_nombre_conocimiento);
                $nombre_idioma=$conocimiento["nombre_conocimiento"];
                } else {
                $nombre_idioma="Desconocido";
                }
        } else {
        $nombre_idioma=$idiomas["conocimiento_personal"];
        }

switch($idiomas["nivel"])
{
        case "1":
        $nombre_nivel="1 (Bajo)";
        break;
        case "2":
        $nombre_nivel="2 (Medio-Bajo)";
        break;
        case "3":
        $nombre_nivel="3 (Medio)";
        break;
        case "4":
        $nombre_nivel="4 (Medio-Alto)";
        break;
        case "5":
        $nombre_nivel="5 (Alto)";
        break;
}

        $data[$contador-1]=array('Num'=>$contador,'Conocimiento'=>$nombre_idioma,'Nivel'=>$nombre_nivel);
}
        $pdf->ezSetDy(-10);

        $pdf->ezTable($data,array('Num'=>'Num.','Conocimiento'=>'Idioma','Nivel'=>'Nivel de conocimiento'),'',array('xPos'=>899,'width'=>510,'fontSize'=>8,'titleFontSize'=>10));
        
        if ($mostar_mensaje_fc==true) {
                $pdf->addText(645,40,6,"<b>NOTA:</b> El usuario disponde de más conocimientos de idiomas, pero toda la información no puede mostrarse en esta página. Para consultar esta información, acceda a la base de datos.");
                }
}
                
$pdf->ezStream();

} else {

?>
<font face="Arial" size=2>
<br><br>
<p align="center">No se ha encontrado el usuario</p>
<p align="center"><a href="javascript:window.close();">Cerrar ventana</a></p>
</font>
<?php 

}

db_close();
?>
