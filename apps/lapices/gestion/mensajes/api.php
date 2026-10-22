<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/centro_mensajes_lib.php';

app_security_headers();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

function staff_messages_api_response($payload, $status)
{
    http_response_code((int) $status);
    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    exit;
}

if (!staff_messages_user_can_access()) {
    staff_messages_api_response(
        array('ok' => false, 'message' => 'No tienes acceso al centro de mensajes.'),
        403
    );
}

if (!($panel_database instanceof mysqli)) {
    staff_messages_api_response(
        array('ok' => false, 'message' => 'No se puede conectar con los mensajes.'),
        503
    );
}

$action = trim((string) app_request_value($_GET, 'action', 'state'));
$method = strtoupper((string) app_request_value($_SERVER, 'REQUEST_METHOD', 'GET'));

if ($method === 'GET' && $action === 'state') {
    $classroomId = app_request_int($_GET, 'id_aula', 0);
    $conversations = staff_messages_conversations($panel_database, $classroomId);
    $totalUnread = 0;
    foreach ($conversations as $conversation) {
        $totalUnread += (int) $conversation['unread_count'];
    }
    staff_messages_api_response(
        array(
            'ok' => true,
            'classrooms' => staff_messages_classrooms($panel_database),
            'conversations' => $conversations,
            'total_unread' => $totalUnread,
        ),
        200
    );
}

if ($method === 'GET' && $action === 'messages') {
    $studentId = app_request_int($_GET, 'id_alumno', 0);
    $chat = staff_messages_chat($panel_database, $studentId);
    if ($chat === false) {
        staff_messages_api_response(
            array('ok' => false, 'message' => 'No se encuentra esta conversación.'),
            404
        );
    }
    staff_messages_api_response(array('ok' => true, 'chat' => $chat), 200);
}

if ($method === 'GET' && $action === 'notifications') {
    $notifications = staff_messages_notifications($panel_database);
    staff_messages_api_response(
        array(
            'ok' => true,
            'total_unread' => (int) $notifications['total'],
            'items' => $notifications['items'],
        ),
        200
    );
}

if ($method === 'POST') {
    $csrf = trim((string) app_request_value($_POST, '_csrf', ''));
    if (!app_verify_csrf($csrf)) {
        staff_messages_api_response(
            array('ok' => false, 'message' => 'La sesión ha caducado. Recarga la página.'),
            419
        );
    }

    if ($action === 'read') {
        $studentId = app_request_int($_POST, 'id_alumno', 0);
        if (!staff_messages_mark_read($panel_database, $studentId)) {
            staff_messages_api_response(
                array('ok' => false, 'message' => 'No se ha podido actualizar la lectura.'),
                400
            );
        }
        staff_messages_api_response(array('ok' => true), 200);
    }

    if ($action === 'send') {
        $studentId = app_request_int($_POST, 'id_alumno', 0);
        $threadId = app_request_int($_POST, 'id_hilo', 0);
        $text = trim((string) app_request_value($_POST, 'texto', ''));
        $subject = trim((string) app_request_value($_POST, 'asunto', ''));
        $sentThread = staff_messages_send(
            $panel_database,
            $studentId,
            $threadId,
            $text,
            $subject
        );
        if ($sentThread === false) {
            staff_messages_api_response(
                array('ok' => false, 'message' => 'No se ha podido enviar el mensaje.'),
                400
            );
        }
        staff_messages_mark_read($panel_database, $studentId);
        staff_messages_api_response(
            array('ok' => true, 'thread_id' => (int) $sentThread),
            201
        );
    }
}

staff_messages_api_response(
    array('ok' => false, 'message' => 'Operación no disponible.'),
    404
);
