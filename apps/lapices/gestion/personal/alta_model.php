<?php
require_once dirname(__DIR__).'/panel/workflow.php';
require_once dirname(__DIR__).'/usuarios/password_consulta_lib.php';
require_once __DIR__.'/contrato_parser.php';
function personal_value($source,$key,$default=''){return isset($source[$key])&&is_scalar($source[$key])?trim((string)$source[$key]):$default;}
function personal_actor(){
    if(empty($GLOBALS['permitido']))throw new DomainException('Inicia sesión para gestionar el personal.');
    $rows=lap_wf_rows('SELECT id_usuario,nivel_acceso,id_centro FROM usuarios WHERE id_usuario=? AND tipo_inscripcion>=3 AND nivel_acceso IN (3,4)','i',array((int)$GLOBALS['idUsuario']));
    if(!$rows)throw new DomainException('No tienes permiso para dar de alta personal.');
    if(empty($GLOBALS['centro_actual'])||((int)$rows[0]['nivel_acceso']!==4&&(int)$rows[0]['id_centro']!==(int)$GLOBALS['centro_actual']))throw new DomainException('Selecciona tu centro de trabajo.');
    return $rows[0];
}
function personal_centres($actor){
    $sql='SELECT id_centro,nombre_centro,poblacion_centro FROM centros';
    if((int)$actor['nivel_acceso']!==4)return lap_wf_rows($sql.' WHERE id_centro=?','i',array((int)$actor['id_centro']));
    return lap_wf_rows($sql.((int)$actor['id_usuario']===1?'':' WHERE mostrar_en_lapices=1').' ORDER BY centro_propio DESC,nombre_centro');
}
function personal_private_dir(){
    $dir=dirname(dirname(__DIR__)).'/.private/contratos-personal';
    if(!is_dir($dir)&&!mkdir($dir,0700,true))throw new RuntimeException('No se ha podido preparar el archivo de contratos.');return $dir;
}
function personal_discard_draft($draft){
    if(is_array($draft)&&isset($draft['upload']['file'])&&preg_match('/^draft-[a-f0-9]{48}\.pdf$/',$draft['upload']['file'])){
        $path=personal_private_dir().'/'.$draft['upload']['file'];
        if(is_file($path)&&!unlink($path))throw new RuntimeException('No se ha podido descartar el PDF del borrador.');
    }
}
function personal_upload($file){
    if(!is_array($file)||!isset($file['error'])||(int)$file['error']!==UPLOAD_ERR_OK)throw new InvalidArgumentException('No se pudo subir el PDF. Revisa el archivo y el límite de tamaño.');
    if($file['size']<5||$file['size']>8*1024*1024||!is_uploaded_file($file['tmp_name']))throw new InvalidArgumentException('Sube un contrato PDF de hasta 8 MB.');
    $h=fopen($file['tmp_name'],'rb');$signature=fread($h,5);fclose($h);
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if($signature!=='%PDF-'||!in_array($mime,array('application/pdf','application/x-pdf'),true))throw new InvalidArgumentException('El archivo debe ser un PDF válido.');
    $dir=personal_private_dir();
    foreach(glob($dir.'/draft-*.pdf') as $old)if(filemtime($old)<time()-86400)@unlink($old);
    $name='draft-'.bin2hex(gestion_password_random(24)).'.pdf';
    if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))throw new RuntimeException('No se ha podido guardar el contrato.');
    return array('file'=>$name,'name'=>mb_substr(basename($file['name']),0,240,'UTF-8'));
}
function personal_date_valid($date){$d=DateTime::createFromFormat('!Y-m-d',$date);return $d&&$d->format('Y-m-d')===$date;}
function personal_defaults(){return array('Nombre'=>'','Apellido1'=>'','Apellido2'=>'','DNI'=>'','NSS'=>'','profesion'=>'','telefono1'=>'','Direccion'=>'','email'=>'','telefono_contacto1'=>'','nivel_acceso'=>'2','fecha_inicio'=>date('Y-m-d'),'fecha_fin'=>'','horas_semana'=>'','hora_entrada1'=>'','hora_salida1'=>'','hora_entrada2'=>'','hora_salida2'=>'','id_centro'=>isset($GLOBALS['centro_actual'])?(string)(int)$GLOBALS['centro_actual']:'0','id_aula'=>'0','id_curso'=>'0','tipo_contrato'=>'2','id_categoria'=>'0','l'=>'1','m'=>'1','x'=>'1','j'=>'1','v'=>'1','s'=>'0','d'=>'0');}
function personal_permissions(){return array('zona_alumnos'=>'Alumnos','zona_aulas'=>'Aulas','zona_comedor'=>'Comedor','zona_almacen'=>'Almacén','zona_incidencias'=>'Incidencias','zona_personal'=>'Personal','zona_pedidos'=>'Pedidos','zona_mensajes'=>'Mensajes','zona_facturacion'=>'Facturación','zona_compras'=>'Compras','zona_gabinete'=>'Gabinete','zona_corregir_pruebas'=>'Pruebas','zona_actividades'=>'Actividades');}
function personal_validate($input,$actor){
    $data=personal_defaults();foreach($data as $key=>$default)$data[$key]=personal_value($input,$key,$default);
    $centreAllowed=false;foreach(personal_centres($actor) as $centre)if((string)(int)$centre['id_centro']===$data['id_centro'])$centreAllowed=true;
    if(!$centreAllowed)throw new DomainException('Selecciona una escuela a la que tengas acceso.');
    foreach(array('Nombre','Apellido1','DNI','NSS','profesion') as $key)if($data[$key]==='')throw new InvalidArgumentException('Completa nombre, apellidos, DNI/NIE, Seguridad Social y puesto de trabajo.');
    foreach(array('Nombre','Apellido1','Apellido2','profesion','email') as $key)if(mb_strlen($data[$key],'UTF-8')>255)throw new InvalidArgumentException('Revisa la longitud de los datos personales.');
    $data['DNI']=preg_replace('/[\s-]+/','',mb_strtoupper($data['DNI'],'UTF-8'));
    if(!preg_match('/^(?:\d{8}|[XYZ]\d{7})[A-Z]$/',$data['DNI']))throw new InvalidArgumentException('Introduce un DNI o NIE válido.');
    $dniNumber=strtr(substr($data['DNI'],0,-1),array('X'=>'0','Y'=>'1','Z'=>'2'));$letters='TRWAGMYFPDXBNJZSQVHLCKE';
    if(substr($data['DNI'],-1)!==$letters[(int)$dniNumber%23])throw new InvalidArgumentException('La letra del DNI o NIE no coincide.');
    $data['NSS']=preg_replace('/[\s\/.-]+/','',$data['NSS']);if(!preg_match('/^\d{12}$/',$data['NSS']))throw new InvalidArgumentException('El número de Seguridad Social debe tener 12 cifras.');
    $role=(int)$data['nivel_acceso'];if(!in_array($role,array(2,3,4,10,11),true)||((int)$actor['nivel_acceso']!==4&&$role===4))throw new DomainException('No puedes asignar ese nivel de acceso.');
    if(!personal_date_valid($data['fecha_inicio'])||($data['fecha_fin']!==''&&(!personal_date_valid($data['fecha_fin'])||$data['fecha_fin']<$data['fecha_inicio'])))throw new InvalidArgumentException('Revisa el inicio y el fin del periodo de trabajo.');
    if(!lap_wf_rows('SELECT id_tipo_contrato FROM personal_tipos_contrato WHERE id_tipo_contrato=?','i',array((int)$data['tipo_contrato'])))throw new InvalidArgumentException('Selecciona el tipo de contrato.');
    if((int)$data['tipo_contrato']===1&&$data['fecha_fin']==='')throw new InvalidArgumentException('Indica cuándo termina el contrato temporal.');
    if(!lap_wf_rows('SELECT id_categoria FROM personal_cat_laborales WHERE id_categoria=?','i',array((int)$data['id_categoria'])))throw new InvalidArgumentException('Selecciona la categoría laboral.');
    $hours=str_replace(',','.',$data['horas_semana']);if(!is_numeric($hours)||(float)$hours<=0||(float)$hours>80)throw new InvalidArgumentException('Indica las horas semanales, entre 0 y 80.');$data['horas_semana']=$hours;
    foreach(array(1,2) as $period){$in=$data['hora_entrada'.$period];$out=$data['hora_salida'.$period];if(($in!==''||$out!=='')&&(!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$in)||!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$out)||$out<=$in))throw new InvalidArgumentException('Revisa las horas de entrada y salida.');}
    if($data['hora_entrada2']!==''&&($data['hora_entrada1']===''||$data['hora_entrada2']<$data['hora_salida1']))throw new InvalidArgumentException('Los dos tramos de trabajo no pueden solaparse.');
    foreach(array('l','m','x','j','v','s','d') as $day)$data[$day]=personal_value($input,$day,'0')==='1'?1:0;
    if(!array_sum(array_intersect_key($data,array_flip(array('l','m','x','j','v','s','d')))))throw new InvalidArgumentException('Selecciona al menos un día de trabajo.');
    if((int)$data['id_aula']){
        if(!in_array($role,array(2,3),true))throw new InvalidArgumentException('La asignación de aula corresponde al personal docente.');
        $classes=lap_wf_rows('SELECT a.id_aula,c.fecha_inicio,c.fecha_fin FROM centro_aulas a JOIN cursos_academicos c ON c.id_curso=a.id_curso WHERE a.id_aula=? AND a.id_centro=? AND a.id_curso=? AND a.tipo=0','iii',array((int)$data['id_aula'],(int)$data['id_centro'],(int)$data['id_curso']));
        if(!$classes)throw new DomainException('El aula no pertenece al centro y curso seleccionados.');
        if($data['fecha_inicio']>$classes[0]['fecha_fin']||($data['fecha_fin']!==''&&$data['fecha_fin']<$classes[0]['fecha_inicio']))throw new InvalidArgumentException('El periodo de trabajo no coincide con el curso del aula.');
    }elseif($role===2)throw new InvalidArgumentException('Selecciona el aula donde trabajará la educadora.');
    if($data['email']!==''&&!filter_var($data['email'],FILTER_VALIDATE_EMAIL))throw new InvalidArgumentException('Revisa el correo electrónico.');
    if(mb_strlen($data['Direccion'],'UTF-8')>1000||strlen($data['telefono1'])>50||strlen($data['telefono_contacto1'])>50)throw new InvalidArgumentException('Revisa la longitud de los datos de contacto.');
    foreach(personal_permissions() as $permission=>$label){$requested=personal_value($input,$permission,'0')==='1';if($requested&&(int)$actor['nivel_acceso']!==4&&empty($GLOBALS[$permission]))throw new DomainException('No puedes conceder un permiso que no tienes.');$data[$permission]=$requested?1:0;}
    return $data;
}
function personal_create($input,$draft){
    $actor=personal_actor();$data=personal_validate($input,$actor);
    if(!isset($draft['token'],$draft['centre'],$draft['actor'],$draft['created'])||!preg_match('/^[a-f0-9]{64}$/',$draft['token'])||(int)$draft['centre']!==(int)$GLOBALS['centro_actual']||(int)$draft['actor']!==(int)$actor['id_usuario']||$draft['created']<time()-3600)throw new DomainException('El borrador ha caducado o ha cambiado el centro. Inicia un alta nueva.');
    $lock=lap_wf_rows("SELECT GET_LOCK('lapices-personal-alta',5) AS ok");if(empty($lock[0]['ok']))throw new RuntimeException('Hay otra alta en curso. Inténtalo de nuevo.');
    $id=0;$enabled=false;$file='';
    try{
        if(lap_wf_rows('SELECT id_usuario FROM personal_altas WHERE solicitud=?','s',array($draft['token'])))throw new InvalidArgumentException('Esta alta ya se ha guardado. Consulta el listado de personal.');
        if(lap_wf_rows('SELECT id_usuario FROM usuarios WHERE REPLACE(REPLACE(UPPER(DNI)," ",""),"-","")=? AND tipo_inscripcion>=3 LIMIT 1','s',array($data['DNI'])))throw new InvalidArgumentException('Ya existe personal con ese DNI/NIE. Edita su ficha para evitar duplicados.');
        $base=substr(strtolower(preg_replace('/[^a-zA-Z0-9]/','',iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$data['Nombre'].$data['Apellido1']))),0,30);if($base==='')$base='personal';$username=$base;$suffix=1;
        while(lap_wf_rows('SELECT id_usuario FROM usuarios WHERE usuario=? LIMIT 1','s',array($username))){$username=$base.(++$suffix);if($suffix>10000)throw new RuntimeException('No se ha podido preparar el usuario.');}
        $password=substr(bin2hex(gestion_password_random(12)),0,20);$hash=password_hash($password,PASSWORD_BCRYPT,array('cost'=>10));if(!$hash)throw new RuntimeException('No se han podido generar las credenciales.');
        $key=gestion_password_key();
        $columns=array('tipo_inscripcion','Nombre','Apellido1','Apellido2','DNI','NSS','profesion','nivel_acceso','id_centro','fechaalta','telefono1','Direccion','email','telefono_contacto1');
        $values=array(4,$data['Nombre'],$data['Apellido1'],$data['Apellido2'],$data['DNI'],$data['NSS'],$data['profesion'],$data['nivel_acceso'],$data['id_centro'],date('Y-m-d'),$data['telefono1'],$data['Direccion'],$data['email'],$data['telefono_contacto1']);
        foreach(personal_permissions() as $permission=>$label){$columns[]=$permission;$values[]=$data[$permission];}
        // Keep login disabled until all legacy tables and the onboarding gate exist.
        $id=lap_wf_exec('INSERT INTO usuarios (`'.implode('`,`',$columns).'`) VALUES ('.implode(',',array_fill(0,count($values),'?')).')',str_repeat('s',count($values)),$values);
        $end=$data['fecha_fin']!==''?$data['fecha_fin']:null;
        lap_wf_exec('INSERT INTO personal_contratos (id_empleado,id_categoria,tipo_contrato,fecha_inicio,fecha_fin,observaciones) VALUES (?,?,?,?,?,?)','iiisss',array($id,(int)$data['id_categoria'],(int)$data['tipo_contrato'],$data['fecha_inicio'],$end,$data['profesion']));
        $schedule=array($id,(int)$data['id_centro'],$data['fecha_inicio'],$end?:'0000-00-00',$data['horas_semana']);foreach(array('hora_entrada1','hora_salida1','hora_entrada2','hora_salida2') as $time)$schedule[]=$data[$time]!==''?$data[$time].':00':'00:00:00';foreach(array('l','m','x','j','v','s','d') as $day)$schedule[]=$data[$day];
        lap_wf_exec('INSERT INTO horario_docentes (id_usuario,id_centro,fecha_inicio,fecha_fin,horas_semana,hora_entrada1,hora_salida1,hora_entrada2,hora_salida2,l,m,x,j,v,s,d) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)','iissdssssiiiiiii',$schedule);
        if((int)$data['id_aula'])lap_wf_exec('INSERT INTO personal_historial (id_empleado,id_escuela,id_aula,fecha_inicio,fecha_fin) VALUES (?,?,?,?,?)','iiiss',array($id,(int)$data['id_centro'],(int)$data['id_aula'],$data['fecha_inicio'],$end));
        if(!empty($draft['upload']['file'])){
            $source=$draft['upload']['file'];if(!preg_match('/^draft-[a-f0-9]{48}\.pdf$/',$source))throw new RuntimeException('El contrato no está disponible.');
            $file='contrato-'.$id.'-'.bin2hex(gestion_password_random(12)).'.pdf';if(!rename(personal_private_dir().'/'.$source,personal_private_dir().'/'.$file))throw new RuntimeException('No se ha podido archivar el contrato.');
        }
        lap_wf_exec('INSERT INTO personal_altas (id_usuario,creado_por,id_centro,pendiente,creado,contrato_archivo,contrato_nombre,solicitud) VALUES (?,?,?,1,NOW(),?,?,?)','iiisss',array($id,(int)$actor['id_usuario'],(int)$data['id_centro'],$file,isset($draft['upload']['name'])?$draft['upload']['name']:'',$draft['token']));
        $sealed=gestion_password_seal($id,$password,$hash,$key);
        lap_wf_exec('UPDATE usuarios SET usuario=?,pass=?,pass_consulta=?,pass_consulta_hash=? WHERE id_usuario=?','ssssi',array($username,$hash,$sealed,hash('sha256',$hash),$id));$enabled=true;
        return array('id'=>$id,'username'=>$username,'password'=>$password,'name'=>trim($data['Nombre'].' '.$data['Apellido1'].' '.$data['Apellido2']),'created'=>time(),'contract'=>$file!=='');
    }catch(Exception $e){
        // Legacy tables are MyISAM: compensate only the rows created by this call.
        if($id&&!$enabled){foreach(array('personal_altas'=>'id_usuario','personal_historial'=>'id_empleado','horario_docentes'=>'id_usuario','personal_contratos'=>'id_empleado','usuarios'=>'id_usuario') as $table=>$field)db_query_params('DELETE FROM '.$table.' WHERE '.$field.'=?','i',array($id));if($file&&is_file(personal_private_dir().'/'.$file)&&!empty($draft['upload']['file']))@rename(personal_private_dir().'/'.$file,personal_private_dir().'/'.$draft['upload']['file']);}throw $e;
    }finally{db_query("SELECT RELEASE_LOCK('lapices-personal-alta')");}
}
