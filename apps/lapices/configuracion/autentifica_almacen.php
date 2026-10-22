<?php
require_once __DIR__ . '/autentifica.php';

if ((int) $nivel_acceso < 4 && empty($zona_almacen)) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}
