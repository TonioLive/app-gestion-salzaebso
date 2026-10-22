<?php
require_once __DIR__ . '/runtime.php';

app_start_session('accesoprivado');
app_security_headers(true);

$expired = app_request_value($_GET, 'motivo', '') === 'inactividad';
app_destroy_current_session();
header('Location: /index.php' . ($expired ? '?sesion=expirada' : ''), true, 303);
exit;
