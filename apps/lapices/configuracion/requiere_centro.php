<?php

if (!function_exists('app_require_selected_centre')) {
    function app_require_selected_centre()
    {
        $is_authenticated = !empty($GLOBALS['permitido']);
        $access_level = isset($GLOBALS['nivel_acceso'])
            ? (int) $GLOBALS['nivel_acceso']
            : 0;
        $selected_centre = isset($GLOBALS['centro_actual'])
            ? (int) $GLOBALS['centro_actual']
            : 0;

        if (!$is_authenticated || $access_level < 2 || $access_level >= 10 || $selected_centre > 0) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $request_path = parse_url($request_uri, PHP_URL_PATH);
        if ($request_path === '/index.php' || $request_path === '/') {
            return;
        }

        header('Location: /index.php');
        exit;
    }
}

app_require_selected_centre();
