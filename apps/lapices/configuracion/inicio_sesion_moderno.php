<?php
require_once __DIR__ . '/config.php';

function login_fail($message, $database = null, $username = '')
{
    if ($database instanceof mysqli) {
        $statement = mysqli_prepare(
            $database,
            'INSERT INTO sesion_fallos '
            . '(id_session, Usuario, Pass, Fecha, Hora, REMOTE_ADDR, '
            . 'HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, descripcion_fallo) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($statement) {
            $session_id = session_id();
            $redacted_password = '';
            $date = date('Y/m/d');
            $time = date('H:i');
            $remote_address = app_request_value($_SERVER, 'REMOTE_ADDR', '');
            $language = app_request_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '');
            $user_agent = app_request_value($_SERVER, 'HTTP_USER_AGENT', '');
            $description = $message;
            mysqli_stmt_bind_param(
                $statement,
                'sssssssss',
                $session_id,
                $username,
                $redacted_password,
                $date,
                $time,
                $remote_address,
                $language,
                $user_agent,
                $description
            );
            @mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
        }
    }

    $_SESSION['login_error'] = $message;
    $_SESSION['login_attempts'] = isset($_SESSION['login_attempts'])
        ? (int) $_SESSION['login_attempts'] + 1
        : 1;

    usleep(min(1200000, 250000 * $_SESSION['login_attempts']));
    header('Location: /index.php');
    exit;
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST') {
    header('Location: /index.php');
    exit;
}

if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
    login_fail('La sesión del formulario ha caducado. Vuelve a intentarlo.');
}

$username = trim((string) app_request_value($_POST, 'user', ''));
$password = (string) app_request_value($_POST, 'pass', '');

if ($username === '' || $password === '') {
    login_fail('Introduce el usuario y la contraseña.');
}

if (strlen($username) > 100 || !preg_match('/^[A-Za-z0-9._@-]+$/', $username)) {
    login_fail('El usuario indicado no tiene un formato válido.');
}

if (strlen($password) > 255) {
    login_fail('Las credenciales no son válidas.');
}

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    login_fail('No se puede conectar con el servicio en este momento. Inténtalo de nuevo más tarde.');
}

$statement = mysqli_prepare($database, 'SELECT * FROM usuarios WHERE usuario = ? LIMIT 1');
if (!$statement) {
    login_fail('No se puede validar el acceso en este momento.', $database, $username);
}

mysqli_stmt_bind_param($statement, 's', $username);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$user = $result ? mysqli_fetch_assoc($result) : false;
if ($result) {
    mysqli_free_result($result);
}
mysqli_stmt_close($statement);

$password_is_modern = false;
$password_is_valid = false;
if (is_array($user)) {
    $stored_password = (string) $user['pass'];
    $password_info = password_get_info($stored_password);
    $password_is_modern = !empty($password_info['algo']);
    $password_is_valid = $password_is_modern
        ? password_verify($password, $stored_password)
        : hash_equals($stored_password, $password);
}

if (!$password_is_valid) {
    login_fail('El usuario o la contraseña no son correctos.', $database, $username);
}

$user_id = (int) $user['id_usuario'];
$contract_statement = mysqli_prepare(
    $database,
    'SELECT 1 FROM personal_contratos '
    . 'WHERE id_empleado = ? AND ('
    . '(fecha_inicio <= CURRENT_DATE AND fecha_fin >= CURRENT_DATE) '
    . 'OR (fecha_inicio <= CURRENT_DATE AND (fecha_fin = ? OR fecha_fin IS NULL)) '
    . 'OR fecha_inicio > CURRENT_DATE'
    . ') LIMIT 1'
);

$has_contract = false;
if ($contract_statement) {
    $zero_date = '0000-00-00';
    mysqli_stmt_bind_param($contract_statement, 'is', $user_id, $zero_date);
    mysqli_stmt_execute($contract_statement);
    $contract_result = mysqli_stmt_get_result($contract_statement);
    $has_contract = $contract_result && mysqli_num_rows($contract_result) > 0;
    if ($contract_result) {
        mysqli_free_result($contract_result);
    }
    mysqli_stmt_close($contract_statement);
}

if (!$has_contract) {
    login_fail('El usuario no tiene un contrato activo o programado.', $database, $username);
}

session_regenerate_id(true);

