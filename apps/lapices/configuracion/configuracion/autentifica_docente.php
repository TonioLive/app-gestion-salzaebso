<?php

if ($permitido != true || $nivel_acceso < 2) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}

require_once __DIR__ . '/requiere_centro.php';
