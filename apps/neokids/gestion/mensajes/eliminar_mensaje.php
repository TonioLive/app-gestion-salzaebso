<?php
require_once __DIR__.'/common.php';
$message=fm_message((int)app_value($_POST,'id_mensaje',app_value($_GET,'id_mensaje')));
$_SESSION['fm_notice']='El historial se conserva. Puedes responder o marcar la conversación como leída.';
fm_redirect('ver_mensaje.php?id_mensaje='.(int)$message['id_nota_padre']);
