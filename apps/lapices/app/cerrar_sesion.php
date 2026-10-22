<?php
require_once dirname(__DIR__) . '/configuracion/runtime.php';

app_start_session('accesoprivado');
app_security_headers(true);

$expired = app_request_value($_GET, 'motivo', '') === 'inactividad';
app_destroy_current_session();
header(
    'Location: /app/login.php?sesion=' . ($expired ? 'expirada' : 'cerrada'),
    true,
    303
);
exit;
