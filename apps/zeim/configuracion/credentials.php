<?php
require_once __DIR__.'/family-credential-vault.php';
// Argon2id: OWASP minimum of 19 MiB, two iterations, one lane.
function security_password_options() { return array('memory_cost'=>19456,'time_cost'=>2,'threads'=>1); }
function security_new_password($plain) { return password_hash((string)$plain,PASSWORD_ARGON2ID,security_password_options()); }
function security_password_needs_rehash($stored) { return password_needs_rehash((string)$stored,PASSWORD_ARGON2ID,security_password_options()); }
function security_password_hash($value) {
    $value=(string)$value;if($value==='' || !empty(password_get_info($value)['algo']))return $value;
    return security_new_password($value);
}
function security_generate_password($username) {
    $password=bin2hex(random_bytes(8));
    $_SESSION['_new_family_credentials'][(string)$username]=array('password'=>$password,'expires'=>time()+600);
    return $password;
}
function security_initial_password($username) {
    $entry=$_SESSION['_new_family_credentials'][(string)$username]??null;
    unset($_SESSION['_new_family_credentials'][(string)$username]);
    return $entry && (int)$entry['expires']>=time() ? $entry['password'] : '';
}
function security_upgrade_family_password($account,$password) {
    $stored=(string)$account['password_webcam'];
    if(empty(password_get_info($stored)['algo'])||security_password_needs_rehash($stored)) {
        $hash=security_new_password($password);
        if(!db_query_params('UPDATE padres SET password_webcam=? WHERE id_usuario=? AND usuario_webcam=? AND BINARY password_webcam=BINARY ?','siss',array($hash,(int)$account['id_usuario'],(string)$account['usuario_webcam'],$stored),app_db()))throw new RuntimeException('Could not upgrade family credential');
        $account['password_webcam']=$hash;
        security_store_family_password($account,$password);
    }
    return $account;
}
function security_escape($value) { return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'ISO-8859-1'); }
function security_html($value) {
    static $purifier;
    if(!$purifier) {
        require_once __DIR__.'/dependencies.php';
        $cache=dirname(__DIR__).'/.private/html-purifier';if(!is_dir($cache))mkdir($cache,0700,true);
        $config=HTMLPurifier_Config::createDefault();$config->set('Cache.SerializerPath',$cache);
        $config->set('HTML.Allowed','p,br,b,strong,i,em,u,ul,ol,li,blockquote,span,div,a[href|title],table,thead,tbody,tr,th,td');
        $config->set('URI.AllowedSchemes',array('https'=>true,'http'=>true,'mailto'=>true));
        $config->set('URI.DisableExternalResources',true);$purifier=new HTMLPurifier($config);
    }
    $raw=(string)$value;$utf8=(bool)preg_match('//u',$raw);$clean=$purifier->purify(app_text($raw));
    return $utf8?$clean:iconv('UTF-8','Windows-1252//TRANSLIT',$clean);
}
function security_unserialize($value,$options=array()) {
    if(!is_string($value)||strlen($value)>8*1024*1024)return false;
    return unserialize($value,array('allowed_classes'=>false,'max_depth'=>64));
}
