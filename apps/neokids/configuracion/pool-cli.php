<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
define('APP_SITE','neokids');
require_once __DIR__.'/runtime.php';
foreach(array('sql_host'=>'APP_DB_HOST','sql_usuario'=>'APP_DB_USER','sql_pass'=>'APP_DB_PASSWORD','sql_db'=>'APP_DB_NAME') as $key=>$env) $GLOBALS[$key]=getenv($env)!==false?getenv($env):app_db_settings($key);
app_db()->set_charset('utf8mb4');
