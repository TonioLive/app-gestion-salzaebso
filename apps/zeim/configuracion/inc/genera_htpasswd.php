<?php 
function htpasswd($pass) {
        $pass = crypt(trim($pass),base64_encode(CRYPT_STD_DES));
        return $pass;
        }

$fecha=strftime("%m/%d/%Y");
$no_padres=false;
$tipo_filtro=1;

  $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 ORDER BY Apellido1") or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  
  $archivo_htpasswd="";
  
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
    $codigo=$rsslc["id_usuario"];
    
    $fecha_control=date("Y-m-d");
    $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());
    
    if (db_num_rows($busca_contratos)) {
    
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
    
    $nombre_usuario_blog=strtolower($rsslc["usuario"]);
    $password_usuario_blog=$rsslc["pass"];
    
    $archivo_htpasswd=$archivo_htpasswd.$nombre_usuario_blog.":".htpasswd($password_usuario_blog)."\n";
    
    $contador=$contador+1;

    }
   }

// Busca padres

if ($no_padres==false) {

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d", time() - 86400 * 30))));

if (db_num_rows($busca_curso_actual)==0) {
                $filtro_cursos="alumnos.id_curso=0 OR ";
                } else {
                $filtro_cursos="";
                }

$contador_cursos=db_num_rows($busca_curso_actual);
while($datos_curso_actual=db_fetch_array($busca_curso_actual)) {
        $id_curso_actual=$datos_curso_actual["id_curso"];
        $filtro_cursos.="alumnos.id_curso=$id_curso_actual OR ";
        }

if (($filtro_cursos!="") and ($contador_cursos>1)) {
        $filtro_cursos="(".substr($filtro_cursos,0,strlen($filtro_cursos)-4).")";
        } else {
        if ($contador_cursos==1) {
                $filtro_cursos=substr($filtro_cursos,0,strlen($filtro_cursos)-4);
                } else {
                $filtro_cursos="alumnos.id_curso=0";
                }
        }

$busca_padres=db_query("SELECT padres.usuario_webcam,padres.password_webcam FROM alumnos,padres WHERE alumnos.id_usuario=padres.padre_de AND $filtro_cursos;") or die(db_error()."q");

$array_padres=array();
$contador_array=0;

while($rsslc=db_fetch_array($busca_padres)) {
        $nombre_usuario_blog=strtolower($rsslc["usuario_webcam"]);
        $password_usuario_blog=$rsslc["password_webcam"];

        if (array_search($nombre_usuario_blog,$array_padres,true)==false) {
        $archivo_htpasswd=$archivo_htpasswd.$nombre_usuario_blog.":".htpasswd($password_usuario_blog)."\n";
        
        $array_padres[$contador_array]=strtolower($nombre_usuario_blog);
        $contador_array++;
        }
        
        
        }
}

// Borra el archivo actual
if (file_exists("/var/pass/.htpasswdsantpau")) {
        unlink("/var/pass/.htpasswdsantpau");
        }
        
// Crea el nuevo archivo
$gestor_nuevo = fopen("/var/pass/.htpasswdsantpau", "w+");
fwrite($gestor_nuevo,$archivo_htpasswd);
fclose($gestor_nuevo);

?>
