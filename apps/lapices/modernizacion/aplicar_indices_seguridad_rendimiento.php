<?php
/**
 * Índices de autenticación y sesión detectados por la auditoría 2026-09-09.
 * Uso exclusivo por CLI:
 *   php modernizacion/aplicar_indices_seguridad_rendimiento.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/configuracion/config.php';

$indexes = array(
    array(
        'table' => 'usuarios',
        'name' => 'idx_usuarios_login',
        'sql' => 'ALTER TABLE usuarios ADD INDEX idx_usuarios_login (usuario)',
    ),
    array(
        'table' => 'padres',
        'name' => 'idx_padres_login',
        'sql' => 'ALTER TABLE padres ADD INDEX idx_padres_login (usuario_webcam)',
    ),
    array(
        'table' => 'tokens_activos',
        'name' => 'idx_tokens_activos_token',
        'sql' => 'ALTER TABLE tokens_activos ADD INDEX idx_tokens_activos_token (token(191))',
    ),
    array(
        'table' => 'tokens_activos',
        'name' => 'idx_tokens_activos_creacion',
        'sql' => 'ALTER TABLE tokens_activos ADD INDEX idx_tokens_activos_creacion (creacion)',
    ),
    array(
        'table' => 'tokens_fcm',
        'name' => 'idx_tokens_fcm_lookup',
        'sql' => 'ALTER TABLE tokens_fcm ADD INDEX idx_tokens_fcm_lookup '
            . '(id_usuario, id_padre, FCMtoken(128))',
    ),
    array(
        'table' => 'control_acceso_padres',
        'name' => 'idx_control_acceso_sesion',
        'sql' => 'ALTER TABLE control_acceso_padres '
            . 'ADD INDEX idx_control_acceso_sesion (sesion)',
    ),
);

$errors = 0;
foreach ($indexes as $index) {
    $exists = db_query_params(
        'SELECT 1 FROM information_schema.statistics '
        . 'WHERE table_schema=DATABASE() AND table_name=? AND index_name=? LIMIT 1',
        'ss',
        array($index['table'], $index['name'])
    );
    if ($exists && db_num_rows($exists) > 0) {
        echo '[YA EXISTE] ' . $index['table'] . '.' . $index['name'] . "\n";
        continue;
    }

    if (!db_query($index['sql'])) {
        fwrite(STDERR, '[ERROR] ' . $index['table'] . '.' . $index['name'] . "\n");
        $errors++;
        continue;
    }

    echo '[CREADO] ' . $index['table'] . '.' . $index['name'] . "\n";
}

exit($errors === 0 ? 0 : 1);
