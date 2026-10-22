<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
foreach (array('PHPMailer'=>'PHPMailer\\PHPMailer\\PHPMailer','phpmailerException'=>'PHPMailer\\PHPMailer\\Exception','SMTP'=>'PHPMailer\\PHPMailer\\SMTP','POP3'=>'PHPMailer\\PHPMailer\\POP3') as $legacy=>$modern) {
    if (!class_exists($legacy,false)) class_alias($modern,$legacy);
}