$_SESSION['nombre_usu'] = $username;
$_SESSION['permitido'] = true;
$_SESSION['nivel_acceso'] = (int) $user['nivel_acceso'];
$_SESSION['nombre_completo'] = trim($user['Nombre'] . ' ' . $user['Apellido1'] . ' ' . $user['Apellido2']);
$_SESSION['email_usu'] = $user['Email'];
$_SESSION['nombre'] = $user['Nombre'];
$_SESSION['idusuario'] = $user_id;
$_SESSION['id_centro_usuario'] = (int) $user['id_centro'];
$_SESSION['usuario_multicentro'] = (int) $user['multicentro'];
$_SESSION['usuario_password'] = hash('sha256', session_id() . $username);
app_touch_user_activity();

$permission_fields = array(
    'zona_alumnos',
    'zona_aulas',
    'zona_facturacion',
    'zona_comedor',
    'zona_almacen',
    'zona_incidencias',
    'zona_personal',
    'zona_pedidos',
    'zona_inferior',
    'zona_mensajes',
    'zona_compras',
    'zona_gabinete',
    'zona_corregir_pruebas',
    'zona_actividades',
);
foreach ($permission_fields as $permission_field) {
    $_SESSION[$permission_field] = isset($user[$permission_field])
        ? (int) $user[$permission_field]
        : 0;
}

if ((int) $user['nivel_acceso'] <= 2 || (int) $user['nivel_acceso'] === 10) {
    $_SESSION['centro_actual'] = (int) $user['id_centro'];
} else {
    /*
     * Administradores y coordinadoras deben elegir el centro de trabajo en
     * cada nuevo inicio de sesión. Así no heredan accidentalmente un centro
     * de una sesión anterior ni acceden a módulos sin contexto.
     */
    $_SESSION['centro_actual'] = 0;
    $_SESSION['nombre_centro_actual'] = '';
    $_SESSION['poblacion_centro_actual'] = '';
    $_SESSION['tipo_centro_actual'] = 0;
}

$session_statement = mysqli_prepare(
    $database,
    'INSERT INTO sesion '
    . '(id_session, Usuario, Tipo, Fecha, Hora, REMOTE_ADDR, '
    . 'HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, Cerrada, nombre_usuario) '
    . 'VALUES (?, ?, 0, ?, ?, ?, ?, ?, 0, ?)'
);
if ($session_statement) {
    $session_id = session_id();
    $date = date('Y/m/d');
    $time = date('H:i');
    $remote_address = app_request_value($_SERVER, 'REMOTE_ADDR', '');
    $language = app_request_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '');
    $user_agent = app_request_value($_SERVER, 'HTTP_USER_AGENT', '');
    $full_name = $_SESSION['nombre_completo'];
    mysqli_stmt_bind_param(
        $session_statement,
        'sissssss',
        $session_id,
        $user_id,
        $date,
        $time,
        $remote_address,
        $language,
        $user_agent,
        $full_name
    );
    @mysqli_stmt_execute($session_statement);
    mysqli_stmt_close($session_statement);
}

/*
 * Transparently upgrade plaintext passwords only when the existing column is
 * large enough for password_hash(). Until the schema migration enlarges it,
 * legacy credentials remain readable but are never written to logs.
 */
if (!$password_is_modern) {
    $column_result = mysqli_query(
        $database,
        "SELECT CHARACTER_MAXIMUM_LENGTH AS max_length "
        . "FROM information_schema.columns "
        . "WHERE table_schema = DATABASE() AND table_name = 'usuarios' "
        . "AND column_name = 'pass' LIMIT 1"
    );
    $column = $column_result ? mysqli_fetch_assoc($column_result) : false;
    if ($column_result) {
        mysqli_free_result($column_result);
    }

    if ($column && (int) $column['max_length'] >= 60) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $upgrade_statement = mysqli_prepare(
            $database,
            'UPDATE usuarios SET pass = ? WHERE id_usuario = ?'
        );
        if ($upgrade_statement) {
            mysqli_stmt_bind_param($upgrade_statement, 'si', $new_hash, $user_id);
            @mysqli_stmt_execute($upgrade_statement);
            mysqli_stmt_close($upgrade_statement);
        }
    }
}

unset($_SESSION['login_attempts']);
mysqli_close($database);

$redirect = app_safe_internal_path(
    app_request_value($_POST, 'redirect', '/index.php'),
    '/index.php'
);
header('Location: ' . $redirect);
exit;
