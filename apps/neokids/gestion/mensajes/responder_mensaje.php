<?php
require_once __DIR__.'/common.php';
$message=fm_message((int)app_value($_GET,'id_mensaje'));
fm_redirect('ver_mensaje.php?id_mensaje='.(int)$message['id_nota_padre'].'#respuesta');
