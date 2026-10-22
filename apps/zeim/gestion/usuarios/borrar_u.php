<?php
require_once '../../configuracion/config.php';
require '../../configuracion/autentifica.php';
http_response_code(410);
exit('Esta operación antigua está desactivada. Gestiona el acceso desde la ficha del usuario.');
