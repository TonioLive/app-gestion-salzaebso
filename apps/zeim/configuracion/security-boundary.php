<?php
/** Common entry checks run before any legacy PHP endpoint. */
function security_stop($message='No tienes permiso para realizar esta operación.', $code=403) {
    http_response_code($code); header('Content-Type: text/plain; charset=UTF-8'); exit($message);
}
function security_family_stamp($rows) {
    $parts=array(); foreach($rows as $r) $parts[(int)$r['id_padre']]=array((int)$r['padre_de'],(string)$r['password_webcam'],(int)app_value($r,'denegar_acceso_agenda',0));
    ksort($parts); return hash('sha256',serialize($parts));
}
function security_family_valid() {
    if(empty($_SESSION['permitido_padre']) || empty($_SESSION['_family_verified']) || empty($_SESSION['id_padre']) || time()-(int)app_value($_SESSION,'_family_seen',0)>3600) return false;
    $rows=app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=?','is',array((int)$_SESSION['id_padre'],(string)app_value($_SESSION,'nombre_padre')));
    $valid=false;foreach($rows as $row) if(empty($row['denegar_acceso_agenda']))$valid=true;
    if(!$valid || empty($_SESSION['_family_credential_stamp']) || !hash_equals($_SESSION['_family_credential_stamp'],security_family_stamp($rows))) {
        unset($_SESSION['permitido_padre'],$_SESSION['_family_verified'],$_SESSION['_family_credential_stamp']); return false;
    }
    $_SESSION['_family_seen']=time();return true;
}
function security_upload_file($file) {
    if(!is_array($file) || !isset($file['error']) || is_array($file['error'])) security_stop('Formato de archivo no válido.',422);
    if((int)$file['error']===UPLOAD_ERR_NO_FILE)return;
    if((int)$file['error']!==UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name']))security_stop('No se ha podido recibir el archivo.',422);
    if(isset($file['full_path']) && $file['full_path']!==$file['name'])security_stop('Nombre de archivo no permitido.',422);
    $name=(string)$file['name'];$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if($name==='' || strlen($name)>180 || preg_match('~[\\\\/:\x00-\x1f\x7f]|(?:^|\.)\.(?:\.|$)|[. ]$~',$name) || preg_match('/\.(?:php\d*|phtml|phar|shtml|cgi|pl|py|exe|dll|bat|cmd|htaccess)(?:\.|$)/i',$name))security_stop('El nombre del archivo no está permitido.',422);
    $size=filesize($file['tmp_name']);if($size<1 || $size>20*1024*1024)security_stop('El archivo debe ocupar menos de 20 MB.',422);
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $images=array('jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp');
    if(isset($images[$ext])) {
        $info=@getimagesize($file['tmp_name']);
        if($mime!==$images[$ext] || !$info || $info[0]<1 || $info[1]<1 || $info[0]*$info[1]>25000000)security_stop('La imagen no es válida o es demasiado grande.',422);
        // Decode and re-encode, discarding scripts or metadata appended to a valid image.
        $im=@imagecreatefromstring(file_get_contents($file['tmp_name']));if(!$im)security_stop('No se puede leer la imagen.',422);
        $ok=false;
        if($ext==='jpg'||$ext==='jpeg')$ok=imagejpeg($im,$file['tmp_name'],92);
        elseif($ext==='png'){$ok=imagesavealpha($im,true)&&imagepng($im,$file['tmp_name']);}
        elseif($ext==='gif')$ok=imagegif($im,$file['tmp_name']);
        elseif($ext==='webp')$ok=imagewebp($im,$file['tmp_name'],90);
        if(!$ok)security_stop('No se ha podido preparar la imagen.',422);
        return;
    }
    $types=array('pdf'=>array('application/pdf'),'txt'=>array('text/plain'),'csv'=>array('text/plain','text/csv'),'rtf'=>array('text/rtf','application/rtf'),'doc'=>array('application/msword','application/CDFV2'),'xls'=>array('application/vnd.ms-excel','application/CDFV2'),'ppt'=>array('application/vnd.ms-powerpoint','application/CDFV2'),'pps'=>array('application/vnd.ms-powerpoint','application/CDFV2'));
    if(isset($types[$ext]) && in_array($mime,$types[$ext],true))return;
    $zipTypes=array('docx'=>'word/','xlsx'=>'xl/','pptx'=>'ppt/','ppsx'=>'ppt/','odt'=>'content.xml','ods'=>'content.xml');
    if(isset($zipTypes[$ext])) {
        $zip=new ZipArchive();if($zip->open($file['tmp_name'])===true){
            $found=false;$total=0;$unsafe=false;
            for($i=0;$i<$zip->numFiles;$i++){$stat=$zip->statIndex($i);$total+=$stat['size'];$n=$stat['name'];if(strpos($n,$zipTypes[$ext])===0)$found=true;if(stripos($n,'vbaProject')!==false || strpos($n,'../')!==false)$unsafe=true;}
            $zip->close();if($found&&!$unsafe&&$total<=100*1024*1024)return;
        }
    }
    security_stop('El contenido o el tipo del archivo no está permitido.',422);
}
function security_validate_uploads() {
    foreach($_FILES as $file) {
        if(is_array($file['error']??null)) {
            foreach($file['error'] as $key=>$error){$one=array();foreach(array('name','full_path','tmp_name','size','error','type') as $field)$one[$field]=$file[$field][$key]??null;security_upload_file($one);}
        }else security_upload_file($file);
    }
}
function security_owned_centre($id) {
    if((int)app_value($_SESSION,'nivel_acceso',0)===4 || !empty($_SESSION['usuario_multicentro']))return true;
    return (int)$id>0 && (int)$id===(int)app_value($_SESSION,'id_centro_usuario',0);
}
function security_staff_record($table,$column,$id) {
    if((int)app_value($_SESSION,'nivel_acceso',0)===4 || !empty($_SESSION['usuario_multicentro']))return;
    if($table==='facturas_emitidas')$sql='SELECT a.id_centro FROM facturas_emitidas f JOIN alumnos a ON a.id_alumno=f.id_cliente WHERE f.id_factura=?';
    elseif($table==='padres')$sql='SELECT a.id_centro FROM padres p JOIN alumnos a ON a.id_usuario=p.padre_de WHERE p.id_padre=?';
    elseif($table==='usuarios')$sql='SELECT id_centro FROM alumnos WHERE id_usuario=? UNION SELECT a.id_centro FROM padres p JOIN alumnos a ON a.id_usuario=p.padre_de WHERE p.id_usuario=?';
    else $sql='SELECT id_centro FROM '.$table.' WHERE '.$column.'=?';
    $rows=app_rows($sql,$table==='usuarios'?'ii':'i',$table==='usuarios'?array($id,$id):array($id));
    foreach($rows as $r)if(security_owned_centre($r['id_centro']))return;
    security_stop();
}
function security_check_record_inputs($route) {
    // These routes use the documented enrolment/class/invoice IDs; other contexts
    // have separate identifiers and are deliberately not guessed here.
    $school=preg_match('~^/(?:alumnos|aulas|aulas_docentes|facturacion|facturacion_vista_previa|gestion/(?:centros|gabinete|galerias_imagenes))/~',$route);
    if(preg_match('~^/(?:pdf|pdf2|pdf3)/~',$route))$school=true;
    if(!$school)return;
    foreach(array($_GET,$_POST) as $source) {
        $checks=array();
        if(preg_match('~^/(?:facturacion|facturacion_vista_previa)/~',$route))$checks=array('id_cliente'=>'alumnos','id_alumno'=>'alumnos');
        if(preg_match('~^/alumnos/(?:eliminar_inscripcion|guardar_inscripcion)\.php$~',$route))$checks['id_alumno']='alumnos';
        if(strpos($route,'/alumnos/')===0)$checks['id_padre']='padres';
        if(preg_match('~^/gestion/(?:centros|gabinete)/~',$route))$checks['id_alumno']='usuarios';
        foreach($checks as $key=>$table)if(isset($source[$key])&&$source[$key]!=='') {
            if(!is_scalar($source[$key])||!ctype_digit((string)$source[$key]))security_stop('Identificador no válido.',422);
            $id=(int)$source[$key];if($id)security_staff_record($table,$table==='alumnos'?'id_alumno':($table==='padres'?'id_padre':'id_usuario'),$id);
        }
    }
    foreach(array($_GET,$_POST) as $source) {
        foreach(array('id_centro','centro_factura','id_aula','id_factura') as $key)if(isset($source[$key])&&$source[$key]!=='') {
            if(!is_scalar($source[$key])||!ctype_digit((string)$source[$key]))security_stop('Identificador no válido.',422);
            $id=(int)$source[$key];if(!$id)continue;
            if($key==='id_centro'||$key==='centro_factura'){if(!security_owned_centre($id))security_stop();}
            else security_staff_record($key==='id_aula'?'centro_aulas':'facturas_emitidas',$key,$id);
        }
        if(preg_match('~^/(?:alumnos|aulas|aulas_docentes|facturacion)/~',$route))foreach(array('u','id_usuario','id_usuario_seleccionado') as $key)if(isset($source[$key])&&$source[$key]!=='') {
            if(!is_scalar($source[$key])||!ctype_digit((string)$source[$key]))security_stop('Identificador no válido.',422);
            if((int)$source[$key])security_staff_record('usuarios','id_usuario',(int)$source[$key]);
        }
    }
}

function security_calendar_range() {
    $dates=array();foreach(array('start','end') as $key) {
        $value=app_value($_GET,$key);
        if(!is_string($value)||!preg_match('/\A\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}:\d{2}(?:Z|[+-]\d{2}:\d{2})?)?\z/',$value))security_stop('Intervalo de fechas no permitido.',422);
        try{$date=new DateTimeImmutable($value);}catch(Throwable $error){security_stop('Fecha no permitida.',422);}
        if($date->format('Y-m-d')!==substr($value,0,10))security_stop('Fecha no permitida.',422);
        $_GET[$key]=$date->format('Y-m-d');$dates[]=$date->getTimestamp();
    }
    if($dates[1]<$dates[0]||$dates[1]-$dates[0]>400*86400)security_stop('El calendario admite intervalos de hasta un año.',422);
}

function security_entry_boundary() {
    if(PHP_SAPI==='cli')return;
    $root=str_replace('\\','/',realpath(dirname(__DIR__)));
    $file=str_replace('\\','/',(string)realpath(app_value($_SERVER,'SCRIPT_FILENAME')));
    if(strncasecmp($file,$root.'/',strlen($root)+1)!==0)security_stop();
    $route=strtolower(substr($file,strlen($root)));
    $GLOBALS['security_entry_route']=$route;
    if(preg_match('~/(?:vendor|node_modules|sample|samples|examples|test|tests|[^/]*_old[^/]*|[^/]*\bcopia\b[^/]*)(?:/|$)~i',$route))security_stop('Página no disponible.',404);
    foreach(array('sql_host','sql_usuario','sql_pass','sql_db') as $key)$GLOBALS[$key]=app_db_settings($key);
    app_start_session(in_array(basename(dirname(__DIR__)),array('nueva.neo-kids.es','neokids'),true)?'neokids_personal':'santpau_personal');
    app_same_origin_guard();
    $public=array('/index.php','/configuracion/inicio_sesion.php','/app/login.php','/app/acceso.php','/app/login_familias.php','/app/acceso_familias.php','/webcam/acceso.php','/webcam/index.php','/api/index.php');
    $isPublic=in_array($route,$public,true);
    // Public enrolment forms are kept separate from staff-only lists and imports.
    if(preg_match('~^/formulario/(?:contacto(?:1)?(?:_ev)?|exito)\.php$~',$route))$isPublic=true;
    if(!in_array(app_value($_SERVER,'REQUEST_METHOD'),array('GET','POST'),true)) {
        if(!($isPublic&&app_value($_SERVER,'REQUEST_METHOD')==='HEAD')){header('Allow: GET, POST');security_stop('Método no permitido.',405);}
    }
    if($route==='/api/index.php')return; // Dedicated HTTPS/token authentication.
    if(app_value($_SERVER,'REQUEST_METHOD')==='POST')app_check_csrf();
    if($isPublic){security_validate_uploads();return;}
    if($route==='/archivo-protegido.php' && security_family_valid())return;
    if(preg_match('~^/(?:app|webcam)/~',$route)||$route==='/configuracion/cerrar_sesion_padres.php') {
        if(!security_family_valid()){header('Location: /app/login.php',true,303);exit;}
        foreach(array($_GET,$_POST) as $source)if(isset($source['id_factura'])&&$source['id_factura']!=='') {
            $id=$source['id_factura'];if(!is_scalar($id)||!ctype_digit((string)$id))security_stop();
            $extra=in_array(basename(dirname(__DIR__)),array('santpau.escuelas-infantiles.com','zeim'),true)?' AND COALESCE(p.denegar_acceso_agenda,0)=0':'';
            if(!app_rows('SELECT f.id_factura FROM facturas_emitidas f JOIN alumnos a ON a.id_alumno=f.id_cliente JOIN padres p ON p.padre_de=a.id_usuario WHERE f.id_factura=? AND p.id_usuario=?'.$extra,'ii',array((int)$id,(int)$_SESSION['id_padre'])))security_stop();
        }
        security_validate_uploads();return;
    }
    if(in_array($route,array('/pdf2/facturacion/factura_alumno.php','/pdf2/facturacion/descargar_menu.php'),true)&&app_value($_GET,'tokendescarga')!=='')return; // Existing one-use, server-bound tokens are verified by the endpoint.
    require_once __DIR__.'/request-security.php';
    $permissions=array('zona_alumnos','zona_aulas','zona_facturacion','zona_comedor','zona_almacen','zona_incidencias','zona_personal','zona_pedidos','zona_inferior','zona_mensajes','zona_compras','zona_gabinete','zona_corregir_pruebas','zona_actividades');
    app_refresh_staff_identity($permissions);
    if(empty($_SESSION['permitido'])||empty($_SESSION['idusuario']))security_stop('Inicia sesión para acceder a esta sección.',401);
    $GLOBALS['permitido']=true;$GLOBALS['nivel_acceso']=(int)$_SESSION['nivel_acceso'];
    foreach($permissions as $p)$GLOBALS[$p]=(int)app_value($_SESSION,$p,0);
    $map=array('alumnos'=>'zona_alumnos','aulas'=>'zona_aulas','aulas_docentes'=>'zona_aulas','agenda'=>'zona_aulas','agenda_alumnos'=>'zona_aulas','programacion'=>'zona_aulas','facturacion'=>'zona_facturacion','facturacion_vista_previa'=>'zona_facturacion','caja'=>'zona_facturacion','clientes'=>'zona_facturacion','clientes_'=>'zona_facturacion','recibos'=>'zona_facturacion','almacen'=>'zona_almacen','pedidos'=>'zona_pedidos','gestion_gastos'=>'zona_compras','gestion_gastos_solo_lapices'=>'zona_compras','lista_espera'=>'zona_alumnos','contactos'=>'zona_alumnos','cocina'=>'zona_comedor','menu'=>'zona_comedor','uploadify'=>'zona_actividades');
    $parts=explode('/',trim($route,'/'));$section=$parts[0];$permission=$map[$section]??null;
    if($section==='gestion') {
        $groups=array('usuarios'=>'admin','personal'=>null,'docentes'=>'zona_personal','prl'=>'zona_personal','gabinete'=>'zona_gabinete','mensajes'=>'zona_mensajes','galerias_imagenes'=>'zona_actividades','centros'=>'zona_aulas','herramientas'=>'admin','doc_subvenciones'=>'admin','noticias'=>'zona_actividades','material'=>'zona_almacen','material_unido'=>'zona_almacen','incidencias'=>'zona_incidencias','contacto'=>'zona_mensajes','respuestas_consultas'=>'zona_mensajes','cuestionario_edu'=>'zona_aulas','listados'=>'zona_alumnos','guardias'=>'zona_personal','calendar'=>'zona_aulas','calendario'=>'zona_aulas');
        $sub=$parts[1]??'';$permission=$groups[$sub]??null;
        if(strpos($sub,'agenda')===0||strpos($sub,'grupos')===0)$permission='zona_aulas';
    }
    if(in_array($section,array('herramientas','mailer','mailing','api','formulario'),true))$permission='admin';
    if($permission && (($permission==='admin'&&(int)$_SESSION['nivel_acceso']!==4)||($permission!=='admin'&&!app_can($permission))))security_stop();
    if(preg_match('~^/gestion/grupos_~',$route)&&(int)$_SESSION['nivel_acceso']<3)security_stop('Usa la programación de tus grupos en la sección Piscina.');
    security_check_record_inputs($route);
    foreach(array($_GET,$_POST) as $source)foreach(array('sub','carpeta','subcarpeta','id_doc','nombre_doc','nombre_plantilla') as $key)if(isset($source[$key])&&$source[$key]!=='') {
        $value=$source[$key];if(!is_string($value)||preg_match('~(?:^|[\\\\/])\.\.(?:[\\\\/]|$)|[:\x00-\x1f]~',$value))security_stop('Ruta no válida.',422);
    }
    security_validate_uploads();
}
