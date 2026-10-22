<?php
if (PHP_SAPI !== 'cli') exit;
require dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(dirname(__DIR__)) . '/gestion/usuarios/password_consulta_lib.php';
$checks=0;
function vault_check($ok,$label){global $checks;if(!$ok)throw new RuntimeException($label);$checks++;echo 'OK '.$label.PHP_EOL;}
function vault_rejects($callback,$class,$label){$rejected=false;try{$callback();}catch(Exception $e){$rejected=$e instanceof $class;}vault_check($rejected,$label);}
try {
    $key=gestion_password_key();
    $plain='Solo prueba: ñ € <texto> & 2026';
    $hash=password_hash($plain,PASSWORD_BCRYPT);
    $fingerprint=hash('sha256',$hash);
    $sealed=gestion_password_seal(9002,$plain,$hash,$key);
    vault_check(gestion_password_open(9002,$sealed,$hash,$fingerprint,$key)===$plain,'encrypted copy restores exact Unicode and markup as text');
    vault_check(strpos($sealed,$plain)===false,'stored copy does not contain plaintext');
    vault_check(gestion_password_seal(9002,$plain,$hash,$key)!==$sealed,'random IV changes ciphertext on every write');
    vault_rejects(function()use($plain,$hash,$key){gestion_password_seal(9002,'different',$hash,$key);},'InvalidArgumentException','cannot save a copy that differs from the login hash');
    $tampered=base64_decode($sealed);$tampered[4]=chr(ord($tampered[4])^1);
    vault_rejects(function()use($tampered,$hash,$fingerprint,$key){gestion_password_open(9002,base64_encode($tampered),$hash,$fingerprint,$key);},'RuntimeException','modified IV fails authentication');
    $tampered=base64_decode($sealed);$tampered[25]=chr(ord($tampered[25])^1);
    vault_rejects(function()use($tampered,$hash,$fingerprint,$key){gestion_password_open(9002,base64_encode($tampered),$hash,$fingerprint,$key);},'RuntimeException','modified ciphertext fails authentication');
    vault_rejects(function()use($sealed,$hash,$fingerprint){gestion_password_open(9002,$sealed,$hash,$fingerprint,str_repeat('x',64));},'RuntimeException','wrong encryption key fails closed');
    vault_rejects(function()use($sealed,$hash,$fingerprint,$key){gestion_password_open(9003,$sealed,$hash,$fingerprint,$key);},'RuntimeException','copy cannot be moved to another account');
    vault_check(gestion_password_open(9002,$sealed,password_hash('changed',PASSWORD_BCRYPT),$fingerprint,$key)===null,'changed login hash never reveals the previous password');
    vault_check(gestion_password_open(9002,null,$hash,null,$key)===null,'historical hash is honestly unavailable');
    if (!db_query('CREATE TEMPORARY TABLE usuarios (id_usuario INT PRIMARY KEY,nivel_acceso INT,tipo_inscripcion INT,pass VARCHAR(255),pass_consulta TEXT,pass_consulta_hash CHAR(64)) ENGINE=InnoDB')
        || !db_query('CREATE TEMPORARY TABLE usuarios_password_consulta_log (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, administrador_id INT NOT NULL, evento VARCHAR(16) NOT NULL, cantidad INT NOT NULL, fecha DATETIME NOT NULL) ENGINE=InnoDB')) {
        throw new RuntimeException('Temporary isolation unavailable; stopping before any fixture writes.');
    }
    $adminPlain='Demo administrador 2026!';$adminHash=password_hash($adminPlain,PASSWORD_BCRYPT);
    db_query_params('INSERT INTO usuarios VALUES (9001,4,4,?,NULL,NULL),(9002,2,4,?,?,?),(9003,2,4,?,NULL,NULL),(9004,2,4,\'\',NULL,NULL),(9005,1,1,?,NULL,NULL)', 'ssssss',array($adminHash,$hash,$sealed,$fingerprint,$hash,$hash));
    $permitido=true;$nivel_acceso=4;$centro_actual=1;$_SESSION['idusuario']=9001;
    $result=gestion_password_reveal(array(9002,9003,9004,9005),$adminPlain);
    vault_check(count($result)===3,'query excludes non-staff accounts');
    $byId=array();foreach($result as $row)$byId[$row['id']]=$row;
    vault_check($byId[9002]['password']===$plain&&$byId[9002]['estado']==='disponible','administrator can read an assigned password');
    vault_check($byId[9003]['password']===null&&$byId[9003]['estado']==='no_disponible','old hashes remain unavailable');
    vault_check($byId[9004]['password']===null&&$byId[9004]['estado']==='sin_configurar','empty account has no fabricated password');
    vault_check(!isset($byId[9002]['pass'])&&!isset($byId[9002]['pass_consulta']),'response never includes hashes or encrypted blobs');
    $nivel_acceso=3;
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array(9002),$adminPlain);},'DomainException','coordinator cannot reveal passwords');
    $nivel_acceso=4;db_query('UPDATE usuarios SET nivel_acceso=2 WHERE id_usuario=9001');
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array(9002),$adminPlain);},'DomainException','revoked administrator cannot use an old session');
    db_query('UPDATE usuarios SET nivel_acceso=4 WHERE id_usuario=9001');
    $permitido=false;
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array(9002),$adminPlain);},'DomainException','unauthenticated session cannot read');
    $permitido=true;
    $token=app_csrf_token();gestion_password_validate_request('POST',$token);
    vault_check(true,'valid POST and CSRF accepted');
    vault_rejects(function(){gestion_password_validate_request('POST','wrong');},'DomainException','missing or invalid CSRF rejected');
    vault_rejects(function()use($token){gestion_password_validate_request('GET',$token);},'InvalidArgumentException','GET cannot reveal credentials');
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array('1 OR 1=1'),$adminPlain);},'InvalidArgumentException','untrusted identifiers rejected');
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array_fill(0,201,9002),$adminPlain);},'InvalidArgumentException','oversized request rejected');
    vault_rejects(function(){gestion_password_reveal(array(9002),'wrong');},'UnexpectedValueException','incorrect administrator confirmation denied');
    for($i=0;$i<4;$i++){try{gestion_password_reveal(array(9002),'wrong');}catch(UnexpectedValueException $e){}}
    vault_rejects(function()use($adminPlain){gestion_password_reveal(array(9002),$adminPlain);},'OverflowException','confirmation attempts limited across requests');
    $audit=db_fetch_assoc(db_query('SELECT COUNT(*) AS total FROM usuarios_password_consulta_log'));
    vault_check((int)$audit['total']===6,'successful and failed checks logged without any credentials');
    echo 'PASS '.$checks." checks. Temporary tables only; no actual credential changed or copied.\n";
} catch(Exception $e) { fwrite(STDERR,'FAIL '.$e->getMessage().PHP_EOL);exit(1); }
