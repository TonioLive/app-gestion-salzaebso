<?php
// Current Composer dependency; old runtime retained only for rollback.
if(PHP_VERSION_ID<80500){require dirname(__DIR__).'/.private/php85-legacy-fallback/mailer/class.phpmailer.php';return;}
require_once dirname(__DIR__).'/configuracion/dependencies.php';
