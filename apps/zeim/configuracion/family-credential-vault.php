<?php
/** Recoverable copies for authorised family access letters; login keeps its hash. */
function security_family_vault_key() {
    static $key;
    if ($key===null) {
        $key=file_get_contents(dirname(__DIR__).'/.private/family-credentials.key');
        if (!is_string($key) || strlen($key)!==32) {
            throw new RuntimeException('Family credential key unavailable');
        }
    }
    return $key;
}
function security_family_vault_id($account) {
    return hash('sha256', (int)$account['id_usuario']."\0".(string)$account['usuario_webcam']."\0".(string)$account['password_webcam']);
}
function security_store_family_password($account,#[\SensitiveParameter] $plain) {
    $plain=(string)$plain;$hash=(string)$account['password_webcam'];
    if ($plain==='' || empty($account['usuario_webcam']) || empty($account['id_usuario']))return;
    if (empty(password_get_info($hash)['algo']) || !password_verify($plain,$hash)) {
        throw new RuntimeException('Family credential copy does not match login');
    }
    $id=security_family_vault_id($account);
    $nonce=random_bytes(12);$tag='';
    $ciphertext=openssl_encrypt($plain,'aes-256-gcm',security_family_vault_key(),OPENSSL_RAW_DATA,$nonce,$tag,$id,16);
    if($ciphertext===false || strlen($tag)!==16)throw new RuntimeException('Family credential encryption failed');
    $sealed=base64_encode($nonce.$tag.$ciphertext);
    if (!db_query_params('INSERT INTO family_access_credentials (credential_key,parent_id,sealed_password) VALUES (?,?,?) ON DUPLICATE KEY UPDATE parent_id=VALUES(parent_id),sealed_password=VALUES(sealed_password)','sis',array($id,(int)$account['id_usuario'],$sealed),app_db())) {
        throw new RuntimeException('Could not save protected family credential');
    }
}
function security_can_view_family_passwords() {
    return PHP_SAPI==='cli' || (!empty($_SESSION['permitido']) && in_array((int)app_value($_SESSION,'nivel_acceso',0),array(3,4),true) && app_can('zona_alumnos'));
}
function security_create_family_relation($parent,$child,$relationship,$username,#[\SensitiveParameter] $password) {
    $hash=security_password_hash((string)$password);
    if(!db_query_params('INSERT INTO padres (id_usuario,padre_de,parentesco,usuario_webcam,password_webcam) VALUES (?,?,?,?,?)','iiiss',array((int)$parent,(int)$child,(int)$relationship,(string)$username,$hash),app_db()))throw new RuntimeException('Could not create family relationship');
    $id=(int)db_insert_id(app_db());
    if(empty(password_get_info((string)$password)['algo']))security_store_family_password(array('id_usuario'=>(int)$parent,'usuario_webcam'=>(string)$username,'password_webcam'=>$hash),(string)$password);
    return $id;
}
function security_family_password($account) {
    if (!is_array($account) || !security_can_view_family_passwords() || empty($account['password_webcam']) || empty($account['usuario_webcam']) || empty($account['id_usuario']))return '';
    if (PHP_SAPI!=='cli' && !security_owned_centre(0)) {
        $rows=app_rows('SELECT id_centro FROM alumnos WHERE id_usuario=? AND id_centro=? LIMIT 1','ii',array((int)app_value($account,'padre_de',0),(int)app_value($_SESSION,'id_centro_usuario',0)));
        if (!$rows)return '';
    }
    $id=security_family_vault_id($account);
    $rows=app_rows('SELECT sealed_password FROM family_access_credentials WHERE credential_key=?','s',array($id));
    if (!$rows)return '';
    $sealed=base64_decode($rows[0]['sealed_password'],true);
    if ($sealed===false || strlen($sealed)<=28)return '';
    $plain=openssl_decrypt(substr($sealed,28),'aes-256-gcm',security_family_vault_key(),OPENSSL_RAW_DATA,substr($sealed,0,12),substr($sealed,12,16),$id);
    if ($plain===false)return '';
    if (PHP_SAPI!=='cli' && !headers_sent())header('Cache-Control: no-store, private');
    return $plain;
}
